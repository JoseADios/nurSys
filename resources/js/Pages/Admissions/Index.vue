<template>
    <AppLayout title="Ingresos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <BreadCrumb :items="[
                    {
                        text: 'Ingresos',
                        route: route('admissions.index')

                        },
                    ]" />
                </h2>
            </template>

                    <div class="flex my-2 px-4 sm:px-0 items-center justify-end">
                        <button v-if="form.admission_id" @click="form.admission_id = null; submitFilters()"
                            class="mr-2 sm:mr-6 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500 self-end">
                            Remover filtro de <FormatId :id="form.admission_id" prefix="ING" class="ml-1"></FormatId>
                        </button>
                    </div>
                <div class="px-4 lg:px-10 mt-4">
            <div class="mb-4">
                <!-- Fila principal: barra + botones -->
                <div class="flex flex-col sm:flex-row gap-3 mb-3">
                <!-- Barra de búsqueda -->
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <SearchIcon class="size-4 text-gray-500 dark:text-gray-400" />
                    </div>

                    <input
                    id="search"
                    name="search"
                    v-model="form.search"
                    type="text"
                    placeholder="Buscar..."
                    @input="submitFilters()"
                    class="w-full pl-10 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                            focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600
                            rounded-md shadow-sm"
                    />

                    <button
                    v-if="form.search"
                    @click="form.search = ''; submitFilters()"
                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 dark:text-gray-400
                            hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                    >
                    <XIcon class="h-5 w-5" />
                    </button>
                </div>

                <!-- Botones (apilan y ocupan ancho completo en móvil) -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2 w-full sm:w-auto">
                    <!-- Mostrar/Ocultar eliminados -->
                    <AccessGate :permission="['admission.delete']">
                    <PersonalizableButton
                        @click="toggleShowDeleted"
                        :color="form.showDeleted ? 'red' : 'gray'"
                        class="w-full sm:w-auto flex justify-center whitespace-nowrap"
                    >
                        {{ form.showDeleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                        <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-5 w-5" />
                        <CircleXIcon   v-else                 class="ml-1 h-5 w-5" />
                    </PersonalizableButton>
                    </AccessGate>

                    <!-- Nuevo Registro -->
                    <AccessGate :permission="['admission.create']">
                    <PrimaryLink
                        :href="
                        !form.admission_id
                            ? route('admissions.create')
                            : route('admissions.create', { admission_id: form.admission_id })
                        "
                        class="w-full sm:w-auto flex justify-center whitespace-nowrap"
                    >
                        <PlusIcon class="size-5" />
                        <span>Nuevo Registro</span>
                    </PrimaryLink>
                    </AccessGate>
                </div>
                </div>

                <!-- Primera fila en dispositivos medianos -->
                <div class="flex flex-col sm:flex-row w-full gap-3 whitespace-nowrap items-center">
                    <!-- Grupo: Mis Registros + En proceso -->
                    <div class="flex w-full flex-col sm:flex-row xl:w-full gap-2 items-center">


                        <select @change="submitFilters()"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            name="admissions_discharged" id="admissions_discharged"
                            v-model="form.admissions_discharged">
                            <option value="">Todos</option>
                            <option value="1">Dados de Alta</option>
                            <option value="2">Ingresados</option>
                        </select>
                        <select @change="submitFilters()"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            name="beds_available" id="beds_available" v-model="form.beds_available">
                            <option value="">Todos</option>
                            <option value="1">Con Camas Asignadas</option>
                            <option value="2">Sin Camas Asignadas</option>
                        </select>


                        <select @change="submitFilters()"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            name="days" id="days" v-model="form.days">
                            <option value="">Siempre</option>
                            <option value="1">Hoy</option>
                            <option value="7">Últimos 7 días</option>
                            <option value="30">Últimos 30 días</option>
                            <option value="90">Últimos 90 días</option>
                            <option value="180">Últimos 180 días</option>
                            <option value="365">Último año</option>
                        </select>

                        <AccessGate :permission="['admission.create']" class="sm:w-fit">
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

                </div>
            </div>
        </div>
        <div
            class="relative overflow-x-auto border border-gray-200 dark:border-gray-700/60 rounded-lg my-4 mx-4 lg:mx-10">
            <div class="min-w-full overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admissions.id')">
                                ID<span v-if="form.sortField === 'admissions.id'">{{ form.sortDirection ===
                                    'asc' ?
                                    '↑' :
                                    '↓'
                                    }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('patients.first_name')">
                                Paciente<span v-if="form.sortField === 'patients.first_name'">{{ form.sortDirection ===
                                    'asc' ?
                                    '↑' :
                                    '↓'
                                    }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('beds.room')">
                                Ubicación<span v-if="form.sortField === 'beds.room'">{{ form.sortDirection === 'asc' ?
                                    '↑' :
                                    '↓'
                                    }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('users.name')">
                                Doctor<span v-if="form.sortField === 'users.name'">{{ form.sortDirection === 'asc' ?
                                    '↑' :
                                    '↓'
                                    }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('discharged_date')">
                                Dias ingresado
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('discharged_date')">
                                Estado<span v-if="form.sortField === 'discharged_date'">{{ form.sortDirection === 'asc'
                                    ?
                                    '↑' :
                                    '↓'
                                    }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('created_at')">
                                Fecha de ingreso<span v-if="form.sortField === 'created_at'">{{ form.sortDirection ===
                                    'asc'
                                    ?
                                    '↑' :
                                    '↓'
                                    }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="admission in admissions.data.filter(admission => admission.id)" :key="admission.id"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ admission.id }}
                            </th>
                            <td scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                                    admission.patient.second_surname }}
                            </td>
                            <td class="px-6 py-4">
                                Sala: {{ admission.bed ? admission.bed.room : 'N/A' }}, Cama: {{ admission.bed ?
                                    admission.bed.number : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ admission.doctor.name }} {{ admission.doctor.last_name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ admission.days_admitted }}
                            </td>
                           <!-- Estado -->
                            <td class="px-6 py-4">
                            <span
                                v-if="admission.discharged_date == null"
                                class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap dark:bg-green-900 dark:text-green-300">
                                Ingresado
                            </span>
                            <span
                                v-else
                                class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap dark:bg-gray-700 dark:text-gray-300">
                                Dado&nbsp;de&nbsp;alta
                            </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs sm:text-sm">
                                {{ formatDate(admission.created_at) }}
                            </td>
                            <td class="px-6 py-4 flex items-center space-x-4">
                                <Link class=" text-primary-500 hover:text-primary-800"
                                    :href="route('admissions.show', admission.id)">
                                Ver
                                </Link>
                                <Link v-if="can.edit" class="text-green-500 hover:text-green-800"
                                    :href="route('admissions.edit', admission.id)">
                                Editar
                                </Link>
                                <!-- <Link method="delete" class="ml-2 text-red-500 hover:text-red-800"
                                :href="route('admissions.destroy', admission.id)" as="button">
                            Eliminar
                            </Link> -->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :pagination="admissions" :filters="form" />
        </div>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import FormatId from '@/Components/FormatId.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import SearchIcon from '@/Components/Icons/SearchIcon.vue';
import XIcon from '@/Components/Icons/XIcon.vue';
import CircleXIcon from '@/Components/Icons/CircleXIcon.vue';
import CirclePlusIcon from '@/Components/Icons/CirclePlusIcon.vue';
import PlusIcon from '@/Components/Icons/PlusIcon.vue';
import moment from 'moment/moment';
import 'moment/locale/es';
import FilterIcon from '@/Components/Icons/FilterIcon.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import BedIcon from '@/Components/Icons/BedIcon.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';
export default {
    components: {
        AppLayout,
        Link,
        PersonalizableButton,
        PrimaryLink,
        Pagination,
        BedIcon,
        FormatId,
        AccessGate,
        BreadCrumb,
        FilterIcon,
        UserIcon,
        PlusIcon,
        SearchIcon,
        XIcon,
        CirclePlusIcon,
        CircleXIcon,
    },
    props: {
        admissions: Object,
        can: [Array, Object],
        filters: Object,
    },
    data() {
        return {
            recordBeingDisabled: null,
            form: {
                search: this.filters.search || '',
                showDeleted: this.filters.show_deleted,
                sortField: this.filters.sortField || 'admissions.updated_at',
                sortDirection: this.filters.sortDirection || 'asc',
                days: this.filters.days || '',
                beds_available: this.filters.beds_available || '',
                admissions_discharged: this.filters.admissions_discharged || '',
                myRecords: this.filters.myRecords || true
            },
            timeout: 1000,
        }
    },
    methods: {
        formatDate(date) {

            return moment(date).format('DD MMMM YYYY HH:mm');
        },
        submitFilters() {
            console.log(this.form)
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            this.timeout
            this.$inertia.get(route('admissions.index'), this.form, {
                preserveState: true,
            });
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
        toggleShowDeleted() {
            this.form.showDeleted = !this.form.showDeleted;
            this.$inertia.get(route('admissions.index', {
                search: this.form.search,
                showDeleted: this.form.showDeleted
            }));
        },
    },

}
</script>

