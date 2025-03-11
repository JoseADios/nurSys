<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Registros de Enfermería
            </h2>
        </template>

        <div class="flex items-center justify-between">
            <div class="ml-4 my-2 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">
                <div class="inline-flex items-center" v-if="admission_id">
                    <Link :href="route('admissions.show', admission_id)"
                        class="inline-flex items-center  hover:text-blue-600 dark:hover:text-white">
                    <FormatId :id="admission_id" prefix="ING"></FormatId>
                    </Link>
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 9 4-4-4-4" />
                    </svg>
                </div>
                <div class="ml-2 inline-flex items-center ">
                    Registros de enfermería
                </div>
            </div>

            <Link :href="route('nurseRecords.index')" v-if="admission_id"
                class="mr-6 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500 self-end">
            Remover filtro de
            <FormatId :id="admission_id" prefix="ING"></FormatId>,
            </Link>
        </div>

        <!-- Navigation -->
        <!-- <div v-if="admission_id" class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
            <Link :href="route('admissions.show', admission_id)"
                class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                    clip-rule="evenodd" />
            </svg>
            <span class="font-medium">Volver</span>
            </Link>
        </div> -->

        <div
            class="bg-gray-100 dark:bg-gray-900 flex justify-between items-end overflow-x-auto sm:rounded-lg mt-4 lg:mx-10">

            <div class="relative mb-2">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="none"
                        viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>

                <input @input="submitFilters()"
                    class="block w-full p-3 ps-10 pr-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    type="text" name="search" id="search" v-model="form.search" placeholder="Buscar ..." />

                <button v-if="form.search" @click="form.search = ''; submitFilters()"
                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex items-center">
                <select @change="submitFilters()"
                    class="bg-gray-50 mr-2 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    name="days" id="days" v-model="form.days">
                    <option value="">Siempre</option>
                    <option value="1">Último día</option>
                    <option value="7">Últimos 7 días</option>
                    <option value="30">Últimos 30 días</option>
                    <option value="90">Últimos 90 días</option>
                    <option value="180">Últimos 180 días</option>
                    <option value="365">Último año</option>
                </select>

                <AccessGate :permission="['nurseRecord.delete']">
                    <!-- Filtro para mostrar registros eliminados -->
                    <button @click="toggleShowDeleted"
                        class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap"
                        :class="{
                            'bg-red-500 hover:bg-red-600 text-white': form.showDeleted,
                            'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200': !form.showDeleted
                        }">
                        {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                        <svg class="ml-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path v-if="form.showDeleted" fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                                clip-rule="evenodd" />
                            <path v-else fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </AccessGate>

                <AccessGate :permission="['nurseRecord.create']">
                    <Link :href="route('nurseRecords.create')"
                        class="flex items-center ml-4 text-base bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-full whitespace-nowrap">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo registro de enfermería
                    </Link>
                </AccessGate>
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">

            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('nurse_records.id')">
                            ID <span v-if="form.sortField === 'nurse_records.id'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                            }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('in_process')">
                            En proceso <span v-if="form.sortField === 'in_process'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                            }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admissions.id')">
                            Ingreso <span v-if="form.sortField === 'admissions.id'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                            }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('patients.first_name')">
                            Paciente <span v-if="form.sortField === 'patients.first_name'">{{ form.sortDirection ===
                                'asc' ?
                                '↑' :
                                '↓'
                            }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('users.name')">
                            Enfermera <span v-if="form.sortField === 'users.name'">{{ form.sortDirection === 'asc'
                                ?
                                '↑' :
                                '↓'
                            }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('nurse_records.created_at')">
                            Fecha de Creación<span v-if="form.sortField === 'nurse_records.created_at'">{{
                                form.sortDirection
                                    ===
                                    'asc' ?
                                    '↑' :
                                    '↓'
                            }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody v-if="nurseRecords.data.length">
                    <tr v-for="nurseRecord in nurseRecords.data.filter(nurseRecord => nurseRecord.id)"
                        :key="nurseRecord.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">
                            {{ nurseRecord.id }}
                        </td>
                        <td class="px-6 py-4">
                            <span v-if="nurseRecord.in_process"
                                class="block w-4 h-4 bg-green-500 rounded-full mx-auto"></span>
                            <span v-else class="block w-4 h-4 bg-orange-500 rounded-full mx-auto"></span>
                        </td>
                        <td class="px-6 py-4">
                            <div v-if="nurseRecord.admission.bed">
                                <FormatId :id="nurseRecord.admission.id" prefix="ING"></FormatId>,
                                Cama {{ nurseRecord.admission.bed.number }}, Sala {{
                                    nurseRecord.admission.bed.room }}
                            </div>
                            <div v-else>
                                <FormatId :id="nurseRecord.admission.id" prefix="ING"></FormatId>,
                                {{ nurseRecord.admission.created_at }} N/A
                            </div>
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ nurseRecord.admission.patient.first_name }} {{
                                nurseRecord.admission.patient.first_surname }} {{
                                nurseRecord.admission.patient.second_surname }}
                        </th>
                        <td class="px-6 py-4">
                            {{ nurseRecord.nurse.name }} {{ nurseRecord.nurse.last_name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ formatDate(nurseRecord.created_at) }}
                        </td>
                        <td class="px-6 py-4">
                            <Link v-if="admission_id" class="ml-2 text-green-500 hover:text-green-800"
                                :href="`${route('nurseRecords.show', nurseRecord.id)}?admission_id=${admission_id}`"
                                as="button">
                            Abrir
                            </Link>
                            <Link v-else class="ml-2 text-green-500 hover:text-green-800"
                                :href="route('nurseRecords.show', nurseRecord.id)" as="button">
                            Abrir
                            </Link>
                        </td>
                    </tr>
                </tbody>
                <div v-else class="text-center text-gray-500 dark:text-gray-400 py-4">
                    No hay registros disponibles.
                </div>
            </table>
            <Pagination :pagination="nurseRecords" :filters="form"  />
        </div>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link
} from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import moment from 'moment';
import AccessGate from '@/Components/Access/AccessGate.vue';
import FormatId from '@/Components/FormatId.vue';

export default {
    props: {
        nurseRecords: Object,
        filters: Object,
        admission_id: String
    },
    components: {
        AppLayout,
        Link,
        Pagination,
        AccessGate,
        FormatId
    },
    data() {
        return {
            form: {
                search: this.filters.search || '',
                in_process: this.filters.in_process || '',
                admission_id: this.filters.admission_id,
                showDeleted: this.filters.show_deleted,
                days: this.filters.days || '',
                sortField: this.filters.sortField || 'nurse_records.created_at',
                sortDirection: this.filters.sortDirection || 'asc',
            },
            timeout: 500,
        }
    },
    methods: {
        submitFilters() {
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            this.timeout
            this.$inertia.get(route('nurseRecords.index'), this.form, {
                preserveState: true,
            });
        },
        toggleShowDeleted() {
            this.form.showDeleted = !this.form.showDeleted;
            this.$inertia.get(route('nurseRecords.index', this.form));
        },
        sort(field) {
            this.form.sortField = field;
            this.form.sortDirection = this.form.sortDirection === 'asc' ? 'desc' : 'asc';
            this.submitFilters();
        },
        formatDate(date) {
            return moment(date).format('DD/MMM/YYYY HH:mm');
        }
    },

}
</script>
