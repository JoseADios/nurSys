<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Pacientes
            </h2>
        </template>

        <div class="flex flex-col items-center justify-center mt-10">
            <Link :href="route('patients.create')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
            Crear nuevo paciente
            </Link>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Nombre
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Teléfono
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Cédula
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nacionalidad
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Correo
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ARS
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="patient in patients" :key="patient.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                        </th>
                        <td class="px-6 py-4">
                            {{ patient.phone }}
                        </td>
                        <td class="px-6 py-4">
                            {{ patient.identification_card }}
                        </td>
                        <td class="px-6 py-4">
                            {{ patient.nationality }}
                        </td>
                        <td class="px-6 py-4">
                            {{ patient.email }}
                        </td>
                        <td class="px-6 py-4">
                            {{ patient.ars }}
                        </td>
                        <td class="px-6 py-4">
                            <Link class="ml-2 text-blue-500 hover:text-blue-800"
                                :href="route('patients.show', patient.id)">
                            Ver
                            </Link>
                            <Link class="text-green-500 hover:text-green-800"
                                :href="route('patients.edit', patient.id)">
                            Editar
                            </Link>
                            <!-- <Link method="delete" class="ml-2 text-red-500 hover:text-red-800"
                                :href="route('patients.destroy', patient.id)" as="button">
                            Eliminar
                            </Link> -->
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
        patients: Array,
    },
    components: {
        AppLayout,
        Link,
    },

}


</script>
