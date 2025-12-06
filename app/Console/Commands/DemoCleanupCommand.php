<?php

namespace App\Console\Commands;

use App\Models\UserSession;
use Illuminate\Console\Command;

/**
 * DemoCleanupCommand
 *
 * Cleans up expired demo sessions and their associated data.
 * Can be run manually or scheduled automatically.
 */
class DemoCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cleanup 
                            {--session-id= : Clean up a specific session ID}
                            {--force : Skip confirmation prompts}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired demo sessions and associated data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sessionId = $this->option('session-id');
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        if ($sessionId) {
            return $this->cleanupSpecificSession($sessionId, $force, $dryRun);
        }

        return $this->cleanupExpiredSessions($force, $dryRun);
    }

    /**
     * Clean up a specific session.
     */
    private function cleanupSpecificSession(string $sessionId, bool $force, bool $dryRun): int
    {
        $session = UserSession::where('session_id', $sessionId)->first();

        if (! $session) {
            $this->error("Session not found: {$sessionId}");

            return 1;
        }

        if ($dryRun) {
            $this->info("Would delete session: {$sessionId}");
            $this->showSessionData($session);

            return 0;
        }

        if (! $force && ! $this->confirm("Delete session {$sessionId}?")) {
            $this->info('Cleanup cancelled.');

            return 0;
        }

        $this->deleteSessionData($session);
        $this->info("Session {$sessionId} cleaned up successfully.");

        return 0;
    }

    /**
     * Clean up all expired sessions.
     */
    private function cleanupExpiredSessions(bool $force, bool $dryRun): int
    {
        $expiredSessions = UserSession::expired()->get();

        if ($expiredSessions->isEmpty()) {
            $this->info('No expired sessions found.');

            return 0;
        }

        $count = $expiredSessions->count();

        if ($dryRun) {
            $this->info("Would delete {$count} expired sessions:");
            foreach ($expiredSessions as $session) {
                $this->line("- {$session->session_id} (expired: {$session->expires_at})");
            }

            return 0;
        }

        if (! $force && ! $this->confirm("Delete {$count} expired demo sessions?")) {
            $this->info('Cleanup cancelled.');

            return 0;
        }

        $deletedCount = 0;
        foreach ($expiredSessions as $session) {
            try {
                $this->deleteSessionData($session);
                $deletedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to delete session {$session->session_id}: ".$e->getMessage());
            }
        }

        $this->info("Successfully cleaned up {$deletedCount} expired demo sessions.");

        return 0;
    }

    /**
     * Delete all data associated with a session.
     */
    private function deleteSessionData(UserSession $session): void
    {
        $sessionId = $session->session_id;

        // Count records before deletion
        $invoicesCount = \App\Models\Invoice::where('session_id', $sessionId)->count();
        $customersCount = \App\Models\Customer::where('session_id', $sessionId)->count();
        $expensesCount = \App\Models\Expense::where('session_id', $sessionId)->count();
        $categoriesCount = \App\Models\ExpenseCategory::where('session_id', $sessionId)->count();
        $atecoCount = \App\Models\AtecoCode::where('session_id', $sessionId)->count();

        // Delete in order to respect foreign key constraints
        \App\Models\Invoice::where('session_id', $sessionId)->delete();
        \App\Models\Expense::where('session_id', $sessionId)->delete();
        \App\Models\Customer::where('session_id', $sessionId)->delete();
        \App\Models\ExpenseCategory::where('session_id', $sessionId)->delete();
        \App\Models\AtecoCode::where('session_id', $sessionId)->delete();

        // Finally delete the session record
        $session->delete();

        $totalRecords = $invoicesCount + $customersCount + $expensesCount + $categoriesCount + $atecoCount;

        if ($this->output->isVerbose()) {
            $this->line("  Deleted: {$invoicesCount} invoices, {$customersCount} customers, ".
                      "{$expensesCount} expenses, {$categoriesCount} categories, ".
                      "{$atecoCount} ateco codes ({$totalRecords} total records)");
        }
    }

    /**
     * Show data associated with a session for dry-run.
     */
    private function showSessionData(UserSession $session): void
    {
        $sessionId = $session->session_id;

        $invoicesCount = \App\Models\Invoice::where('session_id', $sessionId)->count();
        $customersCount = \App\Models\Customer::where('session_id', $sessionId)->count();
        $expensesCount = \App\Models\Expense::where('session_id', $sessionId)->count();
        $categoriesCount = \App\Models\ExpenseCategory::where('session_id', $sessionId)->count();
        $atecoCount = \App\Models\AtecoCode::where('session_id', $sessionId)->count();

        $this->line("  Would delete: {$invoicesCount} invoices, {$customersCount} customers, ".
                   "{$expensesCount} expenses, {$categoriesCount} categories, ".
                   "{$atecoCount} ateco codes");
    }
}
