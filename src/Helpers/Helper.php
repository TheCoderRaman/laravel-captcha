<?php

use TheCoderRaman\Captcha\Captcha;
use Illuminate\Support\Facades\App;
use TheCoderRaman\Captcha\Contracts\DriverInterface;

if (! function_exists('captcha')) {
    /**
     * Get the resolved CAPTCHA driver instance, gracefully handling resolution failures.
     *
     * This helper function resolves the main CAPTCHA manager from the service container,
     * then attempts to safely resolve a CAPTCHA driver using `safeCaptcha()`.
     * The `safeCaptcha()` method suppresses exceptions during driver creation,
     * making this function resilient to configuration or driver instantiation issues.
     *
     * @param string|null $driver
     * @return DriverInterface|null
     */
    function captcha(?string $driver = null): ?DriverInterface
    {
        return (App::make(Captcha::class)
            ->safeCaptcha()->getDriver($driver)
        );
    }
}

if (! function_exists('captcha_style')) {
    /**
     * Get the CSS style markup for the CAPTCHA widget.
     *
     * This helper function resolves the main CAPTCHA manager, attempts to safely
     * get the appropriate driver, and then retrieves its required CSS markup.
     * It gracefully handles cases where the driver cannot be resolved.
     *
     * @param string|null $driver
     * @return string
     */
    function captcha_style(?string $driver = null): string
    {
        $driverInstance = (
            App::make(Captcha::class)
            ->safeCaptcha()->getDriver($driver)
        );

        return (
            null === $driverInstance ? '': $driverInstance->getStyle()
        );
    }
}

if (! function_exists('captcha_code')) {
    /**
     * Get the HTML markup for the CAPTCHA widget.
     *
     * This helper function resolves the main CAPTCHA manager, attempts to safely
     * get the appropriate driver, and then retrieves its necessary HTML elements.
     * It gracefully handles cases where the driver cannot be resolved.
     *
     * @param string|null $driver
     * @return string
     */
    function captcha_code(?string $driver = null): string
    {
        $driverInstance = (
            App::make(Captcha::class)
            ->safeCaptcha()->getDriver($driver)
        );

        return (
            null === $driverInstance ? '': $driverInstance->getCaptcha()
        );
    }
}

if (! function_exists('captcha_script')) {
    /**
     * Get the JavaScript markup for the CAPTCHA widget.
     *
     * This helper function resolves the main CAPTCHA manager, attempts to safely
     * get the appropriate driver, and then retrieves its required JavaScript markup.
     * It gracefully handles cases where the driver cannot be resolved.
     *
     * @param string|null $driver
     * @return string
     */
    function captcha_script(?string $driver = null): string
    {
        $driverInstance = (
            App::make(Captcha::class)
            ->safeCaptcha()->getDriver($driver)
        );

        return (
            null === $driverInstance ? '': $driverInstance->getScript()
        );
    }
}