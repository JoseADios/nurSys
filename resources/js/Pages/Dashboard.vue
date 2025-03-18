<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import BedIcon from '@/Components/Icons/BedIcon.vue';
import AdmissionsChart from '@/Components/Charts/AdmissionsChart.vue';
import FormatId from '@/Components/FormatId.vue';
import { Link } from '@inertiajs/vue3';
import CalendarIcon from '@/Components/Icons/CalendarIcon.vue';
import ChevronRightIcon from '@/Components/Icons/ChevronRightIcon.vue';
import CheckIcon from '@/Components/Icons/CheckIcon.vue';
import PatientsByArsChart from '@/Components/Charts/PatientsByArsChart.vue';
import BedsByStatusChart from '@/Components/Charts/BedsByStatusChart.vue';
import moment from 'moment/moment';
import 'moment/locale/es';
import PatientIcon from '@/Components/Icons/PatientIcon.vue';
import TrendingDownIcon from '@/Components/Icons/TrendingDownIcon.vue';
import TrendingUpIcon from '@/Components/Icons/TrendingUpIcon.vue';

defineProps({
    stats: {
        type: Object,
        required: true
    }
});

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700/60 sm:rounded-lg p-4">
                    <!-- Grid de estadísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Admisiones -->
                        <div
                            class=" bg-blue-500/20 rounded-lg p-6 text-blue-500 dark:text-white dark:border-2 dark:border-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-80 font-bold">Ingresos activos</p>
                                    <p class="text-3xl font-bold mt-1">{{ stats.active_admissions }}</p>
                                </div>
                                <div class="opacity-80">
                                    <UserIcon class="w-8 h-8" />
                                </div>
                            </div>
                            <p class="text-sm mt-4 opacity-80">Total: {{ stats.total_admissions }}</p>
                        </div>

                        <!-- Camas -->
                        <div
                            class="bg-orange-500/20 rounded-lg p-6 text-orange-500 dark:text-white dark:border-2 dark:border-orange-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-80 font-bold">Camas Disponibles</p>
                                    <p class="text-3xl font-bold mt-1">{{ stats.available_beds }}</p>
                                </div>
                                <div class="opacity-80">
                                    <BedIcon class="w-8 h-8" />
                                </div>
                            </div>
                            <p class="text-sm mt-4 opacity-80">Total: {{ stats.total_beds }}</p>
                        </div>

                        <!-- Registros de hoy -->
                        <div
                            class="bg-indigo-500/20 rounded-lg p-6 text-indigo-500 dark:text-white dark:border-2 dark:border-indigo-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-80 font-bold">Nuevos pacientes este mes</p>
                                    <p class="text-3xl font-bold mt-1">{{ stats.new_patients_this_month }}</p>
                                </div>
                                <div class="opacity-80">
                                    <PatientIcon class="w-8 h-8" />
                                </div>
                            </div>
                            <p :class="['text-sm font-bold mt-4 opacity-80 flex',
                                {
                                    'text-red-500 dark:text-red-400': stats.percent_diff_new_patients_month < 0,
                                    'text-green-700 dark:text-green-400': stats.percent_diff_new_patients_month > 0,
                                }
                            ]">
                                <TrendingUpIcon class="size-5 mr-2" v-if="stats.percent_diff_new_patients_month > 0" />
                                <TrendingDownIcon class="size-5 mr-2" v-else />
                                {{ Math.abs(stats.percent_diff_new_patients_month) }}%
                                {{ stats.percent_diff_new_patients_month < 0 ? 'menos' : 'más' }} que el mes pasado </p>
                        </div>
                    </div>

                    <!-- Gráfico de ingresos -->
                    <div
                        class="mt-6 bg-white dark:bg-gray-800 overflow-hidden border border-gray-200/60 dark:border-gray-700/60 rounded-lg">
                        <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                Ingresos y altas de la Semana
                            </h3>
                        </div>
                        <div class="p-4">
                            <AdmissionsChart :admissions="stats.admissions_by_week"
                                :discharges="stats.admissions_discharged_by_week" />
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Ingresos sin cama asignada -->
                        <div
                            class="w-full max-h-[414px] overflow-y-scroll  bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200/30 dark:border-gray-700/30 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300/40 dark:[&::-webkit-scrollbar-track]:bg-neutral-700/30 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500/30">
                            <!-- Encabezado con diseño más moderno -->
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-3">

                                        <h3 class="text-xl font-medium text-gray-800 dark:text-gray-100">
                                            Ingresos sin cama asignada
                                        </h3>
                                    </div>
                                    <span
                                        class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[#FC4C51] text-white dark:bg-[#FC4C51] dark:text-white">
                                        {{ stats.admissions_without_bed.length }}
                                    </span>
                                </div>
                            </div>

                            <!-- Lista de pacientes con diseño más fluido -->
                            <div>
                                <div v-for="adm in stats.admissions_without_bed"
                                    class="group relative px-6 py-3 hover:bg-gray-50 dark:hover:bg-indigo-600/10 transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-indigo-500  flex items-center justify-center text-white font-medium shadow-sm">
                                                    {{ adm.patient.first_name.charAt(0) }}{{
                                                        adm.patient.first_surname.charAt(0) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ adm.patient.first_name }} {{ adm.patient.first_surname }} {{
                                                        adm.patient.second_surname }}
                                                </div>
                                                <div
                                                    class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                    <span
                                                        class="bg-blue-100 text-blue-700 text-xs rounded-full px-2.5 py-0.5 dark:bg-blue-900/40 dark:text-blue-300 mr-2">
                                                        <FormatId :id="adm.id" prefix="ING" />
                                                    </span>
                                                    <span class="flex items-center">
                                                        <CalendarIcon
                                                            class="h-4 w-4 mr-1 text-gray-400 dark:text-gray-500" />
                                                        {{ formatDate(adm.created_at) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <Link :href="route('admissions.show', adm.id)"
                                            class="inline-flex items-center text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200">
                                        <span>Ver</span>
                                        <ChevronRightIcon class="ml-1 h-5 w-5" />
                                        </Link>
                                    </div>

                                    <!-- Línea de separación con degradado -->
                                    <div
                                        class="absolute bottom-0 left-6 right-6 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent dark:via-gray-700">
                                    </div>
                                </div>
                            </div>

                            <!-- Estado vacío mejorado -->
                            <div v-if="stats.admissions_without_bed.length === 0" class="py-12 px-6 text-center">
                                <div
                                    class="rounded-full bg-green-100 dark:bg-green-900/30 mx-auto h-16 w-16 flex items-center justify-center mb-4">
                                    <CheckIcon class="h-8 w-8 text-green-600 dark:text-green-400" />
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Todos asignados</h3>
                                <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                    En este momento todos los ingresos tienen una cama asignada correctamente.
                                </p>
                            </div>
                        </div>

                        <!-- grafico pie de cantidad de camas por estado -->
                        <div class="w-full">
                            <BedsByStatusChart :status-data="stats.beds_by_status" class="" />
                        </div>

                        <!-- grafico donut cantidad de pacientes por ars -->
                        <div class="w-full">
                            <PatientsByArsChart :ars-data="stats.patients_by_ars" class="" />
                        </div>


                    </div>


                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {

    methods: {
        formatDate(date) {
            return moment(date).fromNow();

        }
    }
}
</script>
