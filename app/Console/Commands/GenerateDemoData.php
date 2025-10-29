<?php

namespace App\Console\Commands;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Console\Command;

class GenerateDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:generate
                           {--fresh : Reset database before generating demo data}
                           {--users=3 : Number of demo users to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate demo data for testing and development';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            if ($this->confirm('This will reset the entire database. Are you sure?')) {
                $this->call('migrate:fresh');
                $this->info('🗄️ Database reset completed');
            } else {
                $this->info('Operation cancelled');

                return 0;
            }
        }

        $userCount = (int) $this->option('users');

        $this->info("🚀 Generating demo data for {$userCount} users...");

        $this->call(DemoDataSeeder::class);

        $this->newLine();
        $this->info('✅ Demo data generation completed!');
        $this->info('');
        $this->info('📊 Demo users created:');
        $this->info('• mario.rossi@example.com (Developer - 5% rate)');
        $this->info('• laura.bianchi@example.com (Marketer - 15% rate)');
        $this->info('• giuseppe.verdi@example.com (Artisan - 15% rate)');
        $this->info('');
        $this->info('🔑 All users have password: password');
        $this->info('');
        $this->info('💡 Use these commands to explore the data:');
        $this->info('php artisan tinker');
        $this->info('>>> User::with("invoices", "expenses")->get()');

        return 0;
    }
}
