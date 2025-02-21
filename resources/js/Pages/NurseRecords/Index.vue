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

        <div class="flex">
            <form @submit.prevent="submit" class="mb-2">
                <input @input="submit()" class="rounded-lg" type="text" name="search" id="search" v-model="form.search" placeholder="Buscar ..." />

            </form>
            <AccessGate :permission="['medicationRecords.delete']">
                <!-- Filtro para mostrar registros eliminados -->
                <button @click="toggleShowDeleted" class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap ml-4" :class="{
            'bg-red-500 hover:bg-red-600 text-white': form.showDeleted,
            'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200': !form.showDeleted
        }">
                    {{ form.showDeleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                    <svg class="ml-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path v-if="form.showDeleted" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z" clip-rule="evenodd" />
                        <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                    </svg>
                </button>
            </AccessGate>
        </div>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3" @click="sort('patients.first_name')">
                        Paciente
                        <span v-if="form.sortField === 'patients.first_name'">
                            {{ form.sortDirection === 'asc' ? '↑' : '↓' }}
                        </span>
                    </th>
                    <th scope="col" class="px-6 py-3" @click="sort('users.name')">
                        Enfermera
                        <span v-if="form.sortField === 'users.name'">
                            {{ form.sortDirection === 'asc' ? '↑' : '↓' }}
                        </span>
                    </th>
                    <th scope="col" class="px-6 py-3" @click="sort('admission.discharged_date')">
                        Estado
                        <span v-if="form.sortField === 'admission.discharged_date'">
                            {{ form.sortDirection === 'asc' ? '↑' : '↓' }}
                        </span>
                    </th>
                    <th scope="col" class="px-6 py-3" @click="sort('created_at')">
                        Fecha
                        <span v-if="form.sortField === 'created_at'">
                            {{ form.sortDirection === 'asc' ? '↑' : '↓' }}
                        </span>
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
                        <span v-if="nurseRecord.admission.discharged_date == null" class="block w-4 h-4 bg-green-500 rounded-full mx-auto"></span>
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
import AccessGate from '@/Components/Access/AccessGate.vue';
export default {
    props: {
        nurseRecords: Object,
        filters: Object,
        admission_id: {
            type: Number,
            default: null
        }
    },
    components: {
        AppLayout,
        Link,
        Pagination,
        AccessGate
    },
    data() {
        return {
            form: {
                search: this.filters.search || '',
                showDeleted: this.filters.show_deleted,
                sortField: this.filters.sortField || 'nurse_records.updated_at',
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
            this.$inertia.get(route('nurseRecords.index'), this.form, {
                preserveState: true,
            });
        },
        sort(field) {
            if (this.form.sortField === field) {
                this.form.sortDirection = this.form.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.form.sortField = field;
                this.form.sortDirection = 'asc';
            }
            this.submit();
        },
        toggleShowDeleted() {
            this.form.showDeleted = !this.form.showDeleted;
            this.$inertia.get(route('nurseRecords.index'), {
                showDeleted: this.form.showDeleted,
            });
        },

    },

}
</script>
