<script setup lang="ts">
interface Props {
    value: string;
}

defineProps<Props>();
</script>

<template>
    <option :value="value">
        <slot />
    </option>
</template>
