<?php namespace Plustech\OrganicLinks\Updates;

use Seeder;
use Plustech\OrganicLinks\Models\Link;

class SeedLinksTable extends Seeder
{
    public function run()
    {
        Link::create([
            'title' => 'Winter CMS Docs',
            'url' => 'https://wintercms.com/docs',
            'description' => 'Documentación oficial de Winter CMS'
        ]);

        Link::create([
            'title' => 'Laravel Framework',
            'url' => 'https://laravel.com',
            'description' => 'Documentación del framework Laravel'
        ]);
    }
}
