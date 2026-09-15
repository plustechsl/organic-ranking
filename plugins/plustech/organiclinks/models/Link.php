<?php namespace Plustech\OrganicLinks\Models;

use Model;

class Link extends Model
{
    public $table = 'plustech_organiclinks_links';

    protected $fillable = ['title', 'url', 'description'];

    public $hasMany = [
        'votes' => [\Plustech\OrganicLinks\Models\Vote::class]
    ];
}
