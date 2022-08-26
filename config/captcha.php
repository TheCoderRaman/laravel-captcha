<?php

/**
 * Copyright (c) Animax Developers.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/animaxdev/mvclte-captcha
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

    'default' => 'hcaptcha',

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
            'key' => 'YOUR-SITE-KEY',
            'secret' => 'YOUR-SITE-SECRET',
            'type' => 'hcaptcha'
        ],

        'recaptcha' => [
            'key' => 'YOUR-SITE-KEY',
            'secret' => 'YOUR-SITE-SECRET',
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
