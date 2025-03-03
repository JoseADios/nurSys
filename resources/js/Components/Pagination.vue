<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    pagination: Object,
    filters: Object
});

const filterQuery = computed(() => {
    return new URLSearchParams(props.filters).toString();
});
</script>

<template>
    <div class="flex flex-col sm:flex-row items-center justify-between py-3 px-4 bg-white dark:bg-gray-800">
        <div class="text-sm text-gray-700 dark:text-gray-300 mb-2 sm:mb-0">
            Mostrando
            <span class="font-medium">{{ ((pagination.current_page - 1) * pagination.per_page) + 1 }}</span>
            a
            <span class="font-medium">{{
                Math.min(pagination.current_page * pagination.per_page, pagination.total)
                }}</span>
            de
            <span class="font-medium">{{ pagination.total }}</span>
            resultados
        </div>

        <div class="inline-flex space-x-1">
            <template v-if="pagination.prev_page_url">
                <Link :href="pagination.prev_page_url + '&' + filterQuery" preserve-scroll
                    class="px-3 py-1 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700">
                Anterior
                </Link>
            </template>
            <template v-else>
                <button disabled
                    class="px-3 py-1 rounded-md text-sm font-medium text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-700 cursor-not-allowed opacity-50">
                    Anterior
                </button>
            </template>

            <template v-for="(link, index) in pagination.links" :key="index">
                <template v-if="index !== 0 && index !== pagination.links.length - 1">
                    <Link v-if="link.url" :href="link.url + '&' + filterQuery" preserve-scroll :class="{
                        'bg-blue-500 dark:bg-blue-600 text-white': link.active,
                        'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600': !link.active
                    }" class="px-3 py-1 rounded-md text-sm font-medium" v-html="link.label" />
                </template>
            </template>

            <template v-if="pagination.next_page_url">
                <Link :href="pagination.next_page_url + '&' + filterQuery" preserve-scroll
                    class="px-3 py-1 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700">
                Siguiente
                </Link>
            </template>
            <template v-else>
                <button disabled
                    class="px-3 py-1 rounded-md text-sm font-medium text-gray-400 dark:text-gray-500 bg-gray-200 dark:bg-gray-700 cursor-not-allowed opacity-50">
                    Siguiente
                </button>
            </template>
        </div>
    </div>
</template>
