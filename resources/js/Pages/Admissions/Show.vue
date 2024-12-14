<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-white">Detalles del Ingreso</h2>
                        <Link :href="route('admissions.index')"
                            class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-full transition duration-300 ease-in-out">
                        Volver
                        </Link>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Cama</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ admission.bed.number }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{ admission.patient.second_surname }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Doctor</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ admission.doctor.name }} {{ admission.doctor.last_name }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de Ingreso</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ formatDate(admission.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico de Ingreso</h3>
                            <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                                {{ admission.admission_dx || 'No se proporcionó diagnóstico de ingreso' }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico Final</h3>
                            <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                                {{ admission.final_dx || 'No se ha proporcionado diagnóstico final' }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Observaciones</h3>
                            <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                                {{ admission.comment || 'No hay observaciones' }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-6 shadow-md">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Acciones Adicionales</h3>
                        <div class="grid md:grid-cols-3 gap-4">
                            <Link
                                :href="route('nurseRecords.index', { admission_id: admission.id })"
                                class="flex items-center justify-center bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold rounded-lg p-4 hover:from-green-600 hover:to-green-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105"
                            >
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                Hojas de Enfermería
                            </Link>

                            <Link
                                :href="route('admissions.index', { admission_id: admission.id })"
                                class="flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg p-4 hover:from-blue-600 hover:to-blue-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105"
                            >
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Órdenes Médicas
                            </Link>

                            <Link
                                :href="route('admissions.index', { admission_id: admission.id })"
                                class="flex items-center justify-center bg-gradient-to-r from-purple-500 to-purple-700 text-white font-semibold rounded-lg p-4 hover:from-purple-600 hover:to-purple-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105"
                            >
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Planes de Tratamiento
                            </Link>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <Link
                            :href="route('admissions.edit', admission.id)"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-blue-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </Link>

                        <button
                            @click="confirmDelete"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-700 text-white font-semibold rounded-lg hover:from-red-600 hover:to-red-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        admission: Object,
    },
    components: {
        AppLayout,
        Link,
    },
    methods: {
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        confirmDelete() {
            if (confirm('¿Estás seguro de que deseas eliminar este ingreso?')) {
                this.$inertia.delete(route('admissions.destroy', this.admission.id));
            }
        }
    }
}
</script>
