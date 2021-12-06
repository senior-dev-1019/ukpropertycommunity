<?php

use Botble\Base\Models\BaseModel;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Location\Models\City;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Theme\Resido\Http\Requests\CityRequest;
use Botble\Theme\Supports\Youtube;

register_page_template([
    'homepage' => __('Homepage'),
]);

register_sidebar([
    'id'          => 'footer_sidebar_1',
    'name'        => __('Footer sidebar 1'),
    'description' => __('Footer sidebar 1 for Resido theme'),
]);

register_sidebar([
    'id'          => 'footer_sidebar_2',
    'name'        => __('Footer sidebar 2'),
    'description' => __('Footer sidebar 2 for Resido theme'),
]);

register_sidebar([
    'id'          => 'footer_sidebar_3',
    'name'        => __('Footer sidebar 3'),
    'description' => __('Footer sidebar 3 for Resido theme'),
]);

register_sidebar([
    'id'          => 'property_sidebar',
    'name'        => __('Property sidebar'),
    'description' => __('Property sidebar for Resido theme'),
]);

Event::listen(RenderingSiteMapEvent::class, function () {
    if (is_plugin_active('real-estate')) {
        $properties = app(PropertyInterface::class)->advancedGet([
            'condition' => [
                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            'with'      => ['slugable'],
        ]);

        SiteMapManager::add(route('public.properties'), '2021-07-25 00:00:00', '0.4', 'monthly');

        foreach ($properties as $property) {
            SiteMapManager::add($property->url, $property->updated_at, '0.8');
        }
    }
});

RvMedia::setUploadPathAndURLToPublic();
RvMedia::addSize('large', 1024)
    ->addSize('medium_large', 640)
    ->addSize('property_large', 'auto', 610)
    ->addSize('medium', 400);

if (!function_exists('get_repeat_field')) {
    /**
     * @return string|array
     */
    function get_repeat_field($fieldName)
    {
        $field = json_decode(theme_option($fieldName), true);

        return is_array($field) ? $field : [];
    }
}

if (!function_exists('get_featured_properties')) {
    function get_featured_properties($limit)
    {
        $withCount = [];
        if (is_review_enabled()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }
        return app(PropertyInterface::class)->getPropertiesByConditions(
            [
                're_properties.is_featured'       => true,
                're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
            ],
            (int)$limit,
            config('plugins.real-estate.real-estate.properties.relations'),
            $withCount
        );
    }
}

/**
 * @return Object
 */
function get_object_property_map()
{
    return (object)[
        'name'            => '__name__',
        'type_name'       => '__type_name__',
        'type_slug'       => '__type_slug__',
        'url'             => '__url__',
        'city_name'       => '__city_name__',
        'square_text'     => '__square_text__',
        'number_bedroom'  => '__number_bedroom__',
        'number_bathroom' => '__number_bathroom__',
        'image_thumb'     => '__image_thumb__',
        'price_html'      => '__price_html__',
    ];
}

app()->booted(function () {
    if (is_plugin_active('real-estate')) {
        $videoSupportModels = [Property::class];
        add_action(BASE_ACTION_META_BOXES, function ($context, $object) use ($videoSupportModels) {
            if (in_array(get_class($object), $videoSupportModels) && $context == 'advanced') {
                MetaBox::addMetaBox('additional_property_fields', __('Addition Information'), function () {
                    $videoThumbnail = null;
                    $videoUrl = null;
                    $args = func_get_args();
                    if (!empty($args[0])) {
                        $videoThumbnail = $args[0]->video_thumbnail;
                        $videoUrl = $args[0]->video_url;
                    }

                    return Theme::partial('additional-property-fields', compact('videoThumbnail', 'videoUrl'));
                }, get_class($object), $context);
            }
        }, 28, 2);

        add_action(BASE_ACTION_AFTER_CREATE_CONTENT, function ($type, $request, $object) use ($videoSupportModels) {
            if (in_array(get_class($object), $videoSupportModels)) {
                $data = Arr::only((array)$request->input('video', []), ['url']);

                if ($request->hasFile('thumbnail_input')) {
                    $result = RvMedia::handleUpload($request->file('thumbnail_input'), 0, 'properties');
                    if ($result['error'] == false) {
                        $file = $result['data'];
                        $data['thumbnail'] = $file->url;
                    }
                }

                MetaBox::saveMetaBoxData($object, 'video', $data);
            }
        }, 280, 3);

        add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, function ($type, $request, $object) use ($videoSupportModels) {
            if (in_array(get_class($object), $videoSupportModels)) {
                $data = Arr::only((array)$request->input('video', []), ['url']);

                if ($request->hasFile('thumbnail_input')) {
                    $result = RvMedia::handleUpload($request->file('thumbnail_input'), 0, 'properties');
                    if ($result['error'] == false) {
                        $file = $result['data'];
                        $data['thumbnail'] = $file->url;
                    }
                }

                MetaBox::saveMetaBoxData($object, 'video', $data);
            }
        }, 281, 3);

        // yes or no is okay
        add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, function ($screen, $object) use ($videoSupportModels) {
            if (in_array(get_class($object), $videoSupportModels)) {
                $object->loadMissing(['meta_boxes']);
            }
        }, 56, 2);

        foreach ($videoSupportModels as $supportModel) {
            $supportModel::resolveRelationUsing('meta_boxes', function ($model) {
                return $model->morphMany(MetaBoxModel::class, 'reference')
                    ->select(['reference_id', 'meta_key', 'meta_value']);
            });

            MacroableModels::addMacro($supportModel, 'getVideoThumbnailAttribute', function () {
                /**
                 * @var BaseModel $this
                 */
                if ($this->meta_boxes) {
                    $fistMeta = $this->meta_boxes->firstWhere('meta_key', 'video');

                    if ($fistMeta) {
                        return Arr::get(Arr::first($fistMeta->meta_value), 'thumbnail');
                    }
                }

                return '';
            });

            MacroableModels::addMacro($supportModel, 'getVideoUrlAttribute', function () {
                /**
                 * @var BaseModel $this
                 */
                if ($this->meta_boxes) {
                    $fistMeta = $this->meta_boxes->firstWhere('meta_key', 'video');

                    if ($fistMeta) {
                        $url = Arr::get(Arr::first($fistMeta->meta_value), 'url');

                        if ($url) {
                            return Youtube::getYoutubeWatchURL($url);
                        }
                    }
                }

                return '';
            });
        }


        add_action(BASE_ACTION_META_BOXES, 'add_addition_fields_in_property_screen', 30, 3);

        function add_addition_fields_in_property_screen($context, $object)
        {
            if (get_class($object) == Property::class && $context == 'top') {
                MetaBox::addMetaBox(
                    'additional_property_fields',
                    __('Custom layout'),
                    function () {
                        $headerLayout = null;
                        $args = func_get_args();
                        if (!empty($args[0])) {
                            $headerLayout = MetaBox::getMetaData($args[0], 'header_layout', true);
                        }

                        return Theme::partial('additional-property-fields-top', compact('headerLayout'));
                    },
                    get_class($object),
                    $context
                );
            }
        }

        add_action(BASE_ACTION_AFTER_CREATE_CONTENT, 'save_addition_property_fields', 230, 3);
        add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, 'save_addition_property_fields', 231, 3);

        function save_addition_property_fields($type, $request, $object)
        {
            if (is_plugin_active('blog') && get_class($object) == Property::class) {
                MetaBox::saveMetaBoxData($object, 'header_layout', $request->input('header_layout'));
            }
        }
    }
    if (setting('social_login_enable', false)) {
        remove_filter(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM);
        add_filter(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, 'addLoginOptionsByTheme', 25);
    }
});

