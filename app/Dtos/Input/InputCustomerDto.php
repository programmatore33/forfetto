<?php

namespace App\Dtos\Input;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Size;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

/**
 * DTO for customer input data with validation rules
 */
class InputCustomerDto extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public string $business_name,

        #[Sometimes, Nullable, Size(11)]
        public ?string $vat_number = null,

        #[Sometimes, Nullable, Size(16)]
        public ?string $tax_code = null,

        #[Sometimes, Nullable, Email, Max(255)]
        public ?string $email = null,

        #[Sometimes, Nullable, Max(50)]
        public ?string $phone = null,

        #[Sometimes, Nullable]
        public ?string $address = null,

        #[Sometimes, Nullable, Max(100)]
        public ?string $city = null,

        #[Sometimes, Nullable, Max(10)]
        public ?string $postal_code = null,

        #[Sometimes, Nullable, Size(2)]
        public ?string $province = null,

        #[Sometimes, Nullable, Email, Max(255)]
        public ?string $pec = null,

        #[Sometimes, Nullable, Max(7)]
        public ?string $sdi_code = null,

        #[Sometimes, Nullable]
        public ?string $notes = null,
    ) {}

    /**
     * Convert to array for model creation/update
     */
    public function toModelArray(): array
    {
        return [
            'business_name' => $this->business_name,
            'vat_number' => $this->vat_number,
            'tax_code' => $this->tax_code,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'province' => $this->province,
            'pec' => $this->pec,
            'sdi_code' => $this->sdi_code,
            'notes' => $this->notes,
        ];
    }

    /**
     * Check if customer has VAT number
     */
    public function hasVatNumber(): bool
    {
        return ! empty($this->vat_number);
    }

    /**
     * Check if customer has tax code
     */
    public function hasTaxCode(): bool
    {
        return ! empty($this->tax_code);
    }

    /**
     * Get full contact info
     */
    public function getContactInfo(): array
    {
        return array_filter([
            'email' => $this->email,
            'phone' => $this->phone,
            'pec' => $this->pec,
        ]);
    }
}
