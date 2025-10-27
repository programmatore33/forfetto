<?php

namespace App\Enums;

enum ExpenseCategoryEnum: string
{
    case SOFTWARE = 'Software';
    case HARDWARE = 'Hardware';
    case TRANSPORT = 'Trasporti';
    case TRAINING = 'Formazione';
    case OFFICE_SUPPLIES = 'Materiale Ufficio';
    case MARKETING = 'Marketing';
    case CONSULTATION = 'Consulenze';
    case UTILITIES = 'Utenze';
    case RENT = 'Affitto';
    case INSURANCE = 'Assicurazioni';
    case TAXES = 'Tasse e Tributi';
    case OTHER = 'Altro';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->value])
            ->toArray();
    }
}