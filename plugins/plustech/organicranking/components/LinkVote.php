<?php namespace Plustech\OrganicRanking\Components;

use Cms\Classes\ComponentBase;
use Plustech\OrganicRanking\Models\Link;
use Plustech\OrganicRanking\Models\Vote;
use Auth;
use Flash;
use Redirect;

class LinkVote extends ComponentBase
{
    public function componentDetails()
    {
        return [
            "name"        => "Votación de Enlaces",
            "description" => "Gestión de votos para enlaces."
        ];
    }

    public function onVote()
    {
        $user = Auth::getUser();
        if (!$user) {
            Flash::error("Debes iniciar sesión para votar.");
            return;
        }

        $linkId = post("link_id");
        $link = Link::find($linkId);

        if (!$link) {
            Flash::error("Enlace no encontrado.");
            return;
        }

        $existingVote = Vote::where("link_id", $linkId)->where("user_id", $user->id)->first();
        if ($existingVote) {
            $existingVote->delete();
            $link->decrement("votes_count");
            Flash::info("Voto retirado.");
        } else {
            Vote::create(["link_id" => $linkId, "user_id" => $user->id]);
            $link->increment("votes_count");
            Flash::success("¡Voto registrado!");
        }

        return Redirect::refresh();
    }
}
