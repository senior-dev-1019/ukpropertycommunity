<?php

namespace Database\Seeders;

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\RealEstate\Models\Feature;
use Botble\Slug\Models\Slug;

class PropertyFeatureSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Feature::truncate();
        Slug::where('reference_type', Feature::class)->delete();
        MetaBoxModel::where('reference_type', Feature::class)->delete();
        LanguageMeta::where('reference_type', Feature::class)->delete();

        $data = [
            'en_US' => [
                [
                    'name' => 'Wifi',
                ],
                [
                    'name' => 'Parking',
                ],
                [
                    'name' => 'Swimming pool',
                ],
                [
                    'name' => 'Balcony',
                ],
                [
                    'name' => 'Garden',
                ],
                [
                    'name' => 'Security',
                ],
                [
                    'name' => 'Fitness center',
                ],
                [
                    'name' => 'Air Conditioning',
                ],
                [
                    'name' => 'Central Heating  ',
                ],
                [
                    'name' => 'Laundry Room',
                ],
                [
                    'name' => 'Pets Allow',
                ],
                [
                    'name' => 'Spa & Massage',
                ],
            ],
            'vi'    => [
                [
                    'name' => 'Wifi',
                ],
                [
                    'name' => 'Bãi đậu xe',
                ],
                [
                    'name' => 'Hồ bơi',
                ],
                [
                    'name' => 'Ban công',
                ],
                [
                    'name' => 'Sân vườn',
                ],
                [
                    'name' => 'An ninh',
                ],
                [
                    'name' => 'Trung tâm thể dục',
                ],
                [
                    'name' => 'Điều hoà nhiệt độ',
                ],
                [
                    'name' => 'Hệ thống sưởi trung tâm',
                ],
                [
                    'name' => 'Phòng giặt ủi',
                ],
                [
                    'name' => 'Cho phép nuôi thú',
                ],
                [
                    'name' => 'Spa & Massage',
                ],
            ],
        ];

        foreach ($data as $locale => $properties) {
            foreach ($properties as $index => $item) {
                $item['icon'] = 'fas fa-check';
                $feature = Feature::create($item);

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id'   => $index + 1,
                        'reference_type' => Feature::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($feature, $locale, $originValue);
            }
        }
    }
}
