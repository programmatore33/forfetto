<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Italian services and products common for freelancers
     */
    private array $italianServices = [
        ['code' => 'WEB-001', 'name' => 'Sviluppo Sito Web', 'description' => 'Creazione e sviluppo di sito web responsive', 'price' => 1500.00],
        ['code' => 'WEB-002', 'name' => 'Consulenza Web Development', 'description' => 'Consulenza tecnica per progetti web', 'price' => 80.00],
        ['code' => 'APP-001', 'name' => 'Sviluppo App Mobile', 'description' => 'Sviluppo applicazione mobile iOS/Android', 'price' => 3000.00],
        ['code' => 'CONS-001', 'name' => 'Consulenza IT', 'description' => 'Consulenza informatica generale', 'price' => 70.00],
        ['code' => 'CONS-002', 'name' => 'Analisi Requisiti', 'description' => 'Analisi e documentazione requisiti progettuali', 'price' => 500.00],
        ['code' => 'MKT-001', 'name' => 'Gestione Social Media', 'description' => 'Gestione profili social media (mensile)', 'price' => 350.00],
        ['code' => 'MKT-002', 'name' => 'Campagna Google Ads', 'description' => 'Creazione e gestione campagna pubblicitaria', 'price' => 600.00],
        ['code' => 'GRAPH-001', 'name' => 'Logo Design', 'description' => 'Progettazione logo aziendale', 'price' => 400.00],
        ['code' => 'GRAPH-002', 'name' => 'Brochure Aziendale', 'description' => 'Design e impaginazione brochure', 'price' => 250.00],
        ['code' => 'MAINT-001', 'name' => 'Manutenzione Sito Web', 'description' => 'Manutenzione mensile sito web', 'price' => 150.00],
        ['code' => 'SEO-001', 'name' => 'Ottimizzazione SEO', 'description' => 'Ottimizzazione SEO on-page e off-page', 'price' => 800.00],
        ['code' => 'TRAIN-001', 'name' => 'Formazione WordPress', 'description' => 'Corso di formazione WordPress (4 ore)', 'price' => 300.00],
        ['code' => 'HOST-001', 'name' => 'Hosting Web Annuale', 'description' => 'Servizio hosting web professionale', 'price' => 120.00],
        ['code' => 'DOM-001', 'name' => 'Registrazione Dominio', 'description' => 'Registrazione dominio .it o .com', 'price' => 15.00],
        ['code' => 'COPY-001', 'name' => 'Redazione Testi', 'description' => 'Scrittura contenuti per sito web', 'price' => 100.00],
    ];

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $service = fake()->randomElement($this->italianServices);

        return [
            'user_id' => User::factory(),
            'session_id' => null,
            'code' => $service['code'],
            'name' => $service['name'],
            'description' => $service['description'],
            'unit_price' => $service['price'],
        ];
    }

    /**
     * Product without code
     */
    public function withoutCode(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => null,
        ]);
    }

    /**
     * Product for demo session
     */
    public function forDemoSession(string $sessionId): static
    {
        return $this->state(fn (array $attributes) => [
            'session_id' => $sessionId,
        ]);
    }
}
