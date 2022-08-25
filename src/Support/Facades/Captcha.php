<?php

namespace MvcLTE\Support\Facades;

use MvcLTE\Support\Facades\Facade;

class Captcha extends Facade
{
    /**
     * Get the registered name of the component or service
     * 
     * @return string 
     */    
    protected static function getFacadeAccessor(): string
    {
        return 'Captcha';
    }
}
