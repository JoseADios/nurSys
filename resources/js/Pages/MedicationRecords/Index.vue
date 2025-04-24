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

    <div class="flex my-2 px-4 sm:px-0 items-center justify-end">
        <button v-if="form.admission_id" @click="form.admission_id = null; submitFilters()" class="mr-2 sm:mr-6 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500 self-end">
            Remover filtro de <FormatId :id="form.admission_id" prefix="ING" class="ml-1"></FormatId>
        </button>
    </div>

    <div class="bg-gray-100 dark:bg-gray-900 p-4 flex flex-col gap-4 lg:flex-row lg:justify-between lg:items-end  overflow-x-auto rounded-lg mx-4 lg:mx-10">
        <!-- Búsqueda - Ancho completo en móvil -->
        <div class="relative w-full lg:w-1/3 mb-4 sm:mb-0">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <SearchIcon class="size-4 text-gray-500 dark:text-gray-400" />
            </div>

            <input @input="submitFilters()" class="pl-10 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" type="text" name="search" id="search" v-model="form.search" placeholder="Buscar..." />

            <button v-if="form.search" @click="form.search = ''; submitFilters()" class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                <XIcon class="h-5 w-5" />
            </button>
        </div>

        <!-- Filtros y botones - Se apilan en móvil -->
        <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-2 md:content-end md:justify-end">
            <select @change="submitFilters()" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" name="in_process" id="in_process" v-model="form.in_process">
                <option value="true">En proceso</option>
                <option value="false">Dados de alta</option>
                <option value="">Todos</option>
            </select>

            <select @change="submitFilters()" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" name="days" id="days" v-model="form.days">
                <option value="">Siempre</option>
                <option value="1">Último día</option>
                <option value="7">Últimos 7 días</option>
                <option value="30">Últimos 30 días</option>
                <option value="90">Últimos 90 días</option>
                <option value="180">Últimos 180 días</option>
                <option value="365">Último año</option>
            </select>

            <AccessGate :permission="['medicationRecords.delete']">
                <!-- Filtro para mostrar registros eliminados -->
                <button @click="toggleShowDeleted" class="flex items-center min-w-[40%] space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap w-full sm:w-auto justify-center sm:justify-start" :class="{
                            'bg-red-500 hover:bg-red-600 text-white': form.showDeleted,
                            'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200': !form.showDeleted
                        }">
                    {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                    <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-5 w-5" />
                    <CircleXIcon v-else class="ml-1 h-5 w-5" />
                </button>
            </AccessGate>

            <AccessGate :permission="['medicationRecords.create']">
                <div class="w-full sm:w-auto">
                    <Link v-if="!form.admission_id" :href="route('medicationRecords.create')" class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-3 rounded-lg whitespace-nowrap text-sm">
                    <PlusIcon class="size-5" />
                    <span class="">Nueva Ficha</span>
                    </Link>
                    <Link v-else :href="route('medicationRecords.create', { admission_id: form.admission_id })" class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-3 rounded-lg whitespace-nowrap text-sm">
                    <PlusIcon class="size-5" />
                    <span class="">Nueva Ficha</span>
                    </Link>
                </div>
            </AccessGate>
        </div>
    </div>

    <div class="relative overflow-x-auto border border-gray-200 dark:border-gray-700/60 rounded-lg my-4 mx-4 lg:mx-10">
        <div class="min-w-full overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap hidden sm:table-cell" @click="sort('medication_records.id')">
                            ID <span v-if="form.sortField === 'medication_records.id'">{{ form.sortDirection === 'asc' ?
                                    '↑' :
                                    '↓' }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admission_id')">Ingreso<span v-if="form.sortField === 'admission_id'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span></th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('patients.first_name')">
                            Paciente <span v-if="form.sortField === 'patients.first_name'">{{ form.sortDirection ===
                                'asc' ?
                                '↑' :
                                '↓'
                                }}</span>
                        </th>

                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('diagnosis')">Diagnóstico <span v-if="form.sortField === 'diagnosis'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                                }}</span></th>

                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('diet')">Dieta<span v-if="form.sortField === 'diet'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span></th>

                        <th scope="col" class="px-6 py-3 ">Acciones</th>
                    </tr>
                </thead>
                <tbody>

                    <tr v-for="record in medicationRecords.data.filter(record => record.id)" :key="record.id" :class="[
        'bg-white border-b dark:bg-gray-800 dark:border-gray-700'    ]">

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
                                    {{ record.admission.created_at }} N/A
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"> {{ record.admission.patient.first_name }} {{
                                record.admission.patient.first_surname }} {{
                                record.admission.patient.second_surname }}</td>
                        <td class="px-6 py-4">{{ record.diagnosis }} </td>

                        <td class="px-6 py-4">{{ record.diet }}</td>

                        <td class="px-6 py-4 flex items-center space-x-4">

                            <button class="text-blue-500 hover:text-blue-800" @click="MedicationRecordShow(record.id)">
                                Ver
                            </button>

                            <button class="text-yellow-500 ml-2 hover:text-yellow-800" @click="MedicationRecordEdit(record.id)">
                                Editar
                            </button>

                        </td>

                    </tr>

                </tbody>
            </table>
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
export default {
    props: {
        medicationRecords: Object,
        filters: Object,
        admission: Array,
    },
    components: {
        AppLayout,
        Link,

        Pagination,
        AccessGate,
        FormatId,
        BreadCrumb,
        PlusIcon,
        SearchIcon,
        XIcon,
        CirclePlusIcon,
        CircleXIcon,

    },
    data() {
        return {

            form: {
                search: this.filters.search || '',
                in_process: this.filters.in_process || '',
                showDeleted: this.filters.show_deleted,
                sortField: this.filters.sortField || 'medication_records.updated_at',
                sortDirection: this.filters.sortDirection || 'asc',
                days: this.filters.days || '',
            },
            timeout: 1000,

        }
    },
    methods: {
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

        sort(field) {
            this.form.sortField = field;
            this.form.sortDirection = this.form.sortDirection === 'asc' ? 'desc' : 'asc';
            this.submitFilters();
        },
        toggleShowDeleted() {
            this.form.showDeleted = !this.form.showDeleted;
            this.submitFilters();
        },

        MedicationRecordShow(id) {
            this.$inertia.get(route('medicationRecords.show', id));
        },
        MedicationRecordEdit(id) {
            this.$inertia.get(route('medicationRecords.edit', id));
        },

    }

}
</script>
