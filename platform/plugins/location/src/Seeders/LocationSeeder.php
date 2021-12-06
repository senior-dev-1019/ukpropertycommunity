<?php

namespace Botble\Location\Seeders;

use Artisan;
use Botble\Language\Models\LanguageMeta;
use Botble\Location\Models\City;
use Botble\Location\Models\Country;
use Botble\Location\Models\State;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            LanguageMeta::whereIn('reference_type', [City::class, State::class, Country::class])->delete();
        }

        City::truncate();
        State::truncate();
        Country::truncate();

        $this->createDataForUs();
        $this->createDataForCanada();
    }

    protected function createDataForUs()
    {
        Country::create([
            'id'          => 1,
            'name'        => 'United States of America',
            'nationality' => 'Americans',
            'status'      => 'published',
            'order'       => 0,
        ]);

        $states = file_get_contents(__DIR__ . '/../../database/files/us/states.json');
        $states = json_decode($states, true);
        foreach ($states as $state) {
            State::create($state);
        }

        $cities = file_get_contents(__DIR__ . '/../../database/files/us/cities.json');
        $cities = json_decode($cities, true);
        foreach ($cities as $item) {
            if (City::where('name', $item['fields']['city'])->count() > 0) {
                continue;
            }

            $state = State::where('abbreviation', $item['fields']['state_code'])->first();
            if (!$state) {
                continue;
            }

            $city = [
                'name'       => $item['fields']['city'],
                'state_id'   => $state->id,
                'country_id' => 1,
            ];
            City::create($city);
        }

        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            Artisan::call('cms:language:sync', ['class' => Country::class]);
            Artisan::call('cms:language:sync', ['class' => State::class]);
            Artisan::call('cms:language:sync', ['class' => City::class]);
        }
    }

    protected function createDataForCanada()
    {
        Country::create([
            'id'          => 2,
            'name'        => 'Canada',
            'nationality' => 'Canada',
            'status'      => 'published',
            'order'       => 1,
        ]);

        $states = file_get_contents(__DIR__ . '/../../database/files/ca/states.json');
        $states = json_decode($states, true);
        foreach ($states as $state) {
            State::create($state);
        }

        $cities = file_get_contents(__DIR__ . '/../../database/files/ca/cities.json');
        $cities = json_decode($cities, true);
        foreach ($cities as $item) {

            $state = State::where('name', $item['name'])->first();
            if (!$state) {
                continue;
            }

            foreach ($item['cities'] as $cityName) {
                $city = [
                    'name'       => $cityName,
                    'state_id'   => $state->id,
                    'country_id' => 2,
                ];
                City::create($city);
            }
        }
    }
}
