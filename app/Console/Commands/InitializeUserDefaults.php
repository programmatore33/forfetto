<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class InitializeUserDefaults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:initialize-defaults
                           {--user-id= : Specific user ID to initialize}
                           {--all : Initialize all users without default data}
                           {--force : Force re-initialization even for already initialized users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize default data for users (expense categories, etc.)';    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $all = $this->option('all');
        $force = $this->option('force');

        if ($userId) {
            $this->initializeUser($userId, $force);
        } elseif ($all) {
            $this->initializeAllUsers($force);
        } else {
            $this->error('Specify --user-id=ID or --all to proceed');
            return 1;
        }

        return 0;
    }

    private function initializeUser(int $userId, bool $force = false): void
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return;
        }

        if (!$force && $user->hasCompletedInitialization()) {
            $this->warn("User {$user->name} (ID: {$userId}) already has initialized data. Use --force to override");
            return;
        }

        $this->info("Initializing user: {$user->name} (ID: {$userId})");
        
        if ($force) {
            $user->reinitializeDefaults();
            $this->info("✅ Re-initialization completed");
        } else {
            $user->initializeDefaults();
            $this->info("✅ Initialization completed");
        }
    }

    private function initializeAllUsers(bool $force = false): void
    {
        $query = User::query();
        
        if (!$force) {
            // Only users that don't have initialized data yet
            $query->whereDoesntHave('expenseCategories');
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->info('No users to initialize found');
            return;
        }

        $this->info("Found {$users->count()} users to initialize...");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            if ($force) {
                $user->reinitializeDefaults();
            } else {
                $user->initializeDefaults();
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Initialization completed for {$users->count()} users");
    }
}
