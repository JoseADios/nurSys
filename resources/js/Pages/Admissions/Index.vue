<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Ingresos
            </h2>
        </template>

        <div v-if="can.create" class="flex flex-col items-center justify-center mt-10">
            <Link :href="route('admissions.create')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
            Crear nuevo ingreso
            </Link>
        </div>

        <!-- <div class="text-white">

        </div> -->

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
            <form @submit.prevent="submit" class="mb-2">
                <input @input="submit()" class="rounded-lg" type="text" name="search" id="search" v-model="form.search"
                    placeholder="Buscar ..." />

            </form>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Paciente
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Ubicación
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Doctor
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Dias ingresado
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Estado
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Fecha de ingreso
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="admission in admissions.data.filter(admission => admission.id)" :key="admission.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                                admission.patient.second_surname }}
                        </th>
                        <td class="px-6 py-4">
                            Sala: {{ admission.bed ? admission.bed.room : 'N/A' }}, Cama: {{ admission.bed ?
                            admission.bed.number : 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ admission.doctor.name }} {{ admission.doctor.last_name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ admission.days_admitted }}
                        </td>
                        <td class="px-6 py-4">
                            <div v-if="admission.discharged_date == null">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Ingresado</span>
                            </div>
                            <div v-else>
                                <span
                                    class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">Dado
                                    de alta</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{ admission.created_at }}
                        </td>
                        <td class="px-6 py-4">
                            <Link class="ml-2 text-blue-500 hover:text-blue-800"
                                :href="route('admissions.show', admission.id)">
                            Ver
                            </Link>
                            <Link v-if="can.edit" class="text-green-500 hover:text-green-800"
                                :href="route('admissions.edit', admission.id)">
                            Editar
                            </Link>
                            <!-- <Link method="delete" class="ml-2 text-red-500 hover:text-red-800"
                                :href="route('admissions.destroy', admission.id)" as="button">
                            Eliminar
                            </Link> -->
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :pagination="admissions" />
        </div>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
export default {
    props: {
        admissions: Object,
        can: [Array, Object],
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
            this.$inertia.get(route('admissions.index'), this.form, {
                preserveState: true,
            });
        },
    },

}


</script>
