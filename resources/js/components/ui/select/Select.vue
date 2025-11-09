<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    modelValue?: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const value = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val || ''),
});
</script>

<template>
    <select
        v-model="value"
        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
    >
        <slot />
    </select>
</template>
