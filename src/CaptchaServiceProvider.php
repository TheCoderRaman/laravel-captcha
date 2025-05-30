<?php

namespace TheCoderRaman\Captcha;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CaptchaServiceProvider extends PackageServiceProvider
{
    /**
     * Configures the package with configuration.
     *
     * This method is part of the Spatie Laravel Package Tools integration,
     * allowing for streamlined package setup within a Laravel application.
     *
     * For more information on package configuration, refer to:
     * @see https://github.com/spatie/laravel-package-tools
     *
     * @param Package $package
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        ($package
            ->name('laravel-captcha')->hasConfigFile('captcha')
        );
    }
}
