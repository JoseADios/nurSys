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
        <form @submit.prevent="submit" class="mb-2">
            <input @input="submit()" class="rounded-lg" type="text" name="search" id="search" v-model="form.search" placeholder="Buscar ..." />

        </form>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Ingreso
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Paciente
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Doctor
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Fecha
                    </th>
                    <th scope="col" class="px-6 py-3">
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
    },

}
</script>
