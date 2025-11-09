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

interface Recipe {
    id: number;
    name: string;
    cuisine_type: string;
    ingredients: string;
    steps: string;
    picture: string | null;
}

interface Props {
    recipe: Recipe;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.recipe.name,
    cuisine_type: props.recipe.cuisine_type,
    ingredients: props.recipe.ingredients,
    steps: props.recipe.steps,
    picture: null as File | null,
    _method: 'PUT',
});

const picturePreview = ref<string | null>(
    props.recipe.picture ? `/storage/${props.recipe.picture}` : null
);

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
    form.post(recipesRoute.update.url(props.recipe.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit Recipe" />

    <AppLayout>
        <div class="container mx-auto px-4 py-8 max-w-3xl">
            <!-- Back Button -->
            <Link :href="recipesRoute.show.url(recipe.id)" class="inline-flex items-center text-sm text-muted-foreground hover:text-foreground mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Recipe
            </Link>

            <Card>
                <CardHeader>
                    <CardTitle class="text-3xl">Edit Recipe</CardTitle>
                    <CardDescription>Update your recipe details</CardDescription>
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
                                placeholder="Enter each ingredient on a new line"
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
                                placeholder="Enter each step on a new line"
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
                            <p class="text-sm text-muted-foreground">Upload a new image to replace the current one (max 2MB)</p>
                            <p v-if="form.errors.picture" class="text-sm text-destructive">{{ form.errors.picture }}</p>

                            <!-- Image Preview -->
                            <div v-if="picturePreview" class="mt-4">
                                <p class="text-sm font-medium mb-2">Current/Preview Image:</p>
                                <img :src="picturePreview" alt="Preview" class="max-w-md rounded-lg shadow-md" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-4 pt-4">
                            <Button type="submit" :disabled="form.processing">
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update Recipe</span>
                            </Button>
                            <Link :href="recipesRoute.show.url(recipe.id)">
                                <Button type="button" variant="outline">Cancel</Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
