<template>
    <AppLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-white leading-tight text-center">
                Detalles del Usuario
            </h2>
        </template>

        <div class="max-w-7xl mx-auto p-6">
            <!-- Botón de Volver -->
            <div class="mb-6 flex ">

            </div>

            <!-- Tarjeta Principal -->
            <div
                class="bg-gradient-to-r from-purple-600 to-blue-600 dark:from-purple-900 dark:to-blue-900 rounded-lg shadow-xl mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="flex items-center space-x-4 mb-4 md:mb-0">
                            <img :src="user.profile_photo_url" alt="Foto de Perfil" class="w-20 h-20 rounded-full">
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ user.name }} {{ user.last_name }}</h2>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 bg-white/20 text-white text-sm rounded-full">
                                        <div v-if="user.roles[0]">
                                            <FormatRole :role="user.roles[0].name" />
                                        </div>
                                        <div v-else>N/A</div>

                                    </span>
                                    <span :class="[
                                        'px-2 py-1 text-white text-sm rounded-full',
                                        user.active === '1' ? 'bg-green-500' : 'bg-red-500'
                                    ]">
                                        {{ user.active === '1' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <Link :href="route('users.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-500/90 text-gray-200 text-sm rounded-lg hover:bg-gray-400/50 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                            </Link>
                            <Link :href="route('users.edit', user.id)"
                                class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-all duration-200 flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Editar</span>
                            </Link>
                            <button v-if="user.active === '1'" @click="userBeingDeleted = true"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200">
                                Deshabilitar
                            </button>
                            <button v-else @click="restoreUser"
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-all duration-200">
                                Habilitar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secciones de Información -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Información Personal -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Información Personal
                        </h3>
                        <div class="space-y-4">
                            <InfoItem icon="identification" label="Cédula" :value="user.identification_card" />
                            <InfoItem icon="calendar" label="Fecha de Nacimiento" :value="formatDate(user.birthdate)" />
                            <InfoItem icon="location" label="Dirección" :value="user.address" />
                            <InfoItem icon="clock" label="Fecha de Creación" :value="formatDate(user.created_at)" />
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Información de Contacto
                        </h3>
                        <div class="space-y-4">
                            <InfoItem icon="mail" label="Correo" :value="user.email" />
                            <InfoItem icon="phone" label="Teléfono" :value="user.phone" />
                        </div>
                    </div>
                </div>

                <!-- Información Profesional -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl lg:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Información Profesional
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <InfoItem icon="award" label="Exequatur" :value="user.exequatur" />
                            <InfoItem icon="briefcase" label="Especialidad" :value="user.specialty" />
                            <InfoItem icon="building" label="Área" :value="user.area" />
                            <InfoItem icon="badge" label="Posición" :value="user.position" />
                        </div>

                        <div v-if="user.comment" class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Observaciones</h4>
                            <p class="text-gray-800 dark:text-gray-200">{{ user.comment }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Confirmación -->
            <ConfirmationModal :show="userBeingDeleted" @close="userBeingDeleted = false">
                <template #title>
                    Deshabilitar Usuario
                </template>
                <template #content>
                    ¿Estás seguro de que deseas deshabilitar este usuario? <br>
                    El usuario no podrá acceder al sistema.
                </template>
                <template #footer>
                    <SecondaryButton @click="userBeingDeleted = false">
                        Cancelar
                    </SecondaryButton>
                    <DangerButton class="ms-3" @click="deleteUser">
                        Deshabilitar
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </div>
    </AppLayout>
</template>

<script>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InfoItem from '@/Components/InfoItem.vue';
import moment from "moment/moment";
import 'moment/locale/es';
import FormatRole from '@/Components/FormatRole.vue';


export default {
    components: {
        AppLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        InfoItem,
        FormatRole
    },
    props: {
        user: {
            type: Object,
            required: true,
        },
    },
    setup() {
        const userBeingDeleted = ref(false);

        return {
            userBeingDeleted,
        };
    },
    created() {
        moment.locale('es');
    },
    methods: {
        deleteUser() {
            this.userBeingDeleted = false;
            this.$inertia.delete(route('users.destroy', this.user.id));
        },
        restoreUser() {
            this.$inertia.put(route('users.update', this.user.id), { active: true });
        },
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY');
        }
    },

};
</script>
