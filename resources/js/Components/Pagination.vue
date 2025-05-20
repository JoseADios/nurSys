<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    pagination: Object,
    filters: Object,
    maxVisiblePages: {
        type: Number,
        default: 5
    }
});

const filterQuery = computed(() => {
    const params = new URLSearchParams(props.filters).toString();
    return params ? `&${params}` : '';
});

const buildUrl = (url) => {
    if (!url) return '';
    return url.includes('?') ? `${url}${filterQuery.value}` : `${url}?${filterQuery.value.substring(1)}`;
};

const paginationLinks = computed(() => {
    if (!props.pagination || !props.pagination.links) return [];

    const currentPage = props.pagination.current_page;
    const lastPage = props.pagination.last_page;
    const maxPages = props.maxVisiblePages;
    const links = [];

    // Asegurar que tengamos un número impar de páginas visibles (sin contar primera/última)
    // para que la página actual pueda estar centrada
    const visibleMiddlePages = Math.max(3, maxPages - 2);
    const halfVisible = Math.floor(visibleMiddlePages / 2);

    // Siempre incluir la primera página
    links.push({
        url: buildUrl(props.pagination.first_page_url),
        label: '1',
        active: currentPage === 1
    });

    // Calcular rango centrado alrededor de la página actual
    let startPage = Math.max(2, currentPage - halfVisible);
    let endPage = Math.min(lastPage - 1, startPage + visibleMiddlePages - 1);

    // Reajustar el rango si está cerca del final
    if (endPage >= lastPage - 1) {
        endPage = lastPage - 1;
        startPage = Math.max(2, endPage - visibleMiddlePages + 1);
    }

    // Reajustar el rango si está cerca del inicio
    if (startPage <= 2) {
        startPage = 2;
        endPage = Math.min(lastPage - 1, startPage + visibleMiddlePages - 1);
    }

    // Añadir ellipsis después de la primera página si es necesario
    if (startPage > 2) {
        links.push({ url: null, label: '...', active: false });
    }

    // Añadir páginas intermedias
    for (let i = startPage; i <= endPage; i++) {
        const pageUrl = props.pagination.links.find(link => link.label == i)?.url;
        links.push({
            url: buildUrl(pageUrl),
            label: i.toString(),
            active: currentPage === i
        });
    }

    // Añadir ellipsis antes de la última página si es necesario
    if (endPage < lastPage - 1) {
        links.push({ url: null, label: '...', active: false });
    }

    // Siempre incluir la última página si es distinta a la primera
    if (lastPage > 1) {
        links.push({
            url: buildUrl(props.pagination.last_page_url),
            label: lastPage.toString(),
            active: currentPage === lastPage
        });
    }

    return links;
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
            <!-- Botón Anterior -->
            <template v-if="pagination.prev_page_url">
                <Link :href="buildUrl(pagination.prev_page_url)" preserve-scroll preserve-state
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

            <!-- Enlaces de página optimizados -->
            <template v-for="(link, index) in paginationLinks" :key="index">
                <template v-if="link.url">
                    <Link :href="link.url" preserve-scroll :class="{
                        'bg-primary-500 dark:bg-primary-600 text-white': link.active,
                        'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600': !link.active
                    }" class="px-3 py-1 rounded-md text-sm font-medium" v-html="link.label" />
                </template>
                <template v-else>
                    <span class="px-3 py-1 text-sm text-gray-700 dark:text-gray-300">{{ link.label }}</span>
                </template>
            </template>

            <!-- Botón Siguiente -->
            <template v-if="pagination.next_page_url">
                <Link :href="buildUrl(pagination.next_page_url)" preserve-scroll preserve-state
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
