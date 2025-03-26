<template>
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Seleccionar cama</h3>

        <!-- Grid responsive: cambia de 3 columnas a 1 columna en dispositivos pequeños -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Selector de piso -->
            <div class="mb-2 md:mb-0">
                <label for="bed_floor"
                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Piso</label>
                <div class="flex gap-2">
                    <select name="bed_floor" id="bed_floor" v-model="filtersForm.bed_floor" @change="updateRoomsAndBeds"
                        class="w-full p-2.5 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">Seleccione un piso</option>
                        <option v-for="floor in uniqueFloors" :key="floor" :value="floor">
                            Piso {{ floor }}
                        </option>
                    </select>
                    <button v-if="filtersForm.bed_floor" @click="clearFloorSelection" type="button"
                        class="p-2.5 text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 rounded-lg dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-800 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Selector de sala -->
            <div class="mb-2 md:mb-0">
                <label for="bed_room"
                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Sala</label>
                <div class="flex gap-2">
                    <select name="bed_room" id="bed_room" v-model="filtersForm.bed_room" @change="updateBeds"
                        class="w-full p-2.5 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">Seleccione una sala</option>
                        <option v-for="room in filteredRooms" :key="room" :value="room">
                            Sala {{ room }}
                        </option>
                    </select>
                    <button v-if="filtersForm.bed_room" @click="clearRoomSelection" type="button"
                        class="p-2.5 text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 rounded-lg dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-800 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Selector de cama -->
            <div>
                <label for="bed" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Cama</label>
                <div class="flex gap-2">
                    <select id="bed" v-model="selectedBedId" @change="updateSelectedBed(selectedBedId)"
                        class="w-full p-2.5 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">Seleccione una cama</option>
                        <option v-for="bed in filteredBeds" :key="bed.id" :value="bed.id">
                            Cama {{ bed.number }}
                        </option>
                    </select>
                    <button v-if="selectedBedId" @click="clearBedSelection" type="button"
                        class="p-2.5 text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 rounded-lg dark:bg-gray-900 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-800 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <InputError :message="errors.bed_id" class="mt-2" />
            </div>
        </div>

        <!-- Botón para limpiar todo - Ajustado para móviles -->
        <div class="mt-4 flex justify-center md:justify-end">
            <button v-if="filtersForm.bed_floor || filtersForm.bed_room || selectedBedId" @click="clearAllSelections"
                type="button"
                class="w-full md:w-auto px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-700 dark:hover:bg-red-800">
                Limpiar selección
            </button>
        </div>
    </div>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue';
import InputError from '@/Components/InputError.vue';

export default {
    components: {
        InputError,
    },
    props: {
        beds: {
            type: Array,
            required: true,
        },
        errors: {
            type: Object,
            default: () => ({}),
        },
        initialBedId: Number
    },
    watch: {
        beds: {
            immediate: true,
            handler(beds) {
                beds.sort((a, b) => a.id - b.id);
            }
        }
    },
    emits: ['update:bedId'],
    setup(props, { emit }) {
        // Datos reactivos
        const filtersForm = ref({
            bed_floor: null,
            bed_room: null,
        });
        const selectedBedId = ref(props.initialBedId);

        // Propiedades computadas
        const uniqueFloors = computed(() => {
            return [...new Set(props.beds.map(bed => bed.floor))];
        });

        const filteredRooms = computed(() => {
            if (filtersForm.value.bed_floor) {
                return [...new Set(props.beds
                    .filter(bed => bed.floor === filtersForm.value.bed_floor)
                    .map(bed => bed.room)
                )].sort((a, b) => a - b);
            }
            return [];
        });

        const filteredBeds = computed(() => {
            if (filtersForm.value.bed_floor && filtersForm.value.bed_room) {
                return props.beds.filter(bed =>
                    bed.floor === filtersForm.value.bed_floor &&
                    bed.room === filtersForm.value.bed_room
                );
            }
            return []; // Devuelve una lista vacía si no se ha seleccionado piso o sala
        });

        // Métodos
        const updateRoomsAndBeds = () => {
            filtersForm.value.bed_room = null; // Reiniciar la sala seleccionada
            selectedBedId.value = null; // Reiniciar la cama seleccionada
            emit('update:bedId', null);
        };

        const updateBeds = () => {
            selectedBedId.value = null; // Reiniciar la cama seleccionada
            emit('update:bedId', null);
        };

        // Emitir el valor de la cama seleccionada
        const updateSelectedBed = (value) => {
            selectedBedId.value = value;
            emit('update:bedId', value);
        };

        // Limpiar selección de piso
        const clearFloorSelection = () => {
            filtersForm.value.bed_floor = null;
            filtersForm.value.bed_room = null;
            selectedBedId.value = null;
            emit('update:bedId', null);
        };

        // Limpiar selección de sala
        const clearRoomSelection = () => {
            filtersForm.value.bed_room = null;
            selectedBedId.value = null;
            emit('update:bedId', null);
        };

        // Limpiar selección de cama
        const clearBedSelection = () => {
            selectedBedId.value = null;
            emit('update:bedId', null);
        };

        // Limpiar todas las selecciones
        const clearAllSelections = () => {
            filtersForm.value.bed_floor = null;
            filtersForm.value.bed_room = null;
            selectedBedId.value = null;
            emit('update:bedId', null);
        };

        // Determinar el piso y la sala de la cama seleccionada
        const setInitialFilters = () => {
            if (props.initialBedId) {
                const selectedBed = props.beds.find(bed => bed.id === props.initialBedId);
                if (selectedBed) {
                    filtersForm.value.bed_floor = selectedBed.floor;
                    filtersForm.value.bed_room = selectedBed.room;
                }
            }
        };

        // Ejecutar al montar el componente
        onMounted(() => {
            setInitialFilters();
        });

        // Observar cambios en el ID de la cama seleccionada
        watch(() => props.initialBedId, () => {
            setInitialFilters();
        });

        return {
            filtersForm,
            selectedBedId,
            uniqueFloors,
            filteredRooms,
            filteredBeds,
            updateRoomsAndBeds,
            updateBeds,
            updateSelectedBed,
            clearFloorSelection,
            clearRoomSelection,
            clearBedSelection,
            clearAllSelections,
        };
    },
};
</script>
