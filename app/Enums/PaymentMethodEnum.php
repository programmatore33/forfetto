<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case CASH = 'cash';
    case CHECK = 'check';
    case PAYPAL = 'paypal';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BANK_TRANSFER => 'Bonifico Bancario',
            self::CASH => 'Contanti',
            self::CHECK => 'Assegno',
            self::PAYPAL => 'PayPal',
            self::OTHER => 'Altro',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
