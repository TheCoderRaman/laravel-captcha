<?php

namespace MvcLTE\Captcha;

use MvcLTE\Captcha\CaptchaFactory;
use MvcLTE\Captcha\CaptchaManager;

use MvcLTE\Support\ServiceProvider;
use MvcLTE\Contracts\Container\Container;
use MvcLTE\Core\Application as Application;

class HashidsServiceProvider extends ServiceProvider
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
        $Source = realpath($Raw = __DIR__ . '/../../config/hashids.php') ?: $Raw;

        if ($this->App instanceof Application && $this->App->runningInConsole()) {
            $this->publishes([
                $Source => config_path('hashids.php')
            ]);
        }

        $this->mergeConfigFrom($Source, 'hashids');
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
        $this->App->singleton('Hashids.Factory', function () {
            return new HashidsFactory();
        });

        $this->App->alias('Hashids.Factory', HashidsFactory::class);
    }

    /**
     * Register manager
     * 
     * @return void
     */
    protected function registerManager(): void
    {
        $this->App->singleton('Hashids', function (Container $App) {
            $Config = $App['Config'];
            $Factory = $App['Hashids.Factory'];

            return new HashidsManager($Config, $Factory);
        });

        $this->App->alias('Hashids', HashidsManager::class);
    }

    /**
     * Register bindings
     * 
     * @return void
     */
    protected function registerBindings(): void
    {
        $this->App->bind('Hashids.Connection', function (Container $App) {
            $Manager = $App['Hashids'];

            return $Manager->connection();
        });

        $this->App->alias('Hashids.Connection', Hashids::class);
    }

    /**
     * Hashids provides
     * 
     * @return void
     */
    public function provides(): array
    {
        return [
            'Hashids',
            'Hashids.Factory',
            'Hashids.Connection',
        ];
    }
}
