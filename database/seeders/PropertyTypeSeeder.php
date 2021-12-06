<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\RealEstate\Models\Type;
use Botble\Slug\Models\Slug;

class PropertyTypeSeeder extends BaseSeeder
{
    public function run()
    {
        LanguageMeta::where('reference_type', Type::class)->delete();
        Type::truncate();

        $data = [
            'en_US' => [
                [
                    'name'  => 'For Sale',
                    'slug'  => 'sale',
                    'order' => 0,
                ],
                [
                    'name'  => 'For Rent',
                    'slug'  => 'rent',
                    'order' => 1,
                ],
            ],
            'vi'    => [
                [
                    'name'  => 'Mua bán',
                    'slug'  => 'mua-ban',
                    'order' => 0,
                ],
                [
                    'name'  => 'Cho thuê',
                    'slug'  => 'cho-thue',
                    'order' => 1,
                ],
            ],
        ];

        Slug::where('reference_type', Type::class)->delete();
        LanguageMeta::where('reference_type', Type::class)->delete();

        foreach ($data as $locale => $types) {
            foreach ($types as $index => $item) {
                $type = Type::create($item);

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id'   => $index + 1,
                        'reference_type' => Type::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($type, $locale, $originValue);
            }
        }
    }
}
