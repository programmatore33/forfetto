<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\User;

class InvoiceNumberService
{
    public function __construct(
        private InvoiceNumberFormatter $formatter,
        private SettingService $settingService,
    ) {}

    /**
     * Generate the next invoice number based on the user's settings and year.
     */
    public function generateNextNumber(User $user, int $year): string
    {
        $settings = $this->settingService->getActiveSettings($user);
        $pattern = $settings->invoice_number_format ?: InvoiceNumberFormatter::DEFAULT_PATTERN;

        if (! $this->formatter->validatePattern($pattern)) {
            $pattern = InvoiceNumberFormatter::DEFAULT_PATTERN;
        }

        $maxSequence = Invoice::query()
            ->whereYear('issue_date', $year)
            ->pluck('invoice_number')
            ->map(fn (string $number) => $this->formatter->extractSequence($pattern, $year, $number))
            ->filter()
            ->max() ?? 0;

        $nextSequence = $maxSequence + 1;

        return $this->formatter->format($pattern, $year, $nextSequence);
    }
}
