<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Distribución de Camas
            </h2>
        </template>

        <div class="flex items-center justify-between">
            <div class="ml-4 mt-2 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">
                <div class="ml-2 inline-flex items-center ">
                    Camas
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-6">
            <div v-for="floor in floorPlan" :key="floor.number" class="mb-8">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 text-center">Piso {{ floor.number }}
                </h3>
                <div class="flex flex-wrap justify-center gap-6">
                    <div v-for="room in floor.rooms" :key="room.name"
                        class="bg-gray-200 dark:bg-gray-800 rounded-lg p-6 shadow-lg w-full max-w-md">
                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 text-center">
                            Sala {{ room.name }}
                        </h4>
                        <div class="grid grid-cols-4 gap-4 justify-center">
                            <div v-for="bed in room.beds" :key="bed.id" class="">
                                <div class="w-20 h-32 rounded-lg transition-all duration-300 hover:scale-110 flex flex-col items-center"
                                    :class="{
                                        'bg-orange-500': bed.admission,
                                        'bg-yellow-500': bed.status === 'cleaning',
                                        'bg-red-600': bed.status === 'out_of_service',
                                        'bg-green-500': bed.status === 'available'
                                    }" @mouseenter="showTooltip = bed.admission ? bed.id : null"
                                    @mouseleave="showTooltip = null">

                                    <!-- Header con número fijo -->
                                    <div
                                        class="top-0 w-full h-6 bg-gray-500 dark:bg-gray-700 rounded-t-lg flex items-center justify-center">
                                        <span class="text-gray-100 dark:text-white text-xs">{{ bed.number }}</span>
                                    </div>

                                    <!-- Tooltip estilo elegante alternativo -->
                                    <div v-if="showTooltip === bed.id && bed.admission"
                                        class="absolute z-50 w-72 text-sm bg-white dark:bg-gray-800 rounded-md shadow-2xl top-full left-1/2 -translate-x-1/2 mt-3 overflow-hidden border-0 transform origin-top scale-100 transition-all duration-200">
                                        <!-- Barra superior de color -->
                                        <div
                                            class="h-1.5 bg-gradient-to-r from-teal-400 to-emerald-500 dark:from-teal-600 dark:to-emerald-700">
                                        </div>

                                        <!-- Encabezado con diseño minimalista -->
                                        <div
                                            class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                            <h3 class="font-bold text-gray-800 dark:text-gray-200">
                                                <FormatId :id="bed.admission.id" prefix="ING"></FormatId>
                                            </h3>
                                            <div
                                                class="px-2 py-0.5 bg-teal-50 dark:bg-teal-900/30 rounded-full text-xs text-teal-600 dark:text-teal-400 font-medium">
                                                {{ daysHospitalized(bed.admission.created_at) }}</div>
                                        </div>

                                        <!-- Contenido con nueva distribución -->
                                        <div class="px-5 py-4">
                                            <div class="grid grid-cols-1 gap-3">
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mr-3">
                                                        <UserIcon class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Paciente</p>
                                                        <p class="font-medium text-gray-900 dark:text-white">{{
                                                            bed.admission.patient.first_name }} {{
                                                                bed.admission.patient.first_surname }}</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mr-3">
                                                        <StethoscopeIcon class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Doctor</p>
                                                        <p class="font-medium text-gray-900 dark:text-white">{{
                                                            bed.admission.doctor.name }} {{
                                                                bed.admission.doctor.last_name }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mr-3">
                                                        <DateIcon class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Fecha
                                                        </p>
                                                        <p class="font-medium text-gray-900 dark:text-white">{{
                                                            formatDate(bed.admission.created_at) }}</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mr-3">
                                                        <ClipBoardIcon
                                                            class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            Diagnóstico</p>
                                                        <p class="font-medium text-gray-900 dark:text-white truncate">
                                                            {{
                                                                bed.admission.admission_dx }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Flecha en forma de triángulo elegante -->
                                        <div
                                            class="absolute left-1/2 -translate-x-1/2 -top-2.5 w-0 h-0 border-l-6 border-r-6 border-b-6 border-transparent border-b-white dark:border-b-gray-800 filter drop-shadow-sm">
                                        </div>
                                    </div>

                                    <!-- Icono de cama centrado -->
                                    <div class="flex-1 flex items-center justify-center mt-5">
                                        <BedIcon v-if="bed.admission" class="w-10 h-10 text-white" />
                                        <BroomIcon v-else-if="bed.status === 'cleaning'" class="w-10 h-10 text-white" />
                                        <BanIcon v-else-if="bed.status === 'out_of_service'" class="w-10 h-10 text-white" />
                                        <Bed2Icon v-else class="w-8 h-8 text-white" />
                                    </div>

                                    <!-- Contenedor de botones fijo -->
                                    <div class="w-full p-1 space-y-1">
                                        <!-- Botón Ver Ingreso -->
                                        <div v-if="bed.admission" class="w-full">
                                            <Link
                                                :href="route('admissions.show', { id: bed.admission.id, bedsRoute: true })"
                                                class="w-full block text-center bg-gray-700 dark:bg-gray-700 text-white py-1 rounded-md text-xs hover:bg-gray-600 transition-colors">
                                            Ver Ingreso
                                            </Link>
                                        </div>

                                        <!-- Botones Editar y Crear -->
                                        <div v-if="!bed.admission"
                                            class="w-full space-y-1 flex justify-evenly items-end">

                                            <!-- crear ingreso -->
                                            <AccessGate :role="['receptionist', 'admin']"
                                                v-if="bed.status === 'available'">
                                                <Link :href="route('admissions.create', { bed_id: bed.id })"
                                                    class="text-center h-6 w-8 bg-gray-700 dark:bg-gray-700 hover:bg-blue-600 text-white rounded-md text-xs transition-colors flex items-center justify-center">
                                                <PlusIcon width="14" height="14" />
                                                </Link>
                                            </AccessGate>

                                            <AccessGate :permission="['bed.update']">
                                                <button @click="onBedClick(bed)"
                                                    class="h-6 w-8 bg-gray-600 dark:bg-gray-600 hover:bg-orange-500 text-white rounded-md text-xs transition-colors flex items-center justify-center">
                                                    <EditIcon width="14" height="14" />
                                                </button>
                                            </AccessGate>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <AccessGate :permission="['bed.update']">
            <DialogModal :show="showEditModal != null" @close="showEditModal = null">
                <template #title>
                    Cambiar estado de la cama
                </template>

                <template #content>
                    <form @submit.prevent="submitUpdate">
                        <div class="flex items-center">
                            <div class="flex items-center">

                                <div class="flex flex-wrap">
                                    <div class="flex items-center me-4">
                                        <input id="green-radio" type="radio" value="available"
                                            v-model="selectedBed.status" name="bed-status"
                                            class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="green-radio"
                                            class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Disponible</label>
                                    </div>
                                    <div class="flex items-center me-4">
                                        <input id="yellow-radio" type="radio" value="cleaning"
                                            v-model="selectedBed.status" name="bed-status"
                                            class="w-4 h-4 text-yellow-400 bg-gray-100 border-gray-300 focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="yellow-radio"
                                            class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">En
                                            limpieza</label>
                                    </div>
                                    <div class="flex items-center me-4">
                                        <input id="red-radio" type="radio" value="out_of_service"
                                            v-model="selectedBed.status" name="bed-status"
                                            class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="red-radio"
                                            class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Fuera de
                                            servicio</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </template>

                <template #footer>
                    <SecondaryButton @click="showEditModal = null">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton class="ms-3" @click="submitUpdate">
                        Save
                    </PrimaryButton>
                </template>
            </DialogModal>
        </AccessGate>
    </AppLayout>
</template>

<script>
import AccessGate from '@/Components/Access/AccessGate.vue';
import DialogModal from '@/Components/DialogModal.vue';
import FormatId from '@/Components/FormatId.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import moment from "moment/moment";
import 'moment/locale/es';
import 'moment/locale/es';

// Importar los nuevos componentes de iconos
import BedIcon from '@/Components/Icons/BedIcon.vue';
import DateIcon from '@/Components/Icons/DateIcon.vue';
import PlusIcon from '@/Components/Icons/PlusIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import StethoscopeIcon from '@/Components/Icons/StethoscopeIcon.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import ClipBoardIcon from '@/Components/Icons/ClipBoardIcon.vue';
import Bed2Icon from '@/Components/Icons/Bed2Icon.vue';
import BroomIcon from '@/Components/Icons/BroomIcon.vue';
import BanIcon from '@/Components/Icons/BanIcon.vue';

export default {
    components: {
        AppLayout,
        Link,
        DialogModal,
        PrimaryButton,
        SecondaryButton,
        AccessGate,
        FormatId,
        BedIcon,
        Bed2Icon,
        BroomIcon,
        UserIcon,
        StethoscopeIcon,
        DateIcon,
        ClipBoardIcon,
        PlusIcon,
        EditIcon,
        BanIcon
    },
    props: {
        beds: Array,
    },
    data() {
        return {
            showEditModal: ref(null),
            selectedBed: null,
            showTooltip: null,
        }
    },
    computed: {
        floorPlan() {
            const floors = {};

            this.beds.forEach(bed => {
                if (!floors[bed.floor]) {
                    floors[bed.floor] = {
                        number: bed.floor,
                        rooms: {}
                    };
                }

                if (!floors[bed.floor].rooms[bed.room]) {
                    floors[bed.floor].rooms[bed.room] = {
                        name: bed.room,
                        beds: []
                    };
                }

                floors[bed.floor].rooms[bed.room].beds.push(bed);
            });

            return Object.values(floors).map(floor => ({
                ...floor,
                rooms: Object.values(floor.rooms)
            }));
        }
    },
    methods: {
        onBedClick(bed) {
            this.selectedBed = {
                ...bed,
                admission: undefined
            };
            this.showEditModal = true;
        },
        submitUpdate() {
            this.$inertia.put(route('beds.update', this.selectedBed.id), this.selectedBed, {
                preserveScroll: true
            });
            this.showEditModal = null;
        },
        formatDate(date) {
            moment.locale('es');
            return moment(date).format('DD MMM YYYY, HH:mm');
        },
        daysHospitalized(date) {
            moment.locale('es');
            var dated = moment(date);
            return dated.fromNow();
        }
    }
}
</script>
