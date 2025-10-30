<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    /*
     * Customer Routes
     */
    Route::resource('customers', CustomerController::class);

    /*
     * Color Palette Route
     */
    Route::get('colors', function () {
        return Inertia::render('ColorPalette');
    })->name('colors');
});

require __DIR__.'/settings.php';
