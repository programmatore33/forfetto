<?php

namespace Database\Seeders;

use App\Models\AtecoCode;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creazione dati demo...');

        // Create demo users with different profiles
        $this->createDemoUsers();

        $this->command->info('✅ Dati demo creati con successo!');
    }

    /**
     * Seed data for a specific demo session.
     */
    public function seedForSession(int $userId, string $sessionId): void
    {
        $user = User::find($userId);

        if (! $user || ! $user->is_demo) {
            throw new \Exception("Invalid demo user: {$userId}");
        }

        // Create demo data specific to this session
        $this->createSessionAtecoCode($user, $sessionId);
        $this->createSessionCustomers($user, $sessionId);
        $this->createSessionInvoices($user, $sessionId);
        $this->createSessionExpenses($user, $sessionId);
    }

    /**
     * Create ATECO code for demo session.
     */
    private function createSessionAtecoCode(User $user, string $sessionId): void
    {
        AtecoCode::factory()
            ->software()
            ->primary()
            ->create([
                'user_id' => $user->id,
                'session_id' => $sessionId,
            ]);
    }

    /**
     * Create customers for demo session.
     */
    private function createSessionCustomers(User $user, string $sessionId): void
    {
        // Create 3-5 customers for demo
        $customerCount = fake()->numberBetween(3, 5);

        Customer::factory()
            ->count($customerCount)
            ->create([
                'user_id' => $user->id,
                'session_id' => $sessionId,
            ]);
    }

    /**
     * Create invoices for demo session.
     */
    private function createSessionInvoices(User $user, string $sessionId): void
    {
        $atecoCode = AtecoCode::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->first();

        $customers = Customer::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->get();

        // Create 5-10 invoices for demo
        $invoiceCount = fake()->numberBetween(5, 10);

        for ($i = 0; $i < $invoiceCount; $i++) {
            $customer = fake()->boolean(70) && $customers->isNotEmpty() ? $customers->random() : null;

            Invoice::factory()
                ->create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'customer_id' => $customer?->id,
                    'ateco_code_id' => $atecoCode->id,
                ]);
        }
    }

    /**
     * Create expenses for demo session.
     */
    private function createSessionExpenses(User $user, string $sessionId): void
    {
        // Create basic expense categories
        $categories = [
            ['name' => 'Software', 'description' => 'Software e servizi digitali', 'color' => '#3B82F6'],
            ['name' => 'Hardware', 'description' => 'Attrezzature e dispositivi', 'color' => '#10B981'],
            ['name' => 'Formazione', 'description' => 'Corsi e certificazioni', 'color' => '#8B5CF6'],
            ['name' => 'Ufficio', 'description' => 'Materiale da ufficio', 'color' => '#6B7280'],
        ];

        foreach ($categories as $categoryData) {
            ExpenseCategory::factory()->create([
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'color' => $categoryData['color'],
            ]);
        }

        $expenseCategories = ExpenseCategory::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->get();

        // Create 5-10 expenses for demo
        $expenseCount = fake()->numberBetween(5, 10);

        for ($i = 0; $i < $expenseCount; $i++) {
            Expense::factory()
                ->create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'expense_category_id' => $expenseCategories->random()->id,
                ]);
        }
    }

    private function createDemoUsers(): void
    {
        // Create the main demo user (no data, will be populated at login)
        $demoUser = User::factory()
            ->demo()
            ->create();

        $this->command->info("🎭 Creato utente demo: {$demoUser->email} (password: demo123)");
        $this->command->info('    ℹ️  DEMO CREDENTIALS - Intentionally public for demo purposes');

        // Freelancer software developer (reduced rate)
        $developer = User::factory()
            ->reducedRate()
            ->withoutTwoFactor()
            ->create([
                'name' => 'Mario Rossi',
                'email' => 'mario.rossi@example.com',
                'activity_start_year' => 2022,
            ]);

        $this->setupUserData($developer, 'developer');
        $this->command->info("👨‍💻 Creato sviluppatore: {$developer->name}");

        // Marketing consultant (standard rate)
        $marketer = User::factory()
            ->standardRate()
            ->withoutTwoFactor()
            ->create([
                'name' => 'Laura Bianchi',
                'email' => 'laura.bianchi@example.com',
                'activity_start_year' => 2018,
            ]);

        $this->setupUserData($marketer, 'marketer');
        $this->command->info("📊 Creato consulente marketing: {$marketer->name}");

        // Artisan plumber (standard rate)
        $artisan = User::factory()
            ->standardRate()
            ->withoutTwoFactor()
            ->create([
                'name' => 'Giuseppe Verdi',
                'email' => 'giuseppe.verdi@example.com',
                'activity_start_year' => 2016,
            ]);

        $this->setupUserData($artisan, 'artisan');
        $this->command->info("🔧 Creato artigiano: {$artisan->name}");
    }

    private function setupUserData(User $user, string $type): void
    {
        // Wait for categories to be created by the job
        $this->createAtecoCode($user, $type);
        $this->createCustomers($user);
        $this->createInvoices($user);
        $this->createExpenses($user);
    }

    private function createAtecoCode(User $user, string $type): void
    {
        switch ($type) {
            case 'developer':
                AtecoCode::factory()
                    ->software()
                    ->primary()
                    ->create(['user_id' => $user->id]);
                break;

            case 'marketer':
                AtecoCode::factory()
                    ->primary()
                    ->create([
                        'user_id' => $user->id,
                        'ateco_code' => '73.11.00',
                        'description' => 'Agenzie di pubblicità',
                        'profitability_coeff' => 67.00,
                    ]);
                break;

            case 'artisan':
                AtecoCode::factory()
                    ->artisan()
                    ->primary()
                    ->create(['user_id' => $user->id]);
                break;

            default:
                AtecoCode::factory()
                    ->primary()
                    ->create(['user_id' => $user->id]);
                break;
        }
    }

    private function createCustomers(User $user): void
    {
        // Create 12-25 customers per user
        $customerCount = fake()->numberBetween(12, 25);

        Customer::factory()
            ->count($customerCount)
            ->create(['user_id' => $user->id]);

        // Create at least one large company
        Customer::factory()
            ->largeCompany()
            ->electronicInvoicing()
            ->create(['user_id' => $user->id]);

        // Create at least one small business
        Customer::factory()
            ->smallBusiness()
            ->create(['user_id' => $user->id]);
    }

    private function createInvoices(User $user): void
    {
        $atecoCode = $user->atecoCodes()->first();
        $customers = $user->customers;

        // Create 10-25 invoices per user
        $invoiceCount = fake()->numberBetween(10, 25);

        for ($i = 0; $i < $invoiceCount; $i++) {
            $customer = fake()->boolean(85) ? $customers->random() : null;

            Invoice::factory()
                ->create([
                    'user_id' => $user->id,
                    'customer_id' => $customer?->id,
                    'ateco_code_id' => $atecoCode->id,
                ]);
        }

        // Create some specific invoice types
        Invoice::factory()
            ->highValue()
            ->withWithholding()
            ->paid()
            ->create([
                'user_id' => $user->id,
                'customer_id' => $customers->random()->id,
                'ateco_code_id' => $atecoCode->id,
            ]);

        Invoice::factory()
            ->recent()
            ->unpaid()
            ->create([
                'user_id' => $user->id,
                'customer_id' => $customers->random()->id,
                'ateco_code_id' => $atecoCode->id,
            ]);
    }

    private function createExpenses(User $user): void
    {
        // Get user's expense categories (created by the initialization job)
        $categories = $user->expenseCategories;

        if ($categories->isEmpty()) {
            // Create default categories using the factory
            $defaultCategories = [
                ['name' => 'Software', 'description' => 'Software e servizi digitali', 'color' => '#3B82F6'],
                ['name' => 'Hardware', 'description' => 'Attrezzature e dispositivi', 'color' => '#10B981'],
                ['name' => 'Formazione', 'description' => 'Corsi e certificazioni', 'color' => '#8B5CF6'],
                ['name' => 'Marketing', 'description' => 'Pubblicità e promozione', 'color' => '#F59E0B'],
                ['name' => 'Ufficio', 'description' => 'Materiale da ufficio', 'color' => '#6B7280'],
                ['name' => 'Trasporti', 'description' => 'Viaggi e carburante', 'color' => '#EF4444'],
                ['name' => 'Consulenze', 'description' => 'Consulenze professionali', 'color' => '#14B8A6'],
                ['name' => 'Altro', 'description' => 'Spese varie', 'color' => '#64748B'],
            ];

            foreach ($defaultCategories as $categoryData) {
                ExpenseCategory::factory()->create([
                    'user_id' => $user->id,
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'],
                    'color' => $categoryData['color'],
                ]);
            }

            // Reload categories after creation
            $categories = $user->fresh()->expenseCategories;
        }

        // Create 15-40 expenses per user
        $expenseCount = fake()->numberBetween(15, 40);

        for ($i = 0; $i < $expenseCount; $i++) {
            Expense::factory()
                ->create([
                    'user_id' => $user->id,
                    'expense_category_id' => $categories->random()->id,
                ]);
        }

        // Create some specific expense types
        $softwareCategory = $categories->where('name', 'Software')->first();
        if ($softwareCategory) {
            Expense::factory()
                ->software()
                ->create([
                    'user_id' => $user->id,
                    'expense_category_id' => $softwareCategory->id,
                ]);
        }

        $transportCategory = $categories->where('name', 'Trasporti')->first();
        if ($transportCategory) {
            Expense::factory()
                ->transport()
                ->recent()
                ->create([
                    'user_id' => $user->id,
                    'expense_category_id' => $transportCategory->id,
                ]);
        }

        // Create one high-value expense
        Expense::factory()
            ->highValue()
            ->create([
                'user_id' => $user->id,
                'expense_category_id' => $categories->random()->id,
            ]);

        // Create one generic expense
        Expense::factory()
            ->create([
                'user_id' => $user->id,
                'expense_category_id' => $categories->random()->id,
            ]);
    }
}
