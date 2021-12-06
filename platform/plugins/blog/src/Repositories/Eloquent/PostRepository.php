<?php

namespace Botble\Blog\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Eloquent;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class PostRepository extends RepositoriesAbstract implements PostInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFeatured(int $limit = 5, array $with = [])
    {
        $data = $this->model
            ->where([
                'status'      => BaseStatusEnum::PUBLISHED,
                'is_featured' => 1,
            ])
            ->limit($limit)
            ->with(array_merge(['slugable'], $with))
            ->orderBy('created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getListPostNonInList(array $selected = [], $limit = 7, array $with = [])
    {
        $data = $this->model
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->whereNotIn('id', $selected)
            ->limit($limit)
            ->with($with)
            ->orderBy('created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getRelated($id, $limit = 3)
    {
        $data = $this->model
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->where('id', '!=', $id)
            ->limit($limit)
            ->with('slugable')
            ->orderBy('created_at', 'desc')
            ->whereHas('categories', function ($query) use ($id) {
                $query->whereIn('categories.id', $this->getRelatedCategoryIds($id));
            });

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getRelatedCategoryIds($model)
    {
        $model = $model instanceof Eloquent ? $model : $this->findById($model);

        if (!$model) {
            return [];
        }

        try {
            return $model->categories()->allRelatedIds()->toArray();
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getByCategory($categoryId, $paginate = 12, $limit = 0)
    {
        if (!is_array($categoryId)) {
            $categoryId = [$categoryId];
        }

        $data = $this->model
            ->where('posts.status', BaseStatusEnum::PUBLISHED)
            ->join('post_categories', 'post_categories.post_id', '=', 'posts.id')
            ->join('categories', 'post_categories.category_id', '=', 'categories.id')
            ->whereIn('post_categories.category_id', $categoryId)
            ->select('posts.*')
            ->distinct()
            ->with('slugable')
            ->orderBy('posts.created_at', 'desc');

        if ($paginate != 0) {
            return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
        }

        return $this->applyBeforeExecuteQuery($data)->limit($limit)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getByUserId($authorId, $paginate = 6)
    {
        $data = $this->model
            ->where([
                'status'    => BaseStatusEnum::PUBLISHED,
                'author_id' => $authorId,
            ])
            ->with('slugable')
            ->orderBy('created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
    }

    /**
     * {@inheritDoc}
     */
    public function getDataSiteMap()
    {
        $data = $this->model
            ->with('slugable')
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->orderBy('created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getByTag($tag, $paginate = 12)
    {
        $data = $this->model
            ->with('slugable', 'categories', 'categories.slugable', 'author')
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->whereHas('tags', function ($query) use ($tag) {
                /**
                 * @var Builder $query
                 */
                $query->where('tags.id', $tag);
            })
            ->orderBy('created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
    }

    /**
     * {@inheritDoc}
     */
    public function getRecentPosts($limit = 5, $categoryId = 0)
    {
        $data = $this->model->where(['status' => BaseStatusEnum::PUBLISHED]);

        if ($categoryId != 0) {
            $data = $data->join('post_categories', 'post_categories.post_id', '=', 'posts.id')
                ->where('post_categories.category_id', $categoryId);
        }

        $data = $data->limit($limit)
            ->with('slugable')
            ->select('posts.*')
            ->orderBy('created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getSearch($query, $limit = 10, $paginate = 10)
    {
        $data = $this->model->with('slugable')->where('status', BaseStatusEnum::PUBLISHED);
        foreach (explode(' ', $query) as $term) {
            $data = $data->where('name', 'LIKE', '%' . $term . '%');
        }

        $data = $data
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $data = $data->limit($limit);
        }

        if ($paginate) {
            return $this->applyBeforeExecuteQuery($data)->paginate($paginate);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getAllPosts($perPage = 12, $active = true, array $with = ['slugable'])
    {
        $data = $this->model
            ->with($with)
            ->orderBy('created_at', 'desc');

        if ($active) {
            $data = $data->where('status', BaseStatusEnum::PUBLISHED);
        }

        return $this->applyBeforeExecuteQuery($data)->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function getPopularPosts($limit, array $args = [])
    {
        $data = $this->model
            ->with('slugable')
            ->orderBy('views', 'desc')
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->limit($limit);

        if (!empty(Arr::get($args, 'where'))) {
            $data = $data->where($args['where']);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters(array $filters)
    {
        $this->model = $this->originalModel;

        if ($filters['categories'] !== null) {
            $categories = array_filter((array)$filters['categories']);

            $this->model = $this->model->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('categories.id', $categories);
            });
        }

        if ($filters['categories_exclude'] !== null) {
            $excludeCategories = array_filter((array)$filters['categories_exclude']);

            $this->model = $this->model->whereHas('categories', function ($query) use ($excludeCategories) {
                $query->whereNotIn('categories.id', $excludeCategories);
            });
        }

        if ($filters['exclude'] !== null) {
            $this->model = $this->model->whereNotIn('id', array_filter((array)$filters['exclude']));
        }

        if ($filters['include'] !== null) {
            $this->model = $this->model->whereNotIn('id', array_filter((array)$filters['include']));
        }

        if ($filters['author'] !== null) {
            $this->model = $this->model->whereIn('author_id', array_filter((array)$filters['author']));
        }

        if ($filters['author_exclude'] !== null) {
            $this->model = $this->model->whereNotIn('author_id', array_filter((array)$filters['author_exclude']));
        }

        if ($filters['featured'] !== null) {
            $this->model = $this->model->where('is_featured', $filters['featured']);
        }

        if ($filters['search'] !== null) {
            $this->model = $this->model->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('content', 'like', '%' . $filters['search'] . '%');
        }

        $orderBy = isset($filters['order_by']) ? $filters['order_by'] : 'updated_at';
        $order = isset($filters['order']) ? $filters['order'] : 'desc';

        $this->model = $this->model->where('status', BaseStatusEnum::PUBLISHED)->orderBy($orderBy, $order);

        return $this->applyBeforeExecuteQuery($this->model)->paginate((int)$filters['per_page']);
    }
}
