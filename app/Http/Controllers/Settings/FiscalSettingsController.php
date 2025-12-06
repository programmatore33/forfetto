<?php

namespace App\Http\Controllers\Settings;

use App\Dtos\Input\InputFiscalSettingsDto;
use App\Enums\ProfessionalFundEnum;
use App\Http\Controllers\Controller;
use App\Models\AtecoCode;
use App\Services\InvoiceNumberFormatter;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FiscalSettingsController extends Controller
{
    public function __construct(
        private SettingService $settingService,
        private InvoiceNumberFormatter $formatter,
    ) {}

    /**
     * Show fiscal settings page.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $settings = $this->settingService->getActiveSettings($user);
        $pattern = $settings->invoice_number_format ?: InvoiceNumberFormatter::DEFAULT_PATTERN;

        $atecoCodes = AtecoCode::query()
            ->select('id', 'ateco_code', 'description', 'profitability_coeff', 'is_primary')
            ->orderBy('is_primary', 'desc')
            ->orderBy('ateco_code')
            ->get();

        $currentYear = (int) date('Y');
        $preview = $this->formatter->format($pattern, $currentYear, 1);

        return Inertia::render('settings/Fiscal', [
            'settings' => $settings,
            'atecoCodes' => $atecoCodes,
            'professionalFunds' => ProfessionalFundEnum::options(),
            'patternPreview' => $preview,
        ]);
    }

    /**
     * Update fiscal settings.
     */
    public function update(InputFiscalSettingsDto $fiscalSettingsDto): RedirectResponse
    {
        $payload = $fiscalSettingsDto->toModelArray();
        $this->settingService->upsert($fiscalSettingsDto->user(), $payload);

        return to_route('settings.fiscal.edit')->with('success', 'Impostazioni fiscali aggiornate');
    }
}
