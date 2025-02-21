<template>
<AppLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-white leading-tight text-center">
            Registros de Enfermería
        </h2>
    </template>

    <!-- <div class="text-white">{{ typeof(admission_id) }}</div> -->

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
        <Link :href="route('nurseRecords.create', { admission_id: admission_id })" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
        Crear nuevo Registro de Enfermería
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
                        Paciente
                    </th>
                    <th scope="col" class="px-6 py-3 ">
                        Enfermera
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Estado
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
                <tr v-for="nurseRecord in nurseRecords.data.filter(nurseRecord => nurseRecord.id)" :key="nurseRecord.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ nurseRecord.admission.patient.first_name }} {{
                                nurseRecord.admission.patient.first_surname }} {{
                                nurseRecord.admission.patient.second_surname }}
                    </th>
                    <td class="px-6 py-4 w-2">
                            <span v-if="nurseRecord.admission.discharged_date == null"
                                class="block w-4 h-4 bg-green-500 rounded-full mx-auto"></span>
                            <span v-else class="block w-4 h-4 bg-orange-500 rounded-full mx-auto"></span>
                        </td>
                    <td class="px-6 py-4">
                        {{ nurseRecord.nurse.name }} {{ nurseRecord.nurse.last_name }}
                    </td>

                    <td class="px-6 py-4">
                        {{ nurseRecord.created_at }}
                    </td>
                    <td class="px-6 py-4">
                        <Link class="ml-2 text-green-500 hover:text-green-800" :href="route('nurseRecords.edit', nurseRecord.id)" as="button">
                        Abrir
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination :pagination="nurseRecords" />
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
        nurseRecords: Array,
        filters: Object,
        admission_id: {
            type: Number,
            default: null
        }
    },
    components: {
        AppLayout,
        Link,
        Pagination
    },
    data() {
        return {
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
            this.$inertia.get(route('nurseRecords.index'), this.form, {
                preserveState: true,
            });
        },
    },

}
</script>
