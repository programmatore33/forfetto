<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 10, 2000);
        $vatAmount = fake()->boolean(60) ? $amount * 0.22 : 0; // 60% chance of VAT

        $expenseDescriptions = [
            'Software' => [
                'Abbonamento Adobe Creative Suite',
                'Licenza Microsoft Office',
                'Hosting servizio web',
                'Dominio internet annuale',
                'Software gestionale',
            ],
            'Hardware' => [
                'Computer portatile',
                'Monitor esterno',
                'Tastiera wireless',
                'Hard disk esterno',
                'Webcam HD',
            ],
            'Trasporti' => [
                'Rifornimento carburante',
                'Pedaggi autostradali',
                'Biglietto treno',
                'Parcheggio centro città',
                'Taxi aeroporto',
            ],
            'Formazione' => [
                'Corso online programmazione',
                'Libro tecnico specialistico',
                'Seminario marketing digitale',
                'Conferenza settore',
                'Certificazione professionale',
            ],
            'Marketing' => [
                'Campagna Google Ads',
                'Servizio social media',
                'Stampa biglietti da visita',
                'Materiale promozionale',
                'Servizio fotografico prodotti',
            ],
        ];

        $categories = array_keys($expenseDescriptions);
        $selectedCategory = fake()->randomElement($categories);
        $description = fake()->randomElement($expenseDescriptions[$selectedCategory]);

        $suppliers = [
            'Amazon Italia',
            'MediaWorld',
            'Unieuro',
            'Adobe Systems',
            'Microsoft Italia',
            'Telecom Italia',
            'ENI',
            'Autostrade per l\'Italia',
            'Trenitalia',
        ];

        return [
            'user_id' => User::factory(),
            'expense_category_id' => ExpenseCategory::factory(),
            'expense_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'description' => $description,
            'supplier' => fake()->boolean(80) ? fake()->randomElement($suppliers) : null,
            'amount' => $amount,
            'vat_amount' => $vatAmount,
            'is_deductible' => fake()->boolean(85), // 85% deductible
            'notes' => fake()->boolean(25) ? fake('it_IT')->sentence() : null,
        ];
    }

    /**
     * Software expense.
     */
    public function software(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->randomElement([
                'Abbonamento Adobe Creative Suite',
                'Licenza Microsoft Office',
                'Hosting servizio web',
                'Software gestionale',
            ]),
            'supplier' => fake()->randomElement(['Adobe Systems', 'Microsoft Italia', 'Aruba']),
            'amount' => fake()->randomFloat(2, 30, 200),
        ]);
    }

    /**
     * Transport expense.
     */
    public function transport(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->randomElement([
                'Rifornimento carburante',
                'Pedaggi autostradali',
                'Biglietto treno',
                'Parcheggio',
            ]),
            'supplier' => fake()->randomElement(['ENI', 'Autostrade per l\'Italia', 'Trenitalia']),
            'amount' => fake()->randomFloat(2, 15, 150),
        ]);
    }

    /**
     * High value expense.
     */
    public function highValue(): static
    {
        return $this->state(function (array $attributes) {
            $amount = fake()->randomFloat(2, 1000, 5000);

            return [
                'amount' => $amount,
                'vat_amount' => $amount * 0.22,
                'description' => fake()->randomElement([
                    'Computer portatile professionale',
                    'Attrezzatura fotografica',
                    'Mobili ufficio',
                    'Software Enterprise',
                ]),
            ];
        });
    }

    /**
     * Recent expense (last 3 months).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'expense_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ]);
    }

    /**
     * Non-deductible expense.
     */
    public function nonDeductible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_deductible' => false,
            'description' => fake()->randomElement([
                'Pranzo lavoro non deducibile',
                'Multa parcheggio',
                'Spesa personale erroneamente inserita',
            ]),
        ]);
    }
}
