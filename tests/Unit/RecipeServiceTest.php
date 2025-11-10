<?php

namespace Tests\Unit;

use App\Models\Recipe;
use App\Models\User;
use App\Services\RecipeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Recipe Service Unit Tests
 * Testing business logic in isolation
 */
class RecipeServiceTest extends TestCase
{
    use RefreshDatabase;

    private RecipeService $recipeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recipeService = new RecipeService();
        Storage::fake('public');
    }

    /**
     * Test: Service can create a recipe
     */
    public function test_can_create_recipe(): void
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Test Recipe',
            'cuisine_type' => 'Italian',
            'ingredients' => 'Ingredient 1\nIngredient 2',
            'steps' => 'Step 1\nStep 2',
        ];

        $recipe = $this->recipeService->createRecipe($data, $user);

        $this->assertInstanceOf(Recipe::class, $recipe);
        $this->assertEquals('Test Recipe', $recipe->name);
        $this->assertEquals($user->id, $recipe->user_id);
        $this->assertDatabaseHas('recipes', [
            'name' => 'Test Recipe',
            'cuisine_type' => 'Italian',
        ]);
    }

    /**
     * Test: Service can create recipe with picture
     */
    public function test_can_create_recipe_with_picture(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('recipe.jpg');

        $data = [
            'name' => 'Recipe with Image',
            'cuisine_type' => 'Mexican',
            'ingredients' => 'Ingredient 1',
            'steps' => 'Step 1',
            'picture' => $file,
        ];

        $recipe = $this->recipeService->createRecipe($data, $user);

        $this->assertNotNull($recipe->picture);
        Storage::disk('public')->assertExists($recipe->picture);
    }

    /**
     * Test: Service can update a recipe
     */
    public function test_can_update_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'name' => 'Original Name',
        ]);

        $data = [
            'name' => 'Updated Name',
            'cuisine_type' => 'French',
            'ingredients' => 'New ingredients',
            'steps' => 'New steps',
        ];

        $updatedRecipe = $this->recipeService->updateRecipe($recipe, $data);

        $this->assertEquals('Updated Name', $updatedRecipe->name);
        $this->assertEquals('French', $updatedRecipe->cuisine_type);
        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test: Service can delete a recipe
     */
    public function test_can_delete_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $result = $this->recipeService->deleteRecipe($recipe);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
    }

    /**
     * Test: Service can check if user can modify recipe
     */
    public function test_can_check_user_can_modify_recipe(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $otherUser = User::factory()->create(['role' => 'user']);

        $recipe = Recipe::factory()->create(['user_id' => $owner->id]);

        // Owner can modify
        $this->assertTrue($this->recipeService->canModifyRecipe($owner, $recipe));

        // Admin can modify
        $this->assertTrue($this->recipeService->canModifyRecipe($admin, $recipe));

        // Other user cannot modify
        $this->assertFalse($this->recipeService->canModifyRecipe($otherUser, $recipe));
    }

    /**
     * Test: Service can get paginated recipes
     */
    public function test_can_get_paginated_recipes(): void
    {
        $user = User::factory()->create();
        Recipe::factory()->count(15)->create(['user_id' => $user->id]);

        $recipes = $this->recipeService->getPaginatedRecipes();

        $this->assertCount(12, $recipes->items()); // Default per page is 12
        $this->assertEquals(15, $recipes->total());
    }

    /**
     * Test: Service can filter recipes by search
     */
    public function test_can_filter_recipes_by_search(): void
    {
        $user = User::factory()->create();
        Recipe::factory()->create(['user_id' => $user->id, 'name' => 'Pasta Carbonara']);
        Recipe::factory()->create(['user_id' => $user->id, 'name' => 'Chicken Tikka']);
        Recipe::factory()->create(['user_id' => $user->id, 'name' => 'Pasta Bolognese']);

        $recipes = $this->recipeService->getPaginatedRecipes('Pasta');

        $this->assertCount(2, $recipes->items());
    }

    /**
     * Test: Service can filter recipes by cuisine type
     */
    public function test_can_filter_recipes_by_cuisine_type(): void
    {
        $user = User::factory()->create();
        Recipe::factory()->create(['user_id' => $user->id, 'cuisine_type' => 'Italian']);
        Recipe::factory()->create(['user_id' => $user->id, 'cuisine_type' => 'Italian']);
        Recipe::factory()->create(['user_id' => $user->id, 'cuisine_type' => 'Mexican']);

        $recipes = $this->recipeService->getPaginatedRecipes(null, 'Italian');

        $this->assertCount(2, $recipes->items());
    }
}
