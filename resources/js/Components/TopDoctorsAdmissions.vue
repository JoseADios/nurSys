<template>
    <div
        class="w-full h-full bg-white dark:bg-gray-800 overflow-hidden rounded-lg border border-gray-200/60 dark:border-gray-700/60 shadow-md">
        <!-- Encabezado fijo -->
        <div
            class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-blue-500/5 dark:bg-blue-500/5 z-10">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <h3 class="text-base sm:text-lg font-medium text-gray-800 dark:text-gray-100">
                        Top 3 doctores por ingresos
                    </h3>
                </div>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                    Este mes
                </span>
            </div>
        </div>

        <!-- Contenido principal -->
        <div
            class="[&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300/40 dark:[&::-webkit-scrollbar-track]:bg-neutral-700/30 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500/30">
            <!-- Lista de doctores -->
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <div v-for="(doctor, index) in doctors" :key="index"
                    class="group relative px-4 sm:px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-all duration-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-4">
                        <!-- Información del doctor -->
                        <Link :href="route('users.show', doctor.id)" class="flex items-center space-x-3 flex-grow">
                            <div class="flex-shrink-0 relative">
                                <!-- Avatar: maneja tanto photo_path como generación automática -->
                                <div v-if="doctor.profile_photo_path"
                                    class="size-8 sm:h-10 sm:w-10 rounded-full overflow-hidden">
                                    <img :src="getPhotoUrl(doctor.profile_photo_path)" alt="Doctor avatar"
                                        class="h-full w-full object-cover">
                                </div>
                                <div v-else
                                    class="size-8 sm:h-10 sm:w-10 text-sm rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 font-medium">
                                    {{ getInitials(doctor.doctor) }}
                                </div>
                                <div class="absolute -top-1 -right-1 size-4 rounded-full flex items-center justify-center text-xs text-white font-bold shadow-sm"
                                    :class="{
                                        'bg-blue-500': index === 0,
                                        'bg-indigo-500': index === 1,
                                        'bg-purple-500': index === 2
                                    }">
                                    {{ index + 1 }}
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ doctor.doctor }}
                                </div>
                                <div class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-3.5 w-3.5 mr-1 text-gray-400 dark:text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ doctor.specialty }}
                                </div>
                            </div>
                        </Link>

                        <!-- Contador de admisiones -->
                        <div
                            class="flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-center mt-2 sm:mt-0">
                            <div class="flex items-center text-sm text-gray-900 dark:text-white font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                {{ doctor.cant }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Ingresos
                            </div>
                        </div>
                    </div>

                    <!-- Barra de progreso sutil -->
                    <div class="mt-3 h-1 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-blue-500/70 dark:bg-blue-500/90"
                            :style="{ width: calculatePercentage(doctor.cant) + '%' }">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensaje cuando no hay doctores -->
            <div v-if="doctors.length === 0" class="py-8 px-4 sm:px-6 text-center">
                <div
                    class="rounded-full bg-gray-100 dark:bg-gray-700 mx-auto h-12 w-12 flex items-center justify-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                    No hay información sobre admisiones de doctores para mostrar.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    name: 'TopDoctorsAdmissions',
    components: {
        Link
    },
    props: {
        doctors: {
            type: Array,
            required: true,
            default: () => []
        }
    },
    methods: {
        calculatePercentage(count) {
            if (this.doctors.length === 0) return 0;
            const maxCount = Math.max(...this.doctors.map(d => d.cant));
            return (count / maxCount) * 100;
        },
        getInitials(name) {
            return name.split(' ')
                .map(word => word.charAt(0))
                .join('')
                .toUpperCase()
                .substring(0, 2);
        },
        getPhotoUrl(photoPath) {
            // Verifica si ya es una URL completa
            if (photoPath && (photoPath.startsWith('http://') || photoPath.startsWith('https://'))) {
                return photoPath;
            }

            // Si es una ruta relativa (almacenada en storage)
            if (photoPath) {
                return `/storage/${photoPath}`;
            }
            return null;
        }
    }
}
</script>
