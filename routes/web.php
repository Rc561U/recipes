<?php

use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return redirect()->route('recipes.index');
})->name('home');

// Public recipe routes
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

// Protected recipe routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
});

// Public show route (must be after /recipes/create to avoid conflicts)
Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');

require __DIR__.'/settings.php';
