<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <BreadCrumb :items="[
                    {
                        text: 'Pacientes',
                        route: route('patients.index')
                    },
                    {
                        text: 'Ver'
                    },
                ]" />
            </h2>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-100 dark:bg-gray-900">
            <div class="max-w-6xl mx-auto">
                <!-- Tarjeta del perfil principal -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 mb-8 transform transition-all duration-300">

                    <div class="p-6 lg:p-8 flex flex-col lg:flex-row lg:justify-between lg:items-start md:items-center gap-6">
                        <div class="flex flex-col items-center sm:flex-row gap-5">
                            <DynamicAvatar class="size-8 md:size-14"
                                :name="patient.first_name + ' ' + patient.first_surname" bg-color="#696CFF"
                                color="white" />
                            <div>
                                <div class="flex items-center gap-3">
                                    <h1 class="text-xl md:text-xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                                        {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname
                                        }}
                                    </h1>
                                    <span v-if="patient.active == 1"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        Activo
                                    </span>
                                    <span v-else
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                        Inactivo
                                    </span>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                                    <Id2Icon class="w-4 h-4 mr-1 text-gray-400 dark:text-gray-500" />
                                    Cédula: {{ patient.identification_card }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 justify-center w-full md:w-auto">

                            <Link :href="route('patients.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 border border-gray-200 dark:border-gray-700">
                            <BackIcon class="w-4 h-4 mr-2" />
                            Volver
                            </Link>

                            <AccessGate :permission="['patient.update']">
                                <Link :href="route('patients.edit', patient.id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-lg hover:bg-blue-600 dark:hover:bg-blue-700 transition-all duration-200 border border-gray-200 dark:border-gray-700">
                                <EditIcon class="w-4 h-4 mr-2" />
                                Editar
                                </Link>
                            </AccessGate>

                            <AccessGate :permission="['patient.delete']">
                                <button v-if="patient.active == 1" @click="patientBeingDeleted = true"
                                    class="inline-flex items-center px-4 py-2 bg-red-500 dark:bg-red-600 text-white rounded-lg hover:bg-red-600 dark:hover:bg-red-700 transition-all duration-200 border border-gray-200 dark:border-gray-700">
                                    <TrashIcon class="w-4 h-4 mr-2" />
                                    Deshabilitar
                                </button>
                                <button v-else @click="restorePatient"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-500 dark:bg-emerald-600 text-white rounded-lg hover:bg-emerald-600 dark:hover:bg-emerald-700 transition-all duration-200 border border-gray-200 dark:border-gray-700">
                                    <RestoreIcon class="w-4 h-4 mr-2" />
                                    Habilitar
                                </button>
                            </AccessGate>


                        </div>
                    </div>
                </div>

                <!-- Layout de tarjetas informativas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Tarjeta de Información de contacto -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-[#5FC6FF] rounded-lg border border-gray-200 dark:border-gray-700">
                                <MailIcon class="w-6 h-6 text-white" />
                            </div>
                            <h3 class="ml-4 text-xl font-bold text-gray-900 dark:text-white">Información de contacto
                            </h3>
                        </div>
                        <div class="space-y-5">
                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <PhoneIcon class="w-5 h-5 text-gray-800 dark:text-white" />
                                <span class="ml-3 text-gray-800 dark:text-gray-200 font-medium">{{ patient.phone
                                }}</span>
                            </div>
                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <AtIcon class="w-5 h-5 text-gray-800 dark:text-white" />
                                <a href="mailto:{{ patient.email }}"
                                    class="ml-3 text-gray-800 dark:text-gray-200 font-medium hover:text-gray-700 dark:hover:text-white transition-colors truncate"
                                    >
                                    {{ patient.email }}
                                </a>
                            </div>
                            <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <MapPinIcon class="w-5 h-5 text-gray-800 dark:text-white mt-1" />
                                <span class="ml-3 text-gray-800 dark:text-gray-200 font-medium">{{ patient.address
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Información personal -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-[#696CFF] rounded-lg border border-gray-200 dark:border-gray-700">
                                <UserIcon class="w-6 h-6 text-white" />
                            </div>
                            <h3 class="ml-4 text-xl font-bold text-gray-900 dark:text-white">Información personal</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <div class="p-3 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <p class="text-sm  font-medium">Nacionalidad</p>
                                <p class=" font-semibold mt-1">{{ patient.nationality }}</p>
                            </div>
                            <div class="p-3 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <p class="text-sm  font-medium">Estado civil</p>
                                <p class=" font-semibold mt-1">{{ patient.marital_status }}</p>
                            </div>
                            <div class="p-3 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <p class="text-sm  font-medium">Fecha de nacimiento</p>
                                <p class=" font-semibold mt-1">{{ formatDate(patient.birthdate) }}</p>
                            </div>
                            <div class="p-3 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <p class="text-sm  font-medium">Edad</p>
                                <p class=" font-semibold mt-1">{{ calculateAge(patient.birthdate) }} años</p>
                            </div>
                            <div class="p-3 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <p class="text-sm  font-medium">Fecha de creación</p>
                                <p class=" font-semibold mt-1">{{ formatDate(patient.created_at) }}</p>
                            </div>
                            <div class="p-3 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                <p class="text-sm  font-medium">Fecha de actualización</p>
                                <p class=" font-semibold mt-1">{{ formatDate(patient.updated_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Información laboral -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-[#FFB400] rounded-lg border border-gray-200 dark:border-gray-700">
                                <BriefCaseIcon class="w-6 h-6 text-white" />
                            </div>
                            <h3 class="ml-4 text-xl font-bold text-gray-900 dark:text-white">Información laboral</h3>
                        </div>
                        <div class="space-y-5">
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg text-gray-700 dark:text-gray-200">
                                <p class="text-sm font-medium">Cargo de trabajo</p>
                                <p class="font-semibold mt-1">{{ patient.position || 'No especificado' }}</p>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg text-gray-700 dark:text-gray-200">
                                <p class="text-sm font-medium">ARS</p>
                                <p class="font-semibold mt-1">{{ patient.ars || 'Ninguno' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado de ingreso -->
                <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div v-if="inProcessAdmssion" class="flex flex-col sm:flex-row items-center justify-between">
                        <div class="flex items-center mb-4 sm:mb-0">
                            <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-full mr-4">
                                <CheckCircleIcon class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Paciente ingresado</h3>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">El paciente actualmente se encuentra
                                    ingresado en el sistema</p>
                            </div>
                        </div>
                        <Link
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 hover:from-green-600 hover:to-green-700 transition-all duration-200"
                            :href="route('admissions.show', inProcessAdmssion)">
                        <EyeIcon class="w-5 h-5 mr-2" />
                        Ir al ingreso
                        </Link>
                    </div>
                    <div v-else class="flex flex-col sm:flex-row items-center justify-between">
                        <div class="flex items-center mb-4 sm:mb-0">
                            <div class="p-3 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full mr-4">
                                <AlertTriangleIcon class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Paciente no ingresado</h3>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">El paciente no se encuentra
                                    ingresado
                                    actualmente</p>
                            </div>
                        </div>
                        <AccessGate :permission="['admission.create']" v-if="patient.active == 1">
                            <Link
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 hover:from-blue-600 hover:to-blue-700 transition-all duration-200"
                                :href="route('admissions.create', { patient_id: patient.id })">
                            <PlusIcon class="w-5 h-5 mr-2" />
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
                    <div class="flex items-center text-red-500 dark:text-red-400">
                        <TrashIcon class="w-6 h-6 mr-2" />
                        Deshabilitar Paciente
                    </div>
                </template>

                <template #content>
                    <p class="text-gray-500 dark:text-gray-400">¿Estás seguro de que deseas deshabilitar este paciente?
                        Esta
                        acción no se puede deshacer y puede afectar a todos los registros asociados.</p>
                </template>

                <template #footer>
                    <SecondaryButton @click="patientBeingDeleted = null">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton class="ms-3" @click="deletePatient">
                        Deshabilitar
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
import Id2Icon from '@/Components/Icons/Id2Icon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import RestoreIcon from '@/Components/Icons/RestoreIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import BackIcon from '@/Components/Icons/BackIcon.vue';
import MailIcon from '@/Components/Icons/MailIcon.vue';
import PhoneIcon from '@/Components/Icons/PhoneIcon.vue';
import AtIcon from '@/Components/Icons/AtIcon.vue';
import MapPinIcon from '@/Components/Icons/MapPinIcon.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import BriefCaseIcon from '@/Components/Icons/BriefCaseIcon.vue';
import CheckCircleIcon from '@/Components/Icons/CheckCircleIcon.vue';
import EyeIcon from '@/Components/Icons/EyeIcon.vue';
import AlertTriangleIcon from '@/Components/Icons/AlertTriangleIcon.vue';
import PlusIcon from '@/Components/Icons/PlusIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import DynamicAvatar from '@/Components/DynamicAvatar.vue';


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
        Id2Icon,
        TrashIcon,
        RestoreIcon,
        EditIcon,
        BackIcon,
        MailIcon,
        PhoneIcon,
        AtIcon,
        MapPinIcon,
        UserIcon,
        BriefCaseIcon,
        CheckCircleIcon,
        EyeIcon,
        AlertTriangleIcon,
        PlusIcon,
        BreadCrumb,
        DynamicAvatar
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
