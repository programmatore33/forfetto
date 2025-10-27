<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('it_IT')->name(),
            'email' => fake('it_IT')->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= 'password',
            'vat_number' => $this->generateVatNumber(),
            'tax_code' => $this->generateTaxCode(),
            'tax_rate' => fake()->randomElement([5.00, 15.00]),
            'activity_start_year' => fake()->numberBetween(2018, 2025),
            'remember_token' => Str::random(10),
            'two_factor_secret' => Str::random(10),
            'two_factor_recovery_codes' => Str::random(10),
            'two_factor_confirmed_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model does not have two-factor authentication configured.
     */
    public function withoutTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
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
        
        // Surname (3 consonants)
        $surname = substr(str_shuffle($consonants), 0, 3);
        
        // Name (3 consonants/vowels)
        $name = substr(str_shuffle($consonants), 0, 2) . substr(str_shuffle($vowels), 0, 1);
        
        // Year (2 digits)
        $year = fake()->numberBetween(50, 99);
        
        // Month (letter)
        $months = 'ABCDEHLMPRST';
        $month = $months[fake()->numberBetween(0, 11)];
        
        // Day and gender (2 digits)
        $day = fake()->numberBetween(1, 31);
        if (fake()->boolean()) { // Female
            $day += 40;
        }
        $day = str_pad((string) $day, 2, '0', STR_PAD_LEFT);
        
        // Municipality (4 characters)
        $municipality = substr(str_shuffle($consonants), 0, 1) . fake()->numberBetween(100, 999);
        
        // Check digit
        $checkDigit = substr(str_shuffle($consonants . '0123456789'), 0, 1);
        
        return $surname . $name . $year . $month . $day . $municipality . $checkDigit;
    }

    /**
     * User with reduced tax rate (5%)
     */
    public function reducedRate(): static
    {
        return $this->state(fn (array $attributes) => [
            'tax_rate' => 5.00,
            'activity_start_year' => fake()->numberBetween(2020, 2025), // Recent start
        ]);
    }

    /**
     * User with standard tax rate (15%)
     */
    public function standardRate(): static
    {
        return $this->state(fn (array $attributes) => [
            'tax_rate' => 15.00,
            'activity_start_year' => fake()->numberBetween(2015, 2019), // Older activity
        ]);
    }
}
