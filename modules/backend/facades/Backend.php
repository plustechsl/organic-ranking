<?php namespace Backend\Facades;

use Winter\Storm\Support\Facade;

/**
 * @method static string uri()
<<<<<<< HEAD
 * @method static string url(?string $path = null, array $parameters = [], ?bool $secure = null)
 * @method static string baseUrl(?string $path = null)
 * @method static string skinAsset(?string $path = null)
 * @method static \Illuminate\Http\RedirectResponse redirect(string $path, int $status = 302, array $headers = [], ?bool $secure = null)
 * @method static \Illuminate\Http\RedirectResponse redirectGuest(string $path, int $status = 302, array $headers = [], ?bool $secure = null)
 * @method static \Illuminate\Http\RedirectResponse redirectIntended(string $path, int $status = 302, array $headers = [], ?bool $secure = null)
=======
 * @method static string url(string $path = null, array $parameters = [], bool $secure = null)
 * @method static string baseUrl(string $path = null)
 * @method static string skinAsset(string $path = null)
 * @method static \Illuminate\Http\RedirectResponse redirect(string $path, int $status = 302, array $headers = [], bool $secure = null)
 * @method static \Illuminate\Http\RedirectResponse redirectGuest(string $path, int $status = 302, array $headers = [], bool $secure = null)
 * @method static \Illuminate\Http\RedirectResponse redirectIntended(string $path, int $status = 302, array $headers = [], bool $secure = null)
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
 * @method static string date($dateTime, array $options = [])
 * @method static string dateTime($dateTime, array $options = [])
 *
 * @see \Backend\Helpers\Backend
 */
class Backend extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'backend.helper';
    }
}
