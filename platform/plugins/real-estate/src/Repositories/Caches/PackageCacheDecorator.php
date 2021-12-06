<?php

namespace Botble\RealEstate\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\RealEstate\Repositories\Interfaces\PackageInterface;

class PackageCacheDecorator extends CacheAbstractDecorator implements PackageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByLocale()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
