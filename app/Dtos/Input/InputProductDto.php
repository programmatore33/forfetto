<?php

namespace App\Dtos\Input;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class InputProductDto extends Data
{
    public function __construct(
        #[Nullable]
        public ?string $code,

        #[Required]
        public string $name,

        #[Nullable]
        public ?string $description,

        #[Required, Min(0)]
        public float $unit_price,
    ) {}

    public function toModelArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'unit_price' => $this->unit_price,
        ];
    }
}
