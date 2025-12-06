<?php

namespace App\Dtos;

use App\Enums\PaymentMethodEnum;
use App\Models\Invoice;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

/**
 * Invoice Data Transfer Object
 */
class InvoiceDto extends Data
{
    public function __construct(
        public int $id,
        public int $user_id,
        public ?int $customer_id,
        public int $ateco_code_id,
        // Customer snapshot
        public string $customer_business_name,
        public ?string $customer_email,
        public ?string $customer_vat_number,
        public ?string $customer_tax_code,
        public ?string $customer_address,
        public ?string $customer_city,
        public ?string $customer_province,
        public ?string $customer_postal_code,
        public ?string $customer_country,
        public ?string $customer_phone,
        public ?string $customer_pec,
        public ?string $customer_sdi_code,
        // Invoice data
        public string $invoice_number,
        public Carbon $issue_date,
        public ?Carbon $payment_date,
        public string $description,
        public float $amount,
        public float $withholding_tax,
        public float $net_amount,
        public bool $is_paid,
        public ?PaymentMethodEnum $payment_method,
        public ?string $notes,
        public Carbon $created_at,
        public Carbon $updated_at,
        // Relationships
        public ?CustomerDto $customer = null,
    ) {}

    /**
     * Create from Eloquent model
     */
    public static function fromModel(Invoice $invoice): self
    {
        return new self(
            id: $invoice->id,
            user_id: $invoice->user_id,
            customer_id: $invoice->customer_id,
            ateco_code_id: $invoice->ateco_code_id,
            customer_business_name: $invoice->customer_business_name,
            customer_email: $invoice->customer_email,
            customer_vat_number: $invoice->customer_vat_number,
            customer_tax_code: $invoice->customer_tax_code,
            customer_address: $invoice->customer_address,
            customer_city: $invoice->customer_city,
            customer_province: $invoice->customer_province,
            customer_postal_code: $invoice->customer_postal_code,
            customer_country: $invoice->customer_country,
            customer_phone: $invoice->customer_phone,
            customer_pec: $invoice->customer_pec,
            customer_sdi_code: $invoice->customer_sdi_code,
            invoice_number: $invoice->invoice_number,
            issue_date: $invoice->issue_date,
            payment_date: $invoice->payment_date,
            description: $invoice->description,
            amount: (float) $invoice->amount,
            withholding_tax: (float) $invoice->withholding_tax,
            net_amount: (float) $invoice->net_amount,
            is_paid: $invoice->is_paid,
            payment_method: $invoice->payment_method,
            notes: $invoice->notes,
            created_at: $invoice->created_at,
            updated_at: $invoice->updated_at,
            customer: $invoice->customer ? CustomerDto::fromModel($invoice->customer) : null,
        );
    }

    /**
     * Check if invoice has withholding tax
     */
    public function hasWithholdingTax(): bool
    {
        return $this->withholding_tax > 0;
    }

    /**
     * Check if linked to a customer record
     */
    public function hasCustomerLink(): bool
    {
        return $this->customer_id !== null;
    }
}
