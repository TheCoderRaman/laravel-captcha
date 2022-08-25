<?php
namespace Tests\Unit;

use \Hashids\Hashids;
use \PHPUnit\Framework\TestCase;

use MvcLTE\Hashids\HashidsFactory;

require(__DIR__."/../../vendor/autoload.php");

class HashidsFactoryTest extends TestCase
{
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
        $this->HashidsFactory = new HashidsFactory();
    }

    /**
     * Test hashids factory make
     *
     * @return void
     */
    public function testMake()
    {
        $Config = [
            "salt" => 'Factory Test',
            "length" => 10,
            "alphabet" => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ];

        $Expected = new Hashids(
            $Config['salt'], $Config['length'], $Config['alphabet']
        );
        
        $this->assertEquals($Expected, 
            $this->HashidsFactory->make($Config)
        );
    }
};
