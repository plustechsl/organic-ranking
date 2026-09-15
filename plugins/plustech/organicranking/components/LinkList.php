<?php namespace Plustech\OrganicRanking\Components;

use Cms\Classes\ComponentBase;
use Plustech\OrganicRanking\Models\Link;
use Plustech\OrganicRanking\Models\LinkList as LinkListModel;

class LinkList extends ComponentBase
{
    public $list;
    public $links;

    public function componentDetails()
    {
        return [
            "name"        => "Lista de Enlaces",
            "description" => "Muestra los enlaces de una lista específica."
        ];
    }

    public function onRun()
    {
        $slug = $this->param("slug");
        $this->list = LinkListModel::where("slug", $slug)->first();
        if ($this->list) {
            $this->links = Link::where("list_id", $this->list->id)->orderBy("votes_count", "desc")->get();
        }
    }
}
