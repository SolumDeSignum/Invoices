<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices\Stubs;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use SolumDeSignum\Invoices\Invoices;

class Sample
{
    public function run(): void
    {
        (new Invoices())
            ->addItem('Test Item', 10.25, 2, "1412")
            ->addItem('Test Item 2', 5, 2, "923")
            ->addItem('Test Item 3', 15.55, 5, "42")
            ->addItem('Test Item 4', 1.25, 1, "923")
            ->addItem('Test Item 5', 3.12, 1, "3142")
            ->addItem('Test Item 6', 6.41, 3, "452")
            ->addItem('Test Item 7', 2.86, 1, "1526")
            ->addItem('Test Item 8', 5, 2, "923", 'https://dummyimage.com/64x64/000/fff')
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
    }
}
