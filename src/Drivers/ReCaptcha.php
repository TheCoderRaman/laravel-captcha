<?php

namespace TheCoderRaman\Captcha\Drivers;

use TheCoderRaman\Captcha\Enums\Captcha;
use TheCoderRaman\Captcha\Drivers\Driver;

/**
 * Implements the Google reCAPTCHA v2 ("I'm not a robot" checkbox) as a CAPTCHA driver.
 *
 * This class handles the rendering of the reCAPTCHA widget and the verification
 * of user responses by communicating with the Google reCAPTCHA API.
 * It extends the base `Driver` class to utilize shared HTTP client and request functionalities.
 */
class ReCaptcha extends Driver
{
    /**
     * The Google reCAPTCHA site key (public key).
     * This key is used to render the reCAPTCHA widget on the client-side.
     *
     * @var string
     */
    protected string $key;

    /**
     * The Google reCAPTCHA secret key.
     * This key is used for server-side verification of the user's response.
     *
     * @var string
     */
    protected string $secret;

    /**
     * The URL for the Google reCAPTCHA API verification endpoint.
     * Defaults to Google's official siteverify endpoint.
     *
     * @var string
     */
    protected string $url = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Constructs a new ReCaptcha driver instance.
     *
     * Initializes the driver with the necessary reCAPTCHA site key, secret key,
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
        return Captcha::ReCaptcha->value;
    }

    /**
     * Get the Google reCAPTCHA site key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set the Google reCAPTCHA site key.
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
     * Get the Google reCAPTCHA secret key.
     *
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * Set the Google reCAPTCHA secret key.
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
     * Get the URL for the Google reCAPTCHA API verification service.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the URL for the Google reCAPTCHA API verification service.
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
     * Verifies the reCAPTCHA challenge response.
     *
     * This method sends the user's `g-recaptcha-response` token,
     * along with the secret key and the user's IP address, to Google's
     * reCAPTCHA verification API.
     *
     * @return bool
     */
    public function verify(): bool
    {
        if (!$this->request->has('g-recaptcha-response')) {
            return false;
        }

        $payload = [
            'secret' => $this->getSecret(),
            'remoteip' => $this->request->ip(),
            'response' => (
                $this->request
                ->input('g-recaptcha-response')
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
     * Retrieves the CSS style required for the reCAPTCHA widget.
     *
     * Provides inline CSS to ensure the reCAPTCHA widget's container and iframe
     * take up 100% width, improving responsiveness.
     *
     * @return string
     */
    public function getStyle(): string
    {
        return (<<<EOD
            <!-- Captcha StyleSheet -->
            <style>
                .g-recaptcha > div {
                    width: 100% !important;
                }
                .g-recaptcha iframe {
                    width: 100% !important;
                }
            </style>
        EOD);
    }

    /**
     * Retrieves the HTML markup for rendering the reCAPTCHA widget.
     *
     * This includes a `div` element with the `g-recaptcha` class and `data-sitekey` attribute,
     * which Google's reCAPTCHA JavaScript uses to render the widget.
     *
     * @return string
     */
    public function getCaptcha(): string
    {
        return (<<<EOD
            <!-- Captcha Itself -->
            <div style="display:flex;margin-left:50px;">
                <div class="g-recaptcha" data-sitekey="{$this->key}"></div>
            </div>
        EOD);
    }

    /**
     * Retrieves the JavaScript required for the reCAPTCHA widget.
     *
     * This includes the Google reCAPTCHA API script, loaded asynchronously and deferentially.
     *
     * @return string
     */
    public function getScript(): string
    {
        return (<<<EOD
            <!-- Captcha Script -->
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        EOD);
    }
}