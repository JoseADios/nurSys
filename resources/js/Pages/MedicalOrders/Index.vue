<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">

                <BreadCrumb :items="[
                    {
                        text: 'Órdenes Médicas',
                        route: route('medicalOrders.index')

                    },
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
            class="bg-gray-100 dark:bg-gray-900 p-4 flex flex-col gap-4 lg:flex-row lg:justify-between lg:items-end  overflow-x-auto rounded-lg mx-4 lg:mx-10">
            <!-- Búsqueda - Ancho completo en móvil -->
            <div class="relative w-full lg:w-1/3 mb-4 sm:mb-0">
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
            <div class="flex flex-col w-full lg:w-auto xl:flex-row space-y-3 sm:space-y-3 xl:space-y-0 xl:flex-grow">

                <!-- Primera fila en dispositivos medianos -->
                <div class="flex flex-col sm:flex-row w-full gap-3 items-center">
                    <!-- Grupo: Mis Registros + En proceso -->
                    <div class="flex w-full flex-col sm:flex-row xl:w-full gap-2 items-center">
                        <AccessGate :permission="['temperatureRecord.create']" class="w-full sm:w-fit">
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


                        <select @change="submitFilters()"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            name="in_process" id="in_process" v-model="form.in_process">
                            <option value="true">En proceso</option>
                            <option value="false">Dados de alta</option>
                            <option value="">Todos</option>
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
                    </div>

                </div>

                <div class="flex flex-col sm:flex-row w-full gap-3 xl:ml-2 xl:items-center xl:w-[80%]">
                    <AccessGate :permission="['medicalOrders.delete']">
                        <!-- Filtro para mostrar registros eliminados -->
                        <button @click="toggleShowDeleted"
                            class="flex items-center min-w-[40%] space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap w-full sm:w-auto justify-center sm:justify-start"
                            :class="{
                                'bg-red-500 hover:bg-red-600 text-white': form.showDeleted,
                                'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200': !form.showDeleted
                            }">
                            {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                            <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-5 w-5" />
                            <CircleXIcon v-else class="ml-1 h-5 w-5" />
                        </button>
                    </AccessGate>

                    <AccessGate :permission="['medicalOrders.create']">

                        <Link v-if="!form.admission_id" :href="route('medicalOrders.create')"
                            class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-3 rounded-lg whitespace-nowrap text-sm">
                        <PlusIcon class="size-5" />
                        <span class="">Nueva Ordén</span>
                        </Link>
                        <Link v-else :href="route('medicalOrders.create', { admission_id: form.admission_id })"
                            class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-3 rounded-lg whitespace-nowrap text-sm">
                        <PlusIcon class="size-5" />
                        <span class="">Nueva Ordén</span>
                        </Link>

                    </AccessGate>
                </div>
            </div>
        </div>

        <div
            class="relative overflow-x-auto border border-gray-200 dark:border-gray-700/60 rounded-lg my-4 mx-4 lg:mx-10">
            <div class="min-w-full overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap hidden sm:table-cell"
                                @click="sort('medical_orders.id')">
                                ID <span v-if="form.sortField === 'medical_orders.id'">{{ form.sortDirection === 'asc' ?
                                    '↑' :
                                    '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admission_id')"><span
                                    v-if="form.sortField === 'admission_id'">{{ form.sortDirection === 'asc' ? '↑' :
                                        '↓'
                                    }}</span>
                                Ingreso
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('patients.first_name')"><span
                                    v-if="form.sortField === 'patients.first_name'">{{ form.sortDirection === 'asc' ?
                                        '↑' :
                                        '↓'
                                    }}</span>
                                Paciente
                            </th>

                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('medical_orders.doctor_id')">
                                <span v-if="form.sortField === 'medical_orders.doctor_id'">{{ form.sortDirection ===
                                    'asc' ? '↑' :
                                    '↓'
                                    }}</span>
                                Doctor
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('created_at')"><span
                                    v-if="form.sortField === 'created_at'">{{ form.sortDirection === 'asc' ? '↑' :
                                        '↓'
                                    }}</span>
                                Fecha
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="medicalOrder in medicalOrders.data.filter(medicalOrder => medicalOrder.id)"
                            :key="medicalOrder.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4">{{ medicalOrder.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div :class="[{
                                    'max-w-fit border border-green-600 text-green-600 px-2.5 py-0.5 rounded-md dark:border-green-900 dark:text-green-300': medicalOrder.in_process,
                                    'max-w-fit border border-gray-500 text-gray-800 px-2.5 py-0.5 rounded-md dark:border-gray-600 dark:text-gray-300': !medicalOrder.in_process
                                }]">
                                    <div v-if="medicalOrder.admission.bed">
                                        <FormatId :id="medicalOrder.admission.id" prefix="ING"></FormatId>,
                                        Cama {{ medicalOrder.admission.bed.number }}, Sala {{
                                            medicalOrder.admission.bed.room
                                        }}
                                    </div>
                                    <div v-else>
                                        <FormatId :id="medicalOrder.admission.id" prefix="ING"></FormatId>,
                                        {{ formatDate(medicalOrder.admission.created_at) }} N/A
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ medicalOrder.admission.patient.first_name }} {{
                                    medicalOrder.admission.patient.first_surname }} {{
                                    medicalOrder.admission.patient.second_surname }}
                            </td>

                            <td class="px-6 py-4">
                                {{ medicalOrder.admission.doctor.name }} {{ medicalOrder.admission.doctor.last_name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ formatDate(medicalOrder.created_at) }}
                            </td>
                            <td class="px-6 py-4">
                                <Link class="ml-2 text-green-500 hover:text-green-800"
                                    :href="route('medicalOrders.show', medicalOrder.id)" as="button">
                                Abrir
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="!medicalOrders.data.length" class="text-center text-gray-500 dark:text-gray-400 py-4 w-full">
                    No hay registros disponibles.
                </div>
            </div>
            <Pagination :pagination="medicalOrders" :filters="form" />
        </div>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link
} from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import FormatId from '@/Components/FormatId.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import SearchIcon from '@/Components/Icons/SearchIcon.vue';
import XIcon from '@/Components/Icons/XIcon.vue';
import CircleXIcon from '@/Components/Icons/CircleXIcon.vue';
import CirclePlusIcon from '@/Components/Icons/CirclePlusIcon.vue';
import PlusIcon from '@/Components/Icons/PlusIcon.vue';
import moment from 'moment/moment';
import 'moment/locale/es';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import FilterIcon from '@/Components/Icons/FilterIcon.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
export default {
    props: {
        medicalOrders: Object,
        admission_id: Number,
        filters: Object,
    },
    components: {
        AppLayout,
        Link,
        Pagination,
        FormatId,
        BreadCrumb,
        PlusIcon,
        SearchIcon,
        XIcon,
        CirclePlusIcon,
        CircleXIcon,
        UserIcon,
        FilterIcon,
        AccessGate
    },
    data() {
        return {
            recordBeingDisabled: null,
            form: {
                search: this.filters.search || '',
                showDeleted: this.filters.show_deleted,
                sortField: this.filters.sortField || 'medical_orders.updated_at',
                sortDirection: this.filters.sortDirection || 'asc',
                days: this.filters.days || '',
                search: this.filters.search || '',
                in_process: this.filters.in_process || '',
                myRecords: this.filters.myRecords || true
            },
            timeout: 1000,
        }
    },
    methods: {
        toggleFilterMyRecords() {
            this.form.myRecords = !this.form.myRecords;
            this.submitFilters();
        },
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY HH:mm');
        },
        submitFilters() {

            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            this.timeout
            this.$inertia.get(route('medicalOrders.index'), this.form, {
                preserveState: true,
            });
        },
        sort(field) {
            this.form.sortField = field;
            this.form.sortDirection = this.form.sortDirection === 'asc' ? 'desc' : 'asc';
            this.submitFilters();
        },
        toggleShowDeleted() {
            this.form.showDeleted = !this.form.showDeleted;
            this.$inertia.get(route('medicalOrders.index', {
                search: this.form.search,
                showDeleted: this.form.showDeleted
            }));
        },
    },

}
</script>
