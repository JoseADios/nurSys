<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Detalles del paciente
            </h2>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto">
                <!-- Encabezado del perfil -->
                <div class="mb-6 flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="size-16 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-2xl text-white font-bold">
                                {{ getInitials(patient.first_name, patient.first_surname) }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">
                                {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                            </h1>
                            <p class="text-gray-400">Cédula: {{ patient.identification_card }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <button v-if="patient.active == 1" @click="patientBeingDeleted = true"
                            class="inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:to-red-600 transition-all duration-200">
                            Eliminar
                        </button>
                        <button v-else @click="restorePatient"
                            class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:to-green-600 transition-all duration-200">
                            Restaurar
                        </button>
                        <Link :href="route('patients.edit', patient.id)"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:to-blue-600 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Editar
                        </Link>
                        <Link :href="route('patients.index')"
                            class="inline-flex items-center px-4 py-2 bg-gray-700 text-gray-200 text-sm rounded-lg hover:bg-gray-600 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                        </Link>
                    </div>
                </div>

                <!-- Tarjetas de información -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Tarjeta de Información de contacto -->
                    <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-blue-500/10 rounded-lg">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-white">Información de contacto</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="ml-3 text-gray-300">{{ patient.phone }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                                <span class="ml-3 text-gray-300">{{ patient.email }}</span>
                            </div>
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-gray-400 mt-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                <span class="ml-3 text-gray-300">{{ patient.address }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Información personal -->
                    <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-purple-500/10 rounded-lg">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-white">Información personal</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-400">Nacionalidad</p>
                                    <p class="text-gray-200">{{ patient.nationality }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Estado civil</p>
                                    <p class="text-gray-200">{{ patient.marital_status }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Fecha de nacimiento</p>
                                <p class="text-gray-200">{{ formatDate(patient.birthdate) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Información laboral -->
                    <div class="bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-green-500/10 rounded-lg">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-semibold text-white">Información laboral y seguro</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-400">Cargo de trabajo</p>
                                <p class="text-gray-200">{{ patient.position }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">ARS</p>
                                <p class="text-gray-200">{{ patient.ars || 'Ninguno' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="mt-6 bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-700 flex items-center justify-center">
                    <div v-if="inProcessAdmssion">
                        <strong class="text-white mr-4">Paciente ingresado</strong>
                        <Link
                            class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:to-green-600 transition-all duration-200"
                            :href="route('admissions.show', inProcessAdmssion)">Ir al ingreso</Link>
                    </div>
                    <div v-else>
                        <strong class="text-white mr-4">Paciente no ingresado</strong>
                        <Link
                            class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:to-blue-600 transition-all duration-200"
                            :href="route('admissions.create', { patient_id: patient.id })">Crear ingreso</Link>
                    </div>

                </div>
            </div>
        </div>
        <ConfirmationModal :show="patientBeingDeleted != null" @close="patientBeingDeleted = null">
            <template #title>
                Eliminar Ingreso
            </template>

            <template #content>
                ¿Estás seguro de que deseas eliminar este ingreso?
            </template>

            <template #footer>
                <SecondaryButton @click="patientBeingDeleted = null">
                    Cancelar
                </SecondaryButton>

                <DangerButton class="ms-3" @click="deletePatient">
                    Eliminar
                </DangerButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';


export default {
    props: {
        patient: Object,
        inProcessAdmssion: Number,
    },
    components: {
        AppLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
    },
    data() {
        return {
            patientBeingDeleted: ref(null)
        }
    },
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        getInitials(firstName, lastName) {
            return `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase();
        },
        deletePatient() {
            this.patientBeingDeleted = false
            this.$inertia.delete(route('patients.destroy', this.patient.id));
        },
        restorePatient() {
            this.$inertia.put(route('patients.update', this.patient.id), { active: true });
        }
    }
}
</script>
