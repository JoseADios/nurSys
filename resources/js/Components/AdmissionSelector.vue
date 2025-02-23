<template>
    <div>
        <!-- Filtros de búsqueda -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">
                Buscar Paciente
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="space-y-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" v-model="filters.name" @input="debounceSearch"
                            class="pl-10 w-full rounded-lg border-gray-200 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Nombre del paciente...">
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0h8v12H6V4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" v-model="filters.room" @input="debounceSearch"
                            class="pl-10 w-full rounded-lg border-gray-200 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Número de sala...">
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z" />
                            </svg>
                        </div>
                        <input type="number" v-model="filters.bed" @input="debounceSearch"
                            class="pl-10 w-full rounded-lg border-gray-200 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Número de cama...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de ingresos -->
        <div class="space-y-2">
            <h3 class="text-base font-medium text-gray-900 dark:text-white">
                Seleccionar Ingreso ({{ admissions.length }} resultados)
            </h3>
            <div
                class="max-h-[250px] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                <div v-for="admission in admissions" :key="admission.id" @click="selectAdmission(admission)"
                    :class="['p-3 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20', selectedAdmission === admission.id ? 'bg-purple-100 dark:bg-purple-900/30' : '']">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white text-sm">
                                {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                                    admission.patient.second_surname }}
                            </span>
                            <span
                                class="text-xs ml-2 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-800 dark:text-gray-300">
                                ING-00{{ admission.id }}
                            </span>
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                Sala {{ admission.bed.room }} - Cama {{ admission.bed.number }}
                            </div>
                            <div v-if="selectedAdmissionId === admission.id"
                                class="text-xs text-green-500 dark:text-green-400">
                                Ingreso actual
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ formatDate(admission.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>

<script>
import debounce from 'lodash/debounce';
import axios from 'axios';
import moment from 'moment';

export default {
    props: {
        initialAdmissions: Object,
        selectedAdmissionId: Number
    },
    data() {
        return {
            admissions: [],
            selectedAdmission: this.selectedAdmissionId || null,
            filters: {
                name: '',
                room: '',
                bed: '',
            },
            debouncedSearch: null,
        };
    },
    created() {
        this.debouncedSearch = debounce(this.applyFilters, 300);
    },
    mounted() {
        this.applyFilters();
    },
    methods: {
        selectAdmission(admission) {
            this.selectedAdmission = admission.id;
            this.$emit('update:admission', admission.id);  // Envía el ID al padre
        },
        debounceSearch() {
            this.debouncedSearch();
        },
        async applyFilters() {
            try {
                const response = await axios.get(route('admissions.filter'), {
                    params: {
                        filters: this.filters, admission_id: this.selectedAdmission }
                });
                this.admissions = response.data;
            } catch (error) {
                console.error('Error fetching admissions:', error);
            }
        },
        formatDate(date) {
            return moment(date).format('DD MMM YYYY, HH:mm');
        }
    }
}
</script>
