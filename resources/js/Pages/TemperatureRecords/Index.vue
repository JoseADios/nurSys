<template>
    <AppLayout title="Hojas de temperatura">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <BreadCrumb :items="[
                    ...(form.admission_id ? [{
                        formattedId: { id: form.admission_id, prefix: 'ING' },
                        route: route('admissions.show', form.admission_id)
                    }] : []),
                    {
                        text: 'Hojas de temperatura'
                    }
                ]" />
            </h2>
        </template>

        <!-- Filtro de admisión - Responsive -->
        <div class="flex my-2 px-4 sm:px-0 items-center justify-end">
            <button v-if="form.admission_id" @click="form.admission_id = null; submitFilters()"
                class="mr-2 sm:mr-6 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500 self-end">
                Remover filtro de <FormatId :id="form.admission_id" prefix="ING" class="ml-1"></FormatId>
            </button>
        </div>

        <!-- Filtros y barra de búsqueda - Responsive -->
        <div
            class="bg-gray-100 dark:bg-gray-900 py-4 flex flex-col gap-4 items-center lg:flex-row lg:justify-between lg:items-end xl:items-center overflow-x-auto rounded-lg mx-4 lg:mx-10">
            <!-- Búsqueda - Ancho completo en móvil -->
            <div class="relative self-end w-full sm:mb-0 lg:w-1/2 xl:w-1/3 mb-4">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <SearchIcon class="size-4 text-gray-500 dark:text-gray-400" />
                </div>

                <input @input="submitFilters()"
                    class="pl-10 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    type="text" name="search" id="search" v-model="form.search" placeholder="Buscar..." />

                <button v-if="form.search" @click="form.search = ''; submitFilters()"
                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                    <XIcon class="h-5 w-5" />
                </button>
            </div>

            <!-- Filtros y botones - Con distribución automática del espacio -->
            <div class="flex flex-col w-full sm:space-y-3 lg:w-auto xl:flex-row space-y-3 xl:space-y-0 xl:flex-grow">
                <!-- Primera fila - Filtros principales con distribución automática -->
                <div
                    class="flex flex-col sm:flex-col sm:columns-2 xl:flex-row w-full gap-3 items-center xl:justify-between xl:flex-1">
                    <!-- Contenedor de filtros básicos - Se distribuye automáticamente -->
                    <div class="flex w-full flex-col sm:flex-row gap-2 items-center xl:gap-4">

                        <!-- En proceso - Siempre visible, se expande según el espacio -->
                        <select @change="submitFilters()"
                            class="flex-1 min-w-0 w-full sm:w-fit border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            name="in_process" id="in_process" v-model="form.in_process">
                            <option value="true">En proceso</option>
                            <option value="false">Dados de alta</option>
                            <option value="">Todos</option>
                        </select>

                        <!-- Filtro de días - Siempre visible, se expande según el espacio -->
                        <select @change="submitFilters()"
                            class="flex-1 min-w-0 w-full sm:w-fit border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            name="days" id="days" v-model="form.days">
                            <option value="">Siempre</option>
                            <option value="1">Último día</option>
                            <option value="7">Últimos 7 días</option>
                            <option value="30">Últimos 30 días</option>
                            <option value="90">Últimos 90 días</option>
                            <option value="180">Últimos 180 días</option>
                            <option value="365">Último año</option>
                        </select>

                        <!-- Mis Registros - Solo se muestra si tiene permiso -->
                        <AccessGate :permission="['temperatureRecord.create']" class="sm:w-fit">
                            <PersonalizableButton @click="toggleFilterMyRecords" title="Mostrar solo mis registros"
                                variant="outline" custom-class="relative" :color="form.myRecords ? 'indigo' : 'gray'">
                                Mis registros
                                <div class="pl-1">
                                    <UserIcon class="h-5 w-5" />
                                    <FilterIcon class="h-3 w-3 absolute bottom-1 right-2"
                                        :class="{ 'text-indigo-600 dark:text-indigo-400': form.myRecords }" />
                                    <div v-if="form.myRecords"
                                        class="absolute -top-1 -right-0 xl:-right-1 h-2 w-2 bg-indigo-500 rounded-full">
                                    </div>
                                </div>
                            </PersonalizableButton>
                        </AccessGate>
                    </div>

                    <!-- Contenedor de acciones - Se distribuye automáticamente entre elementos visibles -->
                    <div class="flex flex-col items-center sm:flex-row w-fit lg:place-self-end gap-3 xl:gap-4">

                        <!-- Ver Eliminados - Solo se muestra si tiene permiso -->
                        <AccessGate :permission="['temperatureRecord.delete']" class="sm:w-1/2 xl:w-auto">
                            <PersonalizableButton custom-class="whitespace-nowrap" @click="toggleShowDeleted" :color="form.showDeleted ? 'red' : 'gray'">
                                {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                                <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-5 w-5" />
                                <CircleXIcon v-else class="ml-1 h-5 w-5" />
                            </PersonalizableButton>
                        </AccessGate>

                        <!-- Nuevo Registro - Solo se muestra si tiene permiso -->
                        <AccessGate :permission="['temperatureRecord.create']" class="sm:w-1/2 xl:w-auto">
                            <PrimaryLink class="py-2.5 whitespace-nowrap flex-shrink-0 text-center"
                                v-if="!form.admission_id" :href="route('temperatureRecords.create')">
                                <PlusIcon class="size-5 mr-1 inline" />
                                <span class="pt-0.5">Nuevo Registro</span>
                            </PrimaryLink>
                            <PrimaryLink class="py-2.5 whitespace-nowrap flex-shrink-0 text-center" v-else
                                :href="route('temperatureRecords.create', { admission_id: form.admission_id })">
                                <PlusIcon class="size-5 mr-1 inline" />
                                <span class="pt-0.5">Nuevo Registro</span>
                            </PrimaryLink>
                        </AccessGate>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla responsive -->
        <div
            class="relative overflow-x-auto border border-gray-200 dark:border-gray-700/60 rounded-lg my-4 mx-4 lg:mx-10">
            <div class="min-w-full overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap hidden sm:table-cell"
                                @click="sort('id')">
                                ID <span v-if="form.sortField === 'id'">{{ form.sortDirection === 'asc' ? '↑' : '↓'
                                }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap"
                                @click="sort('admissions.id')">
                                Ingreso <span v-if="form.sortField === 'admissions.id'">{{ form.sortDirection === 'asc'
                                    ? '↑' :
                                    '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap"
                                @click="sort('patients.first_name')">
                                Paciente <span v-if="form.sortField === 'patients.first_name'">{{ form.sortDirection ===
                                    'asc' ?
                                    '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap"
                                @click="sort('users.name')">
                                Enfermero/a <span v-if="form.sortField === 'users.name'">{{ form.sortDirection === 'asc'
                                    ?
                                    '↑' :
                                    '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap hidden lg:table-cell"
                                @click="sort('temperature_records.created_at')">
                                Fecha <span v-if="form.sortField === 'temperature_records.created_at'">{{
                                    form.sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3"> Acciones </th>
                        </tr>
                    </thead>
                    <tbody v-if="temperatureRecords.data.length">
                        <tr v-for="temperatureRecord in temperatureRecords.data" :key="temperatureRecord.id"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                {{ temperatureRecord.id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div :class="[{
                                    'max-w-fit border border-green-600 text-green-600 px-2.5 py-0.5 rounded-md dark:border-green-900 dark:text-green-300': temperatureRecord.in_process,
                                    'max-w-fit border border-gray-500 text-gray-800 px-2.5 py-0.5 rounded-md dark:border-gray-600 dark:text-gray-300': !temperatureRecord.in_process
                                }]">
                                    <div v-if="temperatureRecord.admission.bed">
                                        <FormatId :id="temperatureRecord.admission.id" prefix="ING"></FormatId>,
                                        Cama {{ temperatureRecord.admission.bed.number }}, Sala {{
                                            temperatureRecord.admission.bed.room }}
                                    </div>
                                    <div v-else>
                                        <FormatId :id="temperatureRecord.admission.id" prefix="ING"></FormatId>,
                                        N/A
                                    </div>
                                </div>
                            </td>
                            <td scope="row"
                                class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                {{ temperatureRecord.admission.patient.first_name }}
                                {{ temperatureRecord.admission.patient.first_surname }}
                                {{ temperatureRecord.admission.patient.second_surname }}
                            </td>
                            <td scope="row" class="px-6 py-4 whitespace-nowrap">
                                {{ temperatureRecord.nurse.name }} {{ temperatureRecord.nurse.last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                {{ formatDate(temperatureRecord.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button class="ml-2 text-primary-500 hover:text-primary-800"
                                    @click="temperatureRecordShow(temperatureRecord.id)">
                                    Ver
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="!temperatureRecords.data.length"
                    class="text-center text-gray-500 dark:text-gray-400 py-4 w-full">
                    No hay registros disponibles.
                </div>
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
import TextInput from '@/Components/TextInput.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import FilterIcon from '@/Components/Icons/FilterIcon.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';

export default {
    props: {
        temperatureRecords: Object,
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
        BreadCrumb,
        TextInput,
        UserIcon,
        FilterIcon,
        PrimaryLink,
        PersonalizableButton
    },
    data() {
        return {
            form: {
                search: this.filters.search || '',
                in_process: this.filters.in_process || '',
                admission_id: this.filters.admission_id,
                showDeleted: this.filters.show_deleted,
                days: this.filters.days || '',
                sortField: this.filters.sortField || 'temperature_records.created_at',
                sortDirection: this.filters.sortDirection || 'asc',
                myRecords: this.filters.myRecords !== undefined ? this.filters.myRecords : true
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
        toggleFilterMyRecords() {
            this.form.myRecords = !this.form.myRecords;
            this.submitFilters();
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
