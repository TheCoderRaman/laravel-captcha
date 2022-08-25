<?php

namespace MvcLTE\Hashids;

use \InvalidArgumentException;

use \Hashids\Hashids;

use MvcLTE\Helpers\ArrayData;
use MvcLTE\Hashids\HashidsFactory;
use MvcLTE\Contracts\Config\Config;
use MvcLTE\Contracts\Hashids\ManagerInterface;

/**
 * @method string encode(mixed ...$Numbers)
 * @method array decode(string $Hash)
 * @method string encodeHex(string $Str)
 * @method string decodeHex(string $Hash)
 */
class HashidsManager implements ManagerInterface
{
    /**
     * The config instance.
     *
     * @var MvcLTE\Contracts\Config\Config $Config
     */
    protected $Config;

    /**
     * Hashids factory instance
     * 
     * @var Hashids\HashidsFactory $Factory
     */
    protected $Factory;

    /**
     * The active connection instances.
     *
     * @var array<string,object> $Connections
     */
    protected $Connections = [];

    /**
     * The custom connection resolvers.
     *
     * @var array<string,callable> $Extensions
     */
    protected $Extensions = [];

    /**
     * Construct hashids manager instance
     * 
     * @param MvcLTE\Contracts\Config\Config $Config
     * @param Hashids\HashidsFactory $Factory
     * @return void
     */
    public function __construct(Config $Config, HashidsFactory $Factory)
    {
        $this->Config = $Config;
        $this->Factory = $Factory;
    }

    /**
     * Get a connection instance.
     *
     * @param string|null $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function connection(string $Name = null)
    {
        $Name = $Name ?: $this->getDefaultConnection();

        if (!isset($this->Connections[$Name])) {
            $this->Connections[$Name] = $this->makeConnection($Name);
        }

        return $this->Connections[$Name];
    }

    /**
     * Reconnect to the given connection.
     *
     * @param string|null $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function reconnect(string $Name = null)
    {
        $Name = $Name ?: $this->getDefaultConnection();

        $this->disconnect($Name);

        return $this->connection($Name);
    }

    /**
     * Disconnect from the given connection.
     *
     * @param string|null $Name
     *
     * @return void
     */
    public function disconnect(string $Name = null)
    {
        $Name = $Name ?: $this->getDefaultConnection();

        unset($this->Connections[$Name]);
    }

    /**
     * Create new hashids instance with provided configuration
     * 
     * @param array $Config
     * @return void
     */
    protected function createConnection(array $Config): Hashids
    {
        return $this->Factory->make($Config);
    }

    /**
     * Make the connection instance.
     *
     * @param string $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    protected function makeConnection(string $Name)
    {
        $Config = $this->getConnectionConfig($Name);

        if (isset($this->Extensions[$Name])) {
            return $this->Extensions[$Name]($Config);
        }

        if ($Driver = ArrayData::get($Config, 'driver')) {
            if (isset($this->Extensions[$Driver])) {
                return $this->Extensions[$Driver]($Config);
            }
        }

        return $this->createConnection($Config);
    }

    /**
     * Get config name
     * 
     * @return string
     */
    protected function getConfigName(): string
    {
        return 'hashids';
    }

    /**
     * Get factory instance
     * 
     * @return Hashids\HashidsFactory
     */
    public function getFactory(): HashidsFactory
    {
        return $this->Factory;
    }

    /**
     * Get the configuration for a connection.
     *
     * @param string|null $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getConnectionConfig(string $Name = null)
    {
        $Name = $Name ?: $this->getDefaultConnection();

        return $this->getNamedConfig('connections', 'connection', $Name);
    }

    /**
     * Get the given named configuration.
     *
     * @param string $Type
     * @param string $Desc
     * @param string $Name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function getNamedConfig(string $Type, string $Desc, string $Name)
    {
        $Data = $this->Config->get($this->getConfigName().'.'.$Type);

        if (!is_array($Config = ArrayData::get($Data, $Name)) && !$Config) {
            throw new InvalidArgumentException("$Desc [$Name] not configured.");
        }

        $Config['name'] = $Name;

        return $Config;
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->Config->get($this->getConfigName().'.default');
    }

    /**
     * Set the default connection name.
     *
     * @param string $Name
     *
     * @return void
     */
    public function setDefaultConnection(string $Name)
    {
        $this->Config->set($this->getConfigName().'.default', $Name);
    }

    /**
     * Register an extension connection resolver.
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
     * Return all of the created connections.
     *
     * @return array<string,object>
     */
    public function getConnections()
    {
        return $this->Connections;
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
     * Dynamically pass methods to the default connection.
     *
     * @param string $Method
     * @param array  $Parameters
     *
     * @return mixed
     */
    public function __call(string $Method, array $Parameters)
    {
        return $this->connection()->$Method(...$Parameters);
    }    
}
