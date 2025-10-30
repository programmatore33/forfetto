<?php

namespace App\Services;

use App\Dtos\InputIndexDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Centralized service for handling index operations with search, sort and pagination
 */
class IndexService
{
    /**
     * Build a query with search, sort and pagination
     */
    public function buildQuery(
        Builder $query,
        InputIndexDto $input,
        array $searchableFields = [],
        array $sortableFields = [],
        ?string $defaultSortField = null
    ): LengthAwarePaginator {
        // Apply search if provided
        if ($input->hasSearch() && ! empty($searchableFields)) {
            $this->applySearch($query, $input->search, $searchableFields);
        }

        // Apply sorting if provided and valid
        if ($input->hasSorting() && in_array($input->sort_field, $sortableFields)) {
            $query->orderBy($input->sort_field, $input->sort_direction);
        } elseif ($defaultSortField && in_array($defaultSortField, $sortableFields)) {
            $query->orderBy($defaultSortField, $input->sort_direction);
        }

        // Apply custom filters (to be implemented by specific resources)
        $this->applyCustomFilters($query, $input->filters);

        // Return paginated results
        return $query->paginate($input->per_page, ['*'], 'page', $input->page);
    }

    /**
     * Apply search across multiple fields
     */
    protected function applySearch(Builder $query, string $search, array $searchableFields): void
    {
        $query->where(function ($q) use ($search, $searchableFields) {
            foreach ($searchableFields as $field) {
                $q->orWhere($field, 'like', "%{$search}%");
            }
        });
    }

    /**
     * Apply custom filters - override in specific services
     */
    protected function applyCustomFilters(Builder $query, array $filters): void
    {
        // Base implementation does nothing
        // Override in specific services for custom filtering
    }

    /**
     * Create InputIndexDto from Request
     */
    public static function createInputFromRequest(Request $request): InputIndexDto
    {
        return InputIndexDto::from([
            'search' => $request->get('search'),
            'per_page' => (int) $request->get('per_page', 15),
            'page' => (int) $request->get('page', 1),
            'sort_field' => $request->get('sort_field'),
            'sort_direction' => $request->get('sort_direction', 'asc'),
            'filters' => $request->except(['search', 'per_page', 'page', 'sort_field', 'sort_direction']),
        ]);
    }

    /**
     * Format response for Inertia
     */
    public function formatInertiaResponse(LengthAwarePaginator $paginator, InputIndexDto $input): array
    {
        return [
            'data' => $paginator->items(),
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
}
