<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <BreadCrumb :items="[
                    {
                        text: 'Fichas de Medicamentos',
                        route: route('medicationRecords.index')

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
            class="bg-gray-100 dark:bg-gray-900 py-4 flex flex-col gap-4 items-center lg:flex-row lg:justify-between lg:items-end xl:items-center overflow-x-auto rounded-lg mx-4 lg:mx-10">
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
                        <select @change="submitFilters()"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                           name="in_process" id="in_process" v-model="form.in_process">
                            <option value="">Todos</option>
                            <option value="true">En proceso</option>
                            <option value="false">Dados de alta</option>

                        </select>

                        <select @change="submitFilters()"
                            class="w-full h-min  border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
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
                 <AccessGate :permission="['medicationRecord.view']" >
                 <div class="flex flex-col sm:flex-row w-full gap-3 xl:ml-2 whitespace-nowrap xl:items-center xl:w-[80%]">
                   <AccessGate :permission="['medicationRecord.delete']">
                        <!-- Filtro para mostrar registros eliminados -->
                        <button @click="toggleShowDeleted"
                            class="flex items-center  min-w-[40%] space-x-2 px-4 py-2 rounded-lg transition-colors w-full sm:w-auto justify-center sm:justify-start"
                            :class="{
                                'bg-red-500 hover:bg-red-600 text-white': form.showDeleted,
                                'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200': !form.showDeleted
                            }">
                            {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                            <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-5 w-5" />
                            <CircleXIcon v-else class="ml-1 h-5 w-5" />
                        </button>
                    </AccessGate>

                    <AccessGate :permission="['medicationRecord.create']">
                        <PrimaryLink v-if="!form.admission_id" :href="route('medicationRecords.create')">
                       <PlusIcon class="size-5" />
                        <span class="">Nuevo Registro</span>
                        </PrimaryLink>
                        <PrimaryLink v-else :href="route('medicationRecords.create', { admission_id: form.admission_id })">
                        <PlusIcon class="size-5" />
                        <span class="">Nuevo Registro</span>
                        </PrimaryLink>

                    </AccessGate>
                </div>
                </AccessGate>
            </div>
        </div>

        <div
            class="relative overflow-x-auto border border-gray-200 dark:border-gray-700/60 rounded-lg my-4 mx-4 lg:mx-10">
            <div class="min-w-full overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap hidden sm:table-cell"
                                @click="sort('medication_records.id')">
                                ID <span v-if="form.sortField === 'medication_records.id'">{{ form.sortDirection ===
                                    'asc' ?
                                    '↑' :
                                    '↓' }}</span>
                            </th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admission_id')">Ingreso<span
                                    v-if="form.sortField === 'admission_id'">{{ form.sortDirection === 'asc' ? '↑' :
                                        '↓'
                                    }}</span></th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('patients.first_name')">
                                Paciente <span v-if="form.sortField === 'patients.first_name'">{{ form.sortDirection ===
                                    'asc' ?
                                    '↑' :
                                    '↓'
                                }}</span>
                            </th>

                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admissions.doctor_id')">Doctor
                                <span v-if="form.sortField === 'admissions.doctor_id'">{{ form.sortDirection === 'asc' ?
                                    '↑' :
                                    '↓'
                                }}</span>
                            </th>


                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('created_at')">Fecha de
                                Creación<span v-if="form.sortField === 'created_at'">{{ form.sortDirection === 'asc' ?
                                    '↑' :
                                    '↓'
                                    }}</span></th>


                            <th scope="col" class="px-6 py-3 ">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr v-for="record in medicationRecords.data.filter(record => record.id)" :key="record.id"
                            :class="[
                                'bg-white border-b dark:bg-gray-800 dark:border-gray-700']">

                            <th scope="row" class="px-6 py-4">{{ record.id }}</th>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div :class="[{
                                    'max-w-fit border border-green-600 text-green-600 px-2.5 py-0.5 rounded-md dark:border-green-900 dark:text-green-300': record.in_process,
                                    'max-w-fit border border-gray-500 text-gray-800 px-2.5 py-0.5 rounded-md dark:border-gray-600 dark:text-gray-300': !record.in_process
                                }]">
                                    <div v-if="record.admission.bed">
                                        <FormatId :id="record.admission.id" prefix="ING"></FormatId>,
                                        Cama {{ record.admission.bed.number }}, Sala {{
                                            record.admission.bed.room
                                        }}
                                    </div>
                                    <div v-else>
                                        <FormatId :id="record.admission.id" prefix="ING"></FormatId>,
                                       N/A
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium  whitespace-nowrap dark:text-white">
                          {{ record.admission.patient.first_name }} {{
                                record.admission.patient.first_surname }} {{

                                    record.admission.patient.second_surname }}</td>

                            <td class="px-6 py-4">{{ record.admission.doctor.name }} {{
                                record.admission.doctor.last_name }}
                            </td>
                            <td class="px-6 py-4">{{ formatDate(record.created_at) }}</td>

                            <td class="px-6 py-4 flex items-center space-x-4">

                                <Link  class="flex-1 text-primary-500 hover:text-primary-800"
                                    :href="route('medicationRecords.show', record.id)" as="button">
                                    Ver
                                </Link >



                            </td>

                        </tr>

                    </tbody>
                </table>
                <div v-if="!medicationRecords.data.length"
                    class="text-center text-gray-500 dark:text-gray-400 py-4 w-full">
                    No hay registros disponibles.
                </div>
            </div>
            <Pagination :pagination="medicationRecords" :filters="form" />
        </div>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
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
import UserIcon from '@/Components/Icons/UserIcon.vue';
import moment from 'moment/moment';
import 'moment/locale/es';
import FilterIcon from '@/Components/Icons/FilterIcon.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';
export default {
    props: {
        medicationRecords: Object,
        filters: Object,
        admission: Array,
    },
    components: {
        AppLayout,
        Link,
        FilterIcon,
        Pagination,
        AccessGate,
        FormatId,
        BreadCrumb,
        PlusIcon,
        PrimaryLink,
        SearchIcon,
        XIcon,
        CirclePlusIcon,
        CircleXIcon,
        UserIcon,


    },
    data() {
        return {

            form: {
                search: this.filters.search || '',
                in_process: this.filters.in_process || "",
                showDeleted: this.filters.show_deleted,
                sortField: this.filters.sortField || 'medication_records.updated_at',
                sortDirection: this.filters.sortDirection || 'asc',
                days: this.filters.days || '',
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
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            this.timeout = setTimeout(() => {
                this.$inertia.get(route('medicationRecords.index'), this.form, {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true
                });
            }, 300);
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
            this.submitFilters();
        },


        MedicationRecordEdit(id) {
            this.$inertia.get(route('medicationRecords.edit', id));
        },

    }

}
</script>
