<?php namespace Plustech\OrganicRanking\Components;

use Cms\Classes\ComponentBase;
use Plustech\OrganicRanking\Models\LinkList;
use Auth;

class ListList extends ComponentBase
{
    public $lists;

    public function componentDetails()
    {
        return [
            "name"        => "Listado de Listas",
            "description" => "Muestra todas las listas creadas o las del usuario actual."
        ];
    }

    public function defineProperties()
    {
        return [
            "userOnly" => [
                "title"       => "Solo del usuario",
                "description" => "Muestra únicamente las listas creadas por el usuario logueado",
                "type"        => "checkbox",
                "default"     => false,
            ]
        ];
    }

    public function onRun()
    {
        $query = LinkList::withCount("links");

        if ($this->property("userOnly")) {
            $user = Auth::getUser();
            if ($user) {
                $query->where("user_id", $user->id);
            } else {
                $this->lists = collect([]);
                return;
            }
        }

        $this->lists = $query->get();
    }
}
