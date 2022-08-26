<?php

namespace MvcLTE\Captcha\Captchas;

use MvcLTE\Http\Request;
use MvcLTE\Http\Client\Factory;
use MvcLTE\Contracts\Captcha\CaptchaInterface;

class Hcaptcha implements CaptchaInterface
{
    /**
     * Captch key 
     * 
     * @var string $Key
     */
    protected $Key;

    /**
     * Captch secret
     * 
     * @var string $Secret
     */
    protected $Secret;

    /**
     * The http client instance.
     * 
     * @var MvcLTE\Http\Request $Request
     */
    protected $Client;

    /**
     * The http request instance.
     * 
     * @var MvcLTE\Http\Request $Request
     */
    protected $Request;
    
    /**
     * Captcha url
     * 
     * @var string $Url
     */
    protected $Url = 'https://hcaptcha.com/siteverify';

    /**
     * Create hcaptcha instance.
     * 
     * @param string $Key
     * @param string $Secret
     * @param string $Url
     * @return void
     */
    public function __construct(string $Key,string $Secret,string $Url = null){
        $this->Key = $Key;
        $this->Secret = $Secret;

        if(!empty($Url)){
            $this->Url = $Url;
        }
    }

    /**
     * Set captcha key.
     * 
     * @param string $Key
     * @return MvcLTE\Captcha\Captchas\Hcaptcha
     */
    public function setKey(string $Key)
    {
        $this->Key = $Key;
        
        return $this;
    }

    /**
     * Get captcha key.
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->Key;
    } 

    /**
     * Set captcha secret.
     * 
     * @param string $Secret
     * @return MvcLTE\Captcha\Captchas\Hcaptcha
     */
    public function setSecret(string $Secret)
    {
        $this->Secret = $Secret;
        
        return $this;
    }

    /**
     * Get captcha secret.
     * 
     * @return string
     */
    public function getSecret()
    {
        return $this->Secret;
    } 

    /**
     * Set captcha url.
     * 
     * @param string $Url
     * @return MvcLTE\Captcha\Captchas\Hcaptcha
     */
    public function setUrl(string $Url)
    {
        $this->Url = $Url;
        
        return $this;
    }

    /**
     * Get captcha url.
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->Url;
    } 

    /**
     * Verify captcha response.
     *
     * @return bool
     */
    public function verify(){
        if(!$this->Request->hasInput('h-captcha-response')){
            return false;
        }

        $Response = $this->Client->asForm()->acceptJson()->post($this->getUrl(),[
            'secret' => $this->getSecret(),
            'remoteip' => $this->Request->ip(),
            'response' => $this->Request->getInput(
                'h-captcha-response'
            )
        ]);

        return (($Response->successful())
            ?$Response->json('success'):false
        );
    }
    
    /**
     * Get captcha stylesheet code.
     *
     * @return string
     */
    public function getStyle(){
        return '<!--Captcha StyleSheet-->
        <style>
            .h-captcha > div {
                width: 100% !important;
            }
            .h-captcha iframe {
                width: 100% !important;
            }
        </style>';
    }

    /**
     * Get the captcha html code.
     *
     * @return string
     */
    public function getCaptcha(){
        return '<!--Captcha Code-->
        <div style="display:flex;margin-left:50px;">
            <div class="h-captcha" data-sitekey="'.$this->getKey().'"></div>
        </div>';
    }

    /**
     * Get the captcha script code.
     *
     * @return string
     */
    public function getScript(){
        return '<!--Captcha Script-->
        <script src="https://hcaptcha.com/1/api.js" async defer>
        </script>';
    }

    /**
     * Set http client instance.
     * 
     * @param MvcLTE\Http\Client\Factory $Client
     * @return MvcLTE\Captcha\Captchas\Hcaptcha
     */
    public function setClient(Factory $Client)
    {
        $this->Client = $Client;

        return $this;
    }

    /**
     * Get http client instance.
     * 
     * @return MvcLTE\Http\Client\Factory
     */
    public function getClient()
    {
        return $this->Client;
    }  

    /**
     * Set http request instance.
     * 
     * @param MvcLTE\Http\Request $Request
     * @return MvcLTE\Captcha\Captchas\Hcaptcha
     */
    public function setRequest(Request $Request)
    {
        $this->Request = $Request;

        return $this;
    }

    /**
     * Get http request instance.
     * 
     * @return MvcLTE\Http\Request
     */
    public function getRequest()
    {
        return $this->Request;
    }    
}