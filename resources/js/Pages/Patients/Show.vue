<template>
    <AppLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-white leading-tight text-center">
                Detalles del paciente
            </h2>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-900">
            <div class="max-w-6xl mx-auto">
                <!-- Tarjeta del perfil principal -->
                <div
                    class="bg-gray-800 rounded-2xl overflow-hidden shadow-xl border border-gray-700 mb-8 transform transition-all duration-300">
                    <div class="p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex items-center gap-5">
                            <div
                                class="size-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                                <span class="text-2xl text-white font-bold">
                                    {{ getInitials(patient.first_name, patient.first_surname) }}
                                </span>
                            </div>
                            <div>
                                <div class="flex items-center gap-3">
                                    <h1 class="text-3xl font-bold text-white">
                                        {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname
                                        }}
                                    </h1>
                                    <span v-if="patient.active == 1"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                    <span v-else
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactivo
                                    </span>
                                </div>
                                <p class="text-gray-400 mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Cédula: {{ patient.identification_card }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <AccessGate :permission="['patient.delete']">
                                <button v-if="patient.active == 1" @click="patientBeingDeleted = true"
                                    class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200 shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                                <button v-else @click="restorePatient"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-all duration-200 shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Restaurar
                                </button>
                            </AccessGate>

                            <AccessGate :permission="['patient.update']">
                                <Link :href="route('patients.edit', patient.id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Editar
                                </Link>
                            </AccessGate>

                            <Link :href="route('patients.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600 transition-all duration-200 shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Layout de tarjetas informativas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Tarjeta de Información de contacto -->
                    <div
                        class="bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-700">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="ml-4 text-xl font-bold text-white">Información de contacto</h3>
                        </div>
                        <div class="space-y-5">
                            <div class="flex items-center p-3 bg-gray-700/30 rounded-lg">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="ml-3 text-gray-200 font-medium">{{ patient.phone }}</span>
                            </div>
                            <div class="flex items-center p-3 bg-gray-700/30 rounded-lg">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                                <a href="mailto:{{ patient.email }}"
                                    class="ml-3 text-gray-200 font-medium hover:text-blue-400 transition-colors">{{
                                    patient.email }}</a>
                            </div>
                            <div class="flex items-start p-3 bg-gray-700/30 rounded-lg">
                                <svg class="w-5 h-5 text-blue-400 mt-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                <span class="ml-3 text-gray-200 font-medium">{{ patient.address }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Información personal -->
                    <div
                        class="bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-700">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="ml-4 text-xl font-bold text-white">Información personal</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <div class="p-3 bg-gray-700/30 rounded-lg">
                                <p class="text-sm text-purple-400 font-medium">Nacionalidad</p>
                                <p class="text-gray-200 font-semibold mt-1">{{ patient.nationality }}</p>
                            </div>
                            <div class="p-3 bg-gray-700/30 rounded-lg">
                                <p class="text-sm text-purple-400 font-medium">Estado civil</p>
                                <p class="text-gray-200 font-semibold mt-1">{{ patient.marital_status }}</p>
                            </div>
                            <div class="p-3 bg-gray-700/30 rounded-lg">
                                <p class="text-sm text-purple-400 font-medium">Fecha de nacimiento</p>
                                <p class="text-gray-200 font-semibold mt-1">{{ formatDate(patient.birthdate) }}</p>
                            </div>
                            <div class="p-3 bg-gray-700/30 rounded-lg">
                                <p class="text-sm text-purple-400 font-medium">Edad</p>
                                <p class="text-gray-200 font-semibold mt-1">{{ calculateAge(patient.birthdate) }} años
                                </p>
                            </div>
                            <div class="p-3 bg-gray-700/30 rounded-lg">
                                <p class="text-sm text-purple-400 font-medium">Fecha de creación</p>
                                <p class="text-gray-200 font-semibold mt-1">{{ formatDate(patient.created_at) }}</p>
                            </div>
                            <div class="p-3 bg-gray-700/30 rounded-lg">
                                <p class="text-sm text-purple-400 font-medium">Fecha de actualización</p>
                                <p class="text-gray-200 font-semibold mt-1">{{ formatDate(patient.updated_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Información laboral -->
                    <div
                        class="bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-700">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="ml-4 text-xl font-bold text-white">Información laboral</h3>
                        </div>
                        <div class="space-y-5">
                            <div class="p-3 bg-gray-700/30 rounded-lg">
                                <p class="text-sm text-green-400 font-medium">Cargo de trabajo</p>
                                <p class="text-gray-200 font-semibold mt-1">{{ patient.position || 'No especificado' }}
                                </p>
                            </div>
                            <div class="p-3 bg-gray-700/30 rounded-lg">
                                <p class="text-sm text-green-400 font-medium">ARS</p>
                                <p class="text-gray-200 font-semibold mt-1">{{ patient.ars || 'Ninguno' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado de ingreso -->
                <div
                    class="mt-8 bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-700">
                    <div v-if="inProcessAdmssion" class="flex flex-col sm:flex-row items-center justify-between">
                        <div class="flex items-center mb-4 sm:mb-0">
                            <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-full mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Paciente ingresado</h3>
                                <p class="text-gray-400 text-sm">El paciente actualmente se encuentra ingresado en el
                                    sistema
                                </p>
                            </div>
                        </div>
                        <Link
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-medium rounded-lg shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200"
                            :href="route('admissions.show', inProcessAdmssion)">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Ir al ingreso
                        </Link>
                    </div>
                    <div v-else class="flex flex-col sm:flex-row items-center justify-between">
                        <div class="flex items-center mb-4 sm:mb-0">
                            <div class="p-3 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">Paciente no ingresado</h3>
                                <p class="text-gray-400 text-sm">El paciente no se encuentra ingresado actualmente</p>
                            </div>
                        </div>
                        <AccessGate :permission="['admission.create']" v-if="patient.active == 1">
                            <Link
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200"
                                :href="route('admissions.create', { patient_id: patient.id })">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Crear ingreso
                            </Link>
                        </AccessGate>
                    </div>
                </div>
            </div>
        </div>

        <AccessGate :permission="['patient.delete']">
            <ConfirmationModal :show="patientBeingDeleted != null" @close="patientBeingDeleted = null">
                <template #title>
                    <div class="flex items-center text-red-500">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Eliminar Paciente
                    </div>
                </template>

                <template #content>
                    <p class="text-gray-500">¿Estás seguro de que deseas eliminar este paciente? Esta acción no se puede
                        deshacer y puede afectar a todos los registros asociados.</p>
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
        </AccessGate>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
import moment from "moment/moment";
import 'moment/locale/es';
import 'moment/locale/es';

moment.locale('es');

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
        AccessGate,
    },
    data() {
        return {
            patientBeingDeleted: ref(null)
        }
    },
    methods: {
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY');
        },
        calculateAge(birthdate) {
            return moment().diff(moment(birthdate), 'years');
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
        },

    }
}
</script>
