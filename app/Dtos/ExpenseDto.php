<?php

namespace App\Dtos;

use App\Models\Expense;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

/**
 * Expense Data Transfer Object
 */
class ExpenseDto extends Data
{
    public function __construct(
        public int $id,
        public int $user_id,
        public ?int $expense_category_id,
        public Carbon $expense_date,
        public string $description,
        public ?string $supplier,
        public float $amount,
        public ?string $notes,
        public Carbon $created_at,
        public Carbon $updated_at,
        // Relationships
        public ?ExpenseCategoryDto $expenseCategory = null,
    ) {}

    /**
     * Create from Eloquent model
     */
    public static function fromModel(Expense $expense): self
    {
        return new self(
            id: $expense->id,
            user_id: $expense->user_id,
            expense_category_id: $expense->expense_category_id,
            expense_date: $expense->expense_date,
            description: $expense->description,
            supplier: $expense->supplier,
            amount: (float) $expense->amount,
            notes: $expense->notes,
            created_at: $expense->created_at,
            updated_at: $expense->updated_at,
            expenseCategory: $expense->expenseCategory ? ExpenseCategoryDto::fromModel($expense->expenseCategory) : null,
        );
    }

    /**
     * Check if expense has category
     */
    public function hasCategory(): bool
    {
        return $this->expense_category_id !== null;
    }
}
