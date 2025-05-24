<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <BreadCrumb :items="[
                    {
                        text: 'Usuarios',
                        route: route('users.index')
                    },
                    {
                        text: 'Ver'
                    },
                ]" />
            </h2>
        </template>

        <div class="max-w-7xl mx-auto p-6">

            <!-- Tarjeta Principal -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                        <div class="flex flex-col sm:flex-row items-center gap-4 mb-4 md:mb-0">
                            <div>
                                <img v-if="user.profile_photo_url" :src="user.profile_photo_url" alt="Foto de Perfil"
                                    class="size-20 rounded-full object-cover">
                                <DynamicAvatar v-else :name="$page.props.auth.user.name" class="md:size-20" />
                            </div>
                            <div>
                                <h2 class="text-2xl text-center font-bold text-gray-700 dark:text-white">{{ user.name }}
                                    {{
                                        user.last_name }}</h2>
                                <div class="flex items-center justify-center sm:justify-start space-x-2 mt-2 xl:mt-0">
                                    <span
                                        class="px-2 py-1 bg-gray-200 dark:bg-white/20 text-gray-700 dark:text-white text-sm rounded-full">
                                        <div v-if="user.roles[0]">
                                            <FormatRole :role="user.roles[0].name" />
                                        </div>
                                        <div v-else>N/A</div>
                                    </span>
                                    <span :class="[
                                        'px-2 py-1 text-sm rounded-full',
                                        user.active === '1' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'
                                    ]">
                                        {{ user.active === '1' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex flex-col items-center space-y-2 md:space-y-0 md:space-x-3  md:flex-row lg:space-x-4">
                            <PersonalizableLink :href="route('users.index')" color="gray">
                                <BackIcon class="w-4 h-4 mr-2" />
                                Volver
                            </PersonalizableLink>
                            <PersonalizableLink :href="route('users.edit', user.id)">
                                <EditIcon class="h-4 w-4 mr-2" />
                                <span>Editar</span>
                            </PersonalizableLink>
                            <DangerButton v-if="user.active === '1'" @click="userBeingDeleted = true">
                                <TrashIcon class="w-4 h-4 mr-2" />
                                Deshabilitar
                            </DangerButton>
                            <PersonalizableButton v-else @click="restoreUser" :loading="userBeingRestored" color="green">
                                <RestoreIcon class="w-4 h-4 mr-2" />
                                Habilitar
                            </PersonalizableButton>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secciones de Información -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                <!-- Información Personal -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="p-3 border-2 border-[#696CFF] rounded-lg">
                                <UserIcon class="w-4 h-4 text-[#696CFF]" />
                            </div>
                            <h3 class="ml-4 text-xl font-bold text-gray-900 dark:text-white">Información personal</h3>
                        </div>
                        <div class="space-y-4">
                            <InfoItem icon="identification" label="Cédula" :value="user.identification_card" />
                            <InfoItem icon="calendar" label="Fecha de Nacimiento" :value="formatDate(user.birthdate)" />
                            <InfoItem icon="location" label="Dirección" :value="user.address" />
                            <InfoItem icon="clock" label="Fecha de Creación" :value="formatDate(user.created_at)" />
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="p-3 border-2 border-[#5FC6FF] rounded-lg ">
                                <MailIcon class="w-4 h-4 text-[#5FC6FF]" />
                            </div>
                            <h3 class="ml-4 text-xl font-bold text-gray-900 dark:text-white">Información de contacto
                            </h3>
                        </div>
                        <div class="space-y-4">
                            <InfoItem icon="mail" label="Correo" :value="user.email" />
                            <InfoItem icon="phone" label="Teléfono" :value="user.phone" />
                        </div>
                    </div>
                </div>

                <!-- Información Profesional -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 md:col-span-2 lg:col-span-2">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="p-3 border-2 border-[#FFB400] rounded-lg">
                                <BriefCaseIcon class="w-4 h-4 text-[#FFB400]" />
                            </div>
                            <h3 class="ml-4 text-xl font-bold text-gray-900 dark:text-white">Información laboral</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <InfoItem icon="award" label="Exequatur" :value="user.exequatur" />
                            <InfoItem icon="briefcase" label="Especialidad" :value="user.specialty" />
                            <InfoItem icon="building" label="Área" :value="user.area" />
                            <InfoItem icon="badge" label="Posición" :value="user.position" />
                        </div>

                        <div v-if="user.comment" class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Comentarios</h4>
                            <p class="text-gray-800 dark:text-gray-200">{{ user.comment }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Confirmación -->
            <ConfirmationModal :show="userBeingDeleted" @close="userBeingDeleted = false">
                <template #title>
                    <div class="flex items-center text-red-500">
                        <TrashIcon class="w-6 h-6 mr-2" />
                        Deshabilitar Usuario
                    </div>
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
import BackIcon from '@/Components/Icons/BackIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import RestoreIcon from '@/Components/Icons/RestoreIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import DynamicAvatar from '@/Components/DynamicAvatar.vue';
import MailIcon from '@/Components/Icons/MailIcon.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import BriefCaseIcon from '@/Components/Icons/BriefCaseIcon.vue';
import PersonalizableLink from '@/Components/PersonalizableLink.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';

export default {
    components: {
        AppLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        InfoItem,
        FormatRole,
        BackIcon,
        EditIcon,
        TrashIcon,
        RestoreIcon,
        BreadCrumb,
        DynamicAvatar,
        MailIcon,
        UserIcon,
        BriefCaseIcon,
        PersonalizableLink,
        PersonalizableButton
    },
    props: {
        user: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            userBeingDeleted: ref(false),
            userBeingRestored: ref(false),
        }
    },
    created() {
        moment.locale('es');
    },
    methods: {
        deleteUser() {
            this.userBeingDeleted = true;
            this.$inertia.delete(route('users.destroy', this.user.id), {
                onFinish: () => {
                    this.userBeingDeleted = false;
                }
            });
        },
        restoreUser() {
            this.userBeingRestored = true;
            this.$inertia.put(route('users.update', this.user.id), { active: true }, {
                onFinish: () => {
                    this.userBeingRestored = false;
                }
            });
        },
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY');
        }
    },

};
</script>
