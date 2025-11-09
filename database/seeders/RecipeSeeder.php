<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create regular user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Create sample recipes
        Recipe::create([
            'user_id' => $user->id,
            'name' => 'Classic Spaghetti Carbonara',
            'cuisine_type' => 'Italian',
            'ingredients' => "400g spaghetti\n200g pancetta or guanciale\n4 large eggs\n100g Pecorino Romano cheese\nBlack pepper\nSalt",
            'steps' => "Cook spaghetti in salted boiling water until al dente\nCrisp the pancetta in a large pan\nBeat eggs with grated cheese and black pepper\nDrain pasta, reserving some pasta water\nToss hot pasta with pancetta\nRemove from heat and quickly mix in egg mixture\nAdd pasta water to create creamy sauce\nServe immediately with extra cheese",
        ]);

        Recipe::create([
            'user_id' => $user->id,
            'name' => 'Chicken Tikka Masala',
            'cuisine_type' => 'Indian',
            'ingredients' => "500g chicken breast\n1 cup yogurt\n2 tbsp tikka masala paste\n1 onion, diced\n3 cloves garlic\n1 can tomato sauce\n1 cup heavy cream\nFresh cilantro\nBasmati rice",
            'steps' => "Marinate chicken in yogurt and half the tikka paste for 2 hours\nGrill or pan-fry chicken until cooked\nSauté onion and garlic until soft\nAdd remaining tikka paste and cook for 1 minute\nAdd tomato sauce and simmer for 10 minutes\nStir in cream and cooked chicken\nSimmer for 5 more minutes\nGarnish with cilantro and serve with rice",
        ]);

        Recipe::create([
            'user_id' => $admin->id,
            'name' => 'Classic Beef Tacos',
            'cuisine_type' => 'Mexican',
            'ingredients' => "500g ground beef\n1 packet taco seasoning\nTaco shells\nLettuce, shredded\nTomatoes, diced\nCheddar cheese, shredded\nSour cream\nSalsa",
            'steps' => "Brown ground beef in a large skillet\nDrain excess fat\nAdd taco seasoning and water according to package\nSimmer until thickened\nWarm taco shells in oven\nFill shells with beef\nTop with lettuce, tomatoes, cheese\nAdd sour cream and salsa\nServe immediately",
        ]);

        Recipe::create([
            'user_id' => $admin->id,
            'name' => 'Pad Thai',
            'cuisine_type' => 'Thai',
            'ingredients' => "200g rice noodles\n200g shrimp or chicken\n2 eggs\n3 tbsp fish sauce\n2 tbsp tamarind paste\n2 tbsp sugar\nBean sprouts\nPeanuts, crushed\nLime wedges\nGreen onions",
            'steps' => "Soak rice noodles in warm water for 30 minutes\nHeat oil in wok and scramble eggs, set aside\nStir-fry protein until cooked\nAdd drained noodles to wok\nMix fish sauce, tamarind, and sugar\nPour sauce over noodles and toss\nAdd bean sprouts and eggs\nServe with peanuts, lime, and green onions",
        ]);

        Recipe::create([
            'user_id' => $user->id,
            'name' => 'French Onion Soup',
            'cuisine_type' => 'French',
            'ingredients' => "4 large onions, thinly sliced\n4 tbsp butter\n1 tsp sugar\n2 cloves garlic\n8 cups beef broth\n1 cup white wine\nFrench bread slices\nGruyere cheese\nFresh thyme",
            'steps' => "Melt butter in large pot over medium heat\nAdd onions and sugar, cook for 30-40 minutes until caramelized\nAdd garlic and cook 1 minute\nPour in wine and scrape bottom of pot\nAdd broth and thyme, simmer 30 minutes\nToast bread slices\nLadle soup into oven-safe bowls\nTop with bread and cheese\nBroil until cheese is bubbly and golden",
        ]);
    }
}
