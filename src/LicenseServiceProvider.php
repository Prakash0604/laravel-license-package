<?php
namespace YourCompany\LaravelLicense;
use Illuminate\Support\ServiceProvider;
use YourCompany\LaravelLicense\Console\LicenseRefreshCommand;
use YourCompany\LaravelLicense\Console\LicenseStatusCommand;
use YourCompany\LaravelLicense\Console\LicenseActivateCommand;
use YourCompany\LaravelLicense\Http\Middleware\EnsureLicenseIsValid;
class LicenseServiceProvider extends ServiceProvider
{
    public function register():void
    { $this->mergeConfigFrom(__DIR__.'/../config/license.php','license'); $this->app->singleton(LicenseClient::class); $this->app->singleton(LicenseVerifier::class); $this->app->singleton(LicenseManager::class,fn($app)=>new LicenseManager($app->make(LicenseClient::class),$app->make(LicenseVerifier::class))); }
    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'yourcompany-license'
        );

        $this->publishes([
            __DIR__ . '/../config/license.php' => config_path('license.php'),
        ], 'license-config');

        $this->app['router']->aliasMiddleware(
            'license',
            EnsureLicenseIsValid::class
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                LicenseStatusCommand::class,
                LicenseRefreshCommand::class,
                LicenseActivateCommand::class,
            ]);
        }
    }
}
