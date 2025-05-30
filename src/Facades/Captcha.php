<?php

namespace TheCoderRaman\Captcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Provides a static proxy to the Captcha Factory service.
 *
 * This Facade allows convenient access to the methods of the `CaptchaFactory` class
 * from anywhere in the application, using static syntax. It resolves the
 * `TheCoderRaman\Captcha\Factory` instance from the Laravel service container.
 *
 * The underlying service class that this Facade provides access to.
 * @see \TheCoderRaman\Captcha\Captcha
 */
class Captcha extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * This method tells Laravel's Facade which service container binding
     * (the underlying class) this Facade represents.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \TheCoderRaman\Captcha\Captcha::class;
    }
}
