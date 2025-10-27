<?php

namespace Database\Factories;

use App\Enums\PaymentMethodEnum;
use App\Models\AtecoCode;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 100, 5000);
        $withholdingTax = fake()->boolean(30) ? $amount * 0.20 : 0; // 30% chance of withholding
        $netAmount = $amount - $withholdingTax;

        $services = [
            'Sviluppo applicazione web',
            'Consulenza informatica',
            'Design sito web',
            'Manutenzione software',
            'Formazione tecnica',
            'Analisi dei requisiti',
            'Testing applicazione',
            'Implementazione sistema',
            'Consulenza strategica',
            'Supporto tecnico',
        ];

        return [
            'user_id' => User::factory(),
            'customer_id' => fake()->boolean(80) ? Customer::factory() : null, // 80% with customer
            'ateco_code_id' => AtecoCode::factory(),
            'invoice_number' => $this->generateInvoiceNumber(),
            'issue_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'payment_date' => fake()->boolean(70) ? fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d') : null,
            'description' => fake()->randomElement($services) . ' - ' . fake('it_IT')->sentence(6),
            'amount' => $amount,
            'withholding_tax' => $withholdingTax,
            'net_amount' => $netAmount,
            'is_paid' => fake()->boolean(75), // 75% paid
            'payment_method' => fake()->randomElement(PaymentMethodEnum::cases()),
            'notes' => fake()->boolean(20) ? fake('it_IT')->sentence() : null,
        ];
    }

    /**
     * Generate realistic Italian invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $number = fake()->numberBetween(1, 999);
        return "{$year}/" . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Invoice that is paid
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => true,
            'payment_date' => fake()->dateTimeBetween($attributes['issue_date'], 'now')->format('Y-m-d'),
            'payment_method' => fake()->randomElement(PaymentMethodEnum::cases()),
        ]);
    }

    /**
     * Invoice that is unpaid
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => false,
            'payment_date' => null,
            'payment_method' => null,
        ]);
    }

    /**
     * Invoice with withholding tax
     */
    public function withWithholding(): static
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'];
            $withholdingTax = $amount * 0.20;
            return [
                'withholding_tax' => $withholdingTax,
                'net_amount' => $amount - $withholdingTax,
            ];
        });
    }

    /**
     * Invoice without withholding tax
     */
    public function withoutWithholding(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'withholding_tax' => 0.00,
                'net_amount' => $attributes['amount'],
            ];
        });
    }

    /**
     * High value invoice
     */
    public function highValue(): static
    {
        return $this->state(function (array $attributes) {
            $amount = fake()->randomFloat(2, 3000, 15000);
            $withholdingTax = fake()->boolean(50) ? $amount * 0.20 : 0;
            return [
                'amount' => $amount,
                'withholding_tax' => $withholdingTax,
                'net_amount' => $amount - $withholdingTax,
            ];
        });
    }

    /**
     * Recent invoice (last 3 months)
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'issue_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ]);
    }
}
