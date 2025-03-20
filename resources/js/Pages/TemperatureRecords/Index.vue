<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <BreadCrumb :items="[
                    // Primer elemento (solo si hay form.admission_id)
                    ...(form.admission_id ? [{
                        formattedId: { id: form.admission_id, prefix: 'ING' },
                        route: route('admissions.show', form.admission_id)
                    }] : []),

                    // Segundo elemento (siempre presente)
                    {
                        text: 'Hojas de temperatura'
                    }
                ]" />
            </h2>
        </template>

        <div class="flex items-center justify-end">
            <button v-if="form.admission_id" @click="form.admission_id = null; submitFilters()"
                class="mr-6 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500 self-end">
                Remover filtro de <FormatId :id="form.admission_id" prefix="ING" class="ml-1"></FormatId>
            </button>
        </div>

        <div
            class="bg-gray-100 dark:bg-gray-900 flex justify-between items-end overflow-x-auto sm:rounded-lg mt-4 lg:mx-10">

            <div class="relative mb-2">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <SearchIcon class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                </div>

                <input @input="submitFilters()"
                    class="block w-full p-3 ps-10 pr-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    type="text" name="search" id="search" v-model="form.search" placeholder="Buscar ..." />

                <button v-if="form.search" @click="form.search = ''; submitFilters()"
                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                    <XIcon class="size-4" />
                </button>
            </div>

            <div class="flex items-end">
                <select @change="submitFilters()"
                    class="bg-gray-50 mr-2 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    name="days" id="days" v-model="form.in_process">
                    <option value="true">En proceso</option>
                    <option value="false">Dados de alta</option>
                    <option value="">Todos</option>
                </select>
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

                <AccessGate :permission="['temperatureRecord.delete']">
                    <!-- Filtro para mostrar registros eliminados -->
                    <button @click="toggleShowDeleted"
                        class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap"
                        :class="{
                            'bg-red-500 hover:bg-red-600 text-white': form.showDeleted,
                            'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200': !form.showDeleted
                        }">
                        {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                        <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-5 w-5" />
                        <CircleXIcon v-else class="ml-1 h-5 w-5" />
                    </button>
                </AccessGate>

                <AccessGate :permission="['nurseRecord.create']">
                    <div v-if="!admission_id">
                        <Link :href="route('temperatureRecords.create')"
                            class="flex items-center ml-4 text-base bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-full whitespace-nowrap">
                        <PlusIcon class="size-5 mr-2" />
                        Nueva hoja de temperatura
                        </Link>
                    </div>
                    <div v-if="admission_id">
                        <Link :href="route('temperatureRecords.create', { admission_id: admission_id })"
                            class="flex items-center ml-4 text-base bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-full whitespace-nowrap">
                        <PlusIcon class="size-5 mr-2" />

                        Nueva hoja de temperatura
                        </Link>
                    </div>
                </AccessGate>
            </div>
        </div>

        <div
            class="relative overflow-x-auto border border-gray-200 dark:border-gray-700/60 sm:rounded-lg mt-4 lg:mx-10">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('id')">
                            ID <span v-if="form.sortField === 'id'">{{ form.sortDirection === 'asc' ?
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
                                'asc' ? '↑'
                                : '↓' }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('users.name')">
                            Enfermera <span v-if="form.sortField === 'users.name'">{{ form.sortDirection === 'asc' ? '↑'
                                : '↓'
                            }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer"
                            @click="sort('temperature_records.updated_at')">
                            Fecha de actualización <span v-if="form.sortField === 'temperature_records.updated_at'">{{
                                form.sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3"> Acciones </th>
                    </tr>
                </thead>
                <tbody v-if="temperatureRecords.data.length">
                    <tr v-for="temperatureRecord in temperatureRecords.data" :key="temperatureRecord.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">
                            {{ temperatureRecord.id }}
                        </td>
                        <td class="px-6 py-4">
                            <span v-if="temperatureRecord.in_process"
                                class="block w-4 h-4 bg-green-500 rounded-full mx-auto"></span>
                            <span v-else class="block w-4 h-4 bg-orange-500 rounded-full mx-auto"></span>
                        </td>
                        <td class="px-6 py-4">
                            <div v-if="temperatureRecord.admission.bed">
                                <FormatId :id="temperatureRecord.admission.id" prefix="ING"></FormatId>,
                                Cama {{ temperatureRecord.admission.bed.number }}, Sala {{
                                    temperatureRecord.admission.bed.room }}
                            </div>
                            <div v-else>
                                <FormatId :id="temperatureRecord.admission.id" prefix="ING"></FormatId>,
                                {{ temperatureRecord.admission.created_at }}
                                N/A
                            </div>
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
                            {{ formatDate(temperatureRecord.updated_at) }}
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
            <div v-if="!temperatureRecords.data.length"
                class="text-center text-gray-500 dark:text-gray-400 py-4 w-full">
                No hay registros disponibles.
            </div>
            <Pagination :pagination="temperatureRecords" :filters="form" />
        </div>
    </AppLayout>
</template>

<script>

import AccessGate from '@/Components/Access/AccessGate.vue';
import FormatId from '@/Components/FormatId.vue';
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import moment from "moment/moment";
import 'moment/locale/es';
import BackIcon from '@/Components/Icons/BackIcon.vue';
import SearchIcon from '@/Components/Icons/SearchIcon.vue';
import XIcon from '@/Components/Icons/XIcon.vue';
import CirclePlusIcon from '@/Components/Icons/CirclePlusIcon.vue';
import CircleXIcon from '@/Components/Icons/CircleXIcon.vue';
import ChevronRightIcon from '@/Components/Icons/ChevronRightIcon.vue';
import PlusIcon from '@/Components/Icons/PlusIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';

export default {
    props: {
        temperatureRecords: Object,
        admission_id: Number,
        filters: Object,
    },
    components: {
        AppLayout,
        Link,
        Pagination,
        AccessGate,
        FormatId,
        BackIcon,
        SearchIcon,
        XIcon,
        CirclePlusIcon,
        CircleXIcon,
        FormatId,
        ChevronRightIcon,
        PlusIcon,
        BreadCrumb
    },
    data() {
        return {
            form: {
                search: this.filters.search || '',
                in_process: this.filters.in_process || '',
                admission_id: this.filters.admission_id,
                showDeleted: this.filters.show_deleted,
                days: this.filters.days || '',
                sortField: this.filters.sortField || 'temperature_records.updated_at',
                sortDirection: this.filters.sortDirection || 'asc',
            },
        };
    },
    methods: {
        temperatureRecordShow(id) {
            if (this.form.admission_id) {

                this.$inertia.get(`${route('temperatureRecords.show', id)}?admission_id=${this.form.admission_id}`);
            } else {
                this.$inertia.get(route('temperatureRecords.show', id));
            }
        },
        toggleShowDeleted() {
            this.form.showDeleted = !this.form.showDeleted;
            this.$inertia.get(route('temperatureRecords.index', this.form));
        },
        submitFilters() {
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            this.timeout = setTimeout(() => {
                this.$inertia.get(route('temperatureRecords.index'), this.form, {
                    preserveState: true,
                });
            }, 300);
        },
        sort(field) {
            this.form.sortField = field;
            this.form.sortDirection = this.form.sortDirection === 'asc' ? 'desc' : 'asc';
            this.submitFilters();
        },
        formatDate(date) {
            return moment(date).format('DD MMM YYYY HH:mm');
        }
    }
}
</script>
