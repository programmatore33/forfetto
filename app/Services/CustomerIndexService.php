<?php

namespace App\Services;

use App\Dtos\CustomerDto;
use App\Dtos\InputIndexDto;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

/**
 * Customer-specific index service with custom filtering
 */
class CustomerIndexService extends IndexService
{
    /**
     * Get customers with pagination, search and sorting
     */
    public function getCustomers(InputIndexDto $input): array
    {
        $query = Customer::query();

        // Define searchable and sortable fields for customers
        $searchableFields = [
            'business_name',
            'email',
            'vat_number',
            'tax_code',
            'phone',
        ];

        $sortableFields = [
            'business_name',
            'email',
            'vat_number',
            'tax_code',
            'created_at',
            'updated_at',
        ];

        // Build query with base functionality
        $paginator = $this->buildQuery(
            $query,
            $input,
            $searchableFields,
            $sortableFields,
            'business_name'
        );

        // Transform to CustomerDto collection
        $customerData = CustomerDto::collect($paginator->items());

        return [
            'data' => $customerData,
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
     * Apply customer-specific filters
     */
    protected function applyCustomFilters(Builder $query, array $filters): void
    {
        // Example: Filter by province
        if (! empty($filters['province'])) {
            $query->where('province', $filters['province']);
        }

        // Example: Filter by country
        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        // Example: Filter by creation date range
        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        // Example: Filter customers with/without VAT number
        if (isset($filters['has_vat_number'])) {
            if ($filters['has_vat_number']) {
                $query->whereNotNull('vat_number');
            } else {
                $query->whereNull('vat_number');
            }
        }
    }
}
