<?php

namespace App\Models;

use App\Traits\HasUserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AtecoCode extends Model
{
    use HasUserScope;

    protected $fillable = [
        'user_id',
        'ateco_code',
        'description',
        'profitability_coeff',
        'is_primary',
    ];

    protected $casts = [
        'profitability_coeff' => 'decimal:2',
        'is_primary' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
