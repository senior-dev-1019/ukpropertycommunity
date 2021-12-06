<?php

namespace Database\Seeders;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\LanguageMeta;
use Botble\Location\Models\City;
use Botble\Location\Models\Country;
use Botble\Location\Models\State;
use Botble\Slug\Models\Slug;
use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MetaBox;
use SlugHelper;

class LocationSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('cities');
        City::truncate();
        Country::truncate();
        State::truncate();
        MetaBoxModel::where('reference_type', City::class)->delete();
        LanguageMeta::where('reference_type', City::class)->delete();
        Slug::where('reference_type', City::class)->delete();

        MetaBoxModel::where('reference_type', Country::class)->delete();
        LanguageMeta::where('reference_type', Country::class)->delete();

        $data = [
            'en_US' => [
                [
                    'name'        => 'United States',
                    'nationality' => 'United States of America',
                    'status'      => BaseStatusEnum::PUBLISHED,
                    'states'      => [
                        [
                            'name'   => 'California',
                            'status' => BaseStatusEnum::PUBLISHED,
                        ],
                        [
                            'name'   => 'Alaska',
                            'status' => BaseStatusEnum::PUBLISHED,
                        ],
                        [
                            'name'   => 'Arizona',
                            'status' => BaseStatusEnum::PUBLISHED,
                        ],
                        [
                            'name'   => 'South Carolina',
                            'status' => BaseStatusEnum::PUBLISHED,
                        ],
                        [
                            'name'   => 'New Jersey',
                            'status' => BaseStatusEnum::PUBLISHED,
                        ],
                    ],
                    'cities'      => [
                        [
                            'name'   => 'Alhambra',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                        [
                            'name'   => 'Oakland',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                        [
                            'name'   => 'Bakersfield',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                        [
                            'name'   => 'Anaheim',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                        [
                            'name'   => 'San Francisco',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                        [
                            'name'   => 'San DiegoCounty',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                    ],
                ],
            ],
            'vi'    => [
                [
                    'name'        => 'Việt Nam',
                    'nationality' => 'Việt Nam',
                    'status'      => BaseStatusEnum::PUBLISHED,
                    'states'      => [
                        [
                            'name'   => 'Quảng Ninh',
                            'status' => BaseStatusEnum::PUBLISHED,
                        ],
                        [
                            'name'   => 'Hà Nội',
                            'status' => BaseStatusEnum::PUBLISHED,
                        ],
                        [
                            'name'   => 'Hải Phòng',
                            'status' => BaseStatusEnum::PUBLISHED,
                        ],
                        [
                            'name'   => 'Hồ Chí Minh',
                            'status' => BaseStatusEnum::PUBLISHED,
                        ],
                        [
                            'name'   => 'Đà Nẵng',
                            'status' => BaseStatusEnum::PUBLISHED,
                        ],
                    ],
                    'cities'      => [
                        [
                            'name'   => 'Hồ Chí Minh',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                        [
                            'name'   => 'Hà Nội',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                        [
                            'name'   => 'Đà Nẵng',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                        [
                            'name'   => 'Hải Phòng',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                        [
                            'name'   => 'Bình Dương',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                        [
                            'name'   => 'Long An',
                            'status' => BaseStatusEnum::PUBLISHED,
                            'is_featured' => 1,
                        ],
                    ],
                ],
            ],
        ];

        foreach ($data as $locale => $countries) {
            foreach ($countries as $index => $item) {
                $country = Country::create([
                    'name'        => $item['name'],
                    'nationality' => $item['nationality'],
                    'status'      => $item['status'],
                ]);
                if (isset($item['states']) && !empty($item['states'])) {
                    foreach ($item['states'] as $childIndex => $child) {
                        $this->createState($child, $locale, $index + $childIndex, $country->id);
                    }
                }
                if (isset($item['cities']) && !empty($item['cities'])) {
                    foreach ($item['cities'] as $childIndex => $child) {
                        $city = $this->createCity($child, $locale, $index + $childIndex, $country->id);
                    }
                }

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id'   => $index + 1,
                        'reference_type' => Country::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($country, $locale, $originValue);
            }
        }

        $this->createDataForUs();
    }

    /**
     * @param array $item
     * @param string $locale
     * @param int $index
     * @param int $parentId
     * @return State|Model
     */
    protected function createState(array $item, string $locale, int $index, int $parentId = 0)
    {
        $item['country_id'] = $parentId;
        $state = State::create($item);

        $originValue = null;

        if ($locale !== 'en_US') {
            $originValue = LanguageMeta::where([
                'reference_id'   => $index + 1,
                'reference_type' => State::class,
            ])->value('lang_meta_origin');
        }

        LanguageMeta::saveMetaData($state, $locale, $originValue);

        return $state;
    }

    /**
     * @param array $item
     * @param string $locale
     * @param int $index
     * @param int $parentId
     * @return City|Model
     */
    protected function createCity(array $item, string $locale, int $index, int $parentId = 0)
    {
        $faker = Factory::create();
        $item['country_id'] = $parentId;
        $item['state_id'] = $locale == 'en_US' ? $faker->numberBetween(1, 5) : $faker->numberBetween(6, 10);
        $city = City::create($item);

        MetaBox::saveMetaBoxData($city, 'image', 'cities/c-' . ($index + 1) . '.png');

        Slug::create([
            'reference_type' => City::class,
            'reference_id'   => $city->id,
            'key'            => Str::slug($city->name),
            'prefix'         => SlugHelper::getPrefix(City::class, 'city'),
        ]);

        $originValue = null;

        if ($locale !== 'en_US') {
            $originValue = LanguageMeta::where([
                'reference_id'   => $index + 1,
                'reference_type' => City::class,
            ])->value('lang_meta_origin');
        }

        LanguageMeta::saveMetaData($city, $locale, $originValue);

        return $city;
    }

    protected function createDataForUs()
    {
        $states = file_get_contents(database_path('seeders/files/states.json'));
        $states = json_decode($states, true);
        foreach ($states as $item) {
            $state = State::create($item);

            LanguageMeta::saveMetaData($state, 'en_US');
        }

        $cities = file_get_contents(database_path('seeders/files/cities.json'));
        $cities = json_decode($cities, true);
        foreach ($cities as $item) {
            if (City::where('name', $item['fields']['city'])->count() > 0) {
                continue;
            }

            $state = State::where('abbreviation', $item['fields']['state_code'])->first();

            if (!$state) {
                continue;
            }

            $data = [
                'name'       => $item['fields']['city'],
                'state_id'   => $state->id,
                'country_id' => 1,
            ];

            $city = City::create($data);

            Slug::create([
                'reference_type' => City::class,
                'reference_id'   => $city->id,
                'key'            => Str::slug($city->name),
                'prefix'         => SlugHelper::getPrefix(City::class, 'city'),
            ]);

            LanguageMeta::saveMetaData($city, 'en_US');
        }
    }
}
