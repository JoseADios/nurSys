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
            <div class="flex">
                <form @submit.prevent="submit" class="mb-2 flex-grow">
                <input @input="submit()" class="rounded-lg" type="text" name="search" id="search" v-model="form.search"
                    placeholder="Buscar ..." />

            </form>


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
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('patients.first_name')">
                            Paciente<span v-if="form.sortField === 'patients.first_name'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                                }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('beds.room')">
                            Ubicación<span v-if="form.sortField === 'beds.room'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                                }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('users.name')">
                            Doctor<span v-if="form.sortField === 'users.name'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                                }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('discharged_date')">
                            Dias ingresado<span v-if="form.sortField === 'discharged_date'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                                }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('discharged_date')">
                            Estado<span v-if="form.sortField === 'discharged_date'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                                }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('created_at')">
                            Fecha de ingreso<span v-if="form.sortField === 'created_at'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                                }}</span>
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
            this.$inertia.get(route('admissions.index'), this.form, {
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
            this.$inertia.get(route('admissions.index', {
                search: this.form.search,
                showDeleted: this.form.showDeleted
            }));
        },
    },


}


</script>
