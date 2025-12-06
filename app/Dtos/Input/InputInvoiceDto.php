<?php

namespace App\Dtos\Input;

use App\Enums\PaymentMethodEnum;
use App\Models\Invoice;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\RequiredIf;
use Spatie\LaravelData\Attributes\Validation\Size;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;

/**
 * DTO for invoice input data with validation rules
 */
class InputInvoiceDto extends Data
{
    /**
     * Convert empty strings to null before validation
     */
    public static function prepareForValidation(array $properties): array
    {
        // List of fields that should convert empty strings to null
        $nullableFields = [
            'customer_email',
            'customer_vat_number',
            'customer_tax_code',
            'customer_address',
            'customer_city',
            'customer_province',
            'customer_postal_code',
            'customer_phone',
            'customer_pec',
            'customer_sdi_code',
            'payment_date',
            'payment_method',
            'notes',
        ];

        foreach ($nullableFields as $field) {
            if (isset($properties[$field]) && $properties[$field] === '') {
                $properties[$field] = null;
            }
        }

        return $properties;
    }

    public function __construct(
        #[Required, Max(50)]
        public string $invoice_number,

        #[Required, Date]
        public string $issue_date,

        #[Sometimes, Nullable, Exists('customers', 'id')]
        public ?int $customer_id = null,

        #[Required, Exists('ateco_codes', 'id')]
        public int $ateco_code_id = 0,

        // Customer snapshot fields
        #[Required, Max(255)]
        public string $customer_business_name = '',

        #[Sometimes, Nullable, Email, Max(255)]
        public ?string $customer_email = null,

        #[Sometimes, Nullable, Max(20)]
        public ?string $customer_vat_number = null,

        #[Sometimes, Nullable, Max(20)]
        public ?string $customer_tax_code = null,

        #[Sometimes, Nullable]
        public ?string $customer_address = null,

        #[Sometimes, Nullable, Max(100)]
        public ?string $customer_city = null,

        #[Sometimes, Nullable, Size(2)]
        public ?string $customer_province = null,

        #[Sometimes, Nullable, Max(10)]
        public ?string $customer_postal_code = null,

        #[Sometimes, Nullable, Size(2)]
        public ?string $customer_country = 'IT',

        #[Sometimes, Nullable, Max(30)]
        public ?string $customer_phone = null,

        #[Sometimes, Nullable, Email, Max(255)]
        public ?string $customer_pec = null,

        #[Sometimes, Nullable, Max(7)]
        public ?string $customer_sdi_code = null,

        // Invoice fields
        #[Sometimes, Nullable]
        public ?string $description = '',

        #[Required, Min(0.01)]
        public float $amount = 0,

        #[Sometimes, Nullable, Min(0)]
        public float $net_amount = 0,
        #[Sometimes, BooleanType]
        public bool $contributo_integrativo_applied = false,
        #[Sometimes, Nullable, Min(0)]
        public float $contributo_integrativo_amount = 0,

        #[Sometimes, BooleanType]
        public bool $is_paid = false,

        #[Sometimes, Nullable, Date]
        public ?string $payment_date = null,

        #[RequiredIf('is_paid', true), Nullable, Enum(PaymentMethodEnum::class)]
        public ?string $payment_method = null,

        #[Sometimes, Nullable]
        public ?string $notes = null,

        /** @var InputInvoiceItemDto[] */
        #[Required]
        public array $items = [],
    ) {}

    /**
     * Get additional validation rules
     */
    public static function rules(): array
    {
        return [
            'invoice_number' => [
                function ($attribute, $value, $fail) {
                    /** @var \App\Models\User $user */
                    $user = auth()->user();
                    $userId = $user->id;
                    $invoiceId = request()->route('invoice')?->id;
                    $sessionId = $user->is_demo ? request()->cookie('demo_session_id') : null;

                    $exists = Invoice::query()
                        ->where('user_id', $userId)
                        ->when($sessionId, fn ($q) => $q->where('session_id', $sessionId))
                        ->where('invoice_number', $value)
                        ->when($invoiceId, fn ($q) => $q->where('id', '!=', $invoiceId))
                        ->exists();

                    if ($exists) {
                        $fail('Il numero fattura è già utilizzato.');
                    }
                },
            ],
        ];
    }

    /**
     * Convert to array for model creation/update
     */
    public function toModelArray(): array
    {
        // Calculate amount from items sum
        $calculatedAmount = collect($this->items)->sum(fn ($item) => $item->unit_price * $item->quantity);

        // Calculate net amount: amount + contributo integrativo (if applied)
        $computedContributo = $this->contributo_integrativo_applied ? $this->contributo_integrativo_amount : 0;
        $netAmount = $calculatedAmount + $computedContributo;

        return [
            'customer_id' => $this->customer_id,
            'ateco_code_id' => $this->ateco_code_id,
            'customer_business_name' => $this->customer_business_name,
            'customer_email' => $this->customer_email,
            'customer_vat_number' => $this->customer_vat_number,
            'customer_tax_code' => $this->customer_tax_code,
            'customer_address' => $this->customer_address,
            'customer_city' => $this->customer_city,
            'customer_province' => $this->customer_province,
            'customer_postal_code' => $this->customer_postal_code,
            'customer_country' => $this->customer_country,
            'customer_phone' => $this->customer_phone,
            'customer_pec' => $this->customer_pec,
            'customer_sdi_code' => $this->customer_sdi_code,
            'invoice_number' => $this->invoice_number,
            'issue_date' => $this->issue_date,
            'payment_date' => $this->payment_date,
            'description' => $this->description,
            'amount' => $calculatedAmount,
            'contributo_integrativo_applied' => $this->contributo_integrativo_applied,
            'contributo_integrativo_amount' => $computedContributo,
            'net_amount' => $netAmount,
            'is_paid' => $this->is_paid,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
        ];
    }
}
