<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Distribución de Camas
            </h2>
        </template>

        <div class="flex items-center justify-between mb-4">
            <div class="ml-4 mt-2 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">
                <div class="ml-2 inline-flex items-center">
                    Camas
                </div>
            </div>

            <!-- Botón para mostrar/ocultar leyenda en móviles -->
            <button @click="showLegend = !showLegend"
                class="md:hidden mr-4 p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                aria-label="Mostrar/ocultar leyenda">
                <InformationCircleIcon class="w-5 h-5" />
            </button>
        </div>

        <!-- Leyenda de colores - Visible siempre en desktop, condicional en móvil -->
        <div :class="{ 'hidden': !showLegend && isMobile, 'block': showLegend || !isMobile }"
            class="mx-4 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200/60 dark:border-gray-700/60 backdrop-blur-sm">
            <div class="flex flex-wrap gap-x-4 gap-y-2 items-center">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Estado de camas:</h4>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-emerald-400/90 dark:bg-emerald-500/90 mr-1.5"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Disponible</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-orange-400/90 dark:bg-orange-500/90 mr-1.5"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Ocupada</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-yellow-400/90 dark:bg-yellow-500/90 mr-1.5"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Limpieza</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-red-500/90 dark:bg-red-600/90 mr-1.5"></div>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Fuera de servicio</span>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-2 sm:px-4 py-2 sm:py-6">
            <!-- Selector de pisos -->
            <div
                class="mb-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200/60 dark:border-gray-700/60 backdrop-blur-sm p-4 flex flex-col sm:flex-row items-center justify-between">
                <div class="flex items-center mb-4 sm:mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400 mr-2"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Filtrar por piso</h3>
                </div>
                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <select v-model="selectedFloor"
                        class="form-select rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full sm:w-auto">
                        <option value="all">Todos los pisos</option>
                        <option v-for="floor in availableFloors" :key="floor.number" :value="floor.number">
                            {{ floor.label }}
                        </option>
                    </select>
                </div>
            </div>

            <div v-for="floor in filteredFloorPlan" :key="floor.number" class="mb-6 sm:mb-8">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white mb-3 sm:mb-4 text-center">Piso {{
                    floor.number }}
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 md:gap-6 justify-center">
                    <div v-for="(room, index) in floor.rooms" :key="index"
                        :class="[
                            'bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700',
                        {
                            'p-3': room.beds.length === 1, // Padding más pequeño para salas con 1 cama
                            'p-4': room.beds.length === 2, // Padding mediano para salas con 2 camas
                            'p-5': room.beds.length === 3, // Padding grande para salas con 3 camas
                            'p-6': room.beds.length > 3,   // Padding extra grande para salas con más de 3 camas
                            'col-span-1': room.beds.length === 1, // Salas con 1 cama ocupan 1 columna
                            'col-span-2': room.beds.length > 1,   // Salas con más de 1 cama ocupan toda la fila en móvil
                            'sm:col-span-2': room.beds.length === 2, // En tablets, salas con 2 camas ocupan 2 columnas
                            'sm:col-span-3': room.beds.length === 3 || room.beds.length > 3, // En tablets, salas con 3+ camas ocupan 3 columnas
                            'md:col-span-3': room.beds.length === 3, // En desktop, salas con 3 camas ocupan 3 columnas
                            'md:col-span-4': room.beds.length > 3    // En desktop, salas con más de 3 camas ocupan 4 columnas
                        }
                    ]">
                        <h4
                            class="text-sm sm:text-lg font-semibold text-gray-800 dark:text-white mb-2 sm:mb-3 text-center">
                            Sala {{ room.name }}
                        </h4>
                        <!-- Ajuste de grid para adaptarse al número de camas -->
                        <div :class="[
                            'grid justify-center',
                            {
                                'grid-cols-1': room.beds.length === 1,
                                'grid-cols-2 gap-2': room.beds.length === 2,
                                'grid-cols-3 gap-2': room.beds.length === 3,
                                'grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 sm:gap-3': room.beds.length > 3
                            }
                        ]">
                            <div v-for="(bed, index) in room.beds" :key="index" class="flex justify-center">
                                <div :class="[
                                    'mx-auto rounded-lg transition-all duration-300 hover:scale-105 flex flex-col items-center w-[90px] h-32',
                                    {
                                        'bg-orange-400/90 dark:bg-orange-500/90': bed.admission,
                                        'bg-yellow-400/90 dark:bg-yellow-500/90': bed.status === 'cleaning',
                                        'bg-red-500/90 dark:bg-red-600/90': bed.status === 'out_of_service',
                                        'bg-emerald-400/90 dark:bg-emerald-500/90': bed.status === 'available'
                                    }
                                ]" @mouseenter="handleTooltip(bed.id)" @mouseleave="handleTooltip(null)">

                                    <!-- Header con número fijo -->
                                    <div
                                        class="top-0 w-full h-6 bg-gray-600 dark:bg-gray-700 rounded-t-lg flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">{{ bed.number }}</span>
                                    </div>

                                    <!-- Tooltip estilo elegante alternativo para desktop -->
                                    <div v-if="showTooltip === bed.id && bed.admission"
                                        :ref="el => tooltipRefs[bed.id] = el" :class="{
                                            'top-auto bottom-full mb-3': shouldShowAbove(bed.id),
                                            'top-full mt-3': !shouldShowAbove(bed.id)
                                        }"
                                        class="hidden lg:block absolute z-50 w-60 text-sm bg-white/95 dark:bg-gray-800/95 rounded-md border border-gray-200/60 dark:border-gray-700/60 backdrop-blur-sm left-1/2 -translate-x-1/2 overflow-hidden transform origin-top scale-100 transition-all duration-200"
                                        @mouseenter="handleTooltip(bed.id)" @mouseleave="handleTooltip(null)">
                                        <!-- Barra superior de color -->
                                        <div
                                            class="h-1.5 bg-gradient-to-r from-blue-400 to-emerald-400 dark:from-blue-500 dark:to-emerald-500">
                                        </div>

                                        <!-- Encabezado con diseño minimalista -->
                                        <div
                                            class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                            <h3 class="font-bold text-gray-800 dark:text-gray-200">
                                                <FormatId :id="bed.admission.id" prefix="ING"></FormatId>
                                            </h3>
                                            <div
                                                class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-full text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                                {{ daysHospitalized(bed.admission.created_at) }}
                                            </div>
                                        </div>

                                        <!-- Contenido con nueva distribución -->
                                        <div class="px-5 py-4">
                                            <div class="grid grid-cols-1 gap-3">
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700 mr-3">
                                                        <UserIcon class="h-4 w-4 text-gray-600 dark:text-gray-400" />
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Paciente</p>
                                                        <p class="font-medium text-gray-900 dark:text-white">
                                                            {{ bed.admission.patient.first_name }} {{
                                                                bed.admission.patient.first_surname }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700 mr-3">
                                                        <StethoscopeIcon
                                                            class="h-4 w-4 text-gray-600 dark:text-gray-400" />
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Doctor</p>
                                                        <p class="font-medium text-gray-900 dark:text-white">
                                                            {{ bed.admission.doctor.name }} {{
                                                                bed.admission.doctor.last_name }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700 mr-3">
                                                        <DateIcon class="h-4 w-4 text-gray-600 dark:text-gray-400" />
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Fecha</p>
                                                        <p class="font-medium text-gray-900 dark:text-white">
                                                            {{ formatDate(bed.admission.created_at) }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700 mr-3">
                                                        <ClipBoardIcon
                                                            class="h-4 w-4 text-gray-600 dark:text-gray-400" />
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">Diagnóstico
                                                        </p>
                                                        <p class="font-medium text-gray-900 dark:text-white truncate">
                                                            {{ bed.admission.admission_dx }}
                                                        </p>
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
                                        <BanIcon v-else-if="bed.status === 'out_of_service'"
                                            class="w-10 h-10 text-white" />
                                        <Bed2Icon v-else class="w-8 h-8 text-white" />
                                    </div>

                                    <!-- Contenedor de botones fijo -->
                                    <div class="w-full p-1 space-y-1">
                                        <!-- Botón Ver Ingreso -->
                                        <div v-if="bed.admission" class="w-full">
                                            <Link
                                                :href="route('admissions.show', { id: bed.admission.id, bedsRoute: true })"
                                                class="w-full block text-center bg-gray-700 dark:bg-gray-700 text-white py-1 rounded-md text-xs hover:bg-gray-600 dark:hover:bg-gray-600 transition-colors">
                                            Ver Ingreso
                                            </Link>
                                        </div>

                                        <!-- Botones Editar y Crear -->
                                        <div v-if="!bed.admission"
                                            class="w-full space-y-1 flex justify-evenly items-end">

                                            <!-- crear ingreso -->
                                            <AccessGate :role="['receptionist', 'admin']">
                                                <Link :href="route('admissions.create', { bed_id: bed.id })"
                                                    class="text-center h-6 w-8 bg-gray-700 dark:bg-gray-700 hover:bg-blue-600 dark:hover:bg-blue-600 text-white rounded-md text-xs transition-colors flex items-center justify-center">
                                                <PlusIcon width="14" height="14" />
                                                </Link>
                                            </AccessGate>

                                            <AccessGate :permission="['bed.update']">
                                                <button @click="onBedClick(bed)"
                                                    class="h-6 w-8 bg-gray-600 dark:bg-gray-600 hover:bg-orange-500 dark:hover:bg-orange-500 text-white rounded-md text-xs transition-colors flex items-center justify-center">
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
import InformationCircleIcon from '@/Components/Icons/InformationCircleIcon.vue';

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
        BanIcon,
        InformationCircleIcon,
    },
    props: {
        beds: Object,
        floors: Object,
        rooms: Object,
        admissions: Object,
    },
    data() {
        return {
            showEditModal: null,
            selectedBed: null,
            showTooltip: null,
            showLegend: false,
            isMobile: window.innerWidth < 768,
            form: {
                status: '',
            },
            isDarkMode: localStorage.getItem('darkMode') === 'true' || false,
            selectedFloor: 'all', // 'all' significa mostrar todos los pisos
            tooltipRefs: {},
            tooltipTimeout: null,
        }
    },
    computed: {
        floorPlan() {
            const plan = [];

            // Verificar que beds existe y tiene la estructura correcta
            if (!this.beds || !Array.isArray(this.beds)) {
                return [];
            }

            // Agrupar las camas por piso y habitación
            this.beds.forEach(bed => {
                // Buscar el piso en el plan
                let floor = plan.find(f => f.number === bed.floor);
                if (!floor) {
                    floor = {
                        number: bed.floor,
                        rooms: []
                    };
                    plan.push(floor);
                }

                // Buscar la habitación en el piso
                let room = floor.rooms.find(r => r.name === bed.room);
                if (!room) {
                    room = {
                        name: bed.room,
                        beds: []
                    };
                    floor.rooms.push(room);
                }

                // Agregar la cama a la habitación (ya viene con su admisión relacionada)
                room.beds.push(bed);
            });

            // Ordenar los pisos por número
            plan.sort((a, b) => a.number - b.number);

            // Ordenar las habitaciones por nombre
            plan.forEach(floor => {
                floor.rooms.sort((a, b) => {
                    // Extraer el número de la habitación para ordenar numéricamente
                    const numA = parseInt(a.name.replace(/\D/g, '')) || 0;
                    const numB = parseInt(b.name.replace(/\D/g, '')) || 0;
                    return numA - numB;
                });
            });

            return plan;
        },
        availableFloors() {
            // Asegurarse de que floorPlan existe antes de intentar mapearlo
            if (!this.floorPlan || !Array.isArray(this.floorPlan)) {
                return [];
            }
            return this.floorPlan.map(floor => ({
                number: floor.number,
                label: `Piso ${floor.number}`
            }));
        },
        filteredFloorPlan() {
            if (!this.floorPlan) {
                return [];
            }
            if (this.selectedFloor === 'all') {
                return this.floorPlan;
            } else {
                return this.floorPlan.filter(floor => floor.number === parseInt(this.selectedFloor));
            }
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
        },
        toggleDarkMode() {
            this.isDarkMode = !this.isDarkMode;
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', this.isDarkMode);
        },
        shouldShowAbove(bedId) {
            const tooltip = this.tooltipRefs[bedId];
            if (!tooltip) return false;

            const tooltipRect = tooltip.getBoundingClientRect();
            const bedElement = tooltip.parentElement;
            const bedRect = bedElement.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            const padding = 20; // Espacio de padding para mejor visibilidad

            // Espacio disponible debajo del elemento
            const spaceBelow = windowHeight - bedRect.bottom;
            // Altura total del tooltip
            const tooltipHeight = tooltipRect.height;

            // Solo mostrar arriba si no hay suficiente espacio abajo
            return spaceBelow < (tooltipHeight + padding);
        },
        handleTooltip(bedId) {
            if (this.tooltipTimeout) {
                clearTimeout(this.tooltipTimeout);
            }

            if (this.showTooltip === bedId) {
                this.showTooltip = null;
                this.clearTooltipRef(bedId);
            } else {
                if (this.showTooltip !== null) {
                    this.clearTooltipRef(this.showTooltip);
                }
                this.showTooltip = bedId;
            }
        },
        clearTooltipRef(bedId) {
            if (this.tooltipRefs[bedId]) {
                delete this.tooltipRefs[bedId];
            }
        }
    },
    beforeUnmount() {
        // Limpiar todas las referencias al desmontar
        this.tooltipRefs = {};
        if (this.tooltipTimeout) {
            clearTimeout(this.tooltipTimeout);
        }
    },
    mounted() {
        // Check for dark mode preference
        const darkModePreference = localStorage.getItem('darkMode') === 'true';
        const prefersDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

        // Set dark mode based on saved preference or system preference
        this.isDarkMode = darkModePreference || (prefersDarkMode && localStorage.getItem('darkMode') === null);

        // Apply dark mode if needed
        if (this.isDarkMode) {
            document.documentElement.classList.add('dark');
        }

        // Handle window resize for mobile detection
        window.addEventListener('resize', () => {
            this.isMobile = window.innerWidth < 768;
        });
    }
}
</script>
