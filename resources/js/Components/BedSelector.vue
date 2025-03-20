<template>
    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Seleccionar cama</h3>
        <div class="grid grid-cols-3 gap-4">
            <!-- Selector de piso -->
            <div>
                <label for="bed_floor"
                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Piso</label>
                <select name="bed_floor" id="bed_floor" v-model="filtersForm.bed_floor" @change="updateRoomsAndBeds"
                    class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-200 dark:bg-gray-700 dark:text-white">
                    <option value="">Seleccione un piso</option>
                    <option v-for="floor in uniqueFloors" :key="floor" :value="floor">
                        Piso {{ floor }}
                    </option>
                </select>
            </div>

            <!-- Selector de sala -->
            <div>
                <label for="bed_room"
                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Sala</label>
                <select name="bed_room" id="bed_room" v-model="filtersForm.bed_room" @change="updateBeds"
                    class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-200 dark:bg-gray-700 dark:text-white">
                    <option value="">Seleccione una sala</option>
                    <option v-for="room in filteredRooms" :key="room" :value="room">
                        Sala {{ room }}
                    </option>
                </select>
            </div>

            <!-- Selector de cama -->
            <div>
                <label for="bed" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Cama</label>
                <select id="bed" v-model="selectedBedId" @change="updateSelectedBed(selectedBedId)"
                    class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-200 dark:bg-gray-700 dark:text-white">
                    <option value="">Seleccione una cama</option>
                    <option v-for="bed in filteredBeds" :key="bed.id" :value="bed.id">
                        Cama {{ bed.number }}
                    </option>
                </select>
                <InputError :message="errors.bed_id" class="mt-2" />
            </div>
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
            if (filtersForm.value.bed_room) {
                return props.beds.filter(bed =>
                    bed.room === filtersForm.value.bed_room
                );
            }
            return props.beds;
        });

        // Métodos
        const updateRoomsAndBeds = () => {
            filtersForm.value.bed_room = null; // Reiniciar la sala seleccionada
            selectedBedId.value = null; // Reiniciar la cama seleccionada
        };

        const updateBeds = () => {
            selectedBedId.value = null; // Reiniciar la cama seleccionada
        };

        // Emitir el valor de la cama seleccionada
        const updateSelectedBed = (value) => {
            selectedBedId.value = value;
            emit('update:bedId', value);
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
        };
    },
};
</script>
