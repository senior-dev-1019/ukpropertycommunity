<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\Menu\Models\Menu as MenuModel;
use Botble\Testimonial\Models\Testimonial;
use Faker\Factory;

class TestimonialSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('testimonials');

        $data = [
            'en_US' => [
                [
                    'name'    => 'Adam Williams',
                    'company' => 'CEO Of Microwoft',
                ],
                [
                    'name'    => 'Retha Deowalim',
                    'company' => 'CEO Of Apple',
                ],
                [
                    'name'    => 'Sam J. Wasim',
                    'company' => 'Pio Founder',
                ],
                [
                    'name'    => 'Usan Gulwarm',
                    'company' => 'CEO Of Facewarm',
                ],
                [
                    'name'    => 'Shilpa Shethy',
                    'company' => 'CEO Of Zapple',
                ],
            ],
            'vi'    => [
                [
                    'name'    => 'Adam Williams',
                    'company' => 'Giám đốc Microwoft',
                ],
                [
                    'name'    => 'Retha Deowalim',
                    'company' => 'Giám đốc Apple',
                ],
                [
                    'name'    => 'Sam J. Wasim',
                    'company' => 'Nhà sáng lập Pio',
                ],
                [
                    'name'    => 'Usan Gulwarm',
                    'company' => 'Giám đốc Facewarm',
                ],
                [
                    'name'    => 'Shilpa Shethy',
                    'company' => 'Giám đốc Zapple',
                ],
            ],
        ];

        Testimonial::truncate();

        $faker = Factory::create();

        foreach ($data as $locale => $testimonials) {
            foreach ($testimonials as $index => $item) {
                $item['image'] = 'testimonials/' . ($index + 1) . '.jpg';
                $item['content'] = $faker->realText(50);

                $testimonial = Testimonial::create($item);

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id'   => $index + 1,
                        'reference_type' => MenuModel::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($testimonial, $locale, $originValue);
            }
        }
    }
}
