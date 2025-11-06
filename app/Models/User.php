<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasUserInitialization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUserInitialization, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'vat_number',
        'tax_code',
        'tax_rate',
        'activity_start_year',
        'is_demo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'tax_rate' => 'decimal:2',
            'activity_start_year' => 'integer',
            'is_demo' => 'boolean',
        ];
    }

    public function atecoCodes(): HasMany
    {
        return $this->hasMany(AtecoCode::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function expenseCategories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    /**
     * Get user sessions for demo users.
     */
    public function userSessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Check if user is a demo user.
     */
    public function isDemoUser(): bool
    {
        return $this->is_demo;
    }

    /**
     * Get the current active demo session for this user.
     */
    public function getActiveDemoSession(): ?UserSession
    {
        if (! $this->is_demo) {
            return null;
        }

        return $this->userSessions()
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Create a new demo session for this user.
     */
    public function createDemoSession(): UserSession
    {
        if (! $this->is_demo) {
            throw new \Exception('Cannot create demo session for non-demo user');
        }

        return UserSession::create([
            'user_id' => $this->id,
            'session_id' => \Illuminate\Support\Str::uuid()->toString(),
            'expires_at' => now()->addHours(24),
        ]);
    }

    public function isEligibleForReducedRate(): bool
    {
        if (! $this->activity_start_year) {
            return false;
        }

        return (date('Y') - $this->activity_start_year) < 5;
    }
}
