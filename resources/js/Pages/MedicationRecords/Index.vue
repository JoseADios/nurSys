<template>
<AppLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-white leading-tight text-center">
            Fichas Medicamentos
        </h2>
    </template>

    <div class="flex flex-col items-center justify-center mt-10">
        <Link as="button" :href="route('medicationRecords.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
        Crear Nueva Ficha Medica
        </Link>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">

        <div class="flex">
    <form @submit.prevent="submit" class="mb-2 flex-grow">
        <input @input="submit()" class="rounded-lg " type="text" name="search" id="search" v-model="form.search" placeholder="Buscar ..." />
    </form>

    <AccessGate :permission="['medicationRecords.delete']">
        <!-- Filtro para mostrar registros eliminados -->
        <button @click="toggleShowDeleted" class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap ml-4" :class="{
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
    </AccessGate>
</div>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr> <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admission_id')">Ingreso<span v-if="form.sortField === 'admission_id'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span></th>

                                <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('diagnosis')">Diagnóstico <span v-if="form.sortField === 'diagnosis'">{{ form.sortDirection === 'asc' ?
                                '↑' :
                                '↓'
                                }}</span></th>
                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('admissions.discharged_date')">Estado<span v-if="form.sortField === 'admissions.discharged_date'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span></th>

                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('referrals')">Referencias<span v-if="form.sortField === 'referrals'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span></th>
                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('pending_studies')">Estudios Pendientes<span v-if="form.sortField === 'pending_studies'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span></th>
                    <th scope="col" class="px-6 py-3 cursor-pointer" @click="sort('doctor_sign')">Firma del Doctor<span v-if="form.sortField === 'doctor_sign'">{{ form.sortDirection === 'asc' ? '↑' :
                                '↓'
                                }}</span></th>
                    <th scope="col" class="px-6 py-3 ">Acciones</th>
                </tr>
            </thead>
            <tbody>

                <tr v-for="record in medicationRecords.data.filter(record => record.id)" :key="record.id" :class="[
        'bg-white border-b dark:bg-gray-800 dark:border-gray-700'    ]">
  <td class="px-6 py-4">   ING-00{{ record.admission.id }},
                                Cama {{ record.admission.bed.number }}, Sala {{
                                    record.admission.bed.room }}</td>
                                     <td class="px-6 py-4">{{ record.diagnosis }} </td>
                    <td class="px-6 py-4  w-2">
                            <span v-if="record.admission.discharged_date == null"
                                class="block w-4 h-4 bg-green-500 rounded-full mx-auto">{{  }}</span>
                            <span v-else class="block w-4 h-4 bg-orange-500 rounded-full mx-auto"></span>
                        </td>


                    <td class="px-6 py-4">{{ record.referrals }}</td>
                    <td class="px-6 py-4">{{ record.pending_studies }}</td>
                    <td class="px-6 py-4">{{ record.doctor_sign }}</td>
                    <td class="px-6 py-4 flex items-center space-x-4">

                            <button class="text-blue-500 hover:text-blue-800" @click="MedicationRecordShow(record.id)">
                            Ver
                            </button>

                            <button class="text-yellow-500 ml-2 hover:text-yellow-800" @click="MedicationRecordEdit(record.id)">
                            Editar
                            </button>




                    </td>

                </tr>

            </tbody>
        </table>
        <Pagination :pagination="medicationRecords" />
    </div>


</AppLayout>
</template>

<script>

import AppLayout from '@/Layouts/AppLayout.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
import {
    Link
} from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';
export default {
    props: {
        medicationRecords: Object,
        filters: Object,
        admission: Array,
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
            this.$inertia.get(route('medicationRecords.index'), this.form, {
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

        MedicationRecordShow(id) {
            this.$inertia.get(route('medicationRecords.show', id));
        },
        MedicationRecordEdit(id) {
            this.$inertia.get(route('medicationRecords.edit', id));
        },



    }

}
</script>
