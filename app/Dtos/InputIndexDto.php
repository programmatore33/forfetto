<?php

namespace App\Dtos;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

/**
 * Generic DTO for index operations with pagination, search and sorting
 */
class InputIndexDto extends Data
{
    public function __construct(
        #[Sometimes, Nullable]
        public ?string $search = null,

        #[Sometimes, Min(1), Max(100)]
        public int $per_page = 15,

        #[Sometimes, Min(1)]
        public int $page = 1,

        #[Sometimes, Nullable]
        public ?string $sort_field = null,

        #[Sometimes, In(['asc', 'desc'])]
        public string $sort_direction = 'asc',

        #[Sometimes]
        public array $filters = [],
    ) {}

    /**
     * Get pagination parameters for Eloquent
     */
    public function getPaginationParams(): array
    {
        return [
            'per_page' => $this->per_page,
            'page' => $this->page,
        ];
    }

    /**
     * Get sort parameters
     */
    public function getSortParams(): array
    {
        return [
            'field' => $this->sort_field,
            'direction' => $this->sort_direction,
        ];
    }

    /**
     * Check if has search
     */
    public function hasSearch(): bool
    {
        return ! empty($this->search);
    }

    /**
     * Check if has sorting
     */
    public function hasSorting(): bool
    {
        return ! empty($this->sort_field);
    }
}
