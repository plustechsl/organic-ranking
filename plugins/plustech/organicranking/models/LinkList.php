<?php namespace Plustech\OrganicRanking\Models;

use Model;
use Str;

class LinkList extends Model
{
    public $table = "plustech_organicranking_lists";
    protected $fillable = ["name", "slug", "description", "user_id"];

    public $belongsTo = [
        "user" => [\Winter\User\Models\User::class]
    ];

    public $hasMany = [
        "links" => [\Plustech\OrganicRanking\Models\Link::class, "key" => "list_id"]
    ];

    public function beforeSave()
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }
}
