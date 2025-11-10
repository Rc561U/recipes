<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Recipe Controller - Handles HTTP requests (Dependency Inversion Principle)
 * Depends on abstractions (RecipeService) rather than concrete implementations
 */
class RecipeController extends Controller
{
    public function __construct(
        private RecipeService $recipeService
    ) {
        // Dependency Injection - following Dependency Inversion Principle
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $recipes = $this->recipeService->getPaginatedRecipes(
            $request->input('search'),
            $request->input('cuisine_type')
        );

        $cuisineTypes = $this->recipeService->getUniqueCuisineTypes();

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes,
            'cuisineTypes' => $cuisineTypes,
            'filters' => $request->only(['search', 'cuisine_type']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): InertiaResponse
    {
        $this->authorize('create', Recipe::class);

        return Inertia::render('Recipes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Recipe::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cuisine_type' => 'required|string|max:255',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'picture' => 'nullable|image|max:2048',
        ]);

        $this->recipeService->createRecipe($validated, $request->user());

        return redirect()->route('recipes.index')
            ->with('success', 'Recipe created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe): InertiaResponse
    {
        $this->authorize('view', $recipe);

        $recipe->load('user');

        return Inertia::render('Recipes/Show', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe): InertiaResponse
    {
        $this->authorize('update', $recipe);

        return Inertia::render('Recipes/Edit', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe): RedirectResponse
    {
        $this->authorize('update', $recipe);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cuisine_type' => 'required|string|max:255',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'picture' => 'nullable|image|max:2048',
        ]);

        $this->recipeService->updateRecipe($recipe, $validated);

        return redirect()->route('recipes.index')
            ->with('success', 'Recipe updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe): RedirectResponse
    {
        $this->authorize('delete', $recipe);

        $this->recipeService->deleteRecipe($recipe);

        return redirect()->route('recipes.index')
            ->with('success', 'Recipe deleted successfully!');
    }
}
