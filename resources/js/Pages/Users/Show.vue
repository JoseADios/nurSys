<template>
    <AppLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-white leading-tight text-center">
                Detalles del Usuario
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 shadow-xl rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Nombres</h3>
                            <p class="text-white">{{ user.name }}</p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Apellidos</h3>
                            <p class="text-white">{{ user.last_name }}</p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Correo</h3>
                            <p class="text-white">{{ user.email }}</p>
                        </div>

                        <!-- <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Role</h3>
                            <p class="text-white">{{ user.role }}</p>
                        </div> -->

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Cédula</h3>
                            <p class="text-white">{{ user.identification_card }}</p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Exequatur</h3>
                            <p class="text-white">{{ user.exequatur }}</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Especialidad</h3>
                            <p class="text-white">{{ user.specialty }}</p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Área</h3>
                            <p class="text-white">{{ user.area }}</p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Teléfono</h3>
                            <p class="text-white">{{ user.phone }}</p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Fecha de Nacimiento</h3>
                            <p class="text-white">{{ user.birthdate }}</p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Posición</h3>
                            <p class="text-white">{{ user.position }}</p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Observación</h3>
                            <p class="text-white">{{ user.comment || 'Sin observaciones' }}</p>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Dirección</h3>
                            <p class="text-white">{{ user.address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6">
                    <button @click="goBack"
                        class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-700 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                        Volver
                    </button>
                    <Link :href="route('users.edit', user.id)"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200">
                        Editar
                    </Link>
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
        user: Object,
    },
    components: {
        AppLayout,
        Link
    },
    methods: {
        goBack() {
            this.$inertia.visit(document.referrer)
        }
    }
}
</script>
