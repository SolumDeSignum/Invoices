<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices;

use Illuminate\Support\ServiceProvider;

class InvoicesServiceProvider extends ServiceProvider
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
        $this->app->singleton(
            'package-translator-loader',
            function ($app) {
                return new Invoices($app);
            }
        );
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
        ], 'invoices');
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
}
