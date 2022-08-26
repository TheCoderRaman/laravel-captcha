<?php

namespace MvcLTE\Captcha;

use \InvalidArgumentException;

use MvcLTE\Helpers\ArrayData;
use MvcLTE\Captcha\CaptchaFactory;
use MvcLTE\Contracts\Config\Config;
use MvcLTE\Contracts\Captcha\CaptchaInterface;
use MvcLTE\Contracts\Captcha\ManagerInterface;

class CaptchaManager implements ManagerInterface
{
    /**
     * The config instance.
     *
     * @var MvcLTE\Contracts\Config\Config $Config
     */
    protected $Config;

    /**
     * Captcha factory instance
     * 
     * @var MvcLTE\Captcha\CaptchaFactory $Factory
     */
    protected $Factory;

    /**
     * The created captcha instances.
     *
     * @var array<string,object> $Captchas
     */
    protected $Captchas = [];

    /**
     * The custom captchas resolvers.
     *
     * @var array<string,callable> $Extensions
     */
    protected $Extensions = [];

    /**
     * The active captcha instance.
     *
     * @var array<string,object> $CurrentCaptcha
     */
    protected $CurrentCaptcha;

    /**
     * Construct captchas manager instance
     * 
     * @param MvcLTE\Contracts\Config\Config $Config
     * @param MvcLTE\Captcha\CaptchaFactory $Factory
     * @return void
     */
    public function __construct(Config $Config, CaptchaFactory $Factory)
    {
        $this->Config = $Config;
        $this->Factory = $Factory;
    }

    /**
     * Get a captcha instance.
     *
     * @param string|null $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function captcha(string $Name = null)
    {
        $Name = $Name ?: $this->getDefaultCaptcha();

        if (!isset($this->Captchas[$Name])) {
            $this->Captchas[$Name] = $this->makeCaptcha($Name);
        }

        return $this->CurrentCaptcha = $this->Captchas[$Name];
    }

    /**
     * Reset to the given captcha.
     *
     * @param string|null $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function reset(string $Name = null)
    {
        $Name = $Name ?: $this->getDefaultCaptcha();

        $this->remove($Name);

        return $this->CurrentCaptcha = $this->captcha($Name);
    }

    /**
     * Remove a captcha from the captchas.
     *
     * @param string|null $Name
     *
     * @return void
     */
    public function remove(string $Name = null)
    {
        $Name = $Name ?: $this->getDefaultCaptcha();

        unset($this->Captchas[$Name]);
    }

    /**
     * Create new hashids instance with provided configuration
     * 
     * @param string $Type
     * @param array $Config
     * @return MvcLTE\Contracts\Captcha\CaptchaInterface
     */
    protected function createCaptcha(string $Type,array $Config): CaptchaInterface
    {
        return $this->Factory->make($Type,$Config);
    }

    /**
     * Make the captcha instance.
     *
     * @param string $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    protected function makeCaptcha(string $Name)
    {
        $Config = $this->getCaptchaConfig($Name);

        if (isset($this->Extensions[$Name])) {
            return $this->Extensions[$Name]($Config);
        }

        if ($Type = ArrayData::get($Config, 'type')) {
            if (isset($this->Extensions[$Type])) {
                return $this->Extensions[$Type]($Config);
            }
        }

        return $this->createCaptcha($Type,$Config);
    }

    /**
     * Get config name
     * 
     * @return string
     */
    protected function getConfigName(): string
    {
        return 'captcha';
    }

    /**
     * Get factory instance
     * 
     * @return MvcLTE\Captcha\CaptchaFactory
     */
    public function getFactory(): CaptchaFactory
    {
        return $this->Factory;
    }

    /**
     * Get the configuration for a captcha.
     *
     * @param string|null $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getCaptchaConfig(string $Name = null)
    {
        $Name = $Name ?: $this->getDefaultCaptcha();

        return $this->getNamedConfig('captchas', 'captcha', $Name);
    }

    /**
     * Get the given named configuration.
     *
     * @param string $Type
     * @param string $Description
     * @param string $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function getNamedConfig(string $Type, string $Description, string $Name)
    {
        $Data = $this->Config->get($this->getConfigName().'.'.$Type);

        if (!is_array($Config = ArrayData::get($Data, $Name)) && !$Config) {
            throw new InvalidArgumentException("{$Description} [$Name] not configured.");
        }

        $Config['name'] = $Name;

        return $Config;
    }

    /**
     * Get the default captcha name.
     *
     * @return string
     */
    public function getDefaultCaptcha()
    {
        return $this->Config->get($this->getConfigName().'.default');
    }

    /**
     * Set the default captcha name.
     *
     * @param string $Name
     *
     * @return void
     */
    public function setDefaultCaptcha(string $Name)
    {
        $this->Config->set($this->getConfigName().'.default', $Name);
    }

    /**
     * Register an extension captcha resolver.
     *
     * @param string   $Name
     * @param callable $Resolver
     *
     * @return void
     */
    public function extend(string $Name, callable $Resolver)
    {
        if ($Resolver instanceof Closure) {
            $this->Extensions[$Name] = $Resolver->bindTo($this, $this);
        } else {
            $this->Extensions[$Name] = $Resolver;
        }
    }

    /**
     * Return all of the created captchas.
     *
     * @return array<string,object>
     */
    public function getCaptchas()
    {
        return $this->Captchas;
    }

    /**
     * Get current captcha instance.
     *
     * @return MvcLTE\Contracts\Captcha\CaptchaInterface
     */
    public function getCurrentCaptcha()
    {
        if(!isset($this->CurrentCaptcha)){
            return $this->captcha();
        }

        return $this->CurrentCaptcha;
    }

    /**
     * Get the config instance.
     *
     * @return MvcLTE\Contracts\Config\Config
     */
    public function getConfig()
    {
        return $this->Config;
    }

    /**
     * Dynamically pass methods to the default captcha.
     *
     * @param string $Method
     * @param array  $Parameters
     *
     * @return mixed
     */
    public function __call(string $Method, array $Parameters)
    {
        return $this->captcha()->{$Method}(...$Parameters);
    }    
}
