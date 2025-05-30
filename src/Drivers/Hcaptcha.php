<?php

namespace TheCoderRaman\Captcha\Drivers;

use TheCoderRaman\Captcha\Enums\Captcha;
use TheCoderRaman\Captcha\Drivers\Driver;

/**
 * Implements the hCaptcha as a CAPTCHA driver.
 *
 * This class provides the functionality to display the hCaptcha widget on the client-side
 * and to verify user responses by communicating with the hCaptcha API.
 * It extends the base `Driver` class, leveraging its common properties for
 * HTTP client and request handling.
 */
class Hcaptcha extends Driver
{
    /**
     * The hCaptcha site key (public key).
     * This key is used to render the hCaptcha widget on the client-side.
     *
     * @var string
     */
    protected string $key;

    /**
     * The hCaptcha secret key.
     * This key is used for server-side verification of the user's response.
     *
     * @var string
     */
    protected string $secret;

    /**
     * The URL for the hCaptcha API verification endpoint.
     * Defaults to hCaptcha's official siteverify endpoint.
     *
     * @var string
     */
    protected string $url = 'https://hcaptcha.com/siteverify';

    /**
     * Constructs a new Hcaptcha driver instance.
     *
     * Initializes the driver with the necessary hCaptcha site key, secret key,
     * and an optional custom verification URL.
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

        $this->url = ($url ?? $this->url);
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
        return Captcha::Hcaptcha->value;
    }

    /**
     * Get the hCaptcha site key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set the hCaptcha site key.
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
     * Get the hCaptcha secret key.
     *
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * Set the hCaptcha secret key.
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
     * Get the URL for the hCaptcha API verification service.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the URL for the hCaptcha API verification service.
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
     * Verifies the hCaptcha challenge response.
     *
     * This method sends the user's `h-captcha-response` token,
     * along with the secret key and the user's IP address, to the hCaptcha verification API.
     *
     * @return bool
     */
    public function verify(): bool
    {
        if (!$this->request->has('h-captcha-response')) {
            return false;
        }

        $payload = [
            'secret' => $this->getSecret(),
            'remoteip' => $this->request->ip(),
            'response' => (
                $this->request->input('h-captcha-response')
            )
        ];

        $response = ($this->client->asForm()
            ->acceptJson()->post($this->getUrl(), $payload)
        );

        return (
            ($response->successful()) ? $response->json('success') : false
        );
    }

    /**
     * Retrieves the CSS style required for the hCaptcha widget.
     *
     * Provides inline CSS to ensure the hCaptcha widget's container and iframe
     * take up 100% width, improving responsiveness.
     *
     * @return string
     */
    public function getStyle(): string
    {
        return (<<<EOD
            <!-- Captcha StyleSheet -->
            <style>
                .h-captcha > div {
                    width: 100% !important;
                }
                .h-captcha iframe {
                    width: 100% !important;
                }
            </style>
        EOD);
    }

    /**
     * Retrieves the HTML markup for rendering the hCaptcha widget.
     *
     * This includes a `div` element with the `h-captcha` class and `data-sitekey` attribute,
     * which hCaptcha's JavaScript uses to render the widget.
     *
     * @return string
     */
    public function getCaptcha(): string
    {
        return (<<<EOD
            <!-- Captcha Itself -->
            <div style="display:flex;margin-left:50px;">
                <div class="h-captcha" data-sitekey="{$this->key}"></div>
            </div>
        EOD);
    }

    /**
     * Retrieves the JavaScript required for the hCaptcha widget.
     *
     * This includes the hCaptcha API script, loaded asynchronously and deferentially.
     *
     * @return string
     */
    public function getScript(): string
    {
        return (<<<EOD
            <!-- Captcha Script -->
            <script src="https://hcaptcha.com/1/api.js" async defer></script>
        EOD);
    }
}