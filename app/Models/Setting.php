<?php

namespace App\Models;

use App\Enums\ProfessionalFundEnum;
use App\Traits\HasUserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * User-scoped settings including fiscal configuration.
 */
class Setting extends Model
{
    use HasFactory, HasUserScope;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'ateco_code_id',
        'invoice_number_format',
        'professional_fund',
        'reduced_contributions',
        'startup_rate',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reduced_contributions' => 'boolean',
            'startup_rate' => 'boolean',
            'professional_fund' => ProfessionalFundEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function atecoCode(): BelongsTo
    {
        return $this->belongsTo(AtecoCode::class);
    }
}
