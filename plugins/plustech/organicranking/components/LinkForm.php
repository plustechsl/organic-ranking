<?php namespace Plustech\OrganicRanking\Components;

use Cms\Classes\ComponentBase;
use Plustech\OrganicRanking\Models\Link;
use Plustech\OrganicRanking\Models\LinkList;
use Auth;
use Flash;
use Redirect;

class LinkForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            "name"        => "Formulario de Enlace",
            "description" => "Permite añadir un nuevo enlace a una lista."
        ];
    }

    public function onCreateLink()
    {
        $user = Auth::getUser();
        if (!$user) {
            Flash::error("Debes iniciar sesión para añadir un enlace.");
            return;
        }

        $listSlug = post("list_slug");
        $list = LinkList::where("slug", $listSlug)->first();

        if (!$list) {
            Flash::error("La lista especificada no existe.");
            return;
        }

        Link::create([
            "title"       => post("title"),
            "url"         => post("url"),
            "description" => post("description"),
            "list_id"     => $list->id,
            "user_id"     => $user->id,
        ]);

        Flash::success("Enlace creado correctamente.");
        return Redirect::refresh();
    }
}
