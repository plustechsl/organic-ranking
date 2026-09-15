<?php namespace Plustech\OrganicLinks\Models;

use Model;

class UserExpertise extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    public $table = 'plustech_organiclinks_user_expertises';

    public $rules = [
        'user_id' => 'required|integer',
        'area' => 'required',
    ];

    public $belongsTo = [
        'user' => [\Winter\User\Models\User::class, 'key' => 'user_id'],
    ];
}
