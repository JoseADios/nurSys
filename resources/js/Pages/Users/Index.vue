<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Usuarios
            </h2>
        </template>

        <div class="flex flex-col items-center justify-center mt-10">
            <Link :href="route('users.create')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
            Crear Usuario
            </Link>
        </div>

        <!-- Filtros -->
        <div class="flex flex-col lg:flex-row items-center justify-center gap-4 mt-10 lg:mx-10">
            <!-- Búsqueda general -->
            <input v-model="filters.search" type="text" placeholder="Buscar por nombre"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                @input="applyFilters" />

            <!-- Filtro por rol -->
            <input v-model="filters.role" type="text" placeholder="Filtrar por rol"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                @input="applyFilters" />

            <!-- Filtro por especialidad -->
            <input v-model="filters.specialty" type="text" placeholder="Filtrar por especialidad"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                @input="applyFilters" />

            <!-- Filtro por posición -->
            <input v-model="filters.position" type="text" placeholder="Filtrar por posición"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                @input="applyFilters" />

            <!-- Filtro por área -->
            <input v-model="filters.area" type="text" placeholder="Filtrar por área"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                @input="applyFilters" />

            <!-- Botón para ver registros eliminados -->
            <button @click="toggleShowDeleted"
                :class="filters.show_deleted ? 'bg-red-500 hover:bg-red-700' : 'bg-gray-500 hover:bg-gray-700'"
                class="text-white py-2 px-8 rounded-full">
                {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
            </button>


        </div>

        <!-- Tabla -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
            <table v-if="users.data.length"
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nombre</th>
                        <th scope="col" class="px-6 py-3">Rol</th>
                        <th scope="col" class="px-6 py-3">Especialidad</th>
                        <th scope="col" class="px-6 py-3">Posición</th>
                        <th scope="col" class="px-6 py-3">Área</th>
                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users.data" :key="user.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ user.name }} {{ user.last_name }}
                        </th>
                        <td class="px-6 py-4">
                            {{ user.roles[0] ? user.roles[0].name : 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ user.specialty }}
                        </td>
                        <td class="px-6 py-4">
                            {{ user.position }}
                        </td>
                        <td class="px-6 py-4">
                            {{ user.area }}
                        </td>
                        <td class="px-6 py-4">
                            <Link class="ml-2 text-green-500 hover:text-green-800" :href="route('users.show', user.id)"
                                as="button">
                            Abrir
                            </Link>
                            <Link class="text-blue-500 hover:text-blue-800" :href="route('users.edit', user.id)">
                            Editar
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :pagination="users" />
        </div>
        <div class="pb-4"></div>
    </AppLayout>
</template>

<script>
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
    props: {
        users: Object,
        filters: Object,
    },
    components: {
        AppLayout,
        Link,
        Pagination,
    },
    methods: {
        applyFilters() {
            router.get(route('users.index'), this.filters, {
                preserveState: true,
                replace: true,
            });
        },
        toggleShowDeleted() {
            this.filters.show_deleted = !this.filters.show_deleted;
            this.applyFilters();
        },
    },
};
</script>
