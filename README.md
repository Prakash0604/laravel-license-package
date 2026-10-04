# Laravel License

Private Laravel package for signed, installation-bound license verification.

The package connects a Laravel application to the YourCompany License Server and automatically protects application routes when the license is inactive.

## Requirements

- PHP 8.2+
- Laravel 12
- Composer

## Installation

This package is distributed through a private Git repository.

Add the repository to your Laravel application's `composer.json`:

```json
"repositories": {
    "yourcompany-license": {
        "type": "vcs",
        "url": "git@github.com:Prakash0604/laravel-license-package.git"
    }
}
```

Then install the package:

```bash
composer require yourcompany/laravel-license:^1.1
```

For a specific release:

```bash
composer require yourcompany/laravel-license:1.1.1
```

## Configuration

Add the following values to the application's `.env`:

```env
LICENSE_SERVER=https://license.example.com
LICENSE_KEY=LIC-XXXXXXXX-XXXXXXXX-XXXXXXXX
LICENSE_DOMAIN=example.com
LICENSE_ENVIRONMENT=production

LICENSE_SIGNING_PUBLIC_KEY=base64-public-key

LICENSE_CACHE_STORE=file
LICENSE_CACHE_KEY=yourcompany.license.v2

LICENSE_GRACE_PERIOD_SECONDS=259200
LICENSE_FAIL_CLOSED=true
LICENSE_MAX_CLOCK_SKEW_SECONDS=300

LICENSE_MIDDLEWARE_ENABLED=true
LICENSE_MIDDLEWARE_AUTO_REGISTER=true
```

For local development:

```env
LICENSE_SERVER=http://127.0.0.1:8000
LICENSE_DOMAIN=localhost
LICENSE_ENVIRONMENT=local
```

Clear cached configuration:

```bash
php artisan optimize:clear
```

## Automatic Middleware

The package automatically registers the license middleware in Laravel's:

```text
web
api
```

middleware groups.

No manual middleware registration is required.

When enabled, normal application requests are automatically checked against the local license state.

```env
LICENSE_MIDDLEWARE_ENABLED=true
LICENSE_MIDDLEWARE_AUTO_REGISTER=true
```

The middleware can be disabled when required:

```env
LICENSE_MIDDLEWARE_ENABLED=false
```

## Excluded Paths

License endpoints must remain accessible so the application can activate and refresh its license.

The default excluded paths are:

```text
up
health
api/v1/license/*
```

These paths are not blocked by the license middleware.

## Activate an Application

After installing and configuring the package, activate the application:

```bash
php artisan license:activate
```

A successful activation will create an installation identity and store the license state locally.

## Check License Status

```bash
php artisan license:status
```

Example:

```text
Valid: yes
Installation: Onyn9gKITN6yv5Vm2KkIBcxVYdvLXWwZ
Status: active
Domain: example.com
Lease: 2026-10-07T05:33:39+00:00
```

## Refresh License

To manually check the license with the License Server:

```bash
php artisan license:refresh
```

Example:

```text
License refreshed.
Status: active
```

The application also performs license checks according to the configured check interval.

## Deactivate an Installation

To remove the current installation from the License Server:

```bash
php artisan license:deactivate
```

The application will remove its local license state.

## Request Behavior

When the license is valid:

```text
Application request
       ↓
License middleware
       ↓
License valid
       ↓
Application
```

When the license is inactive:

```text
Application request
       ↓
License middleware
       ↓
License invalid
       ↓
HTTP 503
```

For JSON requests, the response is:

```json
{
    "message": "Application license is inactive.",
    "code": "LICENSE_INACTIVE"
}
```

Browser requests display the package's inactive-license page.

## Manual Middleware

Automatic registration is recommended.

The package also provides the `license` middleware alias for applications that need manual control:

```php
->middleware('license')
```

Most applications do not need to use this because automatic registration is enabled by default.

## License Verification

The package verifies signed license responses using the configured public key.

The private signing key is never required by the client application.

Only the License Server should contain:

```text
LICENSE_SIGNING_PRIVATE_KEY
```

Client applications only need:

```text
LICENSE_SIGNING_PUBLIC_KEY
```

## Installation Binding

Each application installation receives a unique installation ID.

The installation is bound to:

- License key
- Installation ID
- Domain
- Environment

This prevents a license from being freely copied between installations.

## Grace Period

The package supports a configurable grace period for temporary License Server communication failures.

Example:

```env
LICENSE_GRACE_PERIOD_SECONDS=259200
```

The value is specified in seconds.

For example:

```text
259200 seconds = 3 days
```

## Fail Closed

By default:

```env
LICENSE_FAIL_CLOSED=true
```

This means the application will not continue operating indefinitely when a license cannot be verified.

## Updating the Package

Update to the latest compatible release:

```bash
composer update yourcompany/laravel-license
```

Or install a specific version:

```bash
composer require yourcompany/laravel-license:1.1.1
```

After updating:

```bash
php artisan optimize:clear
```

Check the installed version:

```bash
composer show yourcompany/laravel-license
```

## Versioning

The package follows semantic versioning.

Examples:

```text
v1.0.1  Bug fix
v1.1.0  Backward-compatible feature
v2.0.0  Breaking change
```

Production applications should use a stable tagged version rather than the development branch.

## Troubleshooting

Check the current license:

```bash
php artisan license:status
```

Refresh the license:

```bash
php artisan license:refresh
```

Clear Laravel caches:

```bash
php artisan optimize:clear
```

Check the installed package version:

```bash
composer show yourcompany/laravel-license
```

Check Laravel routes:

```bash
php artisan route:list
```

Check application logs:

```bash
tail -f storage/logs/laravel.log
```

## Security

Do not commit the following values:

```env
LICENSE_KEY
LICENSE_SIGNING_PUBLIC_KEY
```

Never place the License Server's private signing key in a client application.

The private signing key belongs only to the License Server.

## New Application Setup

For a new Laravel application:

### 1. Add the private repository

```json
"repositories": {
    "yourcompany-license": {
        "type": "vcs",
        "url": "git@github.com:Prakash0604/laravel-license-package.git"
    }
}
```

### 2. Install the package

```bash
composer require yourcompany/laravel-license:^1.1
```

### 3. Configure `.env`

```env
LICENSE_SERVER=https://license.example.com
LICENSE_KEY=LIC-XXXXXXXX-XXXXXXXX-XXXXXXXX
LICENSE_DOMAIN=example.com
LICENSE_ENVIRONMENT=production
LICENSE_SIGNING_PUBLIC_KEY=base64-public-key
```

### 4. Clear configuration

```bash
php artisan optimize:clear
```

### 5. Activate

```bash
php artisan license:activate
```

### 6. Verify

```bash
php artisan license:status
```

No middleware changes are required in `bootstrap/app.php` or individual routes.

## License Server

The package requires a compatible YourCompany License Server.

The License Server is responsible for:

- Creating licenses
- Activating installations
- Checking licenses
- Managing installation limits
- Suspending licenses
- Revoking licenses
- Signing license responses