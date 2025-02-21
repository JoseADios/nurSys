<template>
    <div class="flex items-start space-x-3">
        <div>
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ label }}</h4>
            <p class="text-gray-900 dark:text-gray-100">{{ value || 'N/A' }}</p>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        label: {
            type: String,
            required: true,
        },
        value: {
            type: [String, Number],
            default: 'N/A',
        },
    },
});
</script>

<style scoped>
/* Tus estilos aquí */
</style>
