<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use App\Traits\HasUserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory, HasUserScope;

    private const DECIMAL_CAST = 'decimal:2';

    protected $fillable = [
        'user_id',
        'session_id',
        'customer_id',
        'ateco_code_id',
        // Customer snapshot fields
        'customer_business_name',
        'customer_email',
        'customer_vat_number',
        'customer_tax_code',
        'customer_address',
        'customer_city',
        'customer_province',
        'customer_postal_code',
        'customer_country',
        'customer_phone',
        'customer_pec',
        'customer_sdi_code',
        // Invoice fields
        'invoice_number',
        'issue_date',
        'payment_date',
        'description',
        'amount',
        'withholding_tax',
        'net_amount',
        'is_paid',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'payment_date' => 'date',
        'amount' => self::DECIMAL_CAST,
        'withholding_tax' => self::DECIMAL_CAST,
        'net_amount' => self::DECIMAL_CAST,
        'is_paid' => 'boolean',
        'payment_method' => PaymentMethodEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function atecoCode(): BelongsTo
    {
        return $this->belongsTo(AtecoCode::class);
    }
}
