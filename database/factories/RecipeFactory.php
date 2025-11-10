<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cuisines = ['Italian', 'Mexican', 'Chinese', 'Indian', 'French', 'Thai', 'Japanese'];

        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'cuisine_type' => fake()->randomElement($cuisines),
            'ingredients' => implode("\n", fake()->sentences(5)),
            'steps' => implode("\n", fake()->sentences(8)),
            'picture' => null,
        ];
    }
}
