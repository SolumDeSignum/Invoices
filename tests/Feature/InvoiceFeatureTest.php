<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use SolumDeSignum\Invoices\Invoice;
use SolumDeSignum\Invoices\InvoicesServiceProvider;
use stdClass;
use Tests\TestCase;

class InvoiceFeatureTest extends TestCase
{
    /** @test */
    public function it_creates_an_invoice_with_default_values()
    {
        $invoice = new Invoice();

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
    public function it_adds_an_item_to_the_invoice()
    {
        $invoice = new Invoice();
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
    public function it_calculates_subtotal_price()
    {
        $invoice = new Invoice();
        $invoice->addItem('Item 1', 100.00, 2);
        $invoice->addItem('Item 2', 50.00, 1);

        $this->assertEquals('250.00', $invoice->subTotalPriceFormatted());
    }

    /** @test */
    public function it_calculates_total_price_with_tax()
    {
        $invoice = new Invoice();
        $invoice->addItem('Item 1', 100.00, 2);
        $invoice->addItem('Item 2', 50.00, 1);
        $invoice->taxRates = [
            ['tax_type' => 'percentage', 'tax' => 10]  // 10% tax
        ];

        $this->assertEquals('275.00', $invoice->totalPriceFormatted());
    }

    /** @test */
    public function it_formats_currency()
    {
        $mockCurrencyData = (object)[
            'USD' => (object)['symbol' => '$', 'name' => 'US Dollar']
        ];

        // Mocking file_get_contents and json_decode
        $this->mockFileReadAndJsonDecode($mockCurrencyData);

        $invoice = new Invoice('USD');
        $currency = $invoice->formatCurrency();

        $this->assertEquals('$', $currency->symbol);
        $this->assertEquals('US Dollar', $currency->name);
    }

    /** @test */
    public function it_handles_invalid_templates()
    {
        $this->expectException(\InvalidArgumentException::class);

        $invoice = new Invoice();
        $invoice->template = 'invalid_template';
        $invoice->generate();
    }

    /** @test */
    public function it_downloads_invoice_pdf()
    {
        $invoice = new Invoice();
        $invoice->generate();

        $response = $invoice->download('invoice');

        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function it_saves_invoice_pdf()
    {
        $invoice = new Invoice();
        $invoice->generate();

        // Mock Storage facade
        Storage::fake('local');

        $invoice->save('invoice.pdf');

        Storage::disk('local')->assertExists('invoice.pdf');
    }

    /** @test */
    public function it_shows_invoice_pdf_in_browser()
    {
        $invoice = new Invoice();
        $invoice->generate();

        $response = $invoice->show('invoice');

        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function it_checks_if_image_column_should_display()
    {
        $invoice = new Invoice();
        $invoice->addItem('Item 1', 100.00, 2, null, 'http://example.com/image.jpg');

        $this->assertTrue($invoice->shouldDisplayImageColumn());

        $invoice->popItem();

        $this->assertFalse($invoice->shouldDisplayImageColumn());
    }

    /**
     * Mock file_get_contents and json_decode for testing currency formatting.
     *
     * @param stdClass $mockData
     */
    private function mockFileReadAndJsonDecode(stdClass $mockData): void
    {
        $this->mock('file_get_contents', function () use ($mockData) {
            return json_encode($mockData);
        });

        $this->mock('json_decode', function ($json) use ($mockData) {
            return $mockData;
        });
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
