<?php

declare(strict_types=1);

namespace SolumDeSignum\Invoices\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;

trait Setters
{
    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function number(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function decimals(int $decimals): static
    {
        $this->decimals = $decimals;
        return $this;
    }

    public function logo(string $logo_url): static
    {
        $this->logo = $logo_url;
        return $this;
    }

    public function date(Carbon $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function notes(string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function business(array $details): static
    {
        $this->businessDetails = Collection::make($details);

        return $this;
    }

    public function customer(array $details): static
    {
        $this->customerDetails = Collection::make($details);

        return $this;
    }

    public function currency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function footnote(string $footnote): static
    {
        $this->footnote = $footnote;

        return $this;
    }

    public function dueDate(?Carbon $due_date = null): static
    {
        $this->dueDate = $due_date;

        return $this;
    }

    public function withPagination(bool $with_pagination): static
    {
        $this->withPagination = $with_pagination;

        return $this;
    }

    public function duplicateHeader(bool $duplicate_header): static
    {
        $this->duplicateHeader = $duplicate_header;

        return $this;
    }
}
