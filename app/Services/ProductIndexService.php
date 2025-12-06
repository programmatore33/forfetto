<?php

namespace App\Services;

use App\Dtos\Input\InputIndexDto;
use App\Dtos\ProductDto;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

/**
 * Product-specific index service with custom filtering
 */
class ProductIndexService extends IndexService
{
    /**
     * Get products with pagination, search and sorting
     */
    public function getProducts(InputIndexDto $input): array
    {
        $query = Product::query();

        // Define searchable and sortable fields for products
        $searchableFields = [
            'code',
            'name',
            'description',
        ];

        $sortableFields = [
            'code',
            'name',
            'unit_price',
            'created_at',
            'updated_at',
        ];

        // Build query with base functionality
        $paginator = $this->buildQuery(
            $query,
            $input,
            $searchableFields,
            $sortableFields,
            'name',
            'asc'
        );

        // Transform to ProductDto collection
        $productData = ProductDto::collect($paginator->items());

        return [
            'data' => $productData,
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
     * Apply product-specific filters
     */
    protected function applyCustomFilters(Builder $query, array $filters): void
    {
        // Filter by minimum price
        if (! empty($filters['min_price'])) {
            $query->where('unit_price', '>=', $filters['min_price']);
        }

        // Filter by maximum price
        if (! empty($filters['max_price'])) {
            $query->where('unit_price', '<=', $filters['max_price']);
        }

        // Filter by code existence
        if (isset($filters['has_code'])) {
            if ($filters['has_code']) {
                $query->whereNotNull('code');
            } else {
                $query->whereNull('code');
            }
        }
    }
}
