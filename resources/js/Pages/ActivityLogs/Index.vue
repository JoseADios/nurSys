<template>
    <AppLayout title="Registros de Actividades">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Registros de Actividades
            </h2>
        </template>

        <div class="bg-gray-100 dark:bg-gray-900 overflow-hidden sm:rounded-lg mt-4 px-4 lg:px-10">
            <div class="bg-gray-100 dark:bg-gray-900 mt-2 mb-6">
                <!-- Filtros -->
                <div class="rounded-lg shadow-sm mb-6">
                    <form @submit.prevent="applyFilters">
                        <div class="flex flex-col lg:flex-row lg:flex-wrap gap-4">
                            <!-- Filtros -->
                            <div class="flex flex-col lg:flex-row lg:flex-grow gap-4">
                                <!-- Selector de acción -->
                                <div class="relative w-full">
                                    <select
                                        class="border-gray-300 w-full dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        name="action" id="action" v-model="localFilters.action">
                                        <option value="">Todas las acciones</option>
                                        <option value="created">Creado</option>
                                        <option value="updated">Actualizado</option>
                                        <option value="activated">Activado</option>
                                        <option value="deactivated">Desactivado</option>
                                    </select>
                                </div>

                                <!-- Selector de modelo -->
                                <div class="relative w-full">
                                    <select
                                        class="border-gray-300 w-full dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        name="model" id="model" v-model="localFilters.model">
                                        <option value="">Todos los modelos</option>
                                        <option v-for="model in availableModels" :key="model.name" :value="model.name">
                                            {{ model.label }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Selector de usuario -->
                                <div class="relative w-full">
                                    <select
                                        class="border-gray-300 w-full dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        name="causer" id="causer" v-model="localFilters.causer">
                                        <option value="">Todos los usuarios</option>
                                        <option v-for="causer in availableCausers" :key="causer" :value="causer">
                                            {{ getCauserName(causer) }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Selector de fecha -->
                                <div
                                    class="relative w-full  flex flex-col sm:flex-row items-center gap-2">
                                    <DateInput class="w-full" id="startDate" v-model="localFilters.startDate"
                                        placeholder="Fecha inicio" />
                                    <span class="text-gray-500 dark:text-gray-400">a</span>
                                    <DateInput class="w-full" id="endDate" v-model="localFilters.endDate"
                                        placeholder="Fecha fin" />
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="relative w-full">
                                <div
                                    class="flex flex-col sm:flex-row justify-end items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                    <SecondaryButton type="button" @click="resetFilters">
                                        Limpiar filtros
                                    </SecondaryButton>
                                    <PrimaryButton :is-loading="localFilters.processing">
                                        Aplicar filtros
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tabla de actividades -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-gray-500 dark:text-gray-300 sm:table-cell">
                                        Acción
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-gray-500 dark:text-gray-300 sm:table-cell">
                                        Modelo
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-gray-500 dark:text-gray-300 sm:table-cell">
                                        Usuario
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-gray-500 dark:text-gray-300 sm:table-cell">
                                        Fecha
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-gray-500 dark:text-gray-300 sm:table-cell">
                                        Detalles
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="log in logs.data" :key="log.id" class="">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        <div :class="getActionBadgeClass(log.description)"
                                            class="inline-flex items-center px-3 py-1 rounded-md">
                                            <component :is="getActionIcon(log.description)" class="w-5 h-5 mr-2" />
                                            <span class="font-semibold text-sm">{{ formatDescription(log.description)
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{ getModelName(log.subject_type) }} ({{ log.subject_id }})
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{ log.causer ? log.causer.name + ' ' + log.causer.last_name : 'Sistema' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{ formatDate(log.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button @click="showDetails(log)"
                                            class=" text-primary-500 hover:text-primary-800">
                                            Ver cambios
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="logs.data.length === 0">
                                    <td colspan="5"
                                        class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800">
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
                <div class="p-4 md:p-6">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detalles de la actividad</h3>
                        <button @click="closeModal"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <span class="sr-only">Cerrar</span>
                            <XIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <div v-if="selectedLog" class="text-gray-700 dark:text-gray-300 max-h-[75vh] overflow-y-auto">
                        <!-- Para un solo registro de actividad -->
                        <ActivityLogDiff :activityItem="selectedLog"
                            :model-label="getModelName(selectedLog.subject_type)" :field-mappings="fieldMappings"
                            :value-mappings="valueMappings" />
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
import { router, useForm } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import XIcon from '@/Components/Icons/XIcon.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SearchIcon from '@/Components/Icons/SearchIcon.vue';
import TextInput from '@/Components/TextInput.vue';
import DateInput from '@/Components/DateInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import ActivityLogDiff from '@/Components/ActivityLogDiff.vue';
import CirclePlusIcon from '@/Components/Icons/CirclePlusIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import RestoreIcon from '@/Components/Icons/RestoreIcon.vue';
import ToggleLeftIcon from '@/Components/Icons/ToggleLeftIcon.vue';
import ToggleRigthIcon from '@/Components/Icons/ToggleRigthIcon.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';

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
        ActivityLogDiff,
        CirclePlusIcon,
        EditIcon,
        TrashIcon,
        RestoreIcon,
        ToggleLeftIcon,
        ToggleRigthIcon,
        PrimaryButton,
        SecondaryButton,
        PersonalizableButton
    },

    props: {
        logs: Object,
        filters: Object,
        availableModels: Object,
        availableCausers: Array,
        fieldMappings: Object,
        valueMappings: Object,
    },

    data() {
        return {
            showModal: false,
            selectedLog: null,
            localFilters: useForm({
                action: this.$props.filters?.action || '',
                model: this.$props.filters?.model || '',
                causer: this.$props.filters?.causer || '',
                startDate: this.$props.filters?.startDate || '',
                endDate: this.$props.filters?.endDate || ''
            })
        };
    },

    methods: {
        formatDate(dateString) {
            return moment(dateString).format('DD MMM YYYY, HH:mm');
        },

        getModelName(subjectType) {
            const model = this.availableModels.find(model => model.name === subjectType);
            return model ? model.label : 'Desconocido';
        },

        getCauserName(causer) {
            return causer.name + ' ' + causer.last_name;
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
            this.localFilters.get(route('activityLogs.index'), {
                preserveState: true,
                replace: true
            });
        },

        resetFilters() {
            this.localFilters.action = '';
            this.localFilters.model = '';
            this.localFilters.causer = '';
            this.localFilters.startDate = '';
            this.localFilters.endDate = '';

            this.localFilters.get(route('activityLogs.index'), {
                preserveState: true,
                replace: true
            });
        },

        formatDescription(description) {
            let mappings = {
                'created': 'Creado',
                'updated': 'Actualizado',
                'deactivated': 'Desactivado',
                'activated': 'Activado'
            }

            return mappings[description];
        },

        formatJSON(jsonData) {
            return JSON.stringify(jsonData, null, 2);
        },

        getActionBadgeClass(description) {
            const classMap = {
                'created': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                'updated': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                'deleted': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                'restored': 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                'deactivated': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                'activated': 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400'
            };

            return classMap[description] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
        },

        getActionIcon(description) {
            const iconMap = {
                'created': 'CirclePlusIcon',
                'updated': 'EditIcon',
                'deleted': 'TrashIcon',
                'restored': 'RestoreIcon',
                'deactivated': 'ToggleLeftIcon',
                'activated': 'ToggleRigthIcon'
            };

            return iconMap[description] || null;
        }
    }
});
</script>
