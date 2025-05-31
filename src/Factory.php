<?php

namespace TheCoderRaman\Captcha;


use \Closure;

use Illuminate\Support\Arr;
use TheCoderRaman\Captcha\Captcha;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use TheCoderRaman\Captcha\Drivers\Driver;
use TheCoderRaman\Captcha\Contracts\DriverInterface;
use TheCoderRaman\Captcha\Exceptions\CaptchaException;
use TheCoderRaman\Captcha\Enums\Captcha as CaptchaEnum;

/**
 * Factory class responsible for creating and initializing CAPTCHA driver instances.
 *
 * This class handles the logic of resolving the correct CAPTCHA driver class,
 * instantiating it, and injecting the necessary dependencies (like HTTP client
 * and HTTP request) into the driver. It supports both default drivers and
 * dynamically resolved drivers based on configuration or direct class names.
 */
class Factory
{
    /**
     * The main CAPTCHA manager instance.
     *
     * This property holds an instance of `TheCoderRaman\Captcha\Captcha` (the manager class),
     * which provides access to shared resources like the HTTP client and request instance,
     * as well as configuration helpers.
     *
     * @var Captcha
     */
    protected Captcha $captcha;

    /**
     * An array of callable resolvers for custom CAPTCHA drivers (extensions).
     * Keys are driver names, values are Closures or callables that return a DriverInterface instance.
     *
     * @var array<string, callable>
     */
    protected array $extensions = [];

    /**
     * Creates a new Factory instance.
     *
     * Injects the main Captcha manager instance, which provides access to
     * global configurations and shared services like the HTTP client and request.
     *
     * @param Captcha $captcha The main CAPTCHA manager instance.
     * @return void
     */
    public function __construct(Captcha $captcha)
    {
        $this->captcha = $captcha;
    }

    /**
     * Registers a custom CAPTCHA driver resolver (extension).
     *
     * This allows developers to add support for new CAPTCHA types or
     * override the creation logic for existing ones. The `$resolver` callable
     * will be invoked when `captcha()` or `make()` attempts to resolve
     * the specified `$driver` and the default factory fails to create it.
     * If the resolver is a Closure, it will be bound to this `Captcha` instance.
     *
     * @param string $driver
     * @param callable $resolver.
     * @return $this
     */
    public function extend(string $driver, callable $resolver): self
    {
        // Bind Closure resolvers to the current object for access to its properties.
        if (!($resolver instanceof Closure)) {
            $this->extensions[$driver] = $resolver;
        } else {
            $this->extensions[$driver] = $resolver->bindTo($this, $this);
        }

        return $this;
    }

    /**
     * Creates a CAPTCHA driver instance without error handling.
     *
     * This method attempts to resolve and instantiate a CAPTCHA driver based on the
     * provided driver name or the default configured driver. It merges explicit
     * configuration with any pre-defined settings for the driver.
     *
     * Unlike the `make()` method, this method does not include try-catch blocks
     * for `CaptchaException` and will directly throw an exception if driver creation
     * fails (e.g., driver class not found, invalid instance returned by an extension).
     *
     * @param string|null $driver
     * @param array $config
     * @return DriverInterface
     * 
     * @throws CaptchaException
     */
    public function unSafeMake(?string $driver = null, array $config = []): DriverInterface
    {
        /** @var string */
        $configName = $this->captcha->getConfigName();

        // Determine the driver name:
        // use provided, or fallback to default from config.
        if (empty($driver)) {
            $driver = Config::get(
                // Use the enum's string value for default
                "{$configName}.default", CaptchaEnum::NullCaptcha->value
            );
        }

        return $this->createDriver($driver, array_merge(
            // Merge explicit config with configured values for the specific driver.
            Config::get("{$configName}.captchas.{$driver}", []), $config
        ));
    }

