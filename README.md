<a href="https://github.com/TheCoderRaman/laravel-captcha">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://banners.beyondco.de/Laravel%20Captcha.png?theme=dark&packageManager=composer+require&packageName=thecoderraman%2Flaravel-captcha&pattern=circuitBoard&style=style_1&description=Captcha+verification+for+Laravel.&md=1&showWatermark=0&fontSize=225px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg" />
        <img alt="laravel-captcha" src="https://banners.beyondco.de/Laravel%20Captcha.png?theme=light&packageManager=composer+require&packageName=thecoderraman%2Flaravel-captcha&pattern=circuitBoard&style=style_1&description=Captcha+verification+for+Laravel.&md=1&showWatermark=0&fontSize=225px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg" />
    </picture>
</a>

# Laravel CAPTCHA Package

<p align="center">
  <a title="license" href="./LICENSE"><img src="https://img.shields.io/github/license/TheCoderRaman/laravel-captcha" alt="license"></a>
  <a title="laravel" href="https://laravel.com"><img src="https://img.shields.io/badge/logo-laravel-blue?logo=laravel" alt="laravel"></a>
  <a title="packagist" href="https://packagist.org/packages/TheCoderRaman/laravel-captcha"><img src="https://img.shields.io/packagist/v/thecoderraman/laravel-captcha.svg?style=flat-square" alt="Latest Version on Packagist"></a>
  <a title="downloads" href="https://packagist.org/packages/thecoderraman/laravel-captcha"><img src="https://img.shields.io/packagist/dt/thecoderraman/laravel-captcha.svg?style=flat-square" alt="Total Downloads on Packagist"></a>
</p>

A flexible and extensible Laravel package for integrating multiple CAPTCHA services including Google reCAPTCHA, hCaptcha, and custom implementations.

## Table of Contents

