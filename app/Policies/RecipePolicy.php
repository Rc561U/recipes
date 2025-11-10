<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;

/**
 * Recipe Policy - Authorization logic (Open/Closed Principle)
 * Separates authorization concerns from business logic
 */
class RecipePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view recipes (including guests)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Recipe $recipe): bool
    {
        // Anyone can view a recipe (including guests)
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create recipes
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Recipe $recipe): bool
    {
        // User can update if they own the recipe or are admin
        return $user->isAdmin() || $recipe->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Recipe $recipe): bool
    {
        // User can delete if they own the recipe or are admin
        return $user->isAdmin() || $recipe->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Recipe $recipe): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Recipe $recipe): bool
    {
        return $user->isAdmin();
    }
}
