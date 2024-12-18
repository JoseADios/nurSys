<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Registros de Enfermería
            </h2>
        </template>

        <!-- <div class="text-white">Datos: {{ admission_id }}</div> -->

        <div class="flex flex-col items-center justify-center mt-10">
            <Link :href="route('nurseRecords.create')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
            Crear nuevo registro de enfermería
            </Link>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Paciente
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Enfermera
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
                    <tr v-for="nurseRecord in nurseRecords" :key="nurseRecord.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ nurseRecord.admission.patient.first_name }} {{
                                nurseRecord.admission.patient.first_surname }} {{
                                nurseRecord.admission.patient.second_surname }}
                        </th>
                        <td class="px-6 py-4">
                            {{ nurseRecord.nurse.name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ nurseRecord.created_at }}
                        </td>
                        <td class="px-6 py-4">
                            <Link class="ml-2 text-green-500 hover:text-green-800" :href="route('nurseRecords.edit',
                                {
                                    'id': nurseRecord.id,
                                    'adm_id': admission_id
                                })" as="button">
                            Abrir
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </AppLayout>
</template>
<script>

import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        nurseRecords: Array,
    },
    components: {
        AppLayout,
        Link,
    },

}
</script>
