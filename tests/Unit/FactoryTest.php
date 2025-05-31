<?php

namespace TheCoderRaman\Captcha\Tests\Unit;

use Mockery;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use TheCoderRaman\Captcha\Captcha;
use TheCoderRaman\Captcha\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use TheCoderRaman\Captcha\Drivers\Driver;
use Illuminate\Http\Client\Factory as HttpClient;
use TheCoderRaman\Captcha\Contracts\DriverInterface;
use TheCoderRaman\Captcha\Exceptions\CaptchaException;
use TheCoderRaman\Captcha\Enums\Captcha as CaptchaEnum;

class BadInterfaceOnlyDriver
{
    public function verify(): bool
    {
        return false;
    }
    public function render(): string
    {
        return '';
    }
    public function setClient($client)
    {
        return $this;
    }
    public function getClient()
    {
        return null;
    }
    public function setRequest($request)
    {
        return $this;
    }
    public function getRequest()
    {
        return null;
    }
}

abstract class BadDriverOnlyBase
{
    //
}

class DummyCaptchaDriver extends Driver implements DriverInterface
{
    public string $key;
    public string $secret;
    public string $url;

    public function __construct(string $key = '', string $secret = '', string $url = '')
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->url = $url;
    }

    public function driver(): string
    {
        return 'dummy-captcha-driver';
    }

    public function verify(): bool
    {
        return true;
    }

    public function getStyle(): string
    {
        return '<!-- Captcha StyleSheet -->';
    }

    public function getCaptcha(): string
    {
        return '<!-- Captcha Itself -->';
    }

    public function getScript(): string
    {
        return '<!-- Captcha Script -->';
    }
}

class BadDriverOnlyConcrete extends BadDriverOnlyBase
{
    public string $key;
    public string $secret;
    public string $url;

    public function __construct(string $key = '', string $secret = '', string $url = '')
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->url = $url;
    }

    public function driver(): string
    {
        return 'bad-driver-only-concrete';
    }

    public function verify(): bool
    {
        return true;
    }

    public function getStyle(): string
    {
        return '<!-- Captcha StyleSheet -->';
    }

    public function getCaptcha(): string
    {
        return '<!-- Captcha Itself -->';
    }

    public function getScript(): string
    {
        return '<!-- Captcha Script -->';
    }
}

class FactoryTest extends TestCase
{
    protected Captcha $mockCaptchaManager;
    protected Request $mockRequest;
    protected HttpClient $mockHttpClient;
    protected Factory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRequest = new Request();
        $this->mockHttpClient = $this->createMock(HttpClient::class);

        $this->mockCaptchaManager = Mockery::mock(Captcha::class);
        $this->mockCaptchaManager
            ->shouldReceive('getConfigName')
            ->andReturn('captcha')
            ->byDefault();
        $this->mockCaptchaManager
            ->shouldReceive('getClient')
            ->andReturn($this->mockHttpClient)
            ->byDefault();
        $this->mockCaptchaManager
            ->shouldReceive('getRequest')
            ->andReturn($this->mockRequest)
            ->byDefault();

        $this->factory = new Factory($this->mockCaptchaManager);

