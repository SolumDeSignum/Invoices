<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices\Facades;

use Illuminate\Support\Facades\Facade;

class Invoices extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'invoices';
    }
}
