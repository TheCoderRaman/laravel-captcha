<?php

namespace TheCoderRaman\Captcha\Contracts;

interface DriverInterface
{
    /**
     * Returns the unique identifier for the CAPTCHA driver.
     *
     * This method should return a string that uniquely identifies the CAPTCHA
     * type (e.g., 'recaptcha', 'hcaptcha', 'null'). This is often used for
     * configuration or factory pattern implementations.
     *
     * @return string
     */
    public function driver(): string;

    /**
     * Verifies the CAPTCHA challenge.
     *
     * This method is responsible for checking if the user's CAPTCHA input
     * or interaction is valid according to the CAPTCHA driver's logic.
     *
     * @return bool
     */
    public function verify(): bool;

    /**
     * Retrieves the CSS style (or a reference to it) required for rendering the CAPTCHA.
     *
     * This method should return any necessary `<style>` tags or links to CSS files
     * that are specific to the CAPTCHA driver's visual presentation.
     *
     * @return string
     */
    public function getStyle(): string;

    /**
     * Retrieves the JavaScript (or a reference to it) required for the CAPTCHA functionality.
     *
     * This method should return any necessary `<script>` tags or links to JavaScript files
     * that handle the interactive behavior, verification, or rendering of the CAPTCHA.
     *
     * @return string
     */
    public function getScript(): string;

    /**
     * Retrieves the HTML markup for rendering the CAPTCHA challenge.
     *
     * This method should return the necessary HTML elements (e.g., `<div>`, `<img>`, `<iframe>`)
     * that display the CAPTCHA to the user.
     *
     * @return string
     */
    public function getCaptcha(): string;
}