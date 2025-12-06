<?php

namespace App\Dtos\Input;

use App\Enums\ProfessionalFundEnum;
use App\Models\AtecoCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

/**
 * DTO per le impostazioni fiscali.
 */
class InputFiscalSettingsDto extends Data
{
    public function __construct(
        #[Sometimes, Nullable]
        public ?int $ateco_code_id,

        #[Required, StringType, Max(120)]
        public string $invoice_number_format,

        #[Sometimes, Nullable]
        public ?string $professional_fund = null,

        #[Sometimes, BooleanType]
        public bool $reduced_contributions = false,

        #[Sometimes, BooleanType]
        public bool $startup_rate = false,
    ) {}

    /**
     * Messaggi di validazione in italiano.
     */
    public static function messages(): array
    {
        return [
            'invoice_number_format.required' => 'Il formato numero fattura è obbligatorio.',
            'invoice_number_format.max' => 'Il formato numero fattura non può superare :max caratteri.',
            'ateco_code_id.exists' => 'Il codice ATECO selezionato non è valido.',
            'professional_fund.in' => 'Seleziona una cassa previdenziale valida.',
            'reduced_contributions.boolean' => 'Il campo contributi ridotti deve essere vero o falso.',
            'startup_rate.boolean' => 'Il campo regime start-up deve essere vero o falso.',
        ];
    }

    /**
     * Regole aggiuntive non coperte dagli attribute validator.
     */
    public static function rules(): array
    {
        $user = Auth::user();
        $sessionId = $user?->is_demo ? request()->cookie('demo_session_id') : null;

        return [
            'ateco_code_id' => [
                Rule::exists(AtecoCode::class, 'id')
                    ->when(
                        $user,
                        fn ($query) => $query
                            ->where('user_id', $user->id)
                            ->when($sessionId, fn ($q) => $q->where('session_id', $sessionId))
                    ),
            ],
            'invoice_number_format' => [
                function (string $attribute, mixed $value, \Closure $fail) {
                    /** @var \App\Services\InvoiceNumberFormatter $formatter */
                    $formatter = app(\App\Services\InvoiceNumberFormatter::class);
                    if (! $formatter->validatePattern((string) $value)) {
                        $fail('Il formato numero fattura non è valido. Usa {year} e {seq} o {seq:N}.');
                    }
                },
            ],
            'professional_fund' => [
                'nullable',
                Rule::in(array_column(ProfessionalFundEnum::cases(), 'value')),
            ],
        ];
    }

    /**
     * Converte i dati per il salvataggio.
     */
    public function toModelArray(): array
    {
        return [
            'ateco_code_id' => $this->ateco_code_id,
            'invoice_number_format' => $this->invoice_number_format,
            'professional_fund' => $this->professional_fund,
            'reduced_contributions' => $this->reduced_contributions,
            'startup_rate' => $this->startup_rate,
        ];
    }

    public function user(): \App\Models\User
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user;
    }
}
