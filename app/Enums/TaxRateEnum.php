<?php

namespace App\Enums;

enum TaxRateEnum: string
{
    case REDUCED = '5.00';
    case STANDARD = '15.00';

    public function label(): string
    {
        return match ($this) {
            self::REDUCED => '5% (Primi 5 anni)',
            self::STANDARD => '15% (Standard)',
        };
    }

    public function percentage(): float
    {
        return (float) $this->value;
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
