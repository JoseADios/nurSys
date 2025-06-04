<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <BreadCrumb :items="[
                    ...(form.admission_id ? [{
                        formattedId: { id: form.admission_id, prefix: 'ING' },
                        route: route('admissions.show', form.admission_id)
                    }] : []),
                    {
                        text: 'Registros de enfermeria'
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
                        <AccessGate :permission="['nurseRecord.create']" class="sm:w-fit">
                            <PersonalizableButton
                                @click="toggleFilterMyRecords"
                                title="Mostrar solo mis registros"
                                variant="outline"
                                custom-class="relative"
                                :color="form.myRecords ?  'indigo' : 'gray'">
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
                        <AccessGate :permission="['nurseRecord.delete']" class="sm:w-1/2 xl:w-auto">
                            <PersonalizableButton custom-class="whitespace-nowrap" @click="toggleShowDeleted" :color="form.showDeleted ? 'red' : 'gray'">
                                {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                                <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-5 w-5" />
                                <CircleXIcon v-else class="ml-1 h-5 w-5" />
                            </PersonalizableButton>
                        </AccessGate>

                        <!-- Nuevo Registro - Solo se muestra si tiene permiso -->
                        <AccessGate :permission="['nurseRecord.create']" class="sm:w-1/2 xl:w-auto">
                            <PrimaryLink class="py-2.5 whitespace-nowrap flex-shrink-0 text-center"
                                v-if="!form.admission_id" :href="route('nurseRecords.create')">
                                <PlusIcon class="size-5 mr-1 inline" />
                                <span class="pt-0.5">Nuevo Registro</span>
                            </PrimaryLink>
                            <PrimaryLink class="py-2.5 whitespace-nowrap flex-shrink-0 text-center" v-else
                                :href="route('nurseRecords.create', { admission_id: form.admission_id })">
                                <PlusIcon class="size-5 mr-1 inline" />
                                <span class="pt-0.5">Nuevo Registro</span>
                            </PrimaryLink>
                        </AccessGate>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla con scroll horizontal para móviles -->
        <div
            class="relative overflow-x-auto border border-gray-200 dark:border-gray-700/60 rounded-lg my-4 mx-4 lg:mx-10">
            <div class="min-w-full overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap hidden sm:table-cell"
                                @click="sort('nurse_records.id')">
                                ID <span v-if="form.sortField === 'nurse_records.id'">{{ form.sortDirection === 'asc' ?
                                    '↑' :
                                    '↓' }}</span>
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
                            <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap"
                                @click="sort('nurse_records.created_at')">
                                Fecha <span v-if="form.sortField === 'nurse_records.created_at'">{{ form.sortDirection
                                    === 'asc'
                                    ? '↑' : '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody v-if="nurseRecords.data.length">
                        <tr v-for="nurseRecord in nurseRecords.data.filter(nurseRecord => nurseRecord.id)"
                            :key="nurseRecord.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                {{ nurseRecord.id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div :class="[{
                                    'max-w-fit border border-green-600 text-green-600 px-2.5 py-0.5 rounded-md dark:border-green-900 dark:text-green-300': nurseRecord.in_process,
                                    'max-w-fit border border-gray-500 text-gray-800 px-2.5 py-0.5 rounded-md dark:border-gray-600 dark:text-gray-300': !nurseRecord.in_process
                                }]">
                                    <div v-if="nurseRecord.admission.bed">
                                        <FormatId :id="nurseRecord.admission.id" prefix="ING"></FormatId>,
                                        Cama {{ nurseRecord.admission.bed.number }}, Sala {{
                                            nurseRecord.admission.bed.room
                                        }}
                                    </div>
                                    <div v-else>
                                        <FormatId :id="nurseRecord.admission.id" prefix="ING"></FormatId>,
                                        {{ nurseRecord.admission.created_at }} N/A
                                    </div>
                                </div>
                            </td>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ nurseRecord.admission.patient.first_name }} {{
                                    nurseRecord.admission.patient.first_surname }}
                                {{ nurseRecord.admission.patient.second_surname }}
                            </th>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ nurseRecord.nurse.name }} {{ nurseRecord.nurse.last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ formatDate(nurseRecord.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <Link v-if="form.admission_id" class="ml-2 text-blue-500 hover:text-blue-800"
                                    :href="`${route('nurseRecords.show', nurseRecord.id)}?admission_id=${form.admission_id}`"
                                    as="button">
                                Ver
                                </Link>
                                <Link v-else class="ml-2 text-primary-500 hover:text-primary-800"
                                    :href="route('nurseRecords.show', nurseRecord.id)" as="button">
                                Ver
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="!nurseRecords.data.length" class="text-center text-gray-500 dark:text-gray-400 py-4">
                No hay registros disponibles.
            </div>
            <Pagination :pagination="nurseRecords" :filters="form" />
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import moment from "moment/moment";
import 'moment/locale/es';
import AccessGate from '@/Components/Access/AccessGate.vue';
import FormatId from '@/Components/FormatId.vue';
import ChevronRightIcon from '@/Components/Icons/ChevronRightIcon.vue';
import PlusIcon from '@/Components/Icons/PlusIcon.vue';
import SearchIcon from '@/Components/Icons/SearchIcon.vue';
import XIcon from '@/Components/Icons/XIcon.vue';
import CircleXIcon from '@/Components/Icons/CircleXIcon.vue';
import CirclePlusIcon from '@/Components/Icons/CirclePlusIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import FilterIcon from '@/Components/Icons/FilterIcon.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';

export default {
    props: {
        nurseRecords: Object,
        filters: Object
    },
    components: {
        AppLayout,
        Link,
        Pagination,
        AccessGate,
        FormatId,
        ChevronRightIcon,
        PlusIcon,
        SearchIcon,
        XIcon,
        CirclePlusIcon,
        CircleXIcon,
        BreadCrumb,
        UserIcon,
        FilterIcon,
        PrimaryButton,
        PrimaryLink,
        PersonalizableButton
    },
    data() {
        return {
            form: {
                search: this.filters.search || '',
                in_process: this.filters.in_process || '',
                admission_id: this.filters.admission_id !== 0 ? this.filters.admission_id : null,
                showDeleted: this.filters.show_deleted,
                days: this.filters.days || '',
                sortField: this.filters.sortField || 'nurse_records.created_at',
                sortDirection: this.filters.sortDirection || 'asc',
                myRecords: this.filters.myRecords !== undefined ? this.filters.myRecords : true
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
        toggleFilterMyRecords() {
            this.form.myRecords = !this.form.myRecords;
            this.submitFilters();
        },
        sort(field) {
            this.form.sortField = field;
            this.form.sortDirection = this.form.sortDirection === 'asc' ? 'desc' : 'asc';
            this.submitFilters();
        },
        formatDate(date) {
            return moment(date).format('DD MMM YYYY HH:mm');
        }
    },
}
</script>