        Log::swap(Mockery::mock('Illuminate\Log\LogManager'));
        Config::swap(Mockery::mock('Illuminate\Contracts\Config\Repository'));
        App::swap(Mockery::mock('Illuminate\Contracts\Foundation\Application'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_constructs_with_captcha_manager()
    {
        $this->assertInstanceOf(Factory::class, $this->factory);
        $reflection = new \ReflectionClass($this->factory);
        $property = $reflection->getProperty('captcha');
        $property->setAccessible(true);
        $this->assertSame($this->mockCaptchaManager, $property->getValue($this->factory));
    }

    #[Test]
    public function it_can_extend_the_factory_with_a_closure()
    {
        $driverName = 'customDriver';
        $mockDriver = Mockery::mock(DummyCaptchaDriver::class);
        $mockDriver
            ->shouldReceive('setClient')
            ->once()
            ->with($this->mockHttpClient)
            ->andReturnSelf();
        $mockDriver
            ->shouldReceive('setRequest')
            ->once()
            ->with($this->mockRequest)
            ->andReturnSelf();

        $testCase = $this;
        $resolver = function (array $config) use ($mockDriver, $testCase) {
            $testCase->assertEquals(['key' => 'abc'], $config);
            return $mockDriver;
        };

        $this->factory->extend($driverName, $resolver);

        Config::shouldReceive('get')
            ->with('captcha.default', CaptchaEnum::NullCaptcha->value)
            ->andReturn($driverName);
        Config::shouldReceive('get')
            ->with("captcha.captchas.{$driverName}", [])
            ->andReturn(['key' => 'abc', 'extra' => 'value']);
        Config::shouldReceive('get')
            ->once()
            ->with("captcha.drivers.{$driverName}", null)
            ->andReturn(null);

        App::shouldNotReceive('make');
        Log::shouldNotReceive('error');

        $result = $this->factory->make($driverName, ['key' => 'abc']);

        $this->assertSame($mockDriver, $result);
    }

    #[Test]
    public function it_can_extend_the_factory_with_a_callable()
    {
        $driverName = 'callableDriver';
        $mockDriver = Mockery::mock(DummyCaptchaDriver::class);
        $mockDriver
            ->shouldReceive('setClient')
            ->once()
            ->with($this->mockHttpClient)
            ->andReturnSelf();
        $mockDriver
            ->shouldReceive('setRequest')
            ->once()
            ->with($this->mockRequest)
            ->andReturnSelf();

        $testCase = $this;
        $this->factory->extend($driverName, function ($config) use ($mockDriver, $testCase) {
            $testCase->assertEquals(['key' => 'def'], $config);
            return $mockDriver;
        });

        Config::shouldReceive('get')
            ->with('captcha.default', CaptchaEnum::NullCaptcha->value)
            ->andReturn($driverName);
        Config::shouldReceive('get')
            ->with("captcha.captchas.{$driverName}", [])
            ->andReturn(['key' => 'def']);
        Config::shouldReceive('get')
            ->once()
            ->with("captcha.drivers.{$driverName}", null)
            ->andReturn(null);

        App::shouldNotReceive('make');
        Log::shouldNotReceive('error');

        $result = $this->factory->make($driverName, ['key' => 'def']);

        $this->assertSame($mockDriver, $result);
    }

    public function callableResolver(array $config): DriverInterface
    {
        return $this->createMock(DriverInterface::class);
    }

    #[Test]
    public function make_resolves_default_driver_from_config()
    {
        $defaultDriverName = 'defaultDriver';
        $dummyDriverClass = DummyCaptchaDriver::class;

        Config::shouldReceive('get')
            ->with('captcha.default', CaptchaEnum::NullCaptcha->value)
            ->andReturn($defaultDriverName);
        Config::shouldReceive('get')
            ->with("captcha.captchas.{$defaultDriverName}", [])
            ->andReturn(['key' => 'cfg_key', 'secret' => 'cfg_secret']);
        Config::shouldReceive('get')
            ->with("captcha.drivers.{$defaultDriverName}", null)
            ->andReturn($dummyDriverClass);

        App::shouldReceive('make')
            ->once()
            ->with($dummyDriverClass, ['key' => 'cfg_key', 'secret' => 'cfg_secret'])
            ->andReturn(new DummyCaptchaDriver('cfg_key', 'cfg_secret'));

        $driver = $this->factory->make();

        $this->assertInstanceOf(DummyCaptchaDriver::class, $driver);
        $this->assertEquals('cfg_key', $driver->key);
        $this->assertEquals('cfg_secret', $driver->secret);
        $this->assertSame($this->mockHttpClient, $driver->getClient());
        $this->assertSame($this->mockRequest, $driver->getRequest());
    }

    #[Test]
    public function make_merges_explicit_config_with_driver_config()
    {
        $driverName = 'recap';
        $dummyDriverClass = DummyCaptchaDriver::class;
        $explicitConfig = ['key' => 'explicit_key', 'url' => 'http://example.com'];

        Config::shouldReceive('get')
            ->with('captcha.default', CaptchaEnum::NullCaptcha->value)
            ->andReturn(CaptchaEnum::NullCaptcha->value);
        Config::shouldReceive('get')
            ->with("captcha.captchas.{$driverName}", [])
            ->andReturn(['key' => 'config_key', 'secret' => 'config_secret']);
        Config::shouldReceive('get')
            ->with("captcha.drivers.{$driverName}", null)
            ->andReturn($dummyDriverClass);

        App::shouldReceive('make')
            ->once()
            ->with($dummyDriverClass, ['key' => 'explicit_key', 'secret' => 'config_secret', 'url' => 'http://example.com'])
            ->andReturn(new DummyCaptchaDriver('explicit_key', 'config_secret', 'http://example.com'));

        $driver = $this->factory->make($driverName, $explicitConfig);

        $this->assertInstanceOf(DummyCaptchaDriver::class, $driver);
        $this->assertEquals('explicit_key', $driver->key);
        $this->assertEquals('config_secret', $driver->secret);
        $this->assertEquals('http://example.com', $driver->url);
    }

    #[Test]
    public function make_returns_false_and_logs_error_on_captcha_exception()
    {
        $driverName = 'failingDriver';
        $dummyDriverClass = DummyCaptchaDriver::class;

        Config::shouldReceive('get')->andReturnUsing(function ($key, $default) use ($driverName, $dummyDriverClass) {
            if ($key === 'captcha.default') {
                return CaptchaEnum::NullCaptcha->value;
            }
            if ($key === "captcha.captchas.{$driverName}") {
                return [];
            }
            if ($key === "captcha.drivers.{$driverName}") {
                return $dummyDriverClass;
            }
            return $default;
        });

        App::shouldReceive('make')
            ->once()
            ->andThrow(new CaptchaException('Simulated driver creation failure.'));

        Log::shouldReceive('error')
            ->once()
            ->with("Failed to create CAPTCHA driver [{$driverName}]: Simulated driver creation failure.");

        $result = $this->factory->make($driverName);

        $this->assertFalse($result);
    }

    #[Test]
    public function create_driver_instantiates_by_direct_class_name()
    {
        $driverClass = DummyCaptchaDriver::class;

        // When a direct class name is provided to make(), the `if (empty($driver))` block
        // (which contains the `captcha.default` config lookup) should be skipped.
        // We explicitly use `shouldNotReceive` to confirm this expected behavior.
        Config::shouldNotReceive('get')->with('captcha.default', CaptchaEnum::NullCaptcha->value);

        // After the `if (empty($driver))` block, the next `Config::get()` call will be
        // for `captcha.captchas.{driver_class_name}`. For a direct class name,
        // it's unlikely to have specific config entries, so return an empty array.
        Config::shouldReceive('get')
            ->once()
            ->with("captcha.captchas.{$driverClass}", [])
            ->andReturn([]);

        // In `createDriver()`, `class_exists($driver)` will be true for a direct class name.
        // Therefore, the `captcha.drivers.{driver_class_name}` config lookup should be skipped.
        Config::shouldNotReceive('get')->with("captcha.drivers.{$driverClass}", null);

        // App::make will be called to instantiate the driver.
        App::shouldReceive('make')
            ->once()
            ->with($driverClass, ['key' => 'direct_key'])
            ->andReturn(new DummyCaptchaDriver('direct_key'));

        Log::shouldNotReceive('error'); // Success path, no error expected

        $driver = $this->factory->make($driverClass, ['key' => 'direct_key']);

        $this->assertInstanceOf(DummyCaptchaDriver::class, $driver);
        $this->assertEquals('direct_key', $driver->key);
        $this->assertSame($this->mockHttpClient, $driver->getClient());
        $this->assertSame($this->mockRequest, $driver->getRequest());
    }

    #[Test]
    public function create_driver_instantiates_by_config_mapping()
    {
        $driverName = 'test_driver';
        $mappedClass = DummyCaptchaDriver::class;

        Config::shouldReceive('get')
            ->with('captcha.default', CaptchaEnum::NullCaptcha->value)
            ->andReturn(CaptchaEnum::NullCaptcha->value);
        Config::shouldReceive('get')
            ->with("captcha.captchas.{$driverName}", [])
            ->andReturn(['secret' => 'mapped_secret']);
        Config::shouldReceive('get')
            ->with("captcha.drivers.{$driverName}", null)
            ->andReturn($mappedClass);

        App::shouldReceive('make')
            ->once()
            ->with($mappedClass, ['secret' => 'mapped_secret'])
            ->andReturn(new DummyCaptchaDriver('', 'mapped_secret'));

        $driver = $this->factory->make($driverName);

        $this->assertInstanceOf(DummyCaptchaDriver::class, $driver);
        $this->assertEquals('mapped_secret', $driver->secret);
        $this->assertSame($this->mockHttpClient, $driver->getClient());
        $this->assertSame($this->mockRequest, $driver->getRequest());
    }

    #[Test]
    public function create_driver_throws_exception_if_class_not_found()
    {
        $driverName = 'non_existent_driver';

        Config::shouldReceive('get')
            ->with('captcha.default', CaptchaEnum::NullCaptcha->value)
            ->andReturn(CaptchaEnum::NullCaptcha->value);
        Config::shouldReceive('get')
            ->with("captcha.captchas.{$driverName}", [])
            ->andReturn([]);
        Config::shouldReceive('get')
            ->with("captcha.drivers.{$driverName}", null)
            ->andReturn(null);

        Log::shouldReceive('error')
            ->once()
            ->with("Failed to create CAPTCHA driver [{$driverName}]: Unable to find CAPTCHA driver class for [{$driverName}]. Check your configuration or class path.");

        $this->assertFalse($this->factory->make($driverName));
    }

    #[Test]
    public function initialize_driver_throws_exception_if_not_instanceof_driver()
    {
        $driverName = 'badDriver';
        $class = BadInterfaceOnlyDriver::class;

        Config::shouldReceive('get')
            ->with('captcha.default', CaptchaEnum::NullCaptcha->value)
            ->andReturn(CaptchaEnum::NullCaptcha->value);
        Config::shouldReceive('get')
            ->with("captcha.captchas.{$driverName}", [])
            ->andReturn([]);
        Config::shouldReceive('get')
            ->with("captcha.drivers.{$driverName}", null)
            ->andReturn($class);

        App::shouldReceive('make')
            ->once()
            ->andReturn(new BadInterfaceOnlyDriver());

        Log::shouldReceive('error');

        $this->expectException(CaptchaException::class);
        $this->expectExceptionMessage(sprintf('CAPTCHA driver [%s] (class: %s) does not extend from [%s].', $driverName, $class, Driver::class));

        $this->factory->unSafeMake($driverName);
    }

    #[Test]
    public function initialize_driver_throws_exception_if_not_instanceof_driver_interface()
    {
        $driverName = 'badInterfaceDriver';
        $class = BadDriverOnlyConcrete::class;

        Config::shouldReceive('get')
            ->with('captcha.default', CaptchaEnum::NullCaptcha->value)
            ->andReturn(CaptchaEnum::NullCaptcha->value);
        Config::shouldReceive('get')
            ->with("captcha.captchas.{$driverName}", [])
            ->andReturn([]);
        Config::shouldReceive('get')
            ->with("captcha.drivers.{$driverName}", null)
            ->andReturn($class);

        App::shouldReceive('make')
            ->once()
            ->andReturn(new BadDriverOnlyConcrete());

        Log::shouldReceive('error');

        $this->expectException(CaptchaException::class);
        $this->expectExceptionMessage(sprintf('CAPTCHA driver [%s] (class: %s) does not extend from [%s].', $driverName, $class, Driver::class));

        $this->factory->unSafeMake($driverName);
    }

    #[Test]
    public function initialize_driver_injects_client_and_request()
    {
        $driverName = 'test_driver';
        $dummyDriverClass = DummyCaptchaDriver::class;

        $spyDriver = Mockery::spy(DummyCaptchaDriver::class);
        $spyDriver
            ->shouldReceive('setClient')
            ->once()
            ->with($this->mockHttpClient)
            ->andReturnSelf();
        $spyDriver
            ->shouldReceive('setRequest')
            ->once()
            ->with($this->mockRequest)
            ->andReturnSelf();

        App::shouldReceive('make')
            ->once()
            ->andReturn($spyDriver);

        Config::shouldReceive('get')
            ->with('captcha.default', CaptchaEnum::NullCaptcha->value)
            ->andReturn(CaptchaEnum::NullCaptcha->value);
        Config::shouldReceive('get')
            ->with("captcha.captchas.{$driverName}", [])
            ->andReturn([]);
        Config::shouldReceive('get')
            ->with("captcha.drivers.{$driverName}", null)
            ->andReturn($dummyDriverClass);

        $driver = $this->factory->make($driverName);

        $this->assertSame($spyDriver, $driver);
        $spyDriver->shouldHaveReceived('setClient');
        $spyDriver->shouldHaveReceived('setRequest');
    }
}
