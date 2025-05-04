<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Registro de Actividades
            </h2>
        </template>

        <div class="bg-gray-100 dark:bg-gray-900 overflow-hidden sm:rounded-lg mt-4 px-4 lg:px-10">
            <div class="bg-gray-100 dark:bg-gray-900 mt-2 mb-6">
                <!-- Filtros -->
                <div class=" rounded-lg shadow-sm  mb-6">
                    <form @submit.prevent="applyFilters">
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Búsqueda -->
                            <div class="relative w-full md:w-4/12">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <SearchIcon class="size-4 text-gray-500 dark:text-gray-400" />
                                </div>
                                <TextInput type="text" v-model="localFilters.search" class="pl-10 w-full"
                                    placeholder="Buscar..." />
                                <button v-if="localFilters.search" @click="localFilters.search = ''"
                                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                                    <XIcon class="h-5 w-5" />
                                </button>
                            </div>

                            <!-- Selector de modelo -->
                            <div class="relative w-full md:w-2/12">
                                <select
                                    class="border-gray-300 w-full dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    name="model" id="model" v-model="localFilters.model">
                                    <option value="">Todos los modelos</option>
                                    <option v-for="model in availableModels" :key="model" :value="model">
                                        {{ getModelName(model) }}
                                    </option>
                                </select>
                            </div>

                            <!-- Selector de fecha -->
                            <div class="relative w-full md:w-2/12">
                                <DateInput id="date" v-model="localFilters.date" />
                            </div>

                            <!-- Botones -->
                            <div class="relative w-full md:w-4/12">
                                <div class="flex justify-end items-center space-x-3">
                                    <button type="button" @click="resetFilters"
                                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                                        Limpiar filtros
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                                        Aplicar filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tabla de actividades -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Usuario
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Modelo
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Detalles
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="log in logs.data" :key="log.id" class="">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{ log.description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{ log.causer ? log.causer.name + ' ' + log.causer.last_name : 'Sistema' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{ getModelName(log.subject_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{ formatDate(log.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button @click="showDetails(log)"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                                            Ver cambios
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="logs.data.length === 0">
                                    <td colspan="5"
                                        class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-2"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p>No hay actividades registradas</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div
                        class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        <Pagination :pagination="logs" :filters="localFilters" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de detalles -->
        <Modal :closeable="true" :show="showModal" @close="closeModal">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detalles de la actividad</h3>
                        <button @click="closeModal"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <span class="sr-only">Cerrar</span>
                            <XIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <div v-if="selectedLog" class="text-gray-700 dark:text-gray-300 max-h-[80vh] overflow-y-auto">
                        <!-- Para un solo registro de actividad -->
                        <ActivityLogDiff :activityItem="selectedLog"/>
                    </div>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script>
import { defineComponent, ref } from 'vue';
import moment from 'moment/moment';
import 'moment/locale/es';
import { router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import XIcon from '@/Components/Icons/XIcon.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SearchIcon from '@/Components/Icons/SearchIcon.vue';
import TextInput from '@/Components/TextInput.vue';
import DateInput from '@/Components/DateInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import ActivityLogDiff from '@/Components/ActivityLogDiff.vue';

export default defineComponent({
    components: {
        AppLayout,
        Pagination,
        Modal,
        XIcon,
        SearchIcon,
        TextInput,
        DateInput,
        InputLabel,
        ActivityLogDiff
    },

    props: {
        logs: Object,
        filters: Object,
        availableModels: Array
    },

    data() {
        return {
            showModal: false,
            selectedLog: null,
            localFilters: {
                search: this.$props.filters?.search || '',
                model: this.$props.filters?.model || '',
                date: this.$props.filters?.date || ''
            }
        };
    },

    methods: {
        formatDate(dateString) {
            return moment(dateString).format('DD MMM YYYY, HH:mm');
        },

        getModelName(subjectType) {
            if (!subjectType) return 'N/A';

            // Obtener solo el nombre de la clase del namespace completo
            const parts = subjectType.split('\\');
            return parts[parts.length - 1];
        },

        showDetails(log) {
            this.selectedLog = log;
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.selectedLog = null;
        },

        applyFilters() {
            router.get(route('activityLogs.index'), {
                search: this.localFilters.search,
                model: this.localFilters.model,
                date: this.localFilters.date
            }, {
                preserveState: true,
                replace: true
            });
        },

        resetFilters() {
            this.localFilters = {
                search: '',
                model: '',
                date: ''
            };

            router.get(route('activityLogs.index'), {}, {
                preserveState: true,
                replace: true
            });
        },

        formatJSON(jsonData) {
            return JSON.stringify(jsonData, null, 2);
        }
    }
});
</script>
