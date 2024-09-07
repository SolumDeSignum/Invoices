<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices\Traits;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Collection;

trait GettersTrait
{
    public function getName(): string
    {
        return $this->name;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getLogoHeight(): ?int
    {
        return $this->logoHeight;
    }

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function getBusinessDetails(): Collection
    {
        return $this->businessDetails;
    }

    public function getCustomerDetails(): Collection
    {
        return $this->customerDetails;
    }

    public function getFootnote(): string
    {
        return $this->footnote;
    }

    public function getTaxRates(): array
    {
        return $this->taxRates;
    }

    public function getDueDate(): ?Carbon
    {
        return $this->dueDate;
    }

    public function isWithPagination(): bool
    {
        return $this->withPagination;
    }

    public function isDuplicateHeader(): bool
    {
        return $this->duplicateHeader;
    }

    public function getPdf(): Dompdf
    {
        return $this->pdf;
    }
}
