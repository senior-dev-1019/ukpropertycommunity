<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\RealEstate\Repositories\Interfaces\PackageInterface;
use Botble\Base\Enums\BaseStatusEnum;
use Language;

class PackageRepository extends RepositoriesAbstract implements PackageInterface
{
    public function getByLocale()
    {
        $data = $this->model
            ->join('language_meta', 'language_meta.reference_id', $this->model->getTable() . '.id')
            ->where('language_meta.reference_type', get_class($this->model))
            ->where('language_meta.lang_meta_code', Language::getCurrentLocaleCode())
            ->where(['status' => BaseStatusEnum::PUBLISHED])
            ->orderBy('order', 'DESC')
            ->get();

        $this->resetModel();

        return $data;
    }
}
