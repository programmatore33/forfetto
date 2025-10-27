<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseCategory>
 */
class ExpenseCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            ['name' => 'Software', 'description' => 'Software e servizi digitali', 'color' => '#3B82F6'],
            ['name' => 'Hardware', 'description' => 'Attrezzature e dispositivi', 'color' => '#10B981'],
            ['name' => 'Formazione', 'description' => 'Corsi e certificazioni', 'color' => '#8B5CF6'],
            ['name' => 'Marketing', 'description' => 'Pubblicità e promozione', 'color' => '#F59E0B'],
            ['name' => 'Ufficio', 'description' => 'Materiale da ufficio', 'color' => '#6B7280'],
            ['name' => 'Trasporti', 'description' => 'Viaggi e carburante', 'color' => '#EF4444'],
            ['name' => 'Consulenze', 'description' => 'Consulenze professionali', 'color' => '#14B8A6'],
            ['name' => 'Altro', 'description' => 'Spese varie', 'color' => '#64748B'],
        ];

        $category = fake()->randomElement($categories);

        return [
            'user_id' => User::factory(),
            'name' => $category['name'],
            'description' => $category['description'],
            'color' => $category['color'],
        ];
    }

    /**
     * Create a software category
     */
    public function software(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Software',
            'description' => 'Software, licenze e abbonamenti digitali',
            'color' => '#3B82F6',
        ]);
    }

    /**
     * Create a custom category with specific attributes
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }
}