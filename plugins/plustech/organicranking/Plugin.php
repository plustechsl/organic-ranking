<?php namespace Plustech\OrganicRanking;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            "name"        => "Organic Ranking",
            "description" => "Gestión de listas de enlaces y votaciones.",
            "author"      => "Plustech",
            "icon"        => "icon-link"
        ];
    }

    public function registerComponents()
    {
        return [
            \Plustech\OrganicRanking\Components\ListList::class => "listList",
            \Plustech\OrganicRanking\Components\LinkList::class => "linkList",
            \Plustech\OrganicRanking\Components\LinkForm::class => "linkForm",
            \Plustech\OrganicRanking\Components\ListForm::class => "listForm",
            \Plustech\OrganicRanking\Components\LinkVote::class => "linkVote",
        ];
    }
}
