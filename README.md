[![Total Downloads](https://poser.pugx.org/solumdesignum/invoices/downloads)](https://packagist.org/packages/solumdesignum/invoices)
[![Latest Stable Version](https://poser.pugx.org/solumdesignum/invoices/v/stable)](https://packagist.org/packages/solumdesignum/invoices)
[![Latest Unstable Version](https://poser.pugx.org/solumdesignum/invoices/v/unstable)](https://packagist.org/packages/solumdesignum/invoices)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Introduction
Invoices is a Laravel library that generates a PDF invoice for your customers.
The PDF can be either downloaded or streamed in the browser. 
It's highly customizable, and you can modify the whole output view as well.

## Installation
To get started, install using the composer package manager:

```shell
composer require solumdesignum/invoices
```

Next, publish resources using the vendor:publish command:

```shell
php artisan vendor:publish --provider="SolumDeSignum\Invoices\InvoicesServiceProvider"
```
This command will publish a config to your config directory, which will be created if it does not exist.

## Invoices Features
The configuration file contains configurations.

```php
<?php

declare(strict_types=1);

return [
    'templates' => [
        'default',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | This value is the default currency that is going to be used in invoices.
    | You can change it on each invoice individually.
    */

    'currency' => 'EUR',

    /*
    |--------------------------------------------------------------------------
    | Default Decimal Precision
    |--------------------------------------------------------------------------
    |
    | This value is the default decimal precision that is going to be used
    | to perform all the calculations.
    */

    'decimals' => 2,


    /*
    |--------------------------------------------------------------------------
    | Default Invoice Logo
    |--------------------------------------------------------------------------
    |
    | This value is the default invoice logo that is going to be used in invoices.
    | You can change it on each invoice individually.
    */

    'logo' => 'http://i.imgur.com/t9G3rFM.png',

    /*
    |--------------------------------------------------------------------------
    | Default Invoice Logo Height
    |--------------------------------------------------------------------------
    |
    | This value is the default invoice logo height that is going to be used in invoices.
    | You can change it on each invoice individually.
    */

    'logo_height' => 60,

    /*
    |--------------------------------------------------------------------------
    | Default Invoice Buissness Details
    |--------------------------------------------------------------------------
    |
    | This value is going to be the default attribute displayed in
    | the customer model.
    */

    'business_details' => [
        'name' => env('APP_NAME', 'My Company'),
        'id' => '1234567890',
        'phone' => '+34 123 456 789',
        'location' => 'Main Street 1st',
        'zip' => '08241',
        'city' => 'Barcelona',
        'country' => 'Spain',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Invoice Footnote
    |--------------------------------------------------------------------------
    |
    | This value is going to be at the end of the document, sometimes telling you
    | some copyright message or simple legal terms.
    */

    'footnote' => '',

    /*
    |--------------------------------------------------------------------------
    | Default Tax Rates
    |--------------------------------------------------------------------------
    |
    | This array group multiple tax rates.
    |
    | The tax type accepted values are: 'percentage' and 'fixed'.
    | The percentage type calculates the tax depending on the invoice price, and
    | the fixed type simply adds a fixed amount to the total price.
    | You can't mix percentage and fixed tax rates.
    */
    'tax_rates' => [
        [
            'name' => '',
            'tax' => 0,
            'tax_type' => 'percentage',
        ],
    ],

    /*
    | Default Invoice Due Date
    |--------------------------------------------------------------------------
    |
    | This value is the default due date that is going to be used in invoices.
    | You can change it on each invoice individually.
    | You can set it null to remove the due date on all invoices.
    */
    'due_date' => date('M dS ,Y', strtotime('+3 months')),

    /*
    | Default pagination parameter
    |--------------------------------------------------------------------------
    |
    | This value is the default pagination parameter.
    | If true and page count are higher than 1, pagination will show at the bottom.
    */
    'with_pagination' => true,

    /*
    | Duplicate header parameter
    |--------------------------------------------------------------------------
    |
    | This value is the default header parameter.
    | If true header will be duplicated on each page.
    */
    'duplicate_header' => false,

];
````

## Sample Invoice
This is a sample invoice generated using this library:

![Sample Invoice](https://i.gyazo.com/768f5b59791162e432f9cdfa15f017bc.png "Sample Invoice Image")

```php
$invoice = (new \SolumDeSignum\Invoices\Invoices())
                ->addItem('Test Item', 10.25, 2, "1412")
                ->addItem('Test Item 2', 5, 2, "923")
                ->addItem('Test Item 3', 15.55, 5, "42")
                ->addItem('Test Item 4', 1.25, 1, "923")
                ->addItem('Test Item 5', 3.12, 1, "3142")
                ->addItem('Test Item 6', 6.41, 3, "452")
                ->addItem('Test Item 7', 2.86, 1, "1526")
                ->addItem('Test Item 8', 5, 2, 923, 'https://dummyimage.com/64x64/000/fff')
                ->setNumber(4021)
                ->setWithPagination(true)
                ->setDuplicateHeader(true)
                ->setDueDate(Carbon::now()->addMonths(1))
                ->setNotes('Lrem ipsum dolor sit amet, consectetur adipiscing elit.')
                ->setCustomerDetails(
                    new Collection([
                        'name' => 'Oskars Germovs',
                        'id' => '12345678A',
                        'phone' => '+371 000 000 00',
                        'location' => 'C / Unknown Street 1st',
                        'zip' => '08241',
                        'city' => 'Manresa',
                        'country' => 'Spain',
                    ])
                )
                ->setBusinessDetails(
                    new Collection([
                        'id' => '123456789',
                        'name' => 'Solum DeSignum',
                        'phone' => '+371 000 000 00',
                        'location' => 'C / Unknown Street 1st',
                        'zip' => 'LV-1046',
                        'city' => 'Riga',
                        'country' => 'Latvia',
                    ])
                )
                ->download('demo')
                ->save('public/myinvoicename.pdf');
```

### Author
- [Oskars Germovs](http://solum-designum.eu)

## License
Solum DeSignum Invoices is open-sourced software licensed under the [MIT license](LICENSE.md).

## Idea
This package is based on a package consoletvs/invoices.
