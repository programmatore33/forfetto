<?php

namespace App\Services;

use App\Dtos\ExpenseDto;
use App\Dtos\Input\InputIndexDto;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;

/**
 * Expense-specific index service with custom filtering
 */
class ExpenseIndexService extends IndexService
{
    /**
     * Get expenses with pagination, search and sorting
     */
    public function getExpenses(InputIndexDto $input): array
    {
        $query = Expense::query()->with('expenseCategory');

        // Define searchable and sortable fields for expenses
        $searchableFields = [
            'description',
            'supplier',
        ];

        $sortableFields = [
            'expense_date',
            'amount',
            'description',
            'supplier',
            'is_deductible',
            'created_at',
            'updated_at',
        ];

        // Build query with base functionality
        $paginator = $this->buildQuery(
            $query,
            $input,
            $searchableFields,
            $sortableFields,
            'expense_date',
            'desc'
        );

        // Transform to ExpenseDto collection
        $expenseData = ExpenseDto::collect($paginator->items());

        return [
            'data' => $expenseData,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'filters' => [
                'search' => $input->search,
                'per_page' => $input->per_page,
                'sort_field' => $input->sort_field,
                'sort_direction' => $input->sort_direction,
            ] + $input->filters,
        ];
    }

    /**
     * Apply expense-specific filters
     */
    protected function applyCustomFilters(Builder $query, array $filters): void
    {
        // Filter by expense date range
        if (! empty($filters['expense_date_from'])) {
            $query->whereDate('expense_date', '>=', $filters['expense_date_from']);
        }

        if (! empty($filters['expense_date_to'])) {
            $query->whereDate('expense_date', '<=', $filters['expense_date_to']);
        }

        // Filter by expense category
        if (! empty($filters['expense_category_id'])) {
            $query->where('expense_category_id', $filters['expense_category_id']);
        }

        // Filter by deductible status
        if (isset($filters['is_deductible'])) {
            $query->where('is_deductible', (bool) $filters['is_deductible']);
        }
    }
}
