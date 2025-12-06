<?php

namespace App\Dtos;

use App\Models\ExpenseCategory;
use Spatie\LaravelData\Data;

/**
 * Expense Category Data Transfer Object
 */
class ExpenseCategoryDto extends Data
{
    public function __construct(
        public int $id,
        public ?int $user_id,
        public string $name,
        public ?string $description,
        public ?string $color,
        public bool $is_global,
    ) {}

    /**
     * Create from Eloquent model
     */
    public static function fromModel(ExpenseCategory $category): self
    {
        return new self(
            id: $category->id,
            user_id: $category->user_id,
            name: $category->name,
            description: $category->description,
            color: $category->color,
            is_global: $category->isGlobal(),
        );
    }
}