    /**
     * Resolves and returns a CAPTCHA driver instance.
     *
     * This is the primary method for obtaining a CAPTCHA driver. It determines
     * which driver to use (either explicitly specified, or the default from config),
     * fetches its configuration, attempts to create the driver, and handles
     * potential `CaptchaException`s by logging them and returning `false`.
     *
     * @param string|null $driver
     * @param array $config
     * @return DriverInterface|bool
     */
    public function make(?string $driver = null, array $config = []): DriverInterface|bool
    {
        try {
            return $this->unSafeMake($driver, $config);
        } catch (CaptchaException $e) {
            // Log the exception for debugging purposes.
            Log::error("Failed to create CAPTCHA driver [{$driver}]: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Creates a new instance of a specific CAPTCHA driver.
     *
     * This method first checks if the provided `$driver` string is a valid
     * class name. If not, it attempts to resolve the class name from the
     * `captcha.drivers` configuration mapping. It then instantiates the
     * resolved class using Laravel's service container and initializes it
     * by injecting common dependencies.
     *
     * @param string $driver
     * @param array $config
     * @return DriverInterface
     *
     * @throws CaptchaException
     */
    protected function createDriver(string $driver, array $config): DriverInterface
    {
        $class = null;

        /** @var string */
        $configName = $this->captcha->getConfigName();

        // 1. Check if the provided $driver
        // string is directly a valid class name.
        if (class_exists($driver)) {
            $class = $driver;
        }

        // 2. If not a class name,
        // try to get the class name from the 'drivers' config mapping.
        if (!$class) {
            $class = Config::get("{$configName}.drivers.{$driver}", null);
        }

        // Ensure a class name was found.
        if (!empty($class) && class_exists($class)) {
            // Instantiate the driver class using Laravel's container,
            // passing only relevant config keys to its constructor.
            $instance = App::make(
                $class, Arr::only($config, ['key', 'secret', 'url'])
            );

            // Initialize the driver by injecting necessary dependencies.
            return $this->initializeDriver($driver, $class, $instance);
        }

        if(isset($this->extensions[$driver]) || isset($this->extensions[$class])) {
            $class = (
                !empty($class) ? $class : Closure::Class
            );

            // If an extension exists, call its resolver to create
            // the driver instance. The resolver receives the merged configuration.
            $resolver = (
                $this->extensions[$driver] ?? $this->extensions[$class]
            );

            // Initialize driver instance after resolving using resolver
            return $this->initializeDriver(
                $driver, $class, $resolver(Arr::only($config, ['key', 'secret', 'url']))
            );
        }

        throw new CaptchaException(sprintf(
            'Unable to find CAPTCHA driver class for [%s]. Check your configuration or class path.',
            $driver
        ));
    }

    /**
     * Initializes a CAPTCHA driver instance by performing validation and injecting common dependencies.
     *
     * This method ensures that the provided driver instance:
     * 1. Extends the abstract `TheCoderRaman\Captcha\Drivers\Driver` class.
     * 2. Implements the `TheCoderRaman\Captcha\Contracts\DriverInterface`.
     * If these conditions are met, it injects the shared HTTP client and HTTP request
     * instances into the driver.
     *
     * @param string $driver
     * @param class-string $class
     * @param mixed $instance
     * @return DriverInterface
     *
     * @throws CaptchaException
     */
    protected function initializeDriver(
        string $driver,
        string $class,
        mixed $instance
    ): DriverInterface {
        // Validate that the instance extends the base Driver class.
        if (!($instance instanceof Driver)) {
            throw new CaptchaException(sprintf(
                'CAPTCHA driver [%s] (class: %s) does not extend from [%s].',
                $driver,
                $class,
                Driver::class
            ));
        }

        // Validate that the instance implements the DriverInterface.
        if (!($instance instanceof DriverInterface)) {
            throw new CaptchaException(sprintf(
                'CAPTCHA driver [%s] (class: %s) does not implement [%s].',
                $driver,
                $class,
                DriverInterface::class
            ));
        }

        // Inject shared dependencies (HTTP client and request) into the driver.
        return (
            $instance->setClient($this->captcha->getClient())->setRequest($this->captcha->getRequest())
        );
    }
}