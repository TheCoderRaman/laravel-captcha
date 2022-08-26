<?php

use MvcLTE\Contracts\Captcha\ManagerInterface;

if (! function_exists('captcha_style')) {
    /**
     * Get captcha stylesheet code.
     *
     * @param  string  $Name
     * @return string
     */ 
    function captcha_style(string $Name = '')
    {
        $CaptchaManager = app(ManagerInterface::class);

        if(empty($Name)){
            return (app(ManagerInterface::class)
                ->getCurrentCaptcha()
                ->getStyle()
            );
        }

        return $CaptchaManager->captcha()->getStyle();
    }
}

if (! function_exists('captcha')) {
    /**
     * Get the captcha html code.
     *
     * @param  string  $Name
     * @return string
     */ 
    function captcha(string $Name = '')
    {
        $CaptchaManager = app(ManagerInterface::class);

        if(empty($Name)){
            return (app(ManagerInterface::class)
                ->getCurrentCaptcha()
                ->getCaptcha($Path)
            );
        }

        return $CaptchaManager->captcha()->getCaptcha();
    }
}

if (! function_exists('captcha_script')) {
    /**
     * Get the captcha script code.
     *
     * @param  string  $Name
     * @return string
     */ 
    function captcha_script(string $Name = '')
    {
        $CaptchaManager = app(ManagerInterface::class);

        if(empty($Name)){
            return (app(ManagerInterface::class)
                ->getCurrentCaptcha()
                ->getScript($Path)
            );
        }

        return $CaptchaManager->captcha()->getScript();
    }
}