- [Features](#features)
- [Major Technologies](#major-technologies)
- [Structure](#structure)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Environment Variables](#environment-variables)
- [Usage](#usage)
  - [Basic Usage with Facade](#basic-usage-with-facade)
  - [Using Dependency Injection](#using-dependency-injection)
  - [Blade Templates](#blade-templates)
- [Available Drivers](#available-drivers)
  - [1. Google reCAPTCHA v2](#1-google-recaptcha-v2)
  - [2. hCaptcha](#2-hcaptcha)
  - [3. NullCaptcha (Testing/Development)](#3-nullcaptcha-testingdevelopment)
- [Creating Custom Drivers](#creating-custom-drivers)
- [Validation](#validation)
  - [Using Laravel Validation](#using-laravel-validation)
- [Testing](#testing)
- [API Reference](#api-reference)
  - [Captcha Manager](#captcha-manager)
  - [Driver Interface](#driver-interface)
- [Configuration Options](#configuration-options)
- [Troubleshooting](#troubleshooting)
  - [Common Issues](#common-issues)
  - [Debug Mode](#debug-mode)
- [Contributing](#contributing)
- [Repository Branches](#repository-branches)
- [Contributions](#contributions)
- [Pull Requests](#pull-requests)
- [License](#license)
- [Authors](#authors)
- [Code of Conduct](#code-of-conduct)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Support](#support)

## Features

- 🔒 Multiple CAPTCHA providers (Google reCAPTCHA v2, hCaptcha)
- 🎯 Easy configuration and setup
- 🔧 Extensible architecture for custom CAPTCHA drivers
- 🧪 Testing-friendly with NullCaptcha driver
- 📦 Laravel package auto-discovery support
- 🎨 Facade support for clean syntax
- ⚡ Configurable global enable/disable functionality

## Major Technologies
- laravel

## Structure
```sh
├───config
├───src
└───tests
    ├───Feature
    └───Unit
```

### Requirements
* PHP >= 8.3

## Installation

Install the package via Composer:

```bash
composer require thecoderraman/laravel-captcha
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="TheCoderRaman\Captcha\CaptchaServiceProvider" --tag="config"
```

## Configuration

The package configuration is located at `config/captcha.php`. Here's the basic structure:

```php:config/captcha.php
<?php

use TheCoderRaman\Captcha\Enums\Captcha;
use TheCoderRaman\Captcha\Drivers\Hcaptcha;
use TheCoderRaman\Captcha\Drivers\ReCaptcha;
use TheCoderRaman\Captcha\Drivers\NullCaptcha;

return [
    /*
    |--------------------------------------------------------------------------
    | Captcha Verification Status
    |--------------------------------------------------------------------------
    |
    | Enable/disable captcha verification globally. When set to false,
    | CAPTCHA will be replaced by the NullCaptcha handler.
    |
    */
    'status' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Captcha Handler
    |--------------------------------------------------------------------------
    |
    | Specify which captcha driver to use as default.
    |
    */
    'default' => Captcha::NullCaptcha->value,

    /*
    |--------------------------------------------------------------------------
    | CAPTCHA Driver Class Mappings
    |--------------------------------------------------------------------------
    |
    | Maps enum values to their respective driver class implementations.
    |
    */
    'drivers' => [
        Captcha::Hcaptcha->value => Hcaptcha::class,
        Captcha::ReCaptcha->value => ReCaptcha::class,
        Captcha::NullCaptcha->value => NullCaptcha::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Captcha Configurations
    |--------------------------------------------------------------------------
    |
    | Specific configurations for each CAPTCHA service.
    |
    */
    'captchas' => [
        'null' => [
            'key' => null,
            'secret' => null,
            'url' => null,
        ],
        'hcaptcha' => [
            'key' => env('HCAPTCHA_SITE_KEY'),
            'secret' => env('HCAPTCHA_SECRET_KEY'),
            'url' => 'https://hcaptcha.com/siteverify',
        ],
        'recaptcha' => [
            'key' => env('RECAPTCHA_SITE_KEY'),
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'url' => 'https://www.google.com/recaptcha/api/siteverify',
        ],
    ],
];
```

## Environment Variables

Add the following environment variables to your `.env` file:

```env
# hCaptcha Configuration
HCAPTCHA_SITE_KEY=your_hcaptcha_site_key
HCAPTCHA_SECRET_KEY=your_hcaptcha_secret_key

# Google reCAPTCHA Configuration
RECAPTCHA_SITE_KEY=your_recaptcha_site_key
RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key
```

## Usage

### Basic Usage with Facade

```php
<?php

use TheCoderRaman\Captcha\Facades\Captcha;

// Get the default CAPTCHA driver
$captcha = Captcha::driver();

// Get a specific CAPTCHA driver
$hcaptcha = Captcha::driver('hcaptcha');
$recaptcha = Captcha::driver('recaptcha');

// Render CAPTCHA HTML
echo $captcha->getCaptcha();

// Render CAPTCHA scripts
echo $captcha->getScript();

// Render CAPTCHA styles
echo $captcha->getStyle();

// Verify CAPTCHA response
$isValid = $captcha->verify();
```

### Using Dependency Injection

```php
<?php

use TheCoderRaman\Captcha\Captcha;

class ContactController extends Controller
{
    protected $captcha;

    public function __construct(Captcha $captcha)
    {
        $this->captcha = $captcha;
    }

    public function showForm()
    {
        $captchaHtml = $this->captcha->getCaptcha();
        $captchaScript = $this->captcha->getScript();

        return view('contact.form', compact('captchaHtml', 'captchaScript'));
    }

    public function submitForm(Request $request)
    {
        if (!$this->captcha->verify()) {
            return back()->withErrors(['captcha' => 'CAPTCHA verification failed.']);
        }

        // Process form submission
    }
}
```

### Blade Templates

Create a Blade template for displaying CAPTCHA:

```blade
{{-- resources/views/contact/form.blade.php --}}
@extends('layouts.app')

@push('styles')
    {!! Captcha::getStyle() !!}
@endpush

@push('scripts')
    {!! Captcha::getScript() !!}
@endpush

@section('content')
    <form method="POST" action="{{ route('contact.submit') }}">
        @csrf

        <div class="form-group">
            <label for="name">{{ trans('Name') }}</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">{{ trans('Email') }}</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="message">{{ trans('Message') }}</label>
            <textarea name="message" id="message" class="form-control" required></textarea>
        </div>

        {{-- CAPTCHA Section --}}
        <div class="form-group">
            {!! Captcha::getCaptcha() !!}
            @error('captcha') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection
```

## Available Drivers

### 1. Google reCAPTCHA v2

```php
// Configure in config/captcha.php
'default' => Captcha::ReCaptcha->value,

// Use in controller
$recaptcha = Captcha::driver('recaptcha');
$isValid = $recaptcha->verify();
```

### 2. hCaptcha

```php
// Configure in config/captcha.php
'default' => Captcha::Hcaptcha->value,

// Use in controller
$hcaptcha = Captcha::driver('hcaptcha');
$isValid = $hcaptcha->verify();
```

### 3. NullCaptcha (Testing/Development)

```php
// Configure in config/captcha.php
'default' => Captcha::NullCaptcha->value,

// This driver always returns true for verification
// Useful for testing and development environments
```

## Creating Custom Drivers

You can create custom CAPTCHA drivers by extending the base `Driver` class:

```php:src/Drivers/CustomCaptcha.php
<?php

namespace App\Captcha\Drivers;

use Illuminate\Http\Request;
use TheCoderRaman\Captcha\Drivers\Driver;

class CustomCaptcha extends Driver
{
    protected string $key;
    protected string $secret;
    protected string $url;

    public function __construct(string $key = null, string $secret = null, string $url = null)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->url = $url;
    }

    public function getCaptcha(): string
    {
        return '<div class="custom-captcha" data-sitekey="' . $this->key . '"></div>';
    }

    public function getStyle(): string
    {
        return '<style>.custom-captcha { /* styles */ }</style>';
    }

    public function getScript(): string
    {
        return '<script src="https://example.com/captcha.js"></script>';
    }

    public function verify(Request $request): bool
    {
        $response = $request->input('custom-captcha-response');
        
        if (empty($response)) {
            return false;
        }

        $result = $this->client->post($this->url, [
            'secret' => $this->secret,
            'response' => $response, 'remoteip' => $request->ip(),
        ]);

        return $result->json('success', false);
    }
}
```

Register your custom driver:

```php
// In a service provider
use TheCoderRaman\Captcha\Facades\Captcha;

Captcha::extend('custom', function ($config) {
    return new CustomCaptcha(
        $config['key'], $config['secret'], $config['url']
    );
});
```

## Validation

### Using Laravel Validation

Create a custom validation rule:

```php:app/Rules/CaptchaRule.php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use TheCoderRaman\Captcha\Facades\Captcha;

class CaptchaRule implements Rule
{
    public function passes($attribute, $value)
    {
        return Captcha::verify(request());
    }

    public function message()
    {
        return 'The CAPTCHA verification failed.';
    }
}
```

Use in your controller:

```php
use App\Rules\CaptchaRule;

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'message' => 'required|string',
        'captcha' => ['required', new CaptchaRule],
    ]);

    // Process the validated data
}
```

## Testing

The package includes a `NullCaptcha` driver for testing purposes:

```php:tests/Feature/ContactFormTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_submission_with_captcha()
    {
        // Set NullCaptcha for testing
        config(['captcha.default' => 'null']);

        $response = $this->post('/contact', [
            'name' => 'John Doe', 'email' => 'john@example.com', 'message' => 'Test message',
        ]);

        $response->assertStatus(200);
    }
}
```

## API Reference

### Captcha Manager

#### Methods

- `driver(string $driver = null)`: Get a CAPTCHA driver instance
- `extend(string $driver, Closure $callback)`: Register a custom driver
- `getDefaultDriver()`: Get the default driver name

### Driver Interface

All CAPTCHA drivers implement the `DriverInterface`:

#### Methods

- `getCaptcha(): string`: Render the CAPTCHA HTML widget
- `getScript(): string`: Render required JavaScript code
- `getStyle(): string`: Render required CSS styles
- `verify(Request $request): bool`: Verify the CAPTCHA response

## Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `status` | boolean | `true` | Enable/disable CAPTCHA globally |
| `default` | string | `'null'` | Default CAPTCHA driver |
| `drivers` | array | `[...]` | Driver class mappings |
| `captchas` | array | `[...]` | Driver-specific configurations |

## Troubleshooting

### Common Issues

1. **CAPTCHA not displaying**
   - Check if the site key is correctly configured
   - Ensure the driver is properly registered
   - Verify that scripts and styles are included

2. **Verification always fails**
   - Verify the secret key is correct
   - Check network connectivity to CAPTCHA service
   - Ensure the request contains the CAPTCHA response

3. **Package not auto-discovered**
   - Manually register the service provider in `config/app.php`
   - Clear configuration cache: `php artisan config:clear`

### Debug Mode

Enable debug logging by setting the log level in your `.env`:

```env
LOG_LEVEL=debug
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## Repository Branches
- **master** -> any pull request of changes this branch
- **main** -> don´t modify, this is what is running in production
## Contributions

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.
Please make sure to update tests as appropriate.

###### Pull Requests
1. Fork the repo and create your branch:
   `#[type]/PR description`
1. Ensure to describe your pull request:
   Edit the PR title by adding a semantic prefix like `Added`, `Updated:`, `Fixed:` etc.
   **Title:**
   `#[issue] PR title -> #90 Fixed styles the button`

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Authors

* [Raman Verma](https://github.com/TheCoderRaman)

## Code of Conduct

In order to ensure that the Laravel Captcha community is welcoming to all, please review and abide by the [Code of Conduct](./CODE_OF_CONDUCT.md).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel Captcha, please send an e-mail to Raman Verma via [e-mail](mailto:devramanverma@gmail.com).
All security vulnerabilities will be promptly addressed.

## Support

For support, please open an issue on the [GitHub repository](https://github.com/thecoderraman/laravel-captcha).
