<?php

namespace MvcLTE\Captcha\Captchas;

use MvcLTE\Http\Request;
use MvcLTE\Support\Facades\Http;
use MvcLTE\Contracts\Captcha\CaptchInterface;

class Hcaptcha implements CaptchInterface
{
    /**
     * The http request instance.
     * 
     * @var MvcLTE\Http\Request $Request
     */
    protected $Request;

    /**
     * Hcaptcha captcha url
     * 
     * @var string $Captcha
     */
    protected $CaptchaUrl = 'https://hcaptcha.com/siteverify';

    /**
     * Create hcaptcha instance.
     * 
     * @param MvcLTE\Http\Request $Request
     * @return void
     */
    public function __construct(Request $Request){
        $this->Request = $Request;
    }

    /**
     * Verify captcha response.
     *
     * @return bool
     */
    public function verify(){
        // Captcha token present in request
        if(!$this->Request->hasInput('h-captcha-response')){
            return false;
        }

        //Verify token
        $Response = Http::acceptJson()->post($this->CaptchaUrl, [
            'secret' => $secretKey,
            'remoteip' => $this->Request->ip(),
            'response' => $this->Request->getInput(
                'h-captcha-response'
            )
        ]);

        if($Response->successful()){
            if(!isset($Response['success'])){
                return false;
            }
        }

        return true;
    }
    
    /**
     * Get captcha headers stylesheet code.
     *
     * @return string
     */
    public function getHeader(){
        return '<style>.h-captcha > div {width: 100% !important;}.h-captcha iframe {width: 100% !important;}</style>';
    }

    /**
     * Get the captcha html code.
     *
     * @return string
     */
    public function getCaptch(){
        return '<div style="display:flex;margin-left:50px;"><div class="h-captcha" data-sitekey="' . $siteKey . '"></div></div>';
    }

    /**
     * Verify captcha footer script code.
     *
     * @return string
     */
    public function getFooter(){
        return '<script src="https://hcaptcha.com/1/api.js" async defer></script>';
    }
}