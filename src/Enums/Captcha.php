<?php

namespace TheCoderRaman\Captcha\Enums;

/**
 * Defines the available CAPTCHA types supported by the package.
 *
 * This enum provides a clear, type-safe way to reference different
 * CAPTCHA implementations (drivers) within the application,
 * making configuration and usage more robust and readable.
 */
enum Captcha: string
{
    /**
     * Represents a "Hcaptcha" CAPTCHA.
     * 
     * Represents the hCaptcha service.
     * This driver integrates with hCaptcha for bot detection and verification.
     */
    case Hcaptcha = 'h-captcha';

    /**
     * Represents a "ReCaptcha" CAPTCHA.
     * 
     * Represents the Google reCAPTCHA v2 ("I'm not a robot" checkbox).
     * This driver integrates with Google's reCAPTCHA service for verification.
     */
    case ReCaptcha = 're-captcha';

    /**
     * Represents a "NullCaptcha" CAPTCHA.
     * 
     * This driver typically bypasses CAPTCHA verification,
     * useful for development, testing, or environments where CAPTCHA is not required.
     */
    case NullCaptcha = 'null-captcha';
}