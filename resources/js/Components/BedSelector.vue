<template>
    <div
        class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
        <!-- Encabezado con estilo moderno -->
        <div class="px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-medium text-gray-900 dark:text-white flex items-center">
                <span
                    class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-800 text-indigo-600 dark:text-indigo-300 mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z" />
                    </svg>
                </span>
                Asignación de Cama
            </h3>
        </div>

        <div class="p-4">
            <!-- Selección secuencial en tarjetas -->
            <div class="flex flex-col md:flex-row md:space-x-3 space-y-3 md:space-y-0">
                <!-- Selector de piso -->
                <div class="flex-1 bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 transition-all duration-300"
                    :class="{ 'ring-2 ring-indigo-500/70': filtersForm.bed_floor }">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div
                                class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-800/70 text-indigo-600 dark:text-indigo-300 flex items-center justify-center mr-2">
                                <span class="text-xs font-medium">1</span>
                            </div>
                            <label for="bed_floor" class="text-sm font-medium text-gray-900 dark:text-white">
                                Piso
                            </label>
                        </div>
                        <button v-if="filtersForm.bed_floor" @click="clearFloorSelection" type="button"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="relative">
                        <div class="relative">
                            <select name="bed_floor" id="bed_floor" v-model="filtersForm.bed_floor"
                                @change="updateRoomsAndBeds"
                                class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6 appearance-none">
                                <option value="" class="bg-white dark:bg-gray-900">Seleccionar piso</option>
                                <option v-for="floor in uniqueFloors" :key="floor" :value="floor"
                                    class="bg-white dark:bg-gray-900">
                                    Piso {{ floor }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Selector de sala -->
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 transition-all duration-300" :class="{
                    'ring-2 ring-indigo-500/70': filtersForm.bed_room,
                    'opacity-50': !filtersForm.bed_floor
                }">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div
                                class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-800/70 text-indigo-600 dark:text-indigo-300 flex items-center justify-center mr-2">
                                <span class="text-xs font-medium">2</span>
                            </div>
                            <label for="bed_room" class="text-sm font-medium text-gray-900 dark:text-white">
                                Sala
                            </label>
                        </div>
                        <button v-if="filtersForm.bed_room" @click="clearRoomSelection" type="button"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="relative">
                        <div class="relative">
                            <select name="bed_room" id="bed_room" v-model="filtersForm.bed_room" @change="updateBeds"
                                class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6 appearance-none"
                                :disabled="!filtersForm.bed_floor">
                                <option value="" class="bg-white dark:bg-gray-900">Seleccionar sala</option>
                                <option v-for="room in filteredRooms" :key="room" :value="room"
                                    class="bg-white dark:bg-gray-900">
                                    Sala {{ room }}
                                </option>
                            </select>
                        </div>
                        <p v-if="filtersForm.bed_floor && !filtersForm.bed_room && filteredRooms.length === 0"
                            class="mt-2 text-sm text-amber-600 dark:text-amber-400">
                            No hay salas disponibles en este piso
                        </p>
                    </div>
                </div>

                <!-- Selector de cama -->
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 transition-all duration-300" :class="{
                    'ring-2 ring-indigo-500/70': selectedBedId,
                    'opacity-50': !filtersForm.bed_room
                }">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div
                                class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-800/70 text-indigo-600 dark:text-indigo-300 flex items-center justify-center mr-2">
                                <span class="text-xs font-medium">3</span>
                            </div>
                            <label for="bed" class="text-sm font-medium text-gray-900 dark:text-white">
                                Cama
                            </label>
                        </div>
                        <button v-if="selectedBedId" @click="clearBedSelection" type="button"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="relative">
                        <div class="relative">
                            <select id="bed" v-model="selectedBedId" @change="updateSelectedBed(selectedBedId)"
                                class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6 appearance-none"
                                :disabled="!filtersForm.bed_room || filteredBeds.length === 0">
                                <option value="" class="bg-white dark:bg-gray-900">Seleccionar cama</option>
                                <option v-for="bed in filteredBeds" :key="bed.id" :value="bed.id"
                                    class="bg-white dark:bg-gray-900">
                                    Cama {{ bed.number }}
                                </option>
                            </select>
                        </div>
                        <InputError :message="errors.bed_id" class="mt-2" />
                        <p v-if="filtersForm.bed_room && !selectedBedId && filteredBeds.length === 0 && !errors.bed_id"
                            class="mt-2 text-sm text-amber-600 dark:text-amber-400">
                            No hay camas disponibles en esta sala
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información de selección actual -->
            <transition name="fade" mode="out-in">
                <div v-if="selectedBedId"
                    class="mt-4 flex items-center justify-between bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-2.5 border border-indigo-100 dark:border-indigo-800/50">
                    <div class="flex items-center space-x-2">
                        <div class="p-1.5 bg-indigo-100 dark:bg-indigo-800 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600 dark:text-indigo-300"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-900 dark:text-white">Cama seleccionada</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Piso {{ filtersForm.bed_floor }}, Sala {{ filtersForm.bed_room }}, Cama {{
                                    filteredBeds.find(bed => bed.id === selectedBedId)?.number}}
                            </p>
                        </div>
                    </div>
                </div>
            </transition>
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

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
