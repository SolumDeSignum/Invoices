<?php

namespace SolumDeSignum\Invoices;

use Illuminate\Support\ServiceProvider;

class InvoicesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'invoices');

        $this->publishes([
            __DIR__ . '/resources/views/' => resource_path('views/vendor/invoices'),
            __DIR__ . '/config/invoices.php' => config_path('invoices.php'),
        ], 'invoices');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/invoices.php',
            'invoices'
        );
    }
}
