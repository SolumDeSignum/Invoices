<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class InvoicesServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'solumdesignum/invoices'
        );

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/invoices.php',
            'invoices'
        );

        // Register the service the package provides.
        $this->app->singleton('invoices', function ($app) {
            return new Invoices($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['invoices'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/views/' => resource_path('views/vendor/invoices'),
            __DIR__ . '/../config/invoices.php' => config_path('invoices.php'),
        ], [
            'invoices.views',
            'invoices.config'
        ]);
    }
}
