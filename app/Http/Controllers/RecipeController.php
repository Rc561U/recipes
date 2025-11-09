<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Recipe::with('user');

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by cuisine type
        if ($request->has('cuisine_type') && $request->cuisine_type) {
            $query->where('cuisine_type', $request->cuisine_type);
        }

        // Order by creation date (newest first)
        $recipes = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get unique cuisine types for filter
        $cuisineTypes = Recipe::distinct()->pluck('cuisine_type');

        return Inertia::render('Recipes/Index', [
            'recipes' => $recipes,
            'cuisineTypes' => $cuisineTypes,
            'filters' => $request->only(['search', 'cuisine_type']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Recipes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cuisine_type' => 'required|string|max:255',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'picture' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('recipes', 'public');
        }

        Recipe::create($validated);

        return redirect()->route('recipes.index')->with('success', 'Recipe created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $recipe->load('user');

        return Inertia::render('Recipes/Show', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        // Check authorization
        if (!auth()->user()->isAdmin() && $recipe->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Recipes/Edit', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        // Check authorization
        if (!auth()->user()->isAdmin() && $recipe->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cuisine_type' => 'required|string|max:255',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            // Delete old picture
            if ($recipe->picture) {
                Storage::disk('public')->delete($recipe->picture);
            }
            $validated['picture'] = $request->file('picture')->store('recipes', 'public');
        }

        $recipe->update($validated);

        return redirect()->route('recipes.index')->with('success', 'Recipe updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        // Check authorization
        if (!auth()->user()->isAdmin() && $recipe->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete picture if exists
        if ($recipe->picture) {
            Storage::disk('public')->delete($recipe->picture);
        }

        $recipe->delete();

        return redirect()->route('recipes.index')->with('success', 'Recipe deleted successfully!');
    }
}
