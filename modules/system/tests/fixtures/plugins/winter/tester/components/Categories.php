<?php namespace Winter\Tester\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\CodeBase;

class Categories extends ComponentBase
{
<<<<<<< HEAD
    public function __construct(?CodeBase $cmsObject = null, $properties = [])
=======
    public function __construct(CodeBase $cmsObject = null, $properties = [])
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
    {
        parent::__construct($cmsObject, $properties);
    }

    public function componentDetails()
    {
        return [
            'name' => 'Blog Categories Dummy Component',
            'description' => 'Displays the list of categories in the blog.'
        ];
    }

    public function posts()
    {
        return [
            ['title' => 'Lorum ipsum', 'content' => 'Post Content #1'],
            ['title' => 'La Playa Nudista', 'content' => 'Second Post Content']
        ];
    }

    public function onTestAjax()
    {
        $this->page['var'] = 'page';
    }
}
