<template>
    <div
        class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
        <!-- Encabezado más compacto -->
        <div class="px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-medium text-gray-900 dark:text-white flex items-center">
                <span
                    class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-800 text-indigo-600 dark:text-indigo-300 mr-2">
                    <AdmissionIcon class="size-4"/>
                </span>
                Selección de Ingreso
            </h3>
        </div>

        <div class="p-4">
            <!-- Filtros de búsqueda optimizados -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 mb-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" v-model="filters.name" @input="debounceSearch"
                            class="pl-10 w-full rounded-md border-0 py-2 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6"
                            placeholder="Nombre del paciente...">
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0h8v12H6V4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" v-model="filters.room" @input="debounceSearch"
                            class="pl-10 w-full rounded-md border-0 py-2 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6"
                            placeholder="Número de sala...">
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z" />
                            </svg>
                        </div>
                        <input type="number" v-model="filters.bed" @input="debounceSearch"
                            class="pl-10 w-full rounded-md border-0 py-2 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6"
                            placeholder="Número de cama...">
                    </div>
                </div>
            </div>

            <!-- Lista de ingresos optimizada -->
            <div class="space-y-2">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-medium text-gray-900 dark:text-white">
                        Ingresos <span class="text-xs text-gray-500 dark:text-gray-400">({{ admissions.total }}
                            resultados)</span>
                    </h3>
                </div>
                <div
                    class="max-h-[200px] overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div v-for="admission in admissions.data" :key="admission.id" @click="selectAdmission(admission)"
                        :class="['py-2 px-3 cursor-pointer border-b last:border-b-0 border-gray-100 dark:border-gray-700 transition-colors duration-150 hover:bg-indigo-50 dark:hover:bg-indigo-900/20', selectedAdmission === admission.id ? 'bg-indigo-100 dark:bg-indigo-900/30' : '']">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white text-sm">
                                    {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                                        admission.patient.second_surname }}
                                </span>
                                <span
                                    class="text-xs w-fit text-nowrap ml-2 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-800 dark:text-gray-300">
                                    <FormatId :id="admission.id" prefix="ING"></FormatId>
                                </span>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    <span v-if="admission.bed">
                                        Sala {{ admission.bed.room }} - Cama {{ admission.bed.number }}
                                    </span>
                                    <span v-else>N/A</span>
                                </div>
                                <div v-if="selectedAdmissionId === admission.id"
                                    class="text-xs text-green-500 dark:text-green-400 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Actual
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ formatDate(admission.created_at) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación y botones optimizados -->
                <div class="flex justify-between mt-3">
                    <div class="flex space-x-2">
                        <button type="button" @click="prevPage" :disabled="!admissions.prev_page_url"
                            class="px-2 py-1 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 flex items-center text-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Anterior
                        </button>
                        <button type="button" @click="nextPage" :disabled="!admissions.next_page_url"
                            class="px-2 py-1 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 flex items-center text-xs">
                            Siguiente
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Información de selección actual optimizada -->
                <transition name="fade" mode="out-in">
                    <div v-if="selectedAdmission"
                        class="mt-3 flex items-center justify-between bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-2 border border-indigo-100 dark:border-indigo-800/50">
                        <div class="flex items-center space-x-2">
                            <div class="p-1 bg-indigo-100 dark:bg-indigo-800 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-indigo-600 dark:text-indigo-300" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900 dark:text-white">Ingreso seleccionado</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{admissions.data.find(admission => admission.id === selectedAdmission)?.patient.first_name}}
                                    {{admissions.data.find(admission => admission.id === selectedAdmission)?.patient.first_surname}}
                                    {{admissions.data.find(admission => admission.id === selectedAdmission)?.patient.second_surname}}
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
import 'moment/locale/es';
import FormatId from './FormatId.vue';
import { Link } from '@inertiajs/vue3';
import AdmissionIcon from '@/Components/Icons/ClipBoardIcon.vue';

export default {
    components: {
        FormatId,
        Link,
        AdmissionIcon
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
            doesntHaveMedR: this.doesntHaveMedicationR || false,
            doesntHaveOrder: this.doesntHaveMedicalOrder || false,
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
                        doesntHaveMedicationR: this.doesntHaveMedR,
                        doesntHaveMedicalOrder: this.doesntHaveOrder,
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
