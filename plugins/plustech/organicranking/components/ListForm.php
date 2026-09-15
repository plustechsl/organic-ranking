<?php namespace Plustech\OrganicRanking\Components;

use Cms\Classes\ComponentBase;
use Plustech\OrganicRanking\Models\LinkList;
use Auth;
use Flash;
use Redirect;

class ListForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            "name"        => "Formulario de Lista",
            "description" => "Permite crear una nueva lista."
        ];
    }

    public function onCreateList()
    {
        $user = Auth::getUser();
        if (!$user) {
            Flash::error("Debes iniciar sesión para crear una lista.");
            return;
        }

        LinkList::create([
            "name"        => post("name"),
            "description" => post("description"),
            "user_id"     => $user->id,
        ]);

        Flash::success("Lista creada correctamente.");
        return Redirect::refresh();
    }
}
