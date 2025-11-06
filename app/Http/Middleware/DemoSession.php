<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * DemoSession Middleware
 *
 * Handles demo session creation and management for demo users.
 * Creates isolated demo environments with sample data.
 */
class DemoSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only process if user is authenticated and is a demo user
        if (Auth::check() && Auth::user()->is_demo) {
            $this->handleDemoSession();
        }

        return $next($request);
    }

    /**
     * Handle demo session logic.
     */
    private function handleDemoSession(): void
    {
        $user = Auth::user();
        $sessionId = session('demo_session_id');

        // Check if we have a valid existing session in the current browser session
        if ($sessionId) {
            $userSession = UserSession::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->first();

            // If session exists and is not expired, continue using it
            if ($userSession && ! $userSession->isExpired()) {
                return;
            }

            // If session is expired, clean it up
            if ($userSession && $userSession->isExpired()) {
                $this->cleanupExpiredSession($userSession);
            }
        }

        // Always create a new demo session if no valid session in browser
        // This means every login creates fresh demo data
        $this->createNewDemoSession($user);
    }

    /**
     * Create a new demo session for the user.
     */
    private function createNewDemoSession($user): void
    {
        $sessionId = Str::uuid()->toString();
        $expiresAt = Carbon::now()->addHours(24);

        Log::debug('Creating new demo session', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'expires_at' => $expiresAt,
        ]);

        // Create the session record
        UserSession::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'expires_at' => $expiresAt,
        ]);

        // Store session ID in Laravel session
        session(['demo_session_id' => $sessionId]);

        Log::debug('Demo session created and stored in Laravel session', [
            'session_id' => $sessionId,
            'laravel_session_stored' => session('demo_session_id'),
        ]);

        // Populate demo data
        $this->populateDemoData($user->id, $sessionId);
    }

    /**
     * Populate demo data for the new session.
     */
    private function populateDemoData(int $userId, string $sessionId): void
    {
        // This method is called only when creating a NEW session,
        // so we always need to populate data
        try {
            $seederClass = app(\Database\Seeders\DemoDataSeeder::class);
            if (method_exists($seederClass, 'seedForSession')) {
                $seederClass->seedForSession($userId, $sessionId);
                Log::info("Demo data populated for session {$sessionId}");
            }
        } catch (\Exception $e) {
            // Seeder not ready yet, skip for now
            Log::error('Demo data seeder failed: '.$e->getMessage());
        }
    }

    /**
     * Clean up expired session data.
     */
    private function cleanupExpiredSession(UserSession $userSession): void
    {
        // Remove session from Laravel session
        session()->forget('demo_session_id');

        // This will call the cleanup command functionality
        try {
            \Illuminate\Support\Facades\Artisan::call('demo:cleanup', ['--session-id' => $userSession->session_id]);
        } catch (\Exception $e) {
            // Cleanup command not ready yet, manual cleanup
            $this->manualCleanup($userSession);
        }
    }

    /**
     * Manual cleanup when command is not available.
     */
    private function manualCleanup(UserSession $userSession): void
    {
        $sessionId = $userSession->session_id;

        // Delete all demo data for this session
        \App\Models\Invoice::where('session_id', $sessionId)->delete();
        \App\Models\Customer::where('session_id', $sessionId)->delete();
        \App\Models\Expense::where('session_id', $sessionId)->delete();
        \App\Models\ExpenseCategory::where('session_id', $sessionId)->delete();
        \App\Models\AtecoCode::where('session_id', $sessionId)->delete();

        // Finally delete the session record
        $userSession->delete();
    }
}
