<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'color',
    ];

    /**
     * Scope to get only global (default) categories.
     */
    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope to get only user-specific categories.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get all available categories for a user (global + user-specific).
     */
    public function scopeAvailableForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('user_id')
                ->orWhere('user_id', $userId);
        });
    }

    /**
     * Check if this is a global category.
     */
    public function isGlobal(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Get the user that owns the category.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the expenses for this category.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
