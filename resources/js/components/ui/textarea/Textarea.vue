<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    modelValue?: string;
    placeholder?: string;
    rows?: number;
    required?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    rows: 3,
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const value = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val || ''),
});
</script>

<template>
    <textarea
        v-model="value"
        :placeholder="placeholder"
        :rows="rows"
        :required="required"
        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
    />
</template>
