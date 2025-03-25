<template>
    <div
        class="w-full h-full bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200/60 dark:border-gray-700/60 shadow-md">
        <!-- Encabezado fijo -->
        <div
            class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-blue-500/5 dark:bg-blue-500/5 z-10">
            <div class="flex flex-wrap justify-between items-center gap-2">
                <h3 class="text-base sm:text-lg font-medium text-gray-800 dark:text-gray-100">
                    {{ userRole === 'doctor' ? 'Órdenes médicas por firmar' : userRole === 'nurse' ? 'Registros de enfermería por firmar' : 'Documentos por firmar' }}
                </h3>
                <span v-if="userRole === 'doctor' || userRole === 'nurse'"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                    {{ userRole === 'doctor' ? 'Hoy' : 'Turno actual' }}
                </span>
                <!-- Espacio vacío para mantener simetría cuando no hay badge -->
                <div v-else class="h-5"></div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div
            class="max-h-[calc(100%-60px)] overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300/40 dark:[&::-webkit-scrollbar-track]:bg-neutral-700/30 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500/30">
            <!-- Lista de documentos -->
            <div v-if="pendingDocuments.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700">
                <div v-for="(document, index) in pendingDocuments" :key="index"
                    class="group relative px-4 sm:px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-all duration-200">
                    <div class="grid grid-cols-1 sm:grid-cols-[1fr,auto] gap-4">
                        <!-- Información del paciente -->
                        <Link :href="getDocumentRoute(document)" class="flex items-start space-x-3 flex-grow">
                            <div class="flex-shrink-0">
                                <div
                                    class="size-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 font-medium">
                                    {{ getInitials(document.admission.patient.first_name + ' ' +
                                        document.admission.patient.first_surname) }}
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ document.admission.patient.first_name + ' ' +
                                        document.admission.patient.first_surname + ' ' +
                                        document.admission.patient.second_surname }}
                                </div>
                                <div class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <BedIcon class="h-3.5 w-3.5 mr-1 text-gray-400 dark:text-gray-500" />
                                    {{ document.admission.bed ? `${document.admission.bed.room} |
                                    ${document.admission.bed.number}` : 'Sin cama asignada' }}
                                </div>
                            </div>
                        </Link>

                        <!-- Tiempo pendiente -->
                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center">
                            <div class="flex items-center text-sm font-medium"
                                :class="getTimeStatusClass(document.created_at)">
                                <ClockIcon class="h-4 w-4 mr-1" />
                                {{ formatTimeAgo(document.created_at) }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Pendiente
                            </div>
                        </div>
                    </div>

                    <!-- Fila inferior con tipo de documento y botón de firma -->
                    <div class="mt-3 flex items-center justify-between">
                        <div class="text-gray-500 dark:text-gray-400 text-sm">
                            {{ getDocType(document) === 'medicalOrder' ? 'Orden médica' : 'Hoja de enfermería' }}
                        </div>

                        <Link :href="getDocumentRoute(document)"
                            class="inline-flex items-center px-3 py-1.5 text-xs sm:text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors">
                            <EditIcon class="h-4 w-4 mr-1.5" />
                            Firmar
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Mensaje cuando no hay documentos -->
            <div v-if="pendingDocuments.length === 0" class="py-8 px-4 sm:px-6 text-center">
                <div
                    class="rounded-full bg-gray-100 dark:bg-gray-700 mx-auto h-12 w-12 flex items-center justify-center mb-3">
                    <CheckIcon class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                    No tienes documentos pendientes por firmar en este momento.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import moment from 'moment/moment';
import 'moment/locale/es';
import BedIcon from './Icons/BedIcon.vue';
import ClockIcon from './Icons/ClockIcon.vue';
import EditIcon from './Icons/EditIcon.vue';
import CheckIcon from './Icons/CheckIcon.vue';

moment.locale('es');

export default {
    name: 'PendingDocumentsToSign',
    components: {
        Link,
        BedIcon,
        ClockIcon,
        EditIcon,
        CheckIcon
    },
    props: {
        pendingDocuments: {
            type: Array,
            required: true,
            default: () => []
        },
        userRole: {
            type: String,
            required: true
        }
    },
    methods: {
        getInitials(name) {
            return name.split(' ')
                .map(word => word.charAt(0))
                .join('')
                .toUpperCase()
                .substring(0, 2);
        },
        formatTimeAgo(date) {
            moment.locale('es');
            return moment(date).fromNow();
        },
        getTimeStatusClass(date) {
            const hoursAgo = moment().diff(moment(date), 'hours');
            if (hoursAgo >= 4) {
                return 'text-red-600 dark:text-red-400';
            } else if (hoursAgo >= 2) {
                return 'text-amber-600 dark:text-amber-400';
            } else {
                return 'text-gray-600 dark:text-gray-400';
            }
        },
        getDocumentRoute(document) {
            return route(`${this.getDocType(document)}s.show`, document.id);
        },
        getDocType(document) {
            if (this.userRole === 'doctor') {
                return 'medicalOrder';
            } else if (this.userRole === 'nurse') {
                return 'nurseRecord';
            } else {
                if (document.hasOwnProperty('doctor_sign')) {
                    return 'medicalOrder';
                } else if (document.hasOwnProperty('nurse_sign')) {
                    return 'nurseRecord';
                }
                return 'document'; // Valor por defecto para evitar undefined
            }
        }
    }
}
</script>
