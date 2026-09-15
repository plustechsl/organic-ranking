<?php namespace Cms\Facades;

use Winter\Storm\Support\Facade;

/**
<<<<<<< HEAD
 * @method static string url(?string $path = null)
=======
 * @method static string url(string $path = null)
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
 *
 * @see \Cms\Helpers\Cms
 */
class Cms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cms.helper';
    }
}
