<?php

namespace App\Enums;

enum ProfessionalFundEnum: string
{
    case GESTIONE_SEPARATA_INPS = 'gestione_separata_inps';
    case INPS_ARTIGIANI = 'inps_artigiani';
    case INPS_COMMERCANTI = 'inps_commercianti';
    case ENASARCO = 'enasarco';
    case CASSA_FORENSE = 'cassa_forense';
    case INARCASSA = 'inarcassa';
    case CNPADC = 'cnpadc';
    case CNPR = 'cnpr';
    case ENPAP = 'enpap';
    case ENPAPI = 'enpapi';
    case ENPAV = 'enpav';
    case ENPAM = 'enpam';
    case ENPAF = 'enpaf';
    case ENPAB = 'enpab';
    case ENPACL = 'enpacl';
    case EPAP = 'epap';
    case EPPI = 'eppi';
    case CIPAG = 'cipag';
    case CASSA_NOTARIATO = 'cassa_notariato';
    case ALTRA_CASSA = 'altra_cassa';

    public function label(): string
    {
        return match ($this) {
            self::GESTIONE_SEPARATA_INPS => 'Gestione Separata INPS',
            self::INPS_ARTIGIANI => 'INPS Artigiani',
            self::INPS_COMMERCANTI => 'INPS Commercianti',
            self::ENASARCO => 'ENASARCO',
            self::CASSA_FORENSE => 'Cassa Forense',
            self::INARCASSA => 'INARCASSA',
            self::CNPADC => 'CNPADC (Dottori Commercialisti)',
            self::CNPR => 'CNPR (Ragionieri)',
            self::ENPAP => 'ENPAP (Psicologi)',
            self::ENPAPI => 'ENPAPI (Infermieri)',
            self::ENPAV => 'ENPAV (Veterinari)',
            self::ENPAM => 'ENPAM (Medici)',
            self::ENPAF => 'ENPAF (Farmacisti)',
            self::ENPAB => 'ENPAB (Biologi)',
            self::ENPACL => 'ENPACL (Consulenti del lavoro)',
            self::EPAP => 'EPAP (Attuari, Chimici, Geologi, Fisici, Agronomi)',
            self::EPPI => 'EPPI (Periti Industriali)',
            self::CIPAG => 'CIPAG (Geometri)',
            self::CASSA_NOTARIATO => 'Cassa Nazionale del Notariato',
            self::ALTRA_CASSA => 'Altra cassa',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $case) => [
                'value' => $case->value,
                'label' => $case->label(),
            ])
            ->toArray();
    }
}
