<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Admisiones
            </h2>
        </template>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <!-- si la tabla ocupa el ancho completo de la ventana, para ponerlo al 80% de ancho de la ventana modificar la clase "w-full" por "w-1/2" -->
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Paciente
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Cama
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Diagnostico de ingreso
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Fecha de ingreso
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="admission in admissions" :key="admission.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ admission.patient.first_name }}
                        </th>
                        <td class="px-6 py-4">
                            {{ admission.bed.number }}
                        </td>
                        <td class="px-6 py-4">
                            {{ admission.admission_dx }}
                        </td>
                        <td class="px-6 py-4">
                            {{ admission.created_at }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        admissions: Array
    },
    components: {
        AppLayout,
    },
}
</script>
