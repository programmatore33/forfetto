<?php

namespace App\Traits;

use App\Jobs\CreateDefaultExpenseCategories;

/**
 * Trait HasUserInitialization
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
        static::created(function ($user) {
            // Dispatch job to create default expense categories
            CreateDefaultExpenseCategories::dispatch($user);
            
            // Here you can add other initialization jobs
            // example: CreateDefaultAtecoCode::dispatch($user);
            // example: SendWelcomeEmail::dispatch($user);
        });
    }

    /**
     * Manually initialize default data for the user
     * Useful for existing users or testing
     */
    public function initializeDefaults(): void
    {
        CreateDefaultExpenseCategories::dispatchSync($this);
    }

    /**
     * Check if user has completed initialization
     */
    public function hasCompletedInitialization(): bool
    {
        return $this->expenseCategories()->exists();
    }

    /**
     * Force re-initialization of default data
     * WARNING: Deletes existing data
     */
    public function reinitializeDefaults(): void
    {
        // Remove existing categories
        $this->expenseCategories()->delete();
        
        // Recreate default categories
        $this->initializeDefaults();
    }
}