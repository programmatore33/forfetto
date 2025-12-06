<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 50, 2000);
        $quantity = fake()->numberBetween(1, 10);
        $total = round($unitPrice * $quantity, 2);

        return [
            'invoice_id' => Invoice::factory(),
            'product_id' => null,
            'code' => fake()->optional(0.6)->regexify('[A-Z]{3}-[0-9]{3}'),
            'description' => fake()->randomElement([
                'Sviluppo sito web aziendale',
                'Consulenza tecnica',
                'Manutenzione server',
                'Grafica e design',
                'Gestione social media',
                'Creazione contenuti',
                'Analisi SEO',
                'Formazione personale',
            ]),
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'total' => $total,
            'sort_order' => 0,
        ];
    }

    /**
     * Item linked to a product
     */
    public function fromProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
            'code' => $product->code,
            'description' => $product->name,
            'unit_price' => $product->unit_price,
        ]);
    }
}
