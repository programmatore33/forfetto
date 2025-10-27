<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ExpenseCategoryService;
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
        ExpenseCategoryService::createDefaultCategories($this->user);
    }
}
