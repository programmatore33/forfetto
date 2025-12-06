<?php

namespace App\Dtos\Input;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class InputInvoiceItemDto extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $product_id,

        #[Nullable]
        public ?string $code,

        #[Required]
        public string $description,

        #[Required, Min(0)]
        public float $unit_price,

        #[Required, Min(1)]
        public int $quantity = 1,

        #[Required, Min(0)]
        public int $sort_order = 0,
    ) {}

    public function toModelArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'code' => $this->code,
            'description' => $this->description,
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'sort_order' => $this->sort_order,
            'total' => round($this->unit_price * $this->quantity, 2),
        ];
    }
}
