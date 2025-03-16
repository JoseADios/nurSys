<template>
    <div>
        <!-- Filtros de búsqueda -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">
                Buscar Paciente
            </h3>
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
        </div>

        <!-- Lista de pacientes -->
        <div class="space-y-2">
            <h3 class="text-base font-medium text-gray-900 dark:text-white">
                Seleccionar Paciente ({{ patients.total }} resultados)
            </h3>
            <div
                class="max-h-[250px] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                <div v-for="patient in patients.data" :key="patient.id" @click="selectPatient(patient)"
                    :class="['p-3 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20', selectedPatient === patient.id ? 'bg-purple-100 dark:bg-purple-900/30' : '']">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white text-sm">
                                {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                            </span>
                            <div v-if="selectedPatientId === patient.id"
                                class="text-xs text-green-500 dark:text-green-400">
                                Paciente actual
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ formatDate(patient.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-start mt-4 space-x-2">
                <button type="button" @click="prevPage" :disabled="!patients.prev_page_url"
                    class="px-3 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Anterior
                </button>
                <button type="button" @click="nextPage" :disabled="!patients.next_page_url"
                    class="px-3 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Siguiente
                </button>
                  <Link :href="route('medicationRecords.create')"
                    class="px-2 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600">
                    <span class="font-medium">Crear Paciente</span>
                </Link>
            </div>
        </div>
    </div>
</template>

<script>
import debounce from 'lodash/debounce';
import axios from 'axios';
import moment from "moment/moment";
import {
    Link
} from '@inertiajs/vue3';
import 'moment/locale/es';

export default {
    props: {
        selectedPatientId: Number
    },
    components: {

        Link,



    },
    data() {
        return {
            patients: {
                data: [],
                total: 0,
                prev_page_url: null,
                next_page_url: null,
            },
            selectedPatient: this.selectedPatientId || null,
            filters: {
                name: '',
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
        selectPatient(patient) {
            this.selectedPatient = patient.id;
            this.$emit('update:patient', patient.id);  // Envía el ID al padre
        },
        debounceSearch() {
            this.debouncedSearch();
        },
        async applyFilters(pageUrl = null) {
            try {
                const response = await axios.get(pageUrl || route('patients.filter'), {
                    params: {
                        filters: this.filters,
                        patient_id: this.selectedPatient
                    }
                });
                this.patients = response.data;
            } catch (error) {
                console.error('Error fetching patients:', error);
            }
        },
        formatDate(date) {
            return moment(date).format('DD MMM YYYY, HH:mm');
        },
        prevPage() {
            if (this.patients.prev_page_url) {
                this.applyFilters(this.patients.prev_page_url)
            }
        },
        nextPage() {
            if (this.patients.next_page_url) {
                this.applyFilters(this.patients.next_page_url)
            }
        }
    }
}
</script>
