<?php

namespace TheCoderRaman\Captcha\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use TheCoderRaman\Captcha\CaptchaServiceProvider;

/**
 * Base test case class for the Captcha package.
 *
 * This class extends Orchestra Testbench's `TestCase` to provide a clean
 * Laravel environment for testing the Captcha package. It automatically
 * registers the `CaptchaServiceProvider` and sets up a default database
 * connection for testing purposes.
 */
class TestCase extends Orchestra
{
    /**
     * Set up the test environment.
     *
     * This method is called before each test. It performs standard parent setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Get the package service providers.
     *
     * This method tells Orchestra Testbench which service providers should be
     * loaded for the tests. In this case, it registers the `CaptchaServiceProvider`.
     *
     * @param Application $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            CaptchaServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * This method is used to configure the Laravel application environment
     * for testing. Here, it sets the default database connection to 'testing'.
     *
     * @param Application $app
     * @return void
     */
    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}