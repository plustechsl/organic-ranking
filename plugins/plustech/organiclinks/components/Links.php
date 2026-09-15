<?php namespace Plustech\OrganicLinks\Components;

use Cms\Classes\ComponentBase;
use Plustech\OrganicLinks\Models\Link;
use Plustech\OrganicLinks\Models\Vote;
use Request;
use Flash;

class Links extends ComponentBase
{
    public $links;

    public function componentDetails()
    {
        return [
            'name'        => 'Lista de Enlaces',
            'description' => 'Muestra el ranking de enlaces y gestiona los votos.'
        ];
    }

    public function onRun()
    {
        $this->prepareVars();
    }

    public function prepareVars($search = null)
    {
        $query = Link::withCount('votes');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%");
            });
        }

        $this->links = $query->orderBy('votes_count', 'desc')->get();
    }

    public function onSearch()
    {
        $search = post('search');
        $this->prepareVars($search);
        return ['#link-list' => $this->renderPartial('@default')];
    }

    public function onVote()
    {
        $linkId = post('link_id');
        $ip = Request::ip();

        if ($linkId) {
            Vote::firstOrCreate([
                'link_id' => $linkId,
                'ip'      => $ip
            ]);
        }

        $this->prepareVars();
        return ['#link-list' => $this->renderPartial('@default')];
    }

    public function onCreateLink()
    {
        $data = post();

        Link::create([
            'title'       => $data['title'] ?? '',
            'url'         => $data['url'] ?? '',
            'description' => $data['description'] ?? ''
        ]);

        Flash::success('¡Enlace añadido con éxito!');

        $this->prepareVars();
        return ['#link-list' => $this->renderPartial('@default')];
    }
}
