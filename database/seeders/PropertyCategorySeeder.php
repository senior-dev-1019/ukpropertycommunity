<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\RealEstate\Models\Category;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Str;
use SlugHelper;

class PropertyCategorySeeder extends BaseSeeder
{
    public function run()
    {
        Slug::where('reference_type', Category::class)->delete();
        LanguageMeta::where('reference_type', Category::class)->delete();
        Category::truncate();

        $data = [
            'en_US' => [
                [
                    'name'       => 'Apartment',
                    'is_default' => true,
                    'order'      => 0,
                ],
                [
                    'name'       => 'Villa',
                    'is_default' => false,
                    'order'      => 1,
                ],
                [
                    'name'       => 'Condo',
                    'is_default' => false,
                    'order'      => 2,
                ],
                [
                    'name'       => 'House',
                    'is_default' => false,
                    'order'      => 3,
                ],
                [
                    'name'       => 'Land',
                    'is_default' => false,
                    'order'      => 4,
                ],
                [
                    'name'       => 'Commercial property',
                    'is_default' => false,
                    'order'      => 5,
                ],
            ],
            'vi'    => [
                [
                    'name'       => 'Căn hộ dịch vụ',
                    'is_default' => true,
                    'order'      => 0,
                ],
                [
                    'name'       => 'Biệt thự',
                    'is_default' => false,
                    'order'      => 1,
                ],
                [
                    'name'       => 'Căn hộ',
                    'is_default' => false,
                    'order'      => 2,
                ],
                [
                    'name'       => 'Nhà',
                    'is_default' => false,
                    'order'      => 3,
                ],
                [
                    'name'       => 'Đất',
                    'is_default' => false,
                    'order'      => 4,
                ],
                [
                    'name'       => 'Bất động sản thương mại',
                    'is_default' => false,
                    'order'      => 5,
                ],
            ],
        ];

        Slug::where('reference_type', Category::class)->delete();
        LanguageMeta::where('reference_type', Category::class)->delete();

        foreach ($data as $locale => $categories) {
            foreach ($categories as $index => $item) {
                $category = Category::create($item);

                Slug::create([
                    'reference_type' => Category::class,
                    'reference_id'   => $category->id,
                    'key'            => Str::slug($category->name),
                    'prefix'         => SlugHelper::getPrefix(Category::class),
                ]);

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id'   => $index + 1,
                        'reference_type' => Category::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($category, $locale, $originValue);
            }
        }
    }
}
