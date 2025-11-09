<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import recipesRoute from '@/routes/recipes';

const form = useForm({
    name: '',
    cuisine_type: '',
    ingredients: '',
    steps: '',
    picture: null as File | null,
});

const picturePreview = ref<string | null>(null);

const handlePictureChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.picture = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            picturePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(recipesRoute.store.url(), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create Recipe" />

    <AppLayout>
        <div class="container mx-auto px-4 py-8 max-w-3xl">
            <!-- Back Button -->
            <Link :href="recipesRoute.index.url()" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Recipes
            </Link>

            <Card>
                <CardHeader>
                    <CardTitle class="text-3xl">Create New Recipe</CardTitle>
                    <CardDescription>Share your favorite recipe with the community</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Recipe Name -->
                        <div class="space-y-2">
                            <Label for="name">Recipe Name *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., Chocolate Chip Cookies"
                                required
                            />
                            <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                        </div>

                        <!-- Cuisine Type -->
                        <div class="space-y-2">
                            <Label for="cuisine_type">Cuisine Type *</Label>
                            <Input
                                id="cuisine_type"
                                v-model="form.cuisine_type"
                                type="text"
                                placeholder="e.g., Italian, Mexican, Asian"
                                required
                            />
                            <p v-if="form.errors.cuisine_type" class="text-sm text-destructive">{{ form.errors.cuisine_type }}</p>
                        </div>

                        <!-- Ingredients -->
                        <div class="space-y-2">
                            <Label for="ingredients">Ingredients *</Label>
                            <Textarea
                                id="ingredients"
                                v-model="form.ingredients"
                                placeholder="Enter each ingredient on a new line&#10;e.g.,&#10;2 cups flour&#10;1 cup sugar&#10;3 eggs"
                                rows="8"
                                required
                            />
                            <p class="text-sm text-muted-foreground">Enter each ingredient on a new line</p>
                            <p v-if="form.errors.ingredients" class="text-sm text-destructive">{{ form.errors.ingredients }}</p>
                        </div>

                        <!-- Steps -->
                        <div class="space-y-2">
                            <Label for="steps">Instructions *</Label>
                            <Textarea
                                id="steps"
                                v-model="form.steps"
                                placeholder="Enter each step on a new line&#10;e.g.,&#10;Preheat oven to 350°F&#10;Mix dry ingredients&#10;Add wet ingredients"
                                rows="8"
                                required
                            />
                            <p class="text-sm text-muted-foreground">Enter each step on a new line</p>
                            <p v-if="form.errors.steps" class="text-sm text-destructive">{{ form.errors.steps }}</p>
                        </div>

                        <!-- Picture -->
                        <div class="space-y-2">
                            <Label for="picture">Recipe Picture</Label>
                            <Input
                                id="picture"
                                type="file"
                                accept="image/*"
                                @change="handlePictureChange"
                            />
                            <p class="text-sm text-muted-foreground">Upload an image of your recipe (max 2MB)</p>
                            <p v-if="form.errors.picture" class="text-sm text-destructive">{{ form.errors.picture }}</p>

                            <!-- Image Preview -->
                            <div v-if="picturePreview" class="mt-4">
                                <img :src="picturePreview" alt="Preview" class="max-w-md rounded-lg shadow-md" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-4 pt-4">
                            <Button type="submit" :disabled="form.processing">
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Recipe</span>
                            </Button>
                            <Link :href="recipesRoute.index.url()">
                                <Button type="button" variant="outline">Cancel</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
