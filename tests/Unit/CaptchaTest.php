<?php

namespace TheCoderRaman\Captcha\Tests;

use \ReflectionClass;

use BadMethodCallException;
use Illuminate\Http\Request;
use TheCoderRaman\Captcha\Captcha;
use TheCoderRaman\Captcha\Factory;
use PHPUnit\Framework\Attributes\Test;
use TheCoderRaman\Captcha\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TheCoderRaman\Captcha\Drivers\NullCaptcha;
use Illuminate\Http\Client\Factory as HttpClient;
use TheCoderRaman\Captcha\Contracts\DriverInterface;
use TheCoderRaman\Captcha\Exceptions\CaptchaException;

class CaptchaTest extends TestCase
{
    /**
     * @var MockObject|Captcha
     */
    protected Captcha $captcha;

    /**
     * @var MockObject|Factory
     */
    protected Factory $mockFactory;

    /**
     * @var MockObject|Request
     */
    protected Request $mockRequest;

    /**
     * @var MockObject|HttpClient
     */
    protected HttpClient $mockHttpClient;

    /**
     * @var MockObject|DriverInterface
     */
    protected DriverInterface $mockDriver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockFactory = $this->createMock(Factory::class);
        $this->mockRequest = new Request();
        $this->mockHttpClient = $this->createMock(HttpClient::class);
        $this->mockDriver = $this->createMock(DriverInterface::class);

