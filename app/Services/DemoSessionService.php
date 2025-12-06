<?php

namespace App\Services;

use App\Models\AtecoCode;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\User;
use App\Models\UserSession;
use Carbon\Carbon;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Service for managing demo user sessions and data.
 */
class DemoSessionService
{
    /**
     * Create a new demo session for the user.
     */
    public function createSession(User $user): string
    {
        if (! $user->is_demo) {
            throw new \InvalidArgumentException('User must be a demo user');
        }

        $sessionId = Str::uuid()->toString();
        $expiresAt = Carbon::now()->addHours(24);

        // Create the session record
        UserSession::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'expires_at' => $expiresAt,
        ]);

        // Set the demo session cookie
        $this->setDemoSessionCookie($sessionId);

        // Populate demo data
        $this->populateDemoData($user->id, $sessionId);

        return $sessionId;
    }

    /**
     * Get or create a demo session for the user.
     */
    public function getOrCreateSession(User $user): ?string
    {
        if (! $user->is_demo) {
            return null;
        }

        // Try to get session ID from cookie
        $sessionId = request()->cookie('demo_session_id');

        if ($sessionId) {
            $userSession = UserSession::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->first();

            // If session exists and is not expired, return it
            if ($userSession && ! $userSession->isExpired()) {
                return $sessionId;
            }

            // If session is expired, clean it up
            if ($userSession && $userSession->isExpired()) {
                $this->cleanupSession($sessionId, $user->id);
            }
        }

        // Check if user has any existing active sessions (fallback)
        $existingSession = UserSession::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($existingSession) {
            $this->setDemoSessionCookie($existingSession->session_id);

            return $existingSession->session_id;
        }

        // Create new demo session
        return $this->createSession($user);
    }

    /**
     * Clean up a demo session and all its data.
     */
    public function cleanupSession(string $sessionId, ?int $userId = null): void
    {
        // Remove cookie
        cookie()->queue(cookie()->forget('demo_session_id'));

        // Find session
        $query = UserSession::where('session_id', $sessionId);
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $userSession = $query->first();

        if (! $userSession) {
            return;
        }

        // Try to use cleanup command
        try {
            Artisan::call('demo:cleanup', ['--session-id' => $sessionId]);
        } catch (\Exception $e) {
            // Manual cleanup if command fails
            $this->manualCleanup($sessionId);
        }
    }

    /**
     * Clean up demo session on logout.
     */
    public function cleanupOnLogout(User $user, string $sessionId): void
    {
        if (! $user->is_demo) {
            return;
        }

        $userSession = UserSession::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->first();

        if ($userSession) {
            $this->cleanupSession($sessionId, $user->id);
        }
    }

    /**
     * Set the demo session cookie.
     */
    private function setDemoSessionCookie(string $sessionId): void
    {
        cookie()->queue('demo_session_id', $sessionId, 60 * 24); // 24 hours
    }

    /**
     * Populate demo data for the session.
     */
    private function populateDemoData(int $userId, string $sessionId): void
    {
        try {
            $seeder = app(DemoDataSeeder::class);
            if (method_exists($seeder, 'seedForSession')) {
                $seeder->seedForSession($userId, $sessionId);
                Log::info("Demo data populated for session {$sessionId}");
            }
        } catch (\Exception $e) {
            Log::error("Demo data seeder failed: {$e->getMessage()}");
        }
    }

    /**
     * Manual cleanup when command is not available.
     */
    private function manualCleanup(string $sessionId): void
    {
        // Delete all demo data for this session
        Invoice::where('session_id', $sessionId)->delete();
        Customer::where('session_id', $sessionId)->delete();
        Expense::where('session_id', $sessionId)->delete();
        ExpenseCategory::where('session_id', $sessionId)->delete();
        AtecoCode::where('session_id', $sessionId)->delete();

        // Delete the session record
        UserSession::where('session_id', $sessionId)->delete();
    }

    /**
     * Remove demo session cookie for any user.
     */
    public function removeDemoSessionCookie(): void
    {
        cookie()->queue(cookie()->forget('demo_session_id'));
    }
}
