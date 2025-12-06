<?php

namespace App\Dtos;

use Spatie\LaravelData\Data;

/**
 * DTO for paginated responses in Inertia pages
 */
class PaginatedResponseDto extends Data
{
    public function __construct(
        public array $data,
        public int $total,
        public int $per_page,
        public int $current_page,
        public int $last_page,
        public ?int $from,
        public ?int $to,
    ) {}

    /**
     * Create from service result array
     */
    public static function fromServiceResult(array $result): self
    {
        return new self(
            data: $result['data'],
            total: $result['meta']['total'],
            per_page: $result['meta']['per_page'],
            current_page: $result['meta']['current_page'],
            last_page: $result['meta']['last_page'],
            from: $result['meta']['from'],
            to: $result['meta']['to'],
        );
    }
}
