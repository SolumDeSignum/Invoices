<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices\Traits;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Collection;

trait SettersTrait
{
    public function setTemplate(string $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function setItems(Collection $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function setNumber(?int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function setDecimals(int $decimals): static
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function setLogoHeight(?int $logoHeight): static
    {
        $this->logoHeight = $logoHeight;

        return $this;
    }

    public function setDate(?Carbon $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function setNotes(string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function setBusinessDetails(Collection $businessDetails): static
    {
        $this->businessDetails = $businessDetails;

        return $this;
    }

    public function setCustomerDetails(Collection $customerDetails): static
    {
        $this->customerDetails = $customerDetails;

        return $this;
    }

    public function setFootnote(string $footnote): static
    {
        $this->footnote = $footnote;

        return $this;
    }

    public function setTaxRates(array $taxRates): static
    {
        $this->taxRates = $taxRates;

        return $this;
    }

    public function setDueDate(?Carbon $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function setWithPagination(bool $withPagination): static
    {
        $this->withPagination = $withPagination;

        return $this;
    }

    public function setDuplicateHeader(bool $duplicateHeader): static
    {
        $this->duplicateHeader = $duplicateHeader;

        return $this;
    }

    public function setPdf(Dompdf $pdf): static
    {
        $this->pdf = $pdf;

        return $this;
    }
}
