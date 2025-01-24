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

                        <div class="space-y-2">
                            <h3 class="text-sm font-medium text-gray-400">Role</h3>
                            <p class="text-white">{{ user.roles[0] ? user.roles[0].name : 'N/A' }}</p>
                        </div>

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
                    <button v-if="user.active == 1" @click="userBeingDeleted = true"
                        class="inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:to-red-600 transition-all duration-200">
                        Deshabilitar
                    </button>
                    <button v-else @click="restoreUser"
                        class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:to-green-600 transition-all duration-200">
                        Habilitar
                    </button>

                    <Link :href="route('users.index')" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-700 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                    Volver
                    </Link>
                    <Link :href="route('users.edit', user.id)"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200">
                    Editar
                    </Link>
                </div>
            </div>
        </div>
        <ConfirmationModal :show="userBeingDeleted != null" @close="userBeingDeleted = null">
            <template #title>
                Eliminar Ingreso
            </template>

            <template #content>
                ¿Estás seguro de que deseas eliminar este ingreso?
            </template>

            <template #footer>
                <SecondaryButton @click="userBeingDeleted = null">
                    Cancelar
                </SecondaryButton>

                <DangerButton class="ms-3" @click="deleteUser">
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
        user: Object,
        previousUrl: String,
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
            userBeingDeleted: ref(null)
        }
    },
    methods: {
        deleteUser() {
            this.userBeingDeleted = false
            this.$inertia.delete(route('users.destroy', this.user.id));
        },
        restoreUser() {
            this.$inertia.put(route('users.update', this.user.id), { active: true });
        },
        goBack() {
            if (this.previousUrl) {
                this.$inertia.visit(this.previousUrl);
            } else {
                window.history.back();
            }
        }
    }
}
</script>
