<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices\Tests\Feature;

use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use JsonException;
use SolumDeSignum\Invoices\Invoices;
use SolumDeSignum\Invoices\InvoicesServiceProvider;
use Tests\TestCase;

class InvoiceFeatureTest extends TestCase
{
    /** @test */
    public function itCreatesAnInvoiceWithDefaultValues()
    {
        $invoice = new Invoices();

        $this->assertEquals('Invoice', $invoice->name);
        $this->assertEquals('default', $invoice->template);
        $this->assertInstanceOf(Collection::class, $invoice->items);
        $this->assertEquals(config('invoices.currency'), $invoice->currency);
        $this->assertEquals(config('invoices.decimals'), $invoice->decimals);
        $this->assertEquals(config('invoices.logo'), $invoice->logo);
        $this->assertEquals(config('invoices.logo_height'), $invoice->logoHeight);
        $this->assertEquals(Carbon::now()->toDateString(), $invoice->date->toDateString());
        $this->assertEquals(config('invoices.footnote'), $invoice->footnote);
        $this->assertEquals(config('invoices.tax_rates'), $invoice->taxRates);
        $this->assertEquals(
            config('invoices.due_date')
                ? Carbon::parse(config('invoices.due_date'))->toDateString() : null,
            $invoice->dueDate?->toDateString()
        );
        $this->assertEquals(config('invoices.with_pagination'), $invoice->withPagination);
        $this->assertEquals(config('invoices.duplicate_header'), $invoice->duplicateHeader);
    }

    /** @test */
    public function itAddsAnItemToTheInvoice()
    {
        $invoice = new Invoices();
        $invoice->addItem('Item 1', 100.00, 2, '001', 'http://example.com/image.jpg');
        $items = $invoice->items;

        $this->assertCount(1, $items);
        $this->assertEquals('Item 1', $items->first()['name']);
        $this->assertEquals('100.00', $items->first()['price']);
        $this->assertEquals(2, $items->first()['amount']);
        $this->assertEquals('200.00', $items->first()['totalPrice']);
        $this->assertEquals('001', $items->first()['id']);
        $this->assertEquals('http://example.com/image.jpg', $items->first()['imageUrl']);
    }

    /** @test */
    public function itCalculatesSubtotalPrice()
    {
        $invoice = new Invoices();
        $invoice->addItem('Item 1', 100.00, 2);
        $invoice->addItem('Item 2', 50.00, 1);

        $this->assertEquals('250.00', $invoice->subTotalPriceFormatted());
    }

    /** @test */
    public function itCalculatesTotalPriceWithTax()
    {
        $invoice = new Invoices();
        $invoice->addItem('Item 1', 100.00, 2);
        $invoice->addItem('Item 2', 50.00, 1);
        $invoice->taxRates = [
            [
                'tax_type' => 'percentage',
                'tax' => 10
            ]
        ];

        $this->assertEquals('275.00', $invoice->totalPriceFormatted());
    }

    /** @test
     * @throws JsonException
     */
    public function itFormatsCurrency()
    {
        $invoice = new Invoices();
        $currency = $invoice->formatCurrency();

        $this->assertEquals('€', $currency->symbol);
        $this->assertEquals('Euro', $currency->name);
    }

    /** @test */
    public function itHandlesInvalidTemplates()
    {
        $this->expectException(\InvalidArgumentException::class);

        $invoice = new Invoices();
        $invoice->template = 'invalid_template';
        $invoice->generate();
    }

    /** @test */
    public function pdfGeneration()
    {
        $invoice = new Invoices();
        $invoice->addItem('Item 1', 100.00, 2, '001', 'http://example.com/image.jpg');
        $generatedPdf = $invoice->generate()->pdf;

        $this->assertNotNull($generatedPdf, 'PDF should not be null.');
        $this->assertTrue($generatedPdf->getCanvas() !== null, 'Canvas should exist, meaning the PDF was rendered.');
        $this->assertNotEmpty($generatedPdf->output(), 'Generated PDF content should not be empty.');
    }

    /** @test */
    public function itSavesInvoicePdf()
    {
        Storage::fake('local');

        $invoice = new Invoices();
        $invoice->addItem('Item 1', 100.00, 2, '001', 'http://example.com/image.jpg');
        $invoice->save('invoice.pdf');

        Storage::disk('local')->assertExists('invoice.pdf');
    }


    /** @test */
    public function itChecksIfImageColumnShouldDisplay()
    {
        $invoice = new Invoices();
        $invoice->addItem('Item 1', 100.00, 2, null, 'http://example.com/image.jpg');
        $item = $invoice->items->first();

        $this->assertIsString($item->get('imageUrl'));
    }

    /** @test */
    public function itChecksIfImageColumnIsEmpty()
    {
        $invoice = new Invoices();
        $invoice->addItem('Item 1', 100.00, 2, null);
        $item = $invoice->items->first();

        $this->assertNull($item->get('imageUrl'));
    }

    /**
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            InvoicesServiceProvider::class,
        ];
    }
}
