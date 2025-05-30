<?php

use TheCoderRaman\Captcha\Enums\Captcha;

/**
 * Captcha Package Configuration
 *
 * This file contains the configuration settings for the laravel-captcha package.
 * It allows you to define a default CAPTCHA handler, control global verification status,
 * and configure individual CAPTCHA services like hCaptcha and reCAPTCHA.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Default Captcha Handler
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the captcha below you wish to use as
    | your default captcha for all work. Of course, you may use any
    | captchas at once using the manager class.
    |
    | The value should correspond to one of the case names in
    | `TheCoderRaman\Captcha\Enums\Captcha` enum (e.g., 'null', 'hcaptcha', 'recaptcha').
    |
    */

    'default' => Captcha::NullCaptcha->value,

    /*
    |--------------------------------------------------------------------------
    | Captcha Verification Status
    |--------------------------------------------------------------------------
    |
    | Here you can enable/disable captcha verification globally. For the
    | convenience of the user, if set to `false`, CAPTCHA will be replaced
    | by the NullCaptcha handler which works as a placeholder, rendering
    | nothing and always returning true for verification. This allows you
    | to add CAPTCHA code once and control its activation without removal.
    |
    */

    'status' => true,

    /*
    |--------------------------------------------------------------------------
    | Captcha Configurations
    |--------------------------------------------------------------------------
    |
    | This section holds the specific configurations for each CAPTCHA service.
    | You can define multiple configurations by adding new keys under 'captchas'.
    | Each entry typically requires 'key', 'secret', and 'type' parameters.
    |
    */

    'captchas' => [
        /*
        |--------------------------------------------------------------------------
        | Null Captcha Configuration
        |--------------------------------------------------------------------------
        |
        | The NullCaptcha provides an easy way to bypass CAPTCHA verification
        | during development or testing. It fully works in an offline environment,
        | giving users the freedom to test forms with CAPTCHA validation easily
        | without needing actual verification.
        |
        | 'key' and 'secret' are placeholders and not functionally required for NullCaptcha.
        |
        */
        'nullcaptcha' => [
            'type' => 'null',
            'key' => 'NOT-REQUIRED',
            'secret' => 'NOT-REQUIRED',
        ],

        /*
        |--------------------------------------------------------------------------
        | hCaptcha Configuration
        |--------------------------------------------------------------------------
        |
        | Configuration for integrating with the hCaptcha service.
        | 'key' is your hCaptcha site key (public).
        | 'secret' is your hCaptcha secret key (private), used for server-side verification.
        | 'type' should be 'hcaptcha'.
        |
        */
        'hcaptcha' => [
            'type' => 'hcaptcha',
            'key' => '10000000-ffff-ffff-ffff-000000000001',
            'secret' => '0x0000000000000000000000000000000000000000',
        ],

        /*
        |--------------------------------------------------------------------------
        | Google reCAPTCHA Configuration
        |--------------------------------------------------------------------------
        |
        | Configuration for integrating with Google reCAPTCHA v2 ("I'm not a robot" checkbox).
        | 'key' is your reCAPTCHA site key (public).
        | 'secret' is your reCAPTCHA secret key (private), used for server-side verification.
        | 'type' should be 'recaptcha'.
        |
        */
        'recaptcha' => [
            'type' => 'recaptcha',
            'key' => '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',
            'secret' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',
        ],
    ],
];