<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AtecoCode>
 */
class AtecoCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $atecoCodes = [
            ['code' => '62.01.00', 'description' => 'Sviluppo software', 'coeff' => 78.00],
            ['code' => '62.02.00', 'description' => 'Consulenza informatica', 'coeff' => 78.00],
            ['code' => '73.11.00', 'description' => 'Agenzie di pubblicità', 'coeff' => 67.00],
            ['code' => '74.10.00', 'description' => 'Design specializzato', 'coeff' => 78.00],
            ['code' => '74.20.00', 'description' => 'Fotografia', 'coeff' => 78.00],
            ['code' => '85.59.20', 'description' => 'Corsi di formazione', 'coeff' => 67.00],
            ['code' => '47.91.10', 'description' => 'Commercio elettronico', 'coeff' => 67.00],
            ['code' => '69.20.00', 'description' => 'Contabilità e consulenza fiscale', 'coeff' => 78.00],
            ['code' => '70.22.00', 'description' => 'Consulenza gestionale', 'coeff' => 78.00],
            ['code' => '95.11.00', 'description' => 'Riparazione computer', 'coeff' => 86.00],
            ['code' => '43.22.00', 'description' => 'Installazioni idrauliche', 'coeff' => 86.00],
            ['code' => '43.21.00', 'description' => 'Installazioni elettriche', 'coeff' => 86.00],
        ];

        $selectedCode = fake()->randomElement($atecoCodes);

        return [
            'user_id' => User::factory(),
            'ateco_code' => $selectedCode['code'],
            'description' => $selectedCode['description'],
            'profitability_coeff' => $selectedCode['coeff'],
            'is_primary' => fake()->boolean(70), // 70% chance of being primary
        ];
    }

    /**
     * Indicate that this ATECO code is the primary activity.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Indicate that this ATECO code is secondary.
     */
    public function secondary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => false,
        ]);
    }

    /**
     * Generate software development ATECO.
     */
    public function software(): static
    {
        return $this->state(fn (array $attributes) => [
            'ateco_code' => '62.01.00',
            'description' => 'Sviluppo software',
            'profitability_coeff' => 78.00,
        ]);
    }

    /**
     * Generate artisan ATECO (higher coefficient).
     */
    public function artisan(): static
    {
        $artisanCodes = [
            ['code' => '43.22.00', 'description' => 'Installazioni idrauliche'],
            ['code' => '43.21.00', 'description' => 'Installazioni elettriche'],
            ['code' => '95.11.00', 'description' => 'Riparazione computer'],
        ];

        $selected = fake()->randomElement($artisanCodes);

        return $this->state(fn (array $attributes) => [
            'ateco_code' => $selected['code'],
            'description' => $selected['description'],
            'profitability_coeff' => 86.00,
        ]);
    }
}