/**
 * @param Property $property
 * @return string
 */
function get_image_from_video_property(BaseModel $property)
{
    if ($property->video_thumbnail) {
        return RvMedia::getImageUrl($property->video_thumbnail);
    }

    $videoID = Youtube::getYoutubeVideoID($property->video_url);

    if ($videoID) {
        return 'https://img.youtube.com/vi/' . $videoID . '/hqdefault.jpg';
    }

    return RvMedia::getDefaultImage();
}

if (is_plugin_active('location')) {
    SlugHelper::registerModule(City::class, 'City');
    SlugHelper::setPrefix(City::class, 'city');

    SeoHelper::registerModule(City::class);

    add_filter(BASE_FILTER_BEFORE_RENDER_FORM, 'add_addition_fields_into_form', 127, 2);

    /**
     * @param \Botble\Base\Forms\FormAbstract $form
     * @param $data
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    function add_addition_fields_into_form($form, $data)
    {
        if (get_class($data) == City::class) {
            $image = MetaBox::getMetaData($data, 'image', true);
            $form
                ->setValidatorClass(CityRequest::class)
                ->addAfter('status', 'image', 'mediaImage', [
                    'label'      => trans('core/base::forms.image'),
                    'label_attr' => ['class' => 'control-label'],
                    'value'      => $image,
                ]);
        }

        return $form;
    }

    add_action(BASE_ACTION_AFTER_CREATE_CONTENT, function ($type, $request, $object) {
        if (get_class($object) == City::class) {
            MetaBox::saveMetaBoxData($object, 'image', $request->input('image'));
        }
    }, 230, 3);

    add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, function ($type, $request, $object) {
        if (get_class($object) == City::class) {
            MetaBox::saveMetaBoxData($object, 'image', $request->input('image'));
        }
    }, 231, 3);

    City::resolveRelationUsing('properties', function ($model) {
        return $model->hasMany(Property::class);
    });
}


if (!function_exists('get_single_header_templates')) {
    /**
     * @return array
     */
    function get_single_header_layout(): array
    {
        return [
            'layout-1' => __('Layout 1'),
            'layout-2' => __('Layout 2'),
            'layout-3' => __('Layout 3'),
        ];
    }
}

