<?php namespace Plustech\OrganicLinks\Models;

use Model;

class Category extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    public $table = 'plustech_organiclinks_categories';

    public $rules = [
        'name' => 'required',
        'slug' => 'required|unique:plustech_organiclinks_categories',
    ];

    public $belongsTo = [
        'parent' => [Category::class, 'key' => 'parent_id'],
    ];

    public $hasMany = [
        'children' => [Category::class, 'key' => 'parent_id'],
        'links' => [Link::class, 'key' => 'category_id'],
    ];
}
