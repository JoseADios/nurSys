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
            <div class="relative w-full">
                <input v-model="form.search" type="text" placeholder="Buscar por nombre"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    @input="applyFilters" />
                <button v-if="form.search" @click="form.search = ''; applyFilters()" type="button"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Filtro por rol -->
            <div class="relative w-full">
                <select v-model="form.role" name="role" id="role" @change="applyFilters"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="admin">Admin</option>
                    <option value="nurse">Nurse</option>
                    <option value="doctor">Doctor</option>
                    <option value="receptionist">Receptionist</option>
                </select>

                <button v-if="form.role" @click="form.role = ''; applyFilters()" type="button"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Filtro por especialidad -->
            <div class="relative w-full">
                <input v-model="form.specialty" type="text" placeholder="Filtrar por especialidad"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    @input="applyFilters" />

                <button v-if="form.specialty" @click="form.specialty = ''; applyFilters()" type="button"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Filtro por posición -->
            <div class="relative w-full">
                <input v-model="form.position" type="text" placeholder="Filtrar por posición"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    @input="applyFilters" />

                <button v-if="form.position" @click="form.position = ''; applyFilters()" type="button"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Filtro por área -->
            <div class="relative w-full">
                <input v-model="form.area" type="text" placeholder="Filtrar por área"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    @input="applyFilters" />

                <button v-if="form.area" @click="form.area = ''; applyFilters()" type="button"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Botón para ver registros eliminados -->
            <button @click="toggleShowDeleted"
                class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap" :class="{
                    'bg-red-500 hover:bg-red-600 text-white': form.show_deleted,
                    'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200': !form.show_deleted
                }">
                {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                <svg class="ml-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path v-if="form.show_deleted" fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                        clip-rule="evenodd" />
                    <path v-else fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                        clip-rule="evenodd" />
                </svg>
            </button>


        </div>

        <!-- Tabla -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
            <table v-if="users.data.length"
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">#</th>
                        <th scope="col" class="px-6 py-3">Nombre</th>
                        <th scope="col" class="px-6 py-3">Rol</th>
                        <th scope="col" class="px-6 py-3">Especialidad</th>
                        <th scope="col" class="px-6 py-3">Posición</th>
                        <th scope="col" class="px-6 py-3">Área</th>
                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(user, index) in users.data" :key="user.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">
                            {{ index + 1 }}
                        </td>
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
    data() {
        return {
            form: {
                search: this.filters.search || '',
                specialty: this.filters.specialty || '',
                role: this.filters.role || '',
                position: this.filters.position || '',
                area: this.filters.area || '',
                show_deleted: this.filters.show_deleted,
            },
        };
    },
    methods: {
        applyFilters() {
            router.get(route('users.index'), this.form, {
                preserveState: true,
                replace: true,
            });
        },
        toggleShowDeleted() {
            this.form.show_deleted = !this.form.show_deleted;
            this.applyFilters();
        }
    },
};
</script>
