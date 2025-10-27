<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initialize default data for all existing users
        // that don't have categories yet
        User::whereDoesntHave('expenseCategories')
            ->get()
            ->each(function ($user) {
                $user->initializeDefaults();
            });
    }
}