        $this->captcha = new Captcha($this->mockRequest, $this->mockHttpClient);
    }

    /**
     * Helper to get protected properties for testing (replaces assertAttributeEquals).
     *
     * @param object $object
     * @param string $propertyName
     * @return mixed
     */
    protected function getProtectedProperty(object $object, string $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    #[Test]
    public function it_constructs_with_dependencies()
    {
        $this->assertInstanceOf(Captcha::class, $this->captcha);
        $this->assertSame($this->mockRequest, $this->getProtectedProperty($this->captcha, 'request'));
        $this->assertSame($this->mockHttpClient, $this->getProtectedProperty($this->captcha, 'client'));
        $this->assertInstanceOf(Factory::class, $this->captcha->getFactory());
    }

    #[Test]
    public function it_can_get_and_set_request_instance()
    {
        $this->assertSame($this->mockRequest, $this->captcha->getRequest());

        $newMockRequest = new Request();
        $this->captcha->setRequest($newMockRequest);
        $this->assertSame($newMockRequest, $this->captcha->getRequest());
    }

    #[Test]
    public function it_can_get_and_set_factory_instance()
    {
        $captchaWithMockFactory = new Captcha($this->mockRequest, $this->mockHttpClient);
        $captchaWithMockFactory->setFactory($this->mockFactory);

        $this->assertSame($this->mockFactory, $captchaWithMockFactory->getFactory());

        $newMockFactory = $this->createMock(Factory::class);
        $captchaWithMockFactory->setFactory($newMockFactory);
        $this->assertSame($newMockFactory, $captchaWithMockFactory->getFactory());
    }

    #[Test]
    public function it_can_get_and_set_http_client_instance()
    {
        $this->assertSame($this->mockHttpClient, $this->captcha->getClient());

        $newMockHttpClient = $this->createMock(HttpClient::class);
        $this->captcha->setClient($newMockHttpClient);
        $this->assertSame($newMockHttpClient, $this->captcha->getClient());
    }

    #[Test]
    public function it_returns_the_correct_config_name()
    {
        $this->assertEquals('captcha', $this->captcha->getConfigName());
    }

    #[Test]
    public function it_can_set_and_get_a_driver_instance()
    {
        $this->assertNull($this->captcha->getDriver('test_driver'));
        $this->assertNull($this->captcha->getDriver());

        $this->captcha->setDriver('test_driver', $this->mockDriver);

        $this->assertSame($this->mockDriver, $this->captcha->getDriver('test_driver'));
        $this->assertSame($this->mockDriver, $this->captcha->getDriver());
    }

    #[Test]
    public function it_gets_the_active_driver_when_no_specific_driver_is_requested()
    {
        $anotherMockDriver = $this->createMock(DriverInterface::class);
        $this->captcha->setDriver('first_driver', $this->mockDriver);
        $this->captcha->setDriver('second_driver', $anotherMockDriver);

        $this->assertSame($this->mockDriver, $this->captcha->getDriver());
        $this->assertSame($anotherMockDriver, $this->captcha->getDriver('second_driver'));
    }

    #[Test]
    public function it_resolves_and_sets_captcha_driver()
    {
        $this->mockFactory
            ->expects($this->once())
            ->method('make')
            ->with('test_driver', ['key' => 'value'])
            ->willReturn($this->mockDriver);

        $this->captcha->setFactory($this->mockFactory);
        $this->captcha->captcha('test_driver', ['key' => 'value']);

        $this->assertSame($this->mockDriver, $this->captcha->getDriver('test_driver'));
        $this->assertSame($this->mockDriver, $this->captcha->getDriver());
    }

    #[Test]
    public function it_throws_exception_when_captcha_resolution_fails()
    {
        $this->mockFactory
            ->expects($this->once())
            ->method('make')
            ->willThrowException(new CaptchaException('Driver not found.'));

        $this->captcha->setFactory($this->mockFactory);

        $this->expectException(CaptchaException::class);
        $this->expectExceptionMessage('Driver not found.');

        $this->captcha->captcha('non_existent_driver');
    }

    #[Test]
    public function it_safely_resolves_captcha_driver_and_catches_exception()
    {
        $this->mockFactory
            ->expects($this->once())
            ->method('make')
            ->willThrowException(new CaptchaException('Driver not found.'));

        $this->captcha->setFactory($this->mockFactory);

        $this->captcha->safeCaptcha('non_existent_driver');
        $this->assertNull($this->captcha->getDriver());
    }

    #[Test]
    public function it_delegates_method_calls_to_the_active_driver()
    {
        $this->captcha->setFactory($this->mockFactory);
        $this->mockFactory->method('make')->willReturn($this->mockDriver);
        $this->captcha->captcha('some_driver');

        $this->mockDriver
            ->expects($this->once())
            ->method('verify')
            ->with('some_token')
            ->willReturn(true);

        $result = $this->captcha->verify('some_token');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_calls_safeCaptcha_if_no_driver_is_set_on_magic_call()
    {
        $this->captcha->setFactory($this->mockFactory);

        $this->mockFactory
            ->expects($this->once())
            ->method('make')
            ->willReturn($this->mockDriver);

        $this->mockDriver
            ->expects($this->once())
            ->method('getCaptcha')
            ->willReturn((new NullCaptcha('', ''))->getCaptcha());

        $result = $this->captcha->getCaptcha();

        $this->assertEquals((new NullCaptcha('', ''))->getCaptcha(), $result);
        $this->assertSame($this->mockDriver, $this->captcha->getDriver());
    }

    #[Test]
    public function it_throws_bad_method_call_exception_if_method_does_not_exist_on_class_or_driver_and_no_driver_can_be_resolved()
    {
        $this->mockFactory
            ->expects($this->once())
            ->method('make')
            ->willThrowException(new CaptchaException('Failed to create driver.'));

        $this->captcha->setFactory($this->mockFactory);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/^Call to undefined method/');

        $this->captcha->nonExistentMethod();
    }

    #[Test]
    public function it_prioritizes_local_method_over_driver_method_on_magic_call()
    {
        $this->captcha->setFactory($this->mockFactory);
        $this->mockFactory->method('make')->willReturn($this->mockDriver);
        $this->captcha->captcha('some_driver');

        $result = $this->captcha->getRequest();

        $this->assertSame($this->mockRequest, $result);
    }
}
