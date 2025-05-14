<template>
    <div
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
        <!-- Encabezado con estilo moderno -->
        <div class="px-6 py-5 bg-indigo-50 dark:bg-indigo-900/20 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-800 text-indigo-600 dark:text-indigo-300 mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                    </svg>
                </span>
                Selección de Paciente
            </h3>
        </div>

        <div class="p-6">
            <!-- Filtros de búsqueda -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 mb-4">
                <div class="flex items-center mb-3">
                    <div
                        class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-800/70 text-indigo-600 dark:text-indigo-300 flex items-center justify-center mr-2">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <label for="patient_search" class="text-sm font-medium text-gray-900 dark:text-white">
                        Buscar Paciente
                    </label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="patient_search" v-model="filters.name" @input="debounceSearch"
                        class="pl-10 w-full rounded-md border-0 py-2.5 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6"
                        placeholder="Nombre del paciente...">
                </div>
            </div>

            <!-- Lista de pacientes -->
            <div class="space-y-3">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div
                            class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-800/70 text-indigo-600 dark:text-indigo-300 flex items-center justify-center mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Seleccionar Paciente <span class="text-xs text-gray-500 dark:text-gray-400">({{
                                patients.total }} resultados)</span>
                        </h3>
                    </div>
                </div>
                <div
                    class="max-h-[300px] overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                    <div v-for="patient in patients.data" :key="patient.id" @click="selectPatient(patient)"
                        :class="['p-3 cursor-pointer border-b last:border-b-0 border-gray-100 dark:border-gray-700 transition-colors duration-150 hover:bg-indigo-50 dark:hover:bg-indigo-900/20', selectedPatient === patient.id ? 'bg-indigo-100 dark:bg-indigo-900/30' : '']">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white text-sm">
                                    {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                                </span>
                                <div v-if="selectedPatientId === patient.id"
                                    class="text-xs text-green-500 dark:text-green-400 flex items-center mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Paciente actual
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ formatDate(patient.created_at) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación y botones de acción -->
                <div class="flex justify-between mt-4">
                    <div class="flex space-x-2">
                        <button type="button" @click="prevPage" :disabled="!patients.prev_page_url"
                            class="px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 flex items-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Anterior
                        </button>
                        <button type="button" @click="nextPage" :disabled="!patients.next_page_url"
                            class="px-3 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 flex items-center text-sm">
                            Siguiente
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <Link :href="route('patients.create')"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Crear Paciente</span>
                    </Link>
                </div>

                <!-- Información de selección actual -->
                <transition name="fade" mode="out-in">
                    <div v-if="selectedPatient"
                        class="mt-6 flex items-center justify-between bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4 border border-indigo-100 dark:border-indigo-800/50">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-800 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-indigo-600 dark:text-indigo-300" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Paciente seleccionado</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{patients.data.find(patient => patient.id === selectedPatient)?.first_name}}
                                    {{patients.data.find(patient => patient.id === selectedPatient)?.first_surname}}
                                    {{patients.data.find(patient => patient.id === selectedPatient)?.second_surname}}
                                </p>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

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
