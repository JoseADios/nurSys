<template>
    <div>
        <!-- Filtros de búsqueda -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">
                Buscar Ingreso
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
                Seleccionar Ingreso ({{ admissions.total }} resultados)
            </h3>
            <div v-if="admissions.data"
                class="max-h-[250px] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                <div v-for="admission in admissions.data" :key="admission.id" @click="selectAdmission(admission)"
                    :class="['p-3 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20', selectedAdmission === admission.id ? 'bg-purple-100 dark:bg-purple-900/30' : '']">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white text-sm">
                                {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                                    admission.patient.second_surname }}
                            </span>
                            <span
                                class="text-xs ml-2 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-800 dark:text-gray-300">
                                <FormatId :id="admission.id" prefix="ING"></FormatId>
                            </span>
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                <span v-if="admission.bed">
                                    Sala {{ admission.bed.room }} - Cama {{ admission.bed.number }}
                                </span>
                                <span v-else>N/A</span>
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
            <div class="flex justify-start mt-4 space-x-2">
                <button type="button" @click="prevPage" :disabled="!admissions.prev_page_url"
                    class="px-3 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Anterior
                </button>
                <button type="button" @click="nextPage" :disabled="!admissions.next_page_url"
                    class="px-3 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Siguiente
                </button>
            </div>
        </div>
    </div>


</template>

<script>
import debounce from 'lodash/debounce';
import axios from 'axios';
import moment from "moment/moment";
import 'moment/locale/es';
import FormatId from './FormatId.vue';

export default {
    components: {
        FormatId
    },
    props: {
        doesntHaveTemperatureR: Boolean,
        doesntHaveMedicationR: Boolean,
        doesntHaveMedicalOrder: Boolean,
        selectedAdmissionId: Number
    },
    data() {
        return {
            admissions: {
                data: [],
                total: 0,
                prev_page_url: null,
                next_page_url: null,
            },
            selectedAdmission: this.selectedAdmissionId || null,
            doesntHaveTempR: this.doesntHaveTemperatureR || false,
            doesntHaveMedicationR: this.doesntHaveMedicationR || false,
            doesntHaveMedicalOrder: this.doesntHaveMedicalOrder || false,
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
        async applyFilters(pageUrl = null) {
            try {
                const response = await axios.get(pageUrl || route('admissions.filter'), {
                    params: {
                        filters: this.filters,
                        admission_id: this.selectedAdmissionId,
                        doesntHaveTemperatureR: this.doesntHaveTempR,
                        doesntHaveMedicationR: this.doesntHaveMedicationR,
                        doesntHaveMedicalOrder: this.doesntHaveMedicalOrder,
                    }
                });
                this.admissions = response.data;
            } catch (error) {
                console.error('Error fetching admissions:', error);
            }
        },
        formatDate(date) {
            return moment(date).format('DD MMM YYYY, HH:mm');
        },
        prevPage() {
            if (this.admissions.prev_page_url) {
                this.applyFilters(this.admissions.prev_page_url)
            }
        },
        nextPage() {
            if (this.admissions.next_page_url) {
                this.applyFilters(this.admissions.next_page_url)
            }
        }
    }
}
</script>
