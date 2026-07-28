<?php namespace Plustech\OrganicLinks;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'OrganicLinks',
            'description' => 'Sistema de ranking orgánico de enlaces.',
            'author'      => 'Plustech',
            'icon'        => 'icon-link'
        ];
    }

    public function registerComponents()
    {
        return [
            \Plustech\OrganicLinks\Components\Links::class => 'organicLinks',
        ];
    }
}
