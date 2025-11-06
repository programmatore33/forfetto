<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait HasUserScope.
 *
 * Automatically applies Global Scope to filter records by user_id and session_id
 * (for demo users) and automatically assigns both when creating new records.
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
        // Apply global filter for user_id and session_id (for demo users)
        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                $builder->where('user_id', $user->id);

                // If it's a demo user, also filter by session_id
                if ($user->is_demo) {
                    $sessionId = request()->cookie('demo_session_id');
                    if ($sessionId) {
                        $builder->where('session_id', $sessionId);
                    }
                }
            }
        });

        // Automatically assign user_id and session_id when creating a new record
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();

                if (! $model->user_id) {
                    $model->user_id = $user->id;
                }

                // If it's a demo user, also assign session_id
                if ($user->is_demo) {
                    $sessionId = request()->cookie('demo_session_id');
                    if ($sessionId && ! $model->session_id) {
                        $model->session_id = $sessionId;
                    }
                }
            }
        });
    }

    /**
     * Temporarily remove Global Scope to get all records.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function withoutUserScope()
    {
        return static::withoutGlobalScope('user');
    }

    /**
     * Get records of a specific user (admin only).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function forUser(int $userId)
    {
        return static::withoutGlobalScope('user')->where('user_id', $userId);
    }

    /**
     * Get records for a specific demo session.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function forDemoSession(string $sessionId, ?int $userId = null)
    {
        $query = static::withoutGlobalScope('user')->where('session_id', $sessionId);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query;
    }

    /**
     * Get all demo records (for cleanup purposes).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function demoOnly()
    {
        return static::withoutGlobalScope('user')->whereNotNull('session_id');
    }
}
