<?php

namespace App\Dtos\Input;

use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

/**
 * DTO for expense input data with validation rules
 */
class InputExpenseDto extends Data
{
    /**
     * Convert empty strings to null before validation
     */
    public static function prepareForValidation(array $properties): array
    {
        // List of fields that should convert empty strings to null
        $nullableFields = [
            'supplier',
            'notes',
        ];

        foreach ($nullableFields as $field) {
            if (isset($properties[$field]) && $properties[$field] === '') {
                $properties[$field] = null;
            }
        }

        return $properties;
    }

    public function __construct(
        #[Required, Date]
        public string $expense_date,

        #[Sometimes, Nullable, Exists('expense_categories', 'id')]
        public ?int $expense_category_id = null,

        #[Required, Max(500)]
        public string $description = '',

        #[Sometimes, Nullable, Max(255)]
        public ?string $supplier = null,

        #[Required, Min(0.01)]
        public float $amount = 0,

        #[Sometimes, Nullable]
        public ?string $notes = null,
    ) {}

    /**
     * Convert to array for model creation/update
     */
    public function toModelArray(): array
    {
        return [
            'expense_date' => $this->expense_date,
            'expense_category_id' => $this->expense_category_id,
            'description' => $this->description,
            'supplier' => $this->supplier,
            'amount' => $this->amount,
            'notes' => $this->notes,
        ];
    }
}
