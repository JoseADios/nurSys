<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Pacientes
            </h2>
        </template>

        <div class="flex flex-col items-center justify-center mt-10">
            <Link :href="route('patients.create')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
            Crear nuevo paciente
            </Link>
        </div>

        <div
            class="bg-gray-100 dark:bg-gray-900 flex justify-between items-end overflow-x-auto sm:rounded-lg mt-4 lg:mx-10">

            <form @submit.prevent="submitFilter" class="mb-2">
                <label for="search" class="block my-2 text-md font-large text-gray-900 dark:text-white">
                    Buscar:
                </label>
                <input @input="submitFilter()" class="rounded-lg" type="text" name="search" id="search"
                    v-model="form.search" placeholder="Buscar ..." />
            </form>

            <!-- Filtro para mostrar registros eliminados -->
            <button @click="toggleShowDeleted"
                class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors" :class="{
                    'bg-red-500 hover:bg-red-600 text-white': form.showDeleted,
                    'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200': !form.showDeleted
                }">
                <span class="font-medium">Mostrar registros eliminados</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path v-if="form.showDeleted" fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                        clip-rule="evenodd" />
                    <path v-else fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
            <table v-if="patients.data.length"
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nombre
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Teléfono
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Cédula
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nacionalidad
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Correo
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ARS
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Ingresado
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(patient, index) in patients.data" :key="patient.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">
                            {{ index + 1 }}
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                        </th>
                        <td class="px-6 py-4">
                            {{ patient.phone }}
                        </td>
                        <td class="px-6 py-4">
                            {{ patient.identification_card }}
                        </td>
                        <td class="px-6 py-4">
                            {{ patient.nationality }}
                        </td>
                        <td class="px-6 py-4">
                            {{ patient.email }}
                        </td>
                        <td class="px-6 py-4">
                            {{ patient.ars }}
                        </td>
                        <td class="px-6 py-4">
                            <span v-if="patient.is_hospitalized"
                                class="block w-4 h-4 bg-green-500 rounded-full mx-auto"></span>
                            <span v-else class="block w-4 h-4 bg-orange-500 rounded-full mx-auto"></span>
                        </td>
                        <td class="px-6 py-4">
                            <Link class="ml-2 text-blue-500 hover:text-blue-800"
                                :href="route('patients.show', patient.id)">
                            Ver
                            </Link>
                            <Link class="text-green-500 hover:text-green-800"
                                :href="route('patients.edit', patient.id)">
                            Editar
                            </Link>
                            <!-- <Link method="delete" class="ml-2 text-red-500 hover:text-red-800"
                                :href="route('patients.destroy', patient.id)" as="button">
                            Eliminar
                            </Link> -->
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="text-center text-gray-500 dark:text-gray-400 py-4">
                No hay registros disponibles.
            </div>
            <Pagination :pagination="patients" />
        </div>

        <div class="pb-4"></div>

    </AppLayout>
</template>

<script>
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        patients: Object,
        filters: Object,
    },
    components: {
        AppLayout,
        Link,
        Pagination
    },
    data() {
        return {
            form: {
                search: this.filters.search || '',
                showDeleted: this.filters.show_deleted,
            },
        };
    },
    methods: {
        toggleShowDeleted() {
            this.form.search = '';
            this.form.showDeleted = !this.form.showDeleted;
            this.$inertia.get(route('patients.index', this.form));
        },
        submitFilter() {
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            this.timeout = setTimeout(() => {
                this.$inertia.get(route('patients.index'), this.form, {
                    preserveState: true,
                });
            }, 300);
        },
    },

}


</script>
