<?php

namespace App\Jobs;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateDefaultExpenseCategories implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private User $user
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $defaultCategories = [
            ['name' => 'Software', 'description' => 'Software, licenze e abbonamenti digitali', 'color' => '#3B82F6'],
            ['name' => 'Hardware', 'description' => 'Computer, periferiche e attrezzature tecniche', 'color' => '#6B7280'],
            ['name' => 'Trasporti', 'description' => 'Benzina, pedaggi, mezzi pubblici', 'color' => '#10B981'],
            ['name' => 'Formazione', 'description' => 'Corsi, libri, conferenze', 'color' => '#F59E0B'],
            ['name' => 'Materiale Ufficio', 'description' => 'Cancelleria, stampanti, materiali vari', 'color' => '#8B5CF6'],
            ['name' => 'Marketing', 'description' => 'Pubblicità, social media, promozioni', 'color' => '#EF4444'],
            ['name' => 'Consulenze', 'description' => 'Consulenti, professionisti esterni', 'color' => '#14B8A6'],
            ['name' => 'Utenze', 'description' => 'Internet, telefono, energia elettrica', 'color' => '#F97316'],
            ['name' => 'Affitto', 'description' => 'Affitto ufficio o spazi lavorativi', 'color' => '#84CC16'],
            ['name' => 'Assicurazioni', 'description' => 'Assicurazioni professionali e RC', 'color' => '#06B6D4'],
            ['name' => 'Tasse e Tributi', 'description' => 'INPS, imposte, bolli', 'color' => '#DC2626'],
            ['name' => 'Altro', 'description' => 'Spese non categorizzabili', 'color' => '#6B7280'],
        ];

        foreach ($defaultCategories as $category) {
            ExpenseCategory::create([
                'user_id' => $this->user->id,
                'name' => $category['name'],
                'description' => $category['description'],
                'color' => $category['color'],
            ]);
        }
    }
}