if (!function_exists('get_properties_page_layout')) {
    /**
     * @return array
     */
    function get_properties_page_layout(): array
    {
        return [
            'sidebar'  => __('Left sidebar'),
            'full'     => __('Full width'),
            'map'      => __('Full map'),
            'half_map' => __('Half map'),
            'grid_map' => __('Grid map'),
        ];
    }
}

if (!function_exists('get_feature_all')) {
    /**
     * @return array
     */
    function get_feature_all(): array
    {
        return app(FeatureInterface::class)->allBy([], [], ['re_features.id', 're_features.name'])->toArray();
    }
}

if (!function_exists('get_properties_featured')) {
    /**
     * @return object
     */
    function get_properties_featured($limit = 6): object
    {
        $params = [
            'condition' => ['is_featured' => true],
            'take'        => $limit,
            'order_by' => [
                'updated_at' => 'DESC',
            ],
        ];
        $withCount = [];
        if (is_review_enabled()) {
            $withCount = [
                'reviews',
                'reviews as reviews_avg' => function ($query) {
                    $query->select(DB::raw('avg(star)'));
                },
            ];
        }
        $params['withCount'] = $withCount;
        return app(PropertyInterface::class)->advancedGet($params);
    }
}

Form::component('themeIcon', Theme::getThemeNamespace() . '::partials.icons-field', [
    'name',
    'value'      => null,
    'attributes' => [],
]);

/**
 * @param string $html
 * @param string $module
 * @return null|string
 * @throws \Throwable
 */
function addLoginOptionsByTheme($html)
{
    if (Route::currentRouteName() == 'access.login') {
        if (defined('THEME_OPTIONS_MODULE_SCREEN_NAME')) {
            Theme::asset()
                ->usePath(false)
                ->add('social-login-css', asset('vendor/core/plugins/social-login/css/social-login.css'), [], [],
                    '1.0.0');
        }

        return $html . view('plugins/social-login::login-options')->render();
    }

    return $html . Theme::partial('login-options');
}

/**
 * @return null|string
 * @throws \Throwable
 */
function get_image_loading() {
    return RvMedia::getImageUrl(theme_option('img_loading'));
}