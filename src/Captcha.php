<?php

namespace TheCoderRaman\Captcha;


use Illuminate\Http\Request;
use TheCoderRaman\Captcha\Factory;
use Illuminate\Http\Client\Factory as HttpClient;
use TheCoderRaman\Captcha\Contracts\DriverInterface;
use TheCoderRaman\Captcha\Exceptions\CaptchaException;

/**
 * Manages CAPTCHA driver instances and provides an extensible interface.
 *
 * This class serves as the primary interface for the CAPTCHA package. It
 * leverages a `Factory` to create CAPTCHA driver instances and allows
 * for custom drivers to be registered and resolved through an extension
 * mechanism. It also uses a magic `__call` method to delegate
 * method calls directly to the currently resolved CAPTCHA driver.
 */
class Captcha
{
    /**
     * The CAPTCHA driver factory instance.
     * Responsible for creating and managing CAPTCHA driver objects.
     *
     * @var Factory
     */
    protected Factory $factory;

    /**
     * The current HTTP request instance.
     * Used by CAPTCHA drivers for verification
     * purposes. (e.g., retrieving response tokens, IP address)
     *
     * @var Request
     */
    protected Request $request;

    /**
     * The HTTP client factory instance.
     * Used by CAPTCHA drivers to make external API calls for verification.
     *
     * @var HttpClient
     */
    protected HttpClient $client;

    /**
     * An array to store instantiated and configured CAPTCHA driver instances.
     * This acts as a cache to prevent redundant object creation.
     *
     * @var array<string,DriverInterface>
     */
    protected array $drivers = [
        // Instances of registered 
        // drivers will be stored here, keyed by driver name.
    ];

    /**
     * The currently resolved CAPTCHA driver instance.
     * This holds the active driver that will handle subsequent CAPTCHA operations.
     *
     * @var DriverInterface
     */
    protected DriverInterface $driver;

    /**
     * Constructs a new Captcha manager instance.
     *
     * @param Request $request
     * @param HttpClient $client
     * @return void
     */
    public function __construct(
        Request $request, HttpClient $client
    ) {
        $this->client = $client;
        $this->request = $request;
        $this->factory = new Factory($this);
    }

    /**
     * Get the current HTTP request instance.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Set the current HTTP request instance.
     *
     * @param  Request  $request
     * @return $this
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the CAPTCHA driver factory instance.
     *
     * This method provides access to the factory responsible for creating
     * and managing CAPTCHA driver instances.
     *
     * @return Factory
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }

    /**
     * Set the CAPTCHA driver factory instance.
     *
     * This method allows for setting or overriding the factory instance
     * used by this CAPTCHA manager. This can be useful for dependency injection
     * or testing purposes.
     *
     * @param Factory $factory
     * @return $this
     */
    public function setFactory(Factory $factory): self
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Get the HTTP client instance.
     *
     * @return HttpClient
     */
    public function getClient(): HttpClient
    {
        return $this->client;
    }

    /**
     * Set the HTTP client instance.
     *
     * @param  HttpClient  $client
     * @return $this
     */
    public function setClient(HttpClient $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get the name of the configuration file.
     *
     * This public method returns the base name of the package's configuration file
     * without the '.php' extension. It's typically used internally to retrieve
     * configuration settings from Laravel's config system (e.g., `config('captcha.default')`).
     *
     * @return string The base name of the configuration file (e.g., 'captcha' for `config/captcha.php`).
     */
    public function getConfigName(): string
    {
        return 'captcha';
    }

    /**
     * Retrieves a specific CAPTCHA driver instance from the internal cache or the currently active driver.
     *
     * This method attempts to return a driver instance based on the provided `$driver` name
     * from the internal collection of previously created/stored drivers (`$this->drivers`).
     * If no `$driver` name is provided, or if the named driver is not found, it falls back
     * to returning the currently active driver (`$this->driver`) that was last set by
     * a method like `captcha()`.
     *
     * @param string|null $driver
     * @return DriverInterface|null
     */
    public function getDriver(?string $driver = null): ?DriverInterface
    {
        return $this->drivers[$driver] ?? $this->driver ?? null;
    }

    /**
     * Stores or updates a CAPTCHA driver instance in the internal cache and potentially sets it as the active driver.
     *
     * This method allows for programmatically adding or overriding a specific
     * CAPTCHA driver instance in the internal collection (`$this->drivers`).
     * This can be useful for testing or when you have a pre-configured driver instance
     * you want to make available without going through the usual creation process.
     *
     * Additionally, if no active driver is currently set (`$this->driver` is null),
     * this newly set driver instance will also be assigned as the active driver.
     *
     * @param string $driver
     * @param DriverInterface $instance
     * @return $this
     */
    public function setDriver(string $driver, DriverInterface $instance): self
    {
        $this->drivers[$driver] = $instance;

        if(!isset($this->driver)) {
            $this->driver = $this->drivers[$driver];
        }

        return $this;
    }

    /**
     * Attempts to resolve and set the active CAPTCHA driver, silently catching exceptions.
     *
     * This method calls the internal `captcha()` method to resolve a driver.
     * However, unlike `captcha()`, it wraps the call in a `try-catch` block,
     * ensuring that any `CaptchaException` thrown during driver creation or
     * initialization is caught and suppressed. This makes it "safe" in the
     * sense that it won't interrupt execution if a driver fails to load,
     * which can be useful for graceful degradation.
     *
     * @param string|null $driver
     * @param array $config
     * @return $this
     */
    public function safeCaptcha(?string $driver = null, array $config = []): self
    {
        try {
            $this->captcha($driver, $config);
        } catch (CaptchaException $e) {}

        return $this;
    }

    /**
     * Resolves and sets the active CAPTCHA driver, caching the instance.
     *
     * This method is responsible for instructing the factory to create a CAPTCHA
     * driver instance based on the given `$driver` name and `$config`.
     * The created driver instance is then stored in the internal `$this->drivers`
     * cache.
     *
     * If no active driver (`$this->driver`) is currently set, the newly created
     * driver will also be assigned as the main active driver.
     *
     * @param string|null $driver
     * @param array $config
     * @return $this
     *
     * @throws CaptchaException
     */
    public function captcha(?string $driver = null, array $config = []): self
    {
        $this->drivers[$driver] = (
            $this->factory->make($driver, $config)
        );

        if(!isset($this->driver)) {
            $this->driver = $this->drivers[$driver];
        }

        return $this;
    }

    /**
     * Dynamically calls a method on the currently active CAPTCHA driver.
     *
     * This magic method intercepts all calls to undefined public methods on this `Captcha` class.
     * It first ensures that a CAPTCHA driver instance (`$this->driver`) is resolved (calling
     * `safeCaptcha()` if necessary). Then, it checks if the called method exists on this `Captcha` class
     * itself. If so, it calls it. Otherwise, it delegates the method call and its parameters to the
     * resolved CAPTCHA driver instance. This provides a fluid interface for interacting with the underlying driver.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (!method_exists($this, $method) && !isset($this->driver)) {
            // Initialize with default or configured driver
            $this->safeCaptcha();
        }

        // Check if the method exists on this class itself, otherwise delegate to the driver.
        return (
            (method_exists($this, $method) ? $this : $this->driver)->{$method}(...$parameters)
        );
    }
}