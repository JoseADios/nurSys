<template>
<AppLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-white leading-tight text-center">
            Ordenes médicas
        </h2>
    </template>

    <!-- <div class="text-white">Datos:  {{ admission_id }}</div> -->

    <!-- Navigation -->
    <div v-if="admission_id" class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
        <Link :href="route('admissions.show', admission_id)" class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        <span class="font-medium">Volver</span>
        </Link>
    </div>

    <div class="flex flex-col items-center justify-center mt-10">
        <Link :href="route('medicalOrders.create', { admission_id: admission_id })" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
        Crear nueva Orden Medica
        </Link>
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
        <div class="flex">
    <form @submit.prevent="submit" class="mb-2 flex-grow">
        <input @input="submit()" class="rounded-lg " type="text" name="search" id="search" v-model="form.search" placeholder="Buscar ..." />
    </form>


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

</div>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3" @click="sort('admission_id')"><span v-if="form.sortField === 'admission_id'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span>
                        Ingreso
                    </th>
                    <th scope="col" class="px-6 py-3" @click="sort('patients.first_name')"><span v-if="form.sortField === 'patients.first_name'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span>
                        Paciente
                    </th>
                    <th scope="col" class="px-6 py-3" @click="sort('discharged_date')"><span v-if="form.sortField === 'admission.discharged_date'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span>
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3" @click="sort('users.name')"><span v-if="form.sortField === 'doctor_id'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span>
                        Doctor
                    </th>
                    <th scope="col" class="px-6 py-3" @click="sort('created_at')"><span v-if="form.sortField === 'created_at'">{{ form.sortDirection === 'asc' ? '↑' :
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
        <Pagination :pagination="medicalOrders" />
    </div>

</AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link
} from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
export default {
    props: {
        medicalOrders: Array,
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
                sortField: this.filters.sortField || 'medication_records.updated_at',
                sortDirection: this.filters.sortDirection || 'asc',
            },
            timeout: 1000,
        }
    },
    methods: {

        submit() {

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
            this.submit();
        },
        toggleShowDeleted() {
            this.form.showDeleted = !this.form.showDeleted;
            this.$inertia.get(route('medicationRecords.index', {
                search: this.form.search,
                showDeleted: this.form.showDeleted
            }));
        },
    },

}
</script>
