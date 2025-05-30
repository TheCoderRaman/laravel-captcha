<?php

namespace TheCoderRaman\Captcha\Drivers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Factory;
use TheCoderRaman\Captcha\Contracts\DriverInterface;

/**
 * Abstract base class for CAPTCHA drivers.
 *
 * This class provides common properties and methods that all CAPTCHA drivers
 * extending it can utilize. It ensures that each driver has access to
 * an HTTP client for making external requests (e.g., to CAPTCHA APIs)
 * and the current HTTP request instance.
 *
 * It also implements the `DriverInterface`, ensuring all concrete drivers
 * adhere to the defined contract for CAPTCHA functionality.
 */
abstract class Driver implements DriverInterface
{
    /**
     * The HTTP client instance for making API requests.
     *
     * @var Factory
     */
    protected Factory $client;

    /**
     * The current HTTP request instance.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * Get the HTTP client instance.
     *
     * @return Factory
     */
    public function getClient(): Factory
    {
        return $this->client;
    }

    /**
     * Set the HTTP client instance.
     *
     * @param  Factory  $client
     * @return $this
     */
    public function setClient(Factory $client): self
    {
        $this->client = $client;

        return $this;
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
}