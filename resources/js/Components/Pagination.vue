<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    pagination: Object
});
</script>

<template>
    <div class="flex flex-col sm:flex-row items-center justify-between py-3 px-4 bg-white dark:bg-gray-800">
        <!-- Información de paginación -->
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

        <!-- Controles de paginación -->
        <div class="inline-flex space-x-1">
            <!-- Botón anterior -->
            <Link
                :href="pagination.prev_page_url ?? ''"
                :class="!pagination.prev_page_url ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                preserve-scroll
                class="px-3 py-1 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700"
            >
                Anterior
            </Link>

            <!-- Números de página -->
            <template v-for="(link, index) in pagination.links" :key="index">
                <!-- Omitimos los botones de "Anterior" y "Siguiente" que proporciona Laravel, ya que los estamos personalizando -->
                <template v-if="index !== 0 && index !== pagination.links.length - 1">
                    <Link
                        :href="link.url ?? ''"
                        preserve-scroll
                        :class="{
                            'bg-blue-500 dark:bg-blue-600 text-white': link.active,
                            'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600': !link.active,
                            'cursor-not-allowed opacity-50': !link.url
                        }"
                        class="px-3 py-1 rounded-md text-sm font-medium"
                        v-html="link.label"
                    />
                </template>
            </template>

            <!-- Botón siguiente -->
            <Link
                :href="pagination.next_page_url ?? ''"
                :class="!pagination.next_page_url ? 'cursor-not-allowed opacity-50' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                preserve-scroll
                class="px-3 py-1 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700"
            >
                Siguiente
            </Link>
        </div>
    </div>
</template>
