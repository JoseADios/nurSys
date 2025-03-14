<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import BedIcon from '@/Components/Icons/BedIcon.vue';
import NurseRecordIcon from '@/Components/Icons/NurseRecordIcon.vue';
import AdmissionsChart from '@/Components/Charts/AdmissionsChart.vue';

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
                <div class="bg-white dark:bg-gray-800 overflow-hidden border border-gray-200 dark:border-gray-700/60 sm:rounded-lg p-4">
                    <!-- Grid de estadísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Admisiones -->
                        <div
                            class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-80">Ingresos activos</p>
                                    <p class="text-3xl font-bold mt-1">{{ stats.active_admissions }}</p>
                                </div>
                                <div class="text-white opacity-80">
                                    <UserIcon class="w-8 h-8" />
                                </div>
                            </div>
                            <p class="text-sm mt-4 opacity-80">Total: {{ stats.total_admissions }}</p>
                        </div>

                        <!-- Camas -->
                        <div
                            class="bg-gradient-to-br from-emerald-500 to-emerald-600 dark:from-emerald-600 dark:to-emerald-700 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-80">Camas Disponibles</p>
                                    <p class="text-3xl font-bold mt-1">{{ stats.available_beds }}</p>
                                </div>
                                <div class="text-white opacity-80">
                                    <BedIcon class="w-8 h-8" />
                                </div>
                            </div>
                            <p class="text-sm mt-4 opacity-80">Total: {{ stats.total_beds }}</p>
                        </div>

                        <!-- Registros de hoy -->
                        <div
                            class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-lg p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-80">Registros de Enfermería Hoy</p>
                                    <p class="text-3xl font-bold mt-1">{{ stats.nurse_records_today }}</p>
                                </div>
                                <div class="text-white opacity-80">
                                    <NurseRecordIcon class="w-8 h-8" />
                                </div>
                            </div>
                            <p class="text-sm mt-4 opacity-80">Medicaciones Pendientes: {{ stats.pending_medications }}</p>
                        </div>
                    </div>

                    <!-- Gráfico de ingresos -->
                    <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden border border-gray-200/60 dark:border-gray-700/60 sm:rounded-lg">
                        <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                Ingresos de la Semana
                            </h3>
                        </div>
                        <div class="p-4">
                            <AdmissionsChart :admissions="stats.admissions_by_week" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
