<?php

use MvcLTE\Contracts\Captcha\ManagerInterface;

if (! function_exists('captcha')) {
    /**
     * Get captcha instance.
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
            );
        }

        return $CaptchaManager->captcha($Name);
    }
}

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

        return $CaptchaManager->captcha($Name)->getStyle();
    }
}

if (! function_exists('captcha_code')) {
    /**
     * Get the captcha code.
     *
     * @param  string  $Name
     * @return string
     */ 
    function captcha_code(string $Name = '')
    {
        $CaptchaManager = app(ManagerInterface::class);

        if(empty($Name)){
            return (app(ManagerInterface::class)
                ->getCurrentCaptcha()
                ->getCaptcha()
            );
        }

        return $CaptchaManager->captcha($Name)->getCaptcha();
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
                ->getScript()
            );
        }

        return $CaptchaManager->captcha($Name)->getScript();
    }
}