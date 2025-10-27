<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait HasUserScope
 * 
 * Automatically applies Global Scope to filter records by user_id
 * and automatically assigns user_id when creating new records.
 * 
 * Usage:
 * class MyModel extends Model
 * {
 *     use HasUserScope;
 * }
 */
trait HasUserScope
{
    protected static function bootHasUserScope()
    {
        // Apply global filter for user_id
        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::id());
            }
        });

        // Automatically assign user_id when creating a new record
        static::creating(function ($model) {
            if (Auth::check() && !$model->user_id) {
                $model->user_id = Auth::id();
            }
        });
    }

    /**
     * Temporarily remove Global Scope to get all records
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function withoutUserScope()
    {
        return static::withoutGlobalScope('user');
    }

    /**
     * Get records of a specific user (admin only)
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function forUser(int $userId)
    {
        return static::withoutGlobalScope('user')->where('user_id', $userId);
    }
}