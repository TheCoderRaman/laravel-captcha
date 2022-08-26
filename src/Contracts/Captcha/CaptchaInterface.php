<?php

namespace MvcLTE\Contracts\Captcha;

interface CaptchaInterface
{
    /**
     * Verify captcha response.
     *
     * @return bool
     */
    public function verify();

    /**
     * Get captcha style.
     *
     * @return string
     */
    public function getStyle();

    /**
     * Get captcha.
     *
     * @return string
     */
    public function getCaptcha();
    
    /**
     * Get captcha script.
     *
     * @return string
     */
    public function getScript();        
}