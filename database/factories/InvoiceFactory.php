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
        // Contributo integrativo optionally applied (app defaults set in settings)
        $applyContributo = fake()->boolean(40);
        $contributoAmount = $applyContributo ? round($amount * 0.04, 2) : 0.00;
        $netAmount = $amount + $contributoAmount;

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

        $hasCustomer = fake()->boolean(80); // 80% with customer

        return [
            'user_id' => User::factory(),
            'customer_id' => $hasCustomer ? Customer::factory() : null,
            'ateco_code_id' => AtecoCode::factory(),
            // Customer snapshot - will be populated in configure()
            'customer_business_name' => fake('it_IT')->company(),
            'customer_email' => fake()->boolean(70) ? fake()->safeEmail() : null,
            'customer_vat_number' => fake()->boolean(80) ? fake()->numerify('###########') : null,
            'customer_tax_code' => fake()->boolean(60) ? strtoupper(fake()->bothify('??????##?##?###?')) : null,
            'customer_address' => fake()->boolean(60) ? fake('it_IT')->streetAddress() : null,
            'customer_city' => fake()->boolean(70) ? fake('it_IT')->city() : null,
            'customer_province' => fake()->boolean(70) ? strtoupper(fake()->lexify('??')) : null,
            'customer_postal_code' => fake()->boolean(70) ? fake()->numerify('#####') : null,
            'customer_country' => 'IT',
            'customer_phone' => fake()->boolean(60) ? fake('it_IT')->phoneNumber() : null,
            'customer_pec' => fake()->boolean(40) ? fake()->safeEmail() : null,
            'customer_sdi_code' => fake()->boolean(40) ? strtoupper(fake()->bothify('?######')) : null,
            'invoice_number' => $this->generateInvoiceNumber(),
            'issue_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'payment_date' => fake()->boolean(70) ? fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d') : null,
            'description' => fake()->randomElement($services).' - '.fake('it_IT')->sentence(6),
            'amount' => $amount,
            'contributo_integrativo_applied' => $applyContributo,
            'contributo_integrativo_amount' => $contributoAmount,
            'net_amount' => $netAmount,
            'is_paid' => fake()->boolean(75), // 75% paid
            'payment_method' => fake()->randomElement(PaymentMethodEnum::cases()),
            'notes' => fake()->boolean(20) ? fake('it_IT')->sentence() : null,
        ];
    }

    /**
     * Generate realistic Italian invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $number = fake()->numberBetween(1, 999);

        return "{$year}/".str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Invoice that is paid.
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
     * Invoice that is unpaid.
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
     * Invoice with withholding tax.
     */
    public function withWithholding(): static
    {
        // Helper for compatibility: ensure net_amount equals amount (no extra logic)
        return $this->state(function (array $attributes) {
            return [
                'contributo_integrativo_applied' => $attributes['contributo_integrativo_applied'] ?? false,
                'contributo_integrativo_amount' => $attributes['contributo_integrativo_amount'] ?? 0.00,
                'net_amount' => $attributes['amount'],
            ];
        });
    }

    /**
     * Invoice without withholding tax.
     */
    public function withoutWithholding(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'contributo_integrativo_applied' => false,
                'contributo_integrativo_amount' => 0.00,
                'net_amount' => $attributes['amount'],
            ];
        });
    }

    /**
     * High value invoice.
     */
    public function highValue(): static
    {
        return $this->state(function (array $attributes) {
            $amount = fake()->randomFloat(2, 3000, 15000);

            return [
                'amount' => $amount,
                'contributo_integrativo_applied' => false,
                'contributo_integrativo_amount' => 0.00,
                'net_amount' => $amount,
            ];
        });
    }

    /**
     * Recent invoice (last 3 months).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'issue_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ]);
    }

    /**
     * Invoice with items (1-5 items)
     */
    public function withItems(?int $itemCount = null): static
    {
        return $this->afterCreating(function ($invoice) use ($itemCount) {
            $count = $itemCount ?? fake()->numberBetween(1, 5);

            $items = [];
            for ($i = 0; $i < $count; $i++) {
                $unitPrice = fake()->randomFloat(2, 50, 2000);
                $quantity = fake()->numberBetween(1, 5);
                $total = round($unitPrice * $quantity, 2);

                $items[] = [
                    'invoice_id' => $invoice->id,
                    'product_id' => null,
                    'code' => fake()->optional(0.6)->regexify('[A-Z]{3}-[0-9]{3}'),
                    'description' => fake()->randomElement([
                        'Sviluppo sito web aziendale',
                        'Consulenza tecnica specialistica',
                        'Manutenzione server mensile',
                        'Grafica e design logo',
                        'Gestione social media',
                        'Creazione contenuti SEO',
                        'Analisi e ottimizzazione',
                        'Formazione personale tecnico',
                    ]),
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'total' => $total,
                    'sort_order' => $i,
                ];
            }

            $invoice->items()->createMany($items);

            // Recalculate invoice amount from items
            $invoice->amount = $invoice->items->sum('total');
            $invoice->net_amount = $invoice->amount + ($invoice->contributo_integrativo_applied ? $invoice->contributo_integrativo_amount : 0);
            $invoice->saveQuietly();
        });
    }

    /**
     * Configure the factory to populate customer snapshot from relationship.
     */
    public function configure(): static
    {
        return $this->afterMaking(function ($invoice) {
            // If customer_id is set, populate snapshot from customer
            if ($invoice->customer_id && $invoice->customer) {
                $customer = $invoice->customer;
                $invoice->customer_business_name = $customer->business_name;
                $invoice->customer_email = $customer->email;
                $invoice->customer_vat_number = $customer->vat_number;
                $invoice->customer_tax_code = $customer->tax_code;
                $invoice->customer_address = $customer->address;
                $invoice->customer_city = $customer->city;
                $invoice->customer_province = $customer->province;
                $invoice->customer_postal_code = $customer->postal_code;
                $invoice->customer_country = $customer->country;
                $invoice->customer_phone = $customer->phone;
                $invoice->customer_pec = $customer->pec;
                $invoice->customer_sdi_code = $customer->sdi_code;
            }
        });
    }
}
