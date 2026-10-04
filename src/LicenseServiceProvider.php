<?php

namespace YourCompany\LaravelLicense;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use YourCompany\LaravelLicense\Console\LicenseActivateCommand;
use YourCompany\LaravelLicense\Console\LicenseRefreshCommand;
use YourCompany\LaravelLicense\Console\LicenseStatusCommand;
use YourCompany\LaravelLicense\Http\Middleware\EnsureLicenseIsValid;

class LicenseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/license.php',
            'license'
        );

        $this->app->singleton(LicenseClient::class);

        $this->app->singleton(LicenseVerifier::class);

        $this->app->singleton(
            LicenseManager::class,
            fn ($app) => new LicenseManager(
                $app->make(LicenseClient::class),
                $app->make(LicenseVerifier::class)
            )
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'yourcompany-license'
        );

        $this->publishes([
            __DIR__ . '/../config/license.php' => config_path('license.php'),
        ], 'license-config');

        /*
         * Keep the middleware alias available for applications that
         * explicitly want to use it.
         */
        $this->app['router']->aliasMiddleware(
            'license',
            EnsureLicenseIsValid::class
        );

        if (config('license.middleware.enabled', true) && config('license.middleware.auto_register', true))
        {
            $this->app->afterResolving(Router::class,function (Router $router): void {
                    $this->registerMiddlewareGroup(
                        $router,
                        'web'
                    );

                    $this->registerMiddlewareGroup(
                        $router,
                        'api'
                    );
                }
            );
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                LicenseStatusCommand::class,
                LicenseRefreshCommand::class,
                LicenseActivateCommand::class,
            ]);
        }
    }

    private function registerMiddlewareGroup(Router $router, string $group): void {
        $middleware = $router->getMiddlewareGroups()[$group] ?? [];
        if (in_array(EnsureLicenseIsValid::class, $middleware, true)) {
            return;
        }

        $router->pushMiddlewareToGroup($group, EnsureLicenseIsValid::class);
    }
}