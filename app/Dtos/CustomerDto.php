<?php

namespace App\Dtos;

use App\Models\Customer;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

/**
 * Customer Data Transfer Object
 */
class CustomerDto extends Data
{
    public function __construct(
        public int $id,
        public string $business_name,
        public string $email,
        public ?string $vat_number,
        public ?string $tax_code,
        public ?string $phone,
        public ?string $address,
        public ?string $city,
        public ?string $province,
        public ?string $postal_code,
        public ?string $country,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}

    /**
     * Create from Eloquent model
     */
    public static function fromModel(Customer $customer): self
    {
        return new self(
            id: $customer->id,
            business_name: $customer->business_name,
            email: $customer->email,
            vat_number: $customer->vat_number,
            tax_code: $customer->tax_code,
            phone: $customer->phone,
            address: $customer->address,
            city: $customer->city,
            province: $customer->province,
            postal_code: $customer->postal_code,
            country: $customer->country,
            created_at: $customer->created_at,
            updated_at: $customer->updated_at,
        );
    }
}
