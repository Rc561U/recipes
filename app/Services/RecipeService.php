<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Recipe Service - Handles business logic for recipe operations
 * Following Single Responsibility Principle (SRP)
 */
class RecipeService
{
    /**
     * Get paginated recipes with optional filters
     */
    public function getPaginatedRecipes(?string $search = null, ?string $cuisineType = null, int $perPage = 12): LengthAwarePaginator
    {
        $query = Recipe::with('user');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($cuisineType) {
            $query->where('cuisine_type', $cuisineType);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get all unique cuisine types
     */
    public function getUniqueCuisineTypes(): array
    {
        return Recipe::distinct()->pluck('cuisine_type')->toArray();
    }

    /**
     * Create a new recipe
     */
    public function createRecipe(array $data, User $user): Recipe
    {
        $data['user_id'] = $user->id;

        if (isset($data['picture']) && $data['picture'] instanceof UploadedFile) {
            $data['picture'] = $this->storePicture($data['picture']);
        }

        return Recipe::create($data);
    }

    /**
     * Update an existing recipe
     */
    public function updateRecipe(Recipe $recipe, array $data): Recipe
    {
        if (isset($data['picture']) && $data['picture'] instanceof UploadedFile) {
            // Delete old picture if exists
            if ($recipe->picture) {
                $this->deletePicture($recipe->picture);
            }
            $data['picture'] = $this->storePicture($data['picture']);
        }

        $recipe->update($data);
        return $recipe->fresh();
    }

    /**
     * Delete a recipe
     */
    public function deleteRecipe(Recipe $recipe): bool
    {
        if ($recipe->picture) {
            $this->deletePicture($recipe->picture);
        }

        return $recipe->delete();
    }

    /**
     * Store recipe picture
     */
    private function storePicture(UploadedFile $file): string
    {
        return $file->store('recipes', 'public');
    }

    /**
     * Delete recipe picture
     */
    private function deletePicture(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Check if user can modify recipe
     */
    public function canModifyRecipe(User $user, Recipe $recipe): bool
    {
        return $user->isAdmin() || $recipe->user_id === $user->id;
    }
}
