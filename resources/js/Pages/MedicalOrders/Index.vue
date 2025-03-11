<template>
<AppLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-white leading-tight text-center">
            Ordenes médicas
        </h2>
    </template>

    <!-- <div class="text-white">Datos:  {{ admission_id }}</div> -->



    <div class="flex items-center justify-between ">
            <div class="ml-4 my-2 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">
            <div class=" inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">
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
                    Fichas de Medicamentos
                </div>
            </div>

            <Link :href="route('medicalOrders.index')" v-if="admission_id"
                class="mr-6 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500 self-end">
            Remover filtro de
            <FormatId :id="admission_id" prefix="ING"></FormatId>,
            </Link>
        </div>
        </div>

        <div
            class="bg-gray-100 dark:bg-gray-900 flex justify-between items-end overflow-x-auto sm:rounded-lg mt-4 lg:mx-10">

            <div class="relative ">
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
            <div class="flex items-center">
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
        <button @click="toggleShowDeleted" class="flex mb-2 items-center space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap ml-4" :class="{
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

                    <Link :href="route('medicalOrders.create')"
                        class="flex items-center ml-4 text-base bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-full whitespace-nowrap">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Orden Medica
                    </Link>

                </div>
</div>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admission_id')"><span v-if="form.sortField === 'admission_id'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span>
                        Ingreso
                    </th>
                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('patients.first_name')"><span v-if="form.sortField === 'patients.first_name'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span>
                        Paciente
                    </th>
                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admissions.discharged_date')"><span v-if="form.sortField === 'admissions.discharged_date'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span>
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('users.name')"><span v-if="form.sortField === 'users.name'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span>
                        Doctor
                    </th>
                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('created_at')"><span v-if="form.sortField === 'created_at'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span>
                        Fecha
                    </th>
                    <th scope="col" class="px-6 py-3" >
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="medicalOrder in medicalOrders.data.filter(medicalOrder => medicalOrder.id)" :key="medicalOrder.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4">   ING-00{{ medicalOrder.admission.id }},
                                Cama {{ medicalOrder.admission.bed.number }}, Sala {{
                                    medicalOrder.admission.bed.room }}</th>
                    <td  class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ medicalOrder.admission.patient.first_name }} {{
                                medicalOrder.admission.patient.first_surname }} {{
                                medicalOrder.admission.patient.second_surname }}
                    </td>


                    <td class="px-6 py-4  w-2">
                           <span v-if="medicalOrder.admission.discharged_date == null"
                                class="block w-4 h-4 bg-green-500 rounded-full mx-auto"></span>
                            <span v-else class="block w-4 h-4 bg-orange-500 rounded-full mx-auto"></span>
                        </td>
                    <td class="px-6 py-4">
                        {{ medicalOrder.doctor.name }}
                    </td>

                    <td class="px-6 py-4">
                        {{ medicalOrder.created_at }}
                    </td>
                    <td class="px-6 py-4">
                        <Link class="ml-2 text-green-500 hover:text-green-800" :href="route('medicalOrders.edit', medicalOrder.id)" as="button">
                        Abrir
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
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
export default {
    props: {
        medicalOrders: Object,
        admission_id: Number,
        filters: Object,
    },
    components: {
        AppLayout,
        Link,
        Pagination
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
            },
            timeout: 1000,
        }
    },
    methods: {

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
