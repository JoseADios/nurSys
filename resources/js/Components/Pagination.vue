<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    pagination: Object
})
</script>

<template>
    <nav class="relative py-1 flex justify-center text-gray-700 bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
        <template v-for="link in pagination.links" :key="link.label">
            <Link preserve-scroll :href="link.url ?? ''" v-html="link.label"
                class="flex items-center justify-center px-3 py-2 text-sm rounded-lg text-gray-600"
                :class="{ 'bg-gray-200': link.active, '!text-gray-300': !link.url }" />
        </template>
    </nav>
</template>
