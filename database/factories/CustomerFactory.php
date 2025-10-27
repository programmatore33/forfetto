<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyTypes = ['S.R.L.', 'S.P.A.', 'S.N.C.', 'S.A.S.', 'DITTA INDIVIDUALE'];
        $cities = ['Roma', 'Milano', 'Napoli', 'Torino', 'Palermo', 'Genova', 'Bologna', 'Firenze', 'Bari', 'Catania'];
        
        $businessName = fake('it_IT')->company() . ' ' . fake()->randomElement($companyTypes);

        return [
            'user_id' => User::factory(),
            'business_name' => $businessName,
            'vat_number' => $this->generateVatNumber(),
            'tax_code' => fake()->boolean(80) ? $this->generateTaxCode() : null,
            'email' => fake('it_IT')->companyEmail(),
            'phone' => $this->generateItalianPhone(),
            'address' => fake('it_IT')->streetAddress(),
            'city' => fake()->randomElement($cities),
            'postal_code' => fake('it_IT')->postcode(),
            'province' => fake()->randomElement(['RM', 'MI', 'NA', 'TO', 'PA', 'GE', 'BO', 'FI', 'BA', 'CT']),
            'pec' => fake()->boolean(60) ? fake('it_IT')->safeEmail() : null,
            'sdi_code' => fake()->boolean(40) ? strtoupper(fake()->bothify('???????')) : null,
            'notes' => fake()->boolean(30) ? fake('it_IT')->sentence() : null,
        ];
    }

    /**
     * Generate a realistic Italian VAT number
     */
    private function generateVatNumber(): string
    {
        return str_pad((string) fake()->numberBetween(10000000000, 99999999999), 11, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a realistic Italian tax code
     */
    private function generateTaxCode(): string
    {
        $consonants = 'BCDFGHJKLMNPQRSTVWXYZ';
        $vowels = 'AEIOU';
        
        // Simple tax code generation
        return substr(str_shuffle($consonants), 0, 6) . 
               fake()->numberBetween(10, 99) . 
               substr(str_shuffle('ABCDEHLMPRST'), 0, 1) . 
               str_pad((string) fake()->numberBetween(1, 71), 2, '0', STR_PAD_LEFT) . 
               substr(str_shuffle($consonants), 0, 1) . 
               fake()->numberBetween(100, 999) . 
               substr(str_shuffle($consonants . '0123456789'), 0, 1);
    }

    /**
     * Generate Italian phone number
     */
    private function generateItalianPhone(): string
    {
        $prefixes = ['02', '06', '081', '011', '091', '010', '051', '055', '080', '095'];
        return fake()->randomElement($prefixes) . fake()->numerify('#######');
    }

    /**
     * Customer with PEC and SDI (electronic invoicing ready)
     */
    public function electronicInvoicing(): static
    {
        return $this->state(fn (array $attributes) => [
            'pec' => fake('it_IT')->safeEmail(),
            'sdi_code' => strtoupper(fake()->bothify('???????')),
        ]);
    }

    /**
     * Small business customer (no VAT)
     */
    public function smallBusiness(): static
    {
        return $this->state(fn (array $attributes) => [
            'business_name' => fake('it_IT')->firstName() . ' ' . fake('it_IT')->lastName(),
            'vat_number' => null,
            'pec' => null,
            'sdi_code' => null,
        ]);
    }

    /**
     * Large company customer
     */
    public function largeCompany(): static
    {
        return $this->state(fn (array $attributes) => [
            'business_name' => fake('it_IT')->company() . ' S.P.A.',
            'pec' => fake('it_IT')->safeEmail(),
            'sdi_code' => strtoupper(fake()->bothify('???????')),
        ]);
    }
}
