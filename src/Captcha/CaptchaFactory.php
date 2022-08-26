<?php

namespace MvcLTE\Captcha;

use MvcLTE\Http\Request;
use MvcLTE\Helpers\ArrayData;
use MvcLTE\Http\Client\Factory;
use MvcLTE\Captcha\Captchas\Hcaptcha;
use MvcLTE\Captcha\Captchas\ReCaptcha;
use MvcLTE\Captcha\Captchas\NullCaptcha;
use MvcLTE\Contracts\Captcha\CaptchaInterface;
use MvcLTE\Captcha\Exceptions\CaptchaException;

class CaptchaFactory
{
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
     * Create captcha factory instance.
     * 
     * @param MvcLTE\Http\Request $Request
     * @param MvcLTE\Http\Client\Factory $Client
     * @return void
     */
    public function __construct(Request $Request,Factory $Client){
        $this->Client = $Client;
        $this->Request = $Request;
    }

    /**
     * Make captcha instance.
     * 
     * @param string $Type
     * @param array $Config
     * @return MvcLTE\Contracts\Captcha\CaptchaInterface
     */
    public function make(string $Type,array $Config): CaptchaInterface
    {
        $Config = $this->getConfig($Config);

        return tap($this->getCaptcha($Type,$Config),
            function($Captcha){
                $Captcha->setClient($this->getClient());
                $Captcha->setRequest(
                    $this->getRequest()
                );
            }
        );
    }

    /**
     * Get configuration for the captcha
     * 
     * @param array $Config
     * @return array
     */
    protected function getConfig(array $Config): array
    {
        return [
            'key' => ArrayData::get($Config, 'key', ''),
            'secret' => ArrayData::get(
                $Config, 'secret', ''
            )
        ];
    }

    /**
     * Create new captcha handler
     * 
     * @param string $Type
     * @param array $Config
     * @return MvcLTE\Contracts\Captcha\CaptchaInterface
     */
    protected function getCaptcha(string $Type,array $Config): CaptchaInterface
    {
        switch(strtoupper($Type)){
            case 'HCAPTCHA':
                return new Hcaptcha($Config['key'],$Config['secret']);
            case 'RECAPTCHA':
                return new ReCaptcha($Config['key'],$Config['secret']);
            case 'NULLCAPTCHA':
                return new NullCaptcha($Config['key'],$Config['secret']);
        }

        throw new CaptchaException("Captcha {$Config['type']} not found.");
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
