<?php

namespace App\Dtos;

use App\Models\Product;
use Spatie\LaravelData\Data;

class ProductDto extends Data
{
    public function __construct(
        public int $id,
        public int $user_id,
        public ?string $session_id,
        public ?string $code,
        public string $name,
        public ?string $description,
        public float $unit_price,
        public string $created_at,
        public string $updated_at,
    ) {}

    public static function fromModel(Product $product): self
    {
        return new self(
            id: $product->id,
            user_id: $product->user_id,
            session_id: $product->session_id,
            code: $product->code,
            name: $product->name,
            description: $product->description,
            unit_price: (float) $product->unit_price,
            created_at: $product->created_at->toISOString(),
            updated_at: $product->updated_at->toISOString(),
        );
    }
}
