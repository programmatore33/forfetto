<?php

namespace App\Dtos\Input;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

/**
 * DTO per la creazione di codici ATECO.
 */
class InputAtecoDto extends Data
{
    public function __construct(
        #[Required, StringType, Max(10)]
        public string $ateco_code,

        #[Required, StringType, Max(255)]
        public string $description,

        #[Required, Numeric, Min(0), Max(100)]
        public float $profitability_coeff,

        #[BooleanType]
        public bool $is_primary = false,
    ) {}

    /**
     * Messaggi di validazione in italiano.
     */
    public static function messages(): array
    {
        return [
            'ateco_code.required' => 'Il codice ATECO è obbligatorio.',
            'ateco_code.max' => 'Il codice ATECO non può superare :max caratteri.',
            'description.required' => 'La descrizione è obbligatoria.',
            'description.max' => 'La descrizione non può superare :max caratteri.',
            'profitability_coeff.required' => 'Il coefficiente di redditività è obbligatorio.',
            'profitability_coeff.numeric' => 'Il coefficiente di redditività deve essere un numero.',
            'profitability_coeff.min' => 'Il coefficiente di redditività deve essere almeno :min.',
            'profitability_coeff.max' => 'Il coefficiente di redditività non può superare :max.',
            'is_primary.boolean' => 'Il campo primario deve essere vero o falso.',
        ];
    }

    /**
     * Array per la creazione del modello.
     */
    public function toModelArray(): array
    {
        return [
            'ateco_code' => $this->ateco_code,
            'description' => $this->description,
            'profitability_coeff' => $this->profitability_coeff,
            'is_primary' => $this->is_primary,
        ];
    }
}
