<?php

namespace Tests\Unit;

use App\Models\Recipe;
use App\Models\User;
use App\Policies\RecipePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Recipe Policy Unit Tests
 * Testing authorization logic in isolation
 */
class RecipePolicyTest extends TestCase
{
    use RefreshDatabase;

    private RecipePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new RecipePolicy();
    }

    /**
     * Test: Anyone can view recipes
     */
    public function test_anyone_can_view_recipes(): void
    {
        $recipe = Recipe::factory()->create();

        // Guest user
        $this->assertTrue($this->policy->view(null, $recipe));

        // Authenticated user
        $user = User::factory()->create();
        $this->assertTrue($this->policy->view($user, $recipe));
    }

    /**
     * Test: Authenticated users can create recipes
     */
    public function test_authenticated_users_can_create_recipes(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($this->policy->create($user));
    }

    /**
     * Test: Owner can update their recipe
     */
    public function test_owner_can_update_their_recipe(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $recipe = Recipe::factory()->create(['user_id' => $owner->id]);

        $this->assertTrue($this->policy->update($owner, $recipe));
    }

    /**
     * Test: Admin can update any recipe
     */
    public function test_admin_can_update_any_recipe(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherUser = User::factory()->create(['role' => 'user']);
        $recipe = Recipe::factory()->create(['user_id' => $otherUser->id]);

        $this->assertTrue($this->policy->update($admin, $recipe));
    }

    /**
     * Test: Non-owner cannot update recipe
     */
    public function test_non_owner_cannot_update_recipe(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create(['role' => 'user']);
        $recipe = Recipe::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($this->policy->update($otherUser, $recipe));
    }

    /**
     * Test: Owner can delete their recipe
     */
    public function test_owner_can_delete_their_recipe(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $recipe = Recipe::factory()->create(['user_id' => $owner->id]);

        $this->assertTrue($this->policy->delete($owner, $recipe));
    }

    /**
     * Test: Admin can delete any recipe
     */
    public function test_admin_can_delete_any_recipe(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherUser = User::factory()->create(['role' => 'user']);
        $recipe = Recipe::factory()->create(['user_id' => $otherUser->id]);

        $this->assertTrue($this->policy->delete($admin, $recipe));
    }

    /**
     * Test: Non-owner cannot delete recipe
     */
    public function test_non_owner_cannot_delete_recipe(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create(['role' => 'user']);
        $recipe = Recipe::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($this->policy->delete($otherUser, $recipe));
    }
}
