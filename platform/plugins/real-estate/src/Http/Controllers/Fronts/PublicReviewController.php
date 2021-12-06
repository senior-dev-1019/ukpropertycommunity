<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Repositories\Interfaces\ReviewInterface;
use Botble\RealEstate\Http\Requests\ReviewRequest;
use Botble\RealEstate\Models\ReviewMeta;

class PublicReviewController
{

    /**
     * @var ReviewInterface
     */
    protected $reviewRepository;

   

    /**
     * PublicReviewController constructor.
     * @param ReviewInterface $reviewRepository
     */
    public function __construct(
        ReviewInterface $reviewRepository
    ) {
        $this->reviewRepository = $reviewRepository;
    }


    /**
     * @param ReviewRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postCreateReview(ReviewRequest $request, BaseHttpResponse $response)
    {
        $exists = $this->reviewRepository->count([
            'account_id' => auth('account')->id(),
            'reviewable_id'  => $request->input('reviewable_id'),
            'reviewable_type'  => $request->input('reviewable_type'),
        ]);
        
        if ($exists > 0) {
            return $response
                ->setError()
                ->setMessage(__('You have reviewed this product already!'));
        }

        $request->merge(['account_id' => auth('account')->id()]);

        $review = $this->reviewRepository->createOrUpdate($request->input());
        
        foreach ($request->input('meta') as $key => $value) {
            ReviewMeta::setMeta($key, $value, $review->id);
        }

        return $response->setMessage(__('Added review successfully!'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function getDeleteReview($id, BaseHttpResponse $response)
    {
        $review = $this->reviewRepository->findOrFail($id);

        if (auth()->check() || (auth('account')->check() && auth('account')->id() == $review->account_id)) {

            $review->meta()->delete();
            $this->reviewRepository->delete($review);

            return $response->setMessage(__('Deleted review successfully!'));
        }

        abort(401);
    }
}
