<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-white">Detalles del Registro de Medicamentos</h2>
                        <Link :href="route('medicationRecords.index')"
                            class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-full transition duration-300 ease-in-out">
                            Volver
                        </Link>
                    </div>
                </div>

                <!-- Información Principal -->
                <div class="p-8 space-y-8">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Paciente -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ medicationRecord.admission.patient.first_name }}
                                {{ medicationRecord.admission.patient.first_surname }}
                                {{ medicationRecord.admission.patient.second_surname }}
                            </p>
                        </div>

                        <!-- Doctor -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Doctor</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                <!-- Verifica que la relación drug esté definida -->
                                    {{ medicationRecord.doctor.name }} {{ medicationRecord.doctor.last_name }}

                            </p>
                        </div>

                        <!-- Dosis -->
                        <!-- <div v-for="detail in details" :key="detail.id" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Dosis</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ medicationRecord.medication_record_detail.d }}
                            </p>
                        </div> -->

                        <!-- Frecuencia -->
                        <!-- <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Frecuencia</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ medicationRecord.medication_record_detail[0].fc }}
                            </p>
                        </div> -->
                    </div>

                    <!-- Observaciones -->
                    <!-- <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Intervalos y Horas</h3>
                        <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                            {{ medicationRecord.medication_record_detail[0].interval_in_hours || 'No hay observaciones' }}
                        </p>
                    </div> -->
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-4 p-6">
                    <Link
                        :href="route('medicationRecords.edit', medicationRecord.id)"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-blue-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </Link>

                    <button
                        @click="confirmDelete"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-700 text-white font-semibold rounded-lg hover:from-red-600 hover:to-red-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4h4"></path>
                        </svg>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default{
    props: {
        medicationRecord: Object,
        details: Array
    },
    components: {
        AppLayout,
        Link,
    },

}

</script>
