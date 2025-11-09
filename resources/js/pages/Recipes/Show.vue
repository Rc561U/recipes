<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import recipesRoute from '@/routes/recipes';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface Recipe {
    id: number;
    name: string;
    cuisine_type: string;
    ingredients: string;
    steps: string;
    picture: string | null;
    user_id: number;
    user: User;
    created_at: string;
}

interface Props {
    recipe: Recipe;
    auth?: {
        user: User | null;
    };
}

const props = defineProps<Props>();

const getImageUrl = (picture: string | null) => {
    if (!picture) return '/placeholder-recipe.jpg';
    return `/storage/${picture}`;
};

const deleteRecipe = () => {
    if (confirm('Are you sure you want to delete this recipe?')) {
        router.delete(recipesRoute.destroy.url(props.recipe.id));
    }
};

const ingredientsList = props.recipe.ingredients.split('\n').filter(i => i.trim());
const stepsList = props.recipe.steps.split('\n').filter(s => s.trim());
</script>

<template>
    <Head :title="recipe.name" />

    <AppLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Back Button -->
            <Link :href="recipesRoute.index.url()" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Recipes
            </Link>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <Card>
                        <!-- Recipe Image -->
                        <div class="aspect-video w-full overflow-hidden bg-muted">
                            <img
                                :src="getImageUrl(recipe.picture)"
                                :alt="recipe.name"
                                class="w-full h-full object-cover"
                            />
                        </div>

                        <CardHeader>
                            <div class="flex justify-between items-start">
                                <div>
                                    <CardTitle class="text-3xl mb-2">{{ recipe.name }}</CardTitle>
                                    <CardDescription class="flex items-center gap-2">
                                        <Badge variant="secondary">{{ recipe.cuisine_type }}</Badge>
                                        <span>•</span>
                                        <span>By {{ recipe.user.name }}</span>
                                    </CardDescription>
                                </div>
                                <div v-if="auth?.user && (auth.user.id === recipe.user_id || auth.user.role === 'admin')" class="flex gap-2">
                                    <Link :href="recipesRoute.edit.url(recipe.id)">
                                        <Button variant="outline">Edit</Button>
                                    </Link>
                                    <Button variant="destructive" @click="deleteRecipe">Delete</Button>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent>
                            <!-- Ingredients -->
                            <div class="mb-8">
                                <h2 class="text-2xl font-semibold mb-4">Ingredients</h2>
                                <ul class="space-y-2">
                                    <li v-for="(ingredient, index) in ingredientsList" :key="index" class="flex items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ ingredient }}</span>
                                    </li>
                                </ul>
                            </div>

                            <Separator class="my-8" />

                            <!-- Steps -->
                            <div>
                                <h2 class="text-2xl font-semibold mb-4">Instructions</h2>
                                <ol class="space-y-4">
                                    <li v-for="(step, index) in stepsList" :key="index" class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 rounded-full bg-primary text-primary-foreground flex items-center justify-center font-semibold mr-4">
                                            {{ index + 1 }}
                                        </span>
                                        <p class="pt-1">{{ step }}</p>
                                    </li>
                                </ol>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <Card>
                        <CardHeader>
                            <CardTitle>Recipe Details</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div>
                                <h3 class="font-semibold text-sm text-muted-foreground mb-1">Cuisine Type</h3>
                                <p>{{ recipe.cuisine_type }}</p>
                            </div>
                            <Separator />
                            <div>
                                <h3 class="font-semibold text-sm text-muted-foreground mb-1">Created By</h3>
                                <p>{{ recipe.user.name }}</p>
                            </div>
                            <Separator />
                            <div>
                                <h3 class="font-semibold text-sm text-muted-foreground mb-1">Created On</h3>
                                <p>{{ new Date(recipe.created_at).toLocaleDateString() }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
