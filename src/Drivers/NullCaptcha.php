<?php

namespace TheCoderRaman\Captcha\Drivers;

use Illuminate\Support\Facades\Config;
use TheCoderRaman\Captcha\Enums\Captcha;
use TheCoderRaman\Captcha\Drivers\Driver;

/**
 * A "Null" CAPTCHA driver implementation for testing or development environments.
 *
 * This class extends the base `Driver` and provides a dummy CAPTCHA implementation.
 * It always returns `true` for verification and placeholder comments for
 * CAPTCHA styles, scripts, and HTML.
 *
 * This driver is useful for:
 * - Local development where actual CAPTCHA verification is not desired.
 * - Testing environments to bypass CAPTCHA challenges during automated tests.
 * - Providing a fallback or default CAPTCHA behavior without external dependencies.
 */
class NullCaptcha extends Driver
{
    /**
     * The public key (or site key) for the CAPTCHA.
     * In this Null implementation, it might not be actively used for verification
     * but is kept for interface consistency.
     *
     * @var string
     */
    protected string $key;

    /**
     * The secret key for the CAPTCHA.
     * In this Null implementation, it might not be actively used for verification
     * but is kept for interface consistency.
     *
     * @var string
     */
    protected string $secret;

    /**
     * The URL for the CAPTCHA service.
     * In this Null implementation, it defaults to an empty string as there's no external service.
     *
     * @var string
     */
    protected string $url = '';

    /**
     * Constructs a new NullCaptcha instance.
     *
     * While this Null driver doesn't require actual keys or a URL for its functionality,
     * the constructor signature matches other drivers for consistency.
     * The `$url` parameter is optional and defaults to an empty string if not provided.
     *
     * @param string $key
     * @param string $secret
     * @param string|null $url
     * @return void
     */
    public function __construct(
        string $key,
        string $secret,
        string $url = null
    ) {
        $this->key = $key;
        $this->secret = $secret;

        $this->url = (
            empty($url) ? '' : $url
        );
    }

    /**
     * Returns the unique identifier for the CAPTCHA driver.
     *
     * This method should return a string that uniquely identifies the CAPTCHA
     * type (e.g., 'recaptcha', 'hcaptcha', 'null'). This is often used for
     * configuration or factory pattern implementations.
     *
     * @return string
     */
    public function driver(): string
    {
        return Captcha::NullCaptcha->value;
    }

    /**
     * Get the public key (site key) for the CAPTCHA.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set the public key (site key) for the CAPTCHA.
     *
     * @param string $key
     * @return $this
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the secret key for the CAPTCHA.
     *
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * Set the secret key for the CAPTCHA.
     *
     * @param string $secret
     * @return $this
     */
    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get the URL for the CAPTCHA service.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the URL for the CAPTCHA service.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Verifies the CAPTCHA challenge.
     *
     * In this Null implementation, verification by default succeeds.
     *
     * @return bool
     */
    public function verify(): bool
    {
        return Config::get(
            'captcha.status', true
        );
    }

    /**
     * Retrieves the CSS style for the CAPTCHA.
     *
     * In this Null implementation, it returns a placeholder HTML comment.
     *
     * @return string
     */
    public function getStyle(): string
    {
        return '<!-- Captcha StyleSheet -->';
    }

    /**
     * Retrieves the HTML markup for the CAPTCHA.
     *
     * In this Null implementation, it returns a placeholder HTML comment.
     *
     * @return string
     */
    public function getCaptcha(): string
    {
        return '<!-- Captcha Itself -->';
    }

    /**
     * Retrieves the JavaScript for the CAPTCHA.
     *
     * In this Null implementation, it returns a placeholder HTML comment.
     *
     * @return string
     */
    public function getScript(): string
    {
        return '<!-- Captcha Script -->';
    }
}