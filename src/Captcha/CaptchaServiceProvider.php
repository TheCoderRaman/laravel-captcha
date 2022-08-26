<?php

namespace MvcLTE\Captcha;

use MvcLTE\Http\Request;
use MvcLTE\Http\Client\Factory;
use MvcLTE\Captcha\CaptchaFactory;
use MvcLTE\Captcha\CaptchaManager;
use MvcLTE\Support\ServiceProvider;
use MvcLTE\Contracts\Container\Container;
use MvcLTE\Core\Application as Application;
use MvcLTE\Contracts\Captcha\ManagerInterface as CaptchaManagerInferface;

class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * Boot up the service provider
     * 
     * @return void
     */
    public function boot(): void
    {
        $this->setupConfig();
    }

    /**
     * Set up configuration for the hashids service
     * 
     * @return void
     */
    protected function setupConfig(): void
    {
        $Source = realpath($Raw = __DIR__ . '/../../config/captcha.php') ?: $Raw;

        if ($this->App instanceof Application && $this->App->runningInConsole()) {
            $this->publishes([
                $Source => config_path('captcha.php')
            ]);
        }

        $this->mergeConfigFrom($Source, 'captcha');
    }

    /**
     * Register hashids service
     * 
     * @var void
     */
    public function register(): void
    {
        $this->registerFactory();
        $this->registerManager();
        $this->registerBindings();
    }

    /**
     * Register factory
     * 
     * @return void
     */
    protected function registerFactory(): void
    {
        $this->App->singleton('Captcha.Factory', function () {
            return new CaptchaFactory(
                $this->App->make(Request::class),
                $this->App->make(Factory::class),
            );
        });

        $this->App->alias('Captcha.Factory', CaptchaFactory::class);
    }

    /**
     * Register manager
     * 
     * @return void
     */
    protected function registerManager(): void
    {
        $this->App->singleton('Captcha.Manager', function (Container $App) {
            $Config = $App['Config'];
            $Factory = $App['Captcha.Factory'];

            return new CaptchaManager($Config, $Factory);
        });

        $this->App->alias('Captcha.Manager', CaptchaManager::class);
        $this->App->alias('Captcha.Manager', CaptchaManagerInferface::class);
    }

    /**
     * Register bindings
     * 
     * @return void
     */
    protected function registerBindings(): void
    {
        $this->App->bind('Captcha', function (Container $App) {
            $Manager = $App['Captcha'];

            return $Manager->captcha();
        });
    }

    /**
     * Hashids provides
     * 
     * @return void
     */
    public function provides(): array
    {
        return [
            'Captcha',
            'Captcha.Factory',
            'Captcha.Manager',
        ];
    }
}
