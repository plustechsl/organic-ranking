<?php namespace Plustech\OrganicRanking\Models;

use Model;

class Link extends Model
{
    public $table = "plustech_organicranking_links";
    protected $fillable = ["title", "url", "description", "list_id", "user_id", "votes_count"];

    public $belongsTo = [
        "list" => [\Plustech\OrganicRanking\Models\LinkList::class],
        "user" => [\Winter\User\Models\User::class]
    ];

    public $hasMany = [
        "votes" => [\Plustech\OrganicRanking\Models\Vote::class]
    ];
}
