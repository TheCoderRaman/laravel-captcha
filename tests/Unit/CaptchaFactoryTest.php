<?php
namespace Tests\Unit;

use \PHPUnit\Framework\TestCase;

use MvcLTE\Http\Request;
use MvcLTE\Config\Config;
use MvcLTE\Core\Application;
use MvcLTE\Http\Client\Factory;
use MvcLTE\Captcha\CaptchaFactory;
use MvcLTE\Captcha\Captchas\NullCaptcha;

require(__DIR__."/../../vendor/autoload.php");

class CaptchaFactoryTest extends TestCase
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
     * Test hashids factory make
     *
     * @return void
     */
    public function testMake()
    {
        $Expected = new NullCaptcha(
            $this->Config->get("captcha.captchas.nullcaptcha.key"),
            $this->Config->get("captcha.captchas.nullcaptcha.secret")
        );

        $Expected->setClient($this->CaptchaFactory->getClient());
        $Expected->setRequest($this->CaptchaFactory->getRequest());
        
        $this->assertEquals($Expected, 
            $this->CaptchaFactory->make('nullcaptcha',
                $this->Config->get("captcha.captchas.nullcaptcha")
            )
        );
    }
};
