<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices;

use SolumDeSignum\Invoices\Traits\GettersTrait;
use SolumDeSignum\Invoices\Traits\InvoiceTrait;
use SolumDeSignum\Invoices\Traits\SettersTrait;

class Invoices
{
    use GettersTrait;
    use SettersTrait;
    use InvoiceTrait;
}
