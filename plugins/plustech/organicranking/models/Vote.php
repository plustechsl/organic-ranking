<?php namespace Plustech\OrganicRanking\Models;

use Model;

class Vote extends Model
{
    public $table = "plustech_organicranking_votes";
    protected $fillable = ["link_id", "user_id"];

    public $belongsTo = [
        "link" => [\Plustech\OrganicRanking\Models\Link::class],
        "user" => [\Winter\User\Models\User::class]
    ];
}
