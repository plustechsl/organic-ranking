<?php namespace Plustech\OrganicLinks\Models;

use Model;

class Vote extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    public $table = 'plustech_organiclinks_votes';

    public $rules = [
        'link_id' => 'required|integer',
        'user_id' => 'required|integer',
    ];

    public $belongsTo = [
        'link' => [Link::class, 'key' => 'link_id'],
        'user' => [\Winter\User\Models\User::class, 'key' => 'user_id'],
    ];
}
