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
     * Get captcha.
     *
     * @return bool
     */
    public function getCaptch();

    /**
     * Get captcha headers.
     *
     * @return bool
     */
    public function getHeader();

    /**
     * Verify captcha response.
     *
     * @return bool
     */
    public function getFooter();        
}