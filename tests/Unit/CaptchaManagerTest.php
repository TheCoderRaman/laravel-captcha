<?php
namespace Tests\Unit;

use \PHPUnit\Framework\TestCase;

use MvcLTE\Http\Request;
use MvcLTE\Config\Config;
use MvcLTE\Core\Application;
use MvcLTE\Http\Client\Factory;
use MvcLTE\Captcha\CaptchaFactory;
use MvcLTE\Captcha\CaptchaManager;
use MvcLTE\Captcha\Captchas\NullCaptcha;

require(__DIR__."/../../vendor/autoload.php");

class CaptchaManagerTest extends TestCase
{
   /**
    * The application instance.
    *
    * @var MvcLTE\Core\Application $App
    */
    protected $App;  

   /**
    * The captcha configuration.
    *
    * @var MvcLTE\Config\Config $Config
    */
    protected $Config;  

   /**
    * Content data
    *
    * @var MvcLTE\Captcha\CaptchaFactory $CaptchaFactory
    */
    protected $CaptchaFactory;    

    /**
     * Setup testing environment variables
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->App = new Application();

        $this->Config = new Config([
            "captcha" => include(__DIR__."/../../config/captcha.php")
        ]);

        $this->CaptchaFactory = new CaptchaFactory(new Request(),
            $this->App->make(Factory::class),
        );
    }

    /**
     * Test captcha manager get config
     *
     * @return void
     */
    public function testGetConfig()
    {

        $CaptchaManager = new CaptchaManager(
            $this->Config,$this->CaptchaFactory
        );

        $this->assertEquals($this->Config, 
            $CaptchaManager->getConfig()
        );
    }

    /**
     * Test captcha manager get factory
     *
     * @return void
     */
    public function testGetFactory()
    {

        $CaptchaManager = new CaptchaManager(
            $this->Config,$this->CaptchaFactory
        );

        $this->assertEquals($this->CaptchaFactory, 
            $CaptchaManager->getFactory()
        );
    }
    
    /**
     * Test captcha manager get captcha config
     *
     * @return void
     */
    public function testGetCaptchaConfig()
    {
        $Expected = array_merge($this->Config->get(
            "captcha.captchas.nullcaptcha"
        ),[
            "name" => 'nullcaptcha'
        ]);

        $CaptchaManager = new CaptchaManager(
            $this->Config,$this->CaptchaFactory
        );

        $this->assertEquals($Expected, 
            $CaptchaManager->getCaptchaConfig("nullcaptcha")
        );
    }

    /**
     * Test captcha manager get default captcha
     *
     * @return void
     */
    public function testGetDefaultCaptcha()
    {
        $Expected = $this->Config->get(
            "captcha.default"
        );

        $this->assertEquals($Expected,(new CaptchaManager(
                $this->Config,$this->CaptchaFactory
           ))->getDefaultCaptcha()
        );
    }

    /**
     * Test hashids manager connection make
     *
     * @return void
     */
    public function testCaptcha()
    {
        $Expected = $this->CaptchaFactory->make(
            $Default = $this->Config->get("captcha.default"),
            $this->Config->get("captcha.captchas.{$Default}")
        );
        
        $this->assertEquals($Expected, (new CaptchaManager(
                $this->Config,$this->CaptchaFactory
           ))->captcha($Default)
        );
    }
};
