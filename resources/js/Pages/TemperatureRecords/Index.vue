<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Hojas de Temperatura
            </h2>
        </template>

        <!-- <div class="text-white">Datos: {{ temperatureRecords.data }}</div> -->

        <!-- Navigation -->
        <div v-if="admission_id" class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
            <Link :href="route('admissions.show', admission_id)"
                class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                    clip-rule="evenodd" />
            </svg>
            <span class="font-medium">Volver</span>
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
            <table v-if="temperatureRecords.data.length"
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Ingreso
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Paciente
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Enfermera
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Fecha
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(temperatureRecord, index) in temperatureRecords.data" :key="temperatureRecord.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">
                            {{ index + 1 }}
                        </td>
                        <td class="px-6 py-4">
                            {{ temperatureRecord.admission.created_at }}
                            Cama {{ temperatureRecord.admission.bed.number }}, Sala {{
                                temperatureRecord.admission.bed.room }}
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ temperatureRecord.admission.patient.first_name }} {{
                                temperatureRecord.admission.patient.first_surname }} {{
                                temperatureRecord.admission.patient.second_surname }}
                        </td>
                        <td scope="row" class="px-6 py-4 ">
                            {{ temperatureRecord.nurse.name }} {{
                                temperatureRecord.nurse.last_surname }}
                        </td>
                        <td class="px-6 py-4">
                            {{ temperatureRecord.created_at }}
                        </td>
                        <td class="px-6 py-4">
                            <button class="ml-2 text-green-500 hover:text-green-800"
                                @click="temperatureRecordShow(temperatureRecord.id)">
                                Abrir
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="text-center text-gray-500 dark:text-gray-400 py-4">
                No hay registros disponibles.
            </div>
            <Pagination :pagination="temperatureRecords" />
        </div>
    </AppLayout>
</template>

<script>

import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        temperatureRecords: Object,
        admission_id: Number,
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
                admission_id: this.filters.admission_id,
                showDeleted: this.filters.show_deleted,
            },
        };
    },
    methods: {
        temperatureRecordShow(id) {
            this.$inertia.get(route('temperatureRecords.customShow', { id: id, admission_id: null }));
        },
        toggleShowDeleted() {
            this.form.search = '';
            this.form.showDeleted = !this.form.showDeleted;
            this.$inertia.get(route('temperatureRecords.index', this.form));
        },
        submitFilter() {
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            this.timeout = setTimeout(() => {
                this.$inertia.get(route('temperatureRecords.index'), this.form, {
                    preserveState: true,
                });
            }, 300);
        },
    }
}
</script>
