<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;

class SettingService
{
    /**
     * Get active settings for the authenticated context (user or demo session).
     */
    public function getActiveSettings(User $user): Setting
    {
        $setting = $this->queryForUser($user)->first();

        if ($setting) {
            return $setting;
        }

        return Setting::create([
            'user_id' => $user->id,
            'invoice_number_format' => InvoiceNumberFormatter::DEFAULT_PATTERN,
            'startup_rate' => $user->isEligibleForReducedRate(),
        ]);
    }

    /**
     * Create or update settings for the authenticated context.
     */
    public function upsert(User $user, array $payload): Setting
    {
        $setting = $this->queryForUser($user)->first();

        if ($setting) {
            $setting->update($payload);

            return $setting;
        }

        return Setting::create($payload + [
            'user_id' => $user->id,
            'invoice_number_format' => $payload['invoice_number_format'] ?? InvoiceNumberFormatter::DEFAULT_PATTERN,
        ]);
    }

    private function queryForUser(User $user)
    {
        $sessionId = $user->is_demo ? request()->cookie('demo_session_id') : null;

        return Setting::query()
            ->where('user_id', $user->id)
            ->when($sessionId, fn ($q) => $q->where('session_id', $sessionId));
    }
}
