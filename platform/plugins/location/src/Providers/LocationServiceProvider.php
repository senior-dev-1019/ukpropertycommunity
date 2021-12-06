<?php

namespace Botble\Location\Providers;

use Botble\Location\Facades\LocationFacade;
use Botble\Location\Models\City;
use Botble\Location\Repositories\Caches\CityCacheDecorator;
use Botble\Location\Repositories\Eloquent\CityRepository;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Models\Country;
use Botble\Location\Repositories\Caches\CountryCacheDecorator;
use Botble\Location\Repositories\Eloquent\CountryRepository;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Botble\Location\Models\State;
use Botble\Location\Repositories\Caches\StateCacheDecorator;
use Botble\Location\Repositories\Eloquent\StateRepository;
use Botble\Location\Repositories\Interfaces\StateInterface;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Botble\Base\Supports\Helper;
use Illuminate\Support\Facades\Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
use Language;

class LocationServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(CountryInterface::class, function () {
            return new CountryCacheDecorator(new CountryRepository(new Country));
        });

        $this->app->bind(StateInterface::class, function () {
            return new StateCacheDecorator(new StateRepository(new State));
        });

        $this->app->bind(CityInterface::class, function () {
            return new CityCacheDecorator(new CityRepository(new City));
        });

        Helper::autoload(__DIR__ . '/../../helpers');

        AliasLoader::getInstance()->alias('Location', LocationFacade::class);
    }

    public function boot()
    {
        $this->setNamespace('plugins/location')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadRoutes(['web'])
            ->publishAssets();

        if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            Language::registerModule([
                Country::class,
                State::class,
                City::class,
            ]);
        }

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id'          => 'cms-plugins-location',
                    'priority'    => 900,
                    'parent_id'   => null,
                    'name'        => 'plugins/location::location.name',
                    'icon'        => 'fas fa-globe',
                    'url'         => null,
                    'permissions' => ['country.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-country',
                    'priority'    => 0,
                    'parent_id'   => 'cms-plugins-location',
                    'name'        => 'plugins/location::country.name',
                    'icon'        => null,
                    'url'         => route('country.index'),
                    'permissions' => ['country.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-state',
                    'priority'    => 1,
                    'parent_id'   => 'cms-plugins-location',
                    'name'        => 'plugins/location::state.name',
                    'icon'        => null,
                    'url'         => route('state.index'),
                    'permissions' => ['state.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-city',
                    'priority'    => 2,
                    'parent_id'   => 'cms-plugins-location',
                    'name'        => 'plugins/location::city.name',
                    'icon'        => null,
                    'url'         => route('city.index'),
                    'permissions' => ['city.index'],
                ]);
        });
    }
}
