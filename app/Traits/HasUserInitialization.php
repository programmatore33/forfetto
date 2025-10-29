<?php

namespace App\Traits;

/**
 * Trait HasUserInitialization.
 *
 * Manages automatic initialization of default data
 * when a new user is created.
 *
 * Usage:
 * class User extends Authenticatable
 * {
 *     use HasUserInitialization;
 * }
 */
trait HasUserInitialization
{
    protected static function bootHasUserInitialization()
    {
        static::created(function () {
            // Here you can add initialization jobs for user-specific data
            // example: CreateDefaultAtecoCode::dispatch($user);
            // example: SendWelcomeEmail::dispatch($user);

            // Note: Expense categories are now global, no need to create them per user
        });
    }

    /**
     * Manually initialize default data for the user
     * Useful for existing users or testing.
     */
    public function initializeDefaults(): void
    {
        // Add any user-specific initialization logic here
        // Expense categories are now global and don't need initialization
    }

    /**
     * Check if user has completed initialization.
     */
    public function hasCompletedInitialization(): bool
    {
        // Since we don't create user-specific data anymore, always return true
        // Or implement other checks for user-specific data if needed
        return true;
    }
}
