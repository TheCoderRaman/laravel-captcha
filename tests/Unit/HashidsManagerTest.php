<?php
namespace Tests\Unit;

use \Hashids\Hashids;
use \PHPUnit\Framework\TestCase;

use MvcLTE\Config\Config;
use MvcLTE\Hashids\HashidsFactory;
use MvcLTE\Hashids\HashidsManager;

require(__DIR__."/../../vendor/autoload.php");

class HashidsManagerTest extends TestCase
{
    /**
    * Configuration data array
    *
    * @var array $Config
    */
    protected $Config; 

    /**
    * Content data
    *
    * @var Hashids\HashidsFactory $HashidsFactory
    */
    protected $HashidsFactory;  

    /**
     * Setup testing environment variables
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->Config = new Config([
            "hashids" => include(__DIR__."/../../config/hashids.php")
        ]);

        $this->HashidsFactory = new HashidsFactory();
    }

    /**
     * Test hashids manager get config
     *
     * @return void
     */
    public function testGetConfig()
    {

        $HashidsManager = new HashidsManager(
            $this->Config,$this->HashidsFactory
        );

        $this->assertEquals($this->Config, 
            $HashidsManager->getConfig()
        );
    }

    /**
     * Test hashids manager get factory
     *
     * @return void
     */
    public function testGetFactory()
    {

        $HashidsManager = new HashidsManager(
            $this->Config,$this->HashidsFactory
        );

        $this->assertEquals($this->HashidsFactory, 
            $HashidsManager->getFactory()
        );
    }
    
    /**
     * Test hashids manager get connection config
     *
     * @return void
     */
    public function testGetConnectionConfig()
    {
        $Expected = array_merge($this->Config->get(
            "hashids.connections.main"
        ),[
            "name" => 'main'
        ]);

        $HashidsManager = new HashidsManager(
            $this->Config,$this->HashidsFactory
        );

        $this->assertEquals($Expected, 
            $HashidsManager->getConnectionConfig("main")
        );
    }

    /**
     * Test hashids manager get default connection
     *
     * @return void
     */
    public function testGetDefaultConnection()
    {
        $Expected = $this->Config->get(
            "hashids.default"
        );

        $this->assertEquals($Expected,(new HashidsManager(
                $this->Config,$this->HashidsFactory
           ))->getDefaultConnection()
        );
    }

    /**
     * Test hashids manager connection make
     *
     * @return void
     */
    public function testConnection()
    {
        $Expected = $this->HashidsFactory->make(
            $this->Config->get("hashids.connections.main")
        );
        
        $this->assertEquals($Expected, (new HashidsManager(
                $this->Config,$this->HashidsFactory
           ))->connection("main")
        );
    }
};
