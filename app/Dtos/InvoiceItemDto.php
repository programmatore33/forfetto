<?php

namespace App\Dtos;

use App\Models\InvoiceItem;
use Spatie\LaravelData\Data;

class InvoiceItemDto extends Data
{
    public function __construct(
        public int $id,
        public int $invoice_id,
        public ?int $product_id,
        public ?string $code,
        public string $description,
        public float $unit_price,
        public int $quantity,
        public float $total,
        public int $sort_order,
    ) {}

    public static function fromModel(InvoiceItem $item): self
    {
        return new self(
            id: $item->id,
            invoice_id: $item->invoice_id,
            product_id: $item->product_id,
            code: $item->code,
            description: $item->description,
            unit_price: (float) $item->unit_price,
            quantity: $item->quantity,
            total: (float) $item->total,
            sort_order: $item->sort_order,
        );
    }
}
