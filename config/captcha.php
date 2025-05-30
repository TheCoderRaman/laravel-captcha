<?php

use TheCoderRaman\Captcha\Enums\Captcha;

/**
 * Copyright (c) Animax Developers.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/TheCoderRaman/laravel-captcha
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
    */

    'default' => Captcha::NullCaptcha->value,

    /*
    |--------------------------------------------------------------------------
    | Captcha verification status
    |--------------------------------------------------------------------------
    |
    | Here you can enable/disable captcha verification. For the convenience of 
    | the user, captcha will be replaced by null handler which will work as 
    | placeholder which do not show anything and also return true. So you can
    | add it once and dont have to worry about anything later on.
    |
    */

    'status' => true,

    /*
    |--------------------------------------------------------------------------
    | Captcha Configurations
    |--------------------------------------------------------------------------
    |
    | Here are each of the configurations setup for your application. Example
    | configuration has been included, but you may add as many configurations as
    | you would like.
    |
    */

    'captchas' => [

        'hcaptcha' => [
            'key' => '10000000-ffff-ffff-ffff-000000000001',
            'secret' => '0x0000000000000000000000000000000000000000',
            'type' => 'hcaptcha'
        ],

        'recaptcha' => [
            'key' => '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',
            'secret' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',
            'type' => 'recaptcha'
        ],       

        /*
        |--------------------------------------------------------------------------
        | Null captcha
        |--------------------------------------------------------------------------
        |
        | Null captcha provides easy way to bypass captcha verification during 
        | development mode. It fully works in offline environment give user
        | freedom to test form captcha validation easily.
        |
        */
        'nullcaptcha' => [
            'key' => 'NOT-REQUIRED',
            'secret' => 'NOT-REQUIRED',
            'type' => 'nullcaptcha'
        ],         

    ],

];
