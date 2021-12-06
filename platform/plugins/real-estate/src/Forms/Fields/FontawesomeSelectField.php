<?php

namespace Botble\RealEstate\Forms\Fields;

use Assets;
use Kris\LaravelFormBuilder\Fields\FormField;

class FontawesomeSelectField extends FormField
{

    /**
     * Get the template, can be config variable or view path.
     *
     * @return string
     */
    protected function getTemplate()
    {
        Assets::addScriptsDirectly('vendor/core/plugins/real-estate/libraries/fontawesome-iconpicker/js/fontawesome-iconpicker.min.js')
            ->addStylesDirectly('vendor/core/plugins/real-estate/libraries/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css');

        return 'plugins/real-estate::forms.fields.fontawesome-select';
    }
}
