<template>
<AppLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-white leading-tight text-center">
            <BreadCrumb :items="[
                     {
                        text: 'Fichas de Medicamentos',
                        route: route('medicationRecords.index')

                    },
                ]" />
        </h2>
    </template>




        <div class="flex items-center justify-between ">
            <div class="ml-4 my-2 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">
            <div class=" inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">
                <div class="inline-flex items-center" v-if="medicationRecords.admission_id">
                    <Link :href="route('admissions.show', medicationRecords.admission_id)"
                        class="inline-flex items-center  hover:text-blue-600 dark:hover:text-white">
                    <FormatId :id="medicationRecords.admission_id" prefix="ING"></FormatId>
                    </Link>
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 9 4-4-4-4" />
                    </svg>
                </div>
                <div class="ml-2 inline-flex items-center ">
                    Fichas de Medicamentos
                </div>
            </div>

            <Link :href="route('nurseRecords.index')" v-if="medicationRecords.admission_id"
                class="mr-6 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500 self-end">
            Remover filtro de
            <FormatId :id="medicationRecords.admission_id" prefix="ING"></FormatId>,
            </Link>
        </div>
        </div>

        <div
            class="bg-gray-100 dark:bg-gray-900 flex justify-between items-end overflow-x-auto sm:rounded-lg mt-2 lg:mx-10">


   <div class="relative mb-2">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="none"
                        viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>

                <input @input="submitFilters()"
                    class="block   p-3 ps-10 pr-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
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
            <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-2 md:content-end md:justify-end">
                <select @change="submitFilters()"
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    name="in_process" id="in_process" v-model="form.in_process">
                    <option value="true">En proceso</option>
                    <option value="false">Dados de alta</option>
                    <option value="">Todos</option>
                </select>
            <select @change="submitFilters()"
                    class="bg-gray-50 w-full  mr-2 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block  p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    name="days" id="days" v-model="form.days">
                    <option value="">Siempre</option>
                    <option value="1">Último día</option>
                    <option value="7">Últimos 7 días</option>
                    <option value="30">Últimos 30 días</option>
                    <option value="90">Últimos 90 días</option>
                    <option value="180">Últimos 180 días</option>
                    <option value="365">Último año</option>
                </select>


        <!-- Filtro para mostrar registros eliminados -->
        <button @click="toggleShowDeleted" class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap ml-4" :class="{
            'bg-red-500 hover:bg-red-600 text-white': form.showDeleted,
            'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200': !form.showDeleted
        }">
            {{ form.showDeleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
            <svg class="ml-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path v-if="form.showDeleted" fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                    clip-rule="evenodd" />
                <path v-else fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                    clip-rule="evenodd" />
            </svg>
        </button>


                    <Link :href="route('medicationRecords.create')"
                        class="flex items-center ml-4 text-base bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-full whitespace-nowrap">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Ficha de Medicamentos
                    </Link>

</div>
</div>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr> <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admission_id')">Ingreso<span v-if="form.sortField === 'admission_id'">{{ form.sortDirection === 'asc' ? '↑' :
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
                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admissions.discharged_date')">Estado<span v-if="form.sortField === 'admissions.discharged_date'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span></th>

                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('diet')">Referencias<span v-if="form.sortField === 'diet'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span></th>

                    <th scope="col" class="px-6 py-3 ">Acciones</th>
                </tr>
            </thead>
            <tbody>

                <tr v-for="record in medicationRecords.data.filter(record => record.id)" :key="record.id" :class="[
        'bg-white border-b dark:bg-gray-800 dark:border-gray-700'    ]">
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
                    <td class="px-6 py-4  w-2">
                            <span v-if="record.admission.discharged_date == null"
                                class="block w-4 h-4 bg-green-500 rounded-full mx-auto">{{  }}</span>
                            <span v-else class="block w-4 h-4 bg-orange-500 rounded-full mx-auto"></span>
                        </td>


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
        BreadCrumb

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
