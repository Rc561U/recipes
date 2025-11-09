<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
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
    recipes: {
        data: Recipe[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    cuisineTypes: string[];
    filters: {
        search?: string;
        cuisine_type?: string;
    };
    auth?: {
        user: User | null;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const cuisineType = ref(props.filters.cuisine_type || '');

const handleSearch = () => {
    router.get('/recipes', {
        search: search.value,
        cuisine_type: cuisineType.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    cuisineType.value = '';
    router.get('/recipes', {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const getImageUrl = (picture: string | null) => {
    if (!picture) return '/placeholder-recipe.jpg';
    return `/storage/${picture}`;
};
</script>

<template>
    <Head title="Recipes" />

    <AppLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold">Recipe Book</h1>
                    <p class="text-muted-foreground mt-2">Discover and share amazing recipes</p>
                </div>
                <Link v-if="auth?.user" :href="recipesRoute.create.url()">
                    <Button>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add Recipe
                    </Button>
                </Link>
            </div>

            <!-- Search and Filter -->
            <Card class="mb-8">
                <CardContent class="pt-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <Input
                                v-model="search"
                                type="text"
                                placeholder="Search recipes by name..."
                                @keyup.enter="handleSearch"
                            />
                        </div>
                        <div class="w-full md:w-64">
                            <select
                                v-model="cuisineType"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="">All Cuisines</option>
                                <option v-for="type in cuisineTypes" :key="type" :value="type">
                                    {{ type }}
                                </option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <Button @click="handleSearch">Search</Button>
                            <Button variant="outline" @click="clearFilters">Clear</Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Recipes Grid -->
            <div v-if="recipes.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <Card v-for="recipe in recipes.data" :key="recipe.id" class="overflow-hidden hover:shadow-lg transition-shadow">
                    <Link :href="recipesRoute.show.url(recipe.id)">
                        <div class="aspect-video w-full overflow-hidden bg-muted">
                            <img
                                :src="getImageUrl(recipe.picture)"
                                :alt="recipe.name"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </Link>
                    <CardHeader>
                        <CardTitle class="line-clamp-1">
                            <Link :href="recipesRoute.show.url(recipe.id)" class="hover:underline">
                                {{ recipe.name }}
                            </Link>
                        </CardTitle>
                        <CardDescription>
                            <Badge variant="secondary">{{ recipe.cuisine_type }}</Badge>
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="flex justify-between items-center">
                        <span class="text-sm text-muted-foreground">
                            By {{ recipe.user.name }}
                        </span>
                        <div v-if="auth?.user && (auth.user.id === recipe.user_id || auth.user.role === 'admin')" class="flex gap-2">
                            <Link :href="recipesRoute.edit.url(recipe.id)">
                                <Button variant="outline" size="sm">Edit</Button>
                            </Link>
                        </div>
                    </CardFooter>
                </Card>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-muted-foreground mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-xl font-semibold mb-2">No recipes found</h3>
                <p class="text-muted-foreground mb-4">Be the first to share a recipe!</p>
                <Link v-if="auth?.user" :href="recipesRoute.create.url()">
                    <Button>Add Your First Recipe</Button>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="recipes.last_page > 1" class="mt-8 flex justify-center gap-2">
                <Button
                    v-for="page in recipes.last_page"
                    :key="page"
                    :variant="page === recipes.current_page ? 'default' : 'outline'"
                    @click="router.get(`/recipes?page=${page}`)"
                >
                    {{ page }}
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
