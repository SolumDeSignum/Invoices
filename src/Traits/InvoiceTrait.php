<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices\Traits;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use stdClass;

trait InvoiceTrait
{
    public function __construct(
        public readonly string $name = 'Invoice',
        public string $template = 'default',
        public Collection $items = new Collection([]),
        public string $currency = '',
        public ?int $number = null,
        public int $decimals = 2,
        public string $logo = '',
        public ?int $logoHeight = null,
        public ?Carbon $date = null,
        public string $notes = '',
        public Collection $businessDetails = new Collection([]),
        public Collection $customerDetails = new Collection([]),
        public string $footnote = '',
        public array $taxRates = [],
        public ?Carbon $dueDate = null,
        public bool $withPagination = false,
        public bool $duplicateHeader = false,
        public Dompdf $pdf = new Dompdf()
    ) {
        $this->currency = $currency ?: config('invoices.currency');
        $this->decimals = $decimals ?: (int)config('invoices.decimals');
        $this->logo = $logo ?: config('invoices.logo');
        $this->logoHeight = $logoHeight ?: (int)config('invoices.logo_height');
        $this->date = $date ?: Carbon::now();
        $this->businessDetails = $businessDetails ?: (array)config('invoices.business_details');
        $this->footnote = $footnote ?: config('invoices.footnote');
        $this->taxRates = $taxRates ?: (array)config('invoices.tax_rates');
        $this->dueDate = $dueDate ?: (config('invoices.due_date')
            ? Carbon::parse(config('invoices.due_date')) : Carbon::parse($dueDate)
        );
        $this->withPagination = $withPagination ?: (bool)config('invoices.with_pagination');
        $this->duplicateHeader = $duplicateHeader ?: (bool)config('invoices.duplicate_header');
    }

    public function template(string $template = 'default'): self
    {
        $this->template = $template;

        return $this;
    }

    public function addItem(
        string $name,
        float $price,
        int $amount = 1,
        ?string $id = '-',
        ?string $imageUrl = null
    ): self {
        $this->items->push(new Collection([
            'name' => $name,
            'price' => $price,
            'amount' => $amount,
            'totalPrice' => number_format(
                (float)bcmul((string)$price, (string)$amount, $this->decimals),
                $this->decimals
            ),
            'id' => $id,
            'imageUrl' => $imageUrl,
        ]));

        return $this;
    }

    public function popItem(): self
    {
        $this->items->pop();

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function formatCurrency(): stdClass
    {
        $currencies = json_decode(
            file_get_contents(__DIR__ . '/../../storage/currencies.json'),
            false,
            512,
            JSON_THROW_ON_ERROR
        );

        return $currencies->{$this->currency};
    }

    public function subTotalPrice(): float
    {
        return (float)$this->items->sum(function ($item) {
            return bcmul((string)$item['price'], (string)$item['amount'], $this->decimals);
        });
    }

    public function subTotalPriceFormatted(): string
    {
        return number_format($this->subTotalPrice(), $this->decimals);
    }

    public function totalPrice(): float
    {
        return (float)bcadd(
            (string)$this->subTotalPrice(),
            (string)$this->taxPrice(),
            $this->decimals
        );
    }

    public function totalPriceFormatted(): string
    {
        return number_format($this->totalPrice(), $this->decimals);
    }

    public function taxPrice(?object $taxRate = null): float
    {
        $taxTotal = '0.0';

        if (is_null($taxRate)) {
            foreach ($this->taxRates as $tax) {
                if ($tax['tax_type'] === 'percentage') {
                    $percentageTax = bcdiv(
                        bcmul((string)$tax['tax'], (string)$this->subTotalPrice(), $this->decimals),
                        '100',
                        $this->decimals
                    );
                    $taxTotal = bcadd($taxTotal, $percentageTax, $this->decimals);
                } else {
                    $taxTotal = bcadd($taxTotal, (string)$tax['tax'], $this->decimals);
                }
            }
        } else {
            if ($taxRate->tax_type === 'percentage') {
                $taxTotal = bcdiv(
                    bcmul((string)$taxRate->tax, (string)$this->subTotalPrice(), $this->decimals),
                    '100',
                    $this->decimals
                );
            } else {
                $taxTotal = (string)$taxRate->tax;
            }
        }

        return (float)$taxTotal;
    }

    public function taxPriceFormatted(?object $taxRate): string
    {
        return number_format($this->taxPrice($taxRate), $this->decimals);
    }

    public function generate(): self
    {
        $template = strtolower($this->template);
        $availableTemplates = config('invoices.templates');

        if (!in_array($template, $availableTemplates, true)) {
            throw new InvalidArgumentException('Invalid template specified.');
        }

        $options = new Options();
        $options->set('isRemoteEnabled', false); // Disable unless remote content is needed
        $options->set('isPhpEnabled', false);   // Disable PHP execution in templates for security

        $pdf = new Dompdf($options);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false, // Ensure no self-signed certificates
            ],
        ]);
        $pdf->setHttpContext($context);

        $htmlContent = view("solumdesignum/invoices::$template", [
            'invoice' => $this,
            'with_pagination' => $this->withPagination
        ])
            ->render();

        $pdf->loadHtml($htmlContent);

        try {
            $pdf->render();
        } catch (Exception $e) {
            throw new RuntimeException('Failed to generate PDF: ' . $e->getMessage());
        }

        $this->pdf = $pdf;

        return $this;
    }

    public function download(string $name = 'invoice'): mixed
    {
        $this->generate();
        return $this->pdf->stream($name);
    }

    public function save(string $name = 'invoice.pdf'): void
    {
        $invoice = $this->generate();
        Storage::put($name, $invoice->pdf->output());
    }

    public function show(string $name = 'invoice'): void
    {
        $this->generate();
        $this->pdf->stream($name, ['Attachment' => false]);
    }

    public function hasImage(): bool
    {
        foreach ($this->items as $item) {
            if (!is_null($item['imageUrl'])) {
                return true;
            }
        }

        return false;
    }
}
