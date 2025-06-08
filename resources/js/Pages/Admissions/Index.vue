<template>
    <AppLayout>
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

        <div class="flex flex-col sm:flex-row gap-3 mb-3">
                    <!-- Búsqueda general - siempre visible -->
                    <div class="relative flex-grow">
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

            <!-- Filtros y botones - Reorganizados para mejor responsividad -->
           <div class="flex justify-center items-center whitespace-nowrap md:flex-wrap gap-2">
                <!-- Botón para ver registros eliminados -->
               <AccessGate :permission="['admission.delete']">
                        <!-- Filtro para mostrar registros eliminados -->
                        <PersonalizableButton custom-class="whitespace-nowrap" @click="toggleShowDeleted" :color="form.showDeleted ? 'red' : 'gray'">
                                {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                                <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-5 w-5" />
                                <CircleXIcon v-else class="ml-1 h-5 w-5" />
                            </PersonalizableButton>
                    </AccessGate>

                      <AccessGate :permission="['admission.create']">

                           <PrimaryLink v-if="!form.admission_id" :href="route('admissions.create')">
                       <PlusIcon class="size-5" />
                        <span class="">Nuevo Registro</span>
                        </PrimaryLink>
                        <PrimaryLink v-else :href="route('admissions.create', { admission_id: form.admission_id })">
                        <PlusIcon class="size-5" />
                        <span class="">Nuevo Registro</span>
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
                            name="admissions_discharged" id="admissions_discharged" v-model="form.admissions_discharged">
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
                            <option value="1">Último día</option>
                            <option value="7">Últimos 7 días</option>
                            <option value="30">Últimos 30 días</option>
                            <option value="90">Últimos 90 días</option>
                            <option value="180">Últimos 180 días</option>
                            <option value="365">Último año</option>
                        </select>



                         <AccessGate :permission="['admission.create']" class="w-full sm:w-fit">
                            <!-- Filtro Mis Registros con ícono más grande -->
                            <button
                                class="w-full sm:w-fit border flex whitespace-nowrap items-center justify-center border-gray-300 dark:border-gray-700 px-2.5 pr-1 rounded-md transition-colors duration-200 "
                                :class="{
                                    'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200': form.myRecords,
                                    'bg-transparent text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800': !form.myRecords
                                }" @click="toggleFilterMyRecords" title="Mostrar solo mis registros">
                                Mis registros
                                <div class="relative p-2.5 pl-1">
                                    <UserIcon class="h-5 w-5" />
                                    <FilterIcon class="h-3 w-3 absolute bottom-1 right-1"
                                        :class="{ 'text-indigo-600 dark:text-indigo-400': form.myRecords }" />
                                    <div v-if="form.myRecords"
                                        class="absolute -top-1 -right-1 h-2 w-2 bg-indigo-500 rounded-full">
                                    </div>
                                </div>
                            </button>

                        </AccessGate>



                    </div>

                </div>



          </div>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
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
                            Estado<span v-if="form.sortField === 'discharged_date'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                                }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('created_at')">
                            Fecha de ingreso<span v-if="form.sortField === 'created_at'">{{ form.sortDirection === 'asc'
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
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ admission.id }}
                        </th>
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
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
                        <td class="px-6 py-4">
                            <div v-if="admission.discharged_date == null">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Ingresado</span>
                            </div>
                            <div v-else>
                                <span
                                    class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">Dado
                                    de alta</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
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
    props: {
        admissions: Object,
        can: [Array, Object],
        filters: Object,
    },
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
