<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Distribución de Camas
            </h2>
        </template>

        <div class="container mx-auto px-4 py-6">
            <div v-for="floor in floorPlan" :key="floor.number" class="mb-8">
                <h3 class="text-2xl font-bold text-white mb-4 text-center">Piso {{ floor.number }}</h3>
                <div class="flex flex-wrap justify-center gap-6">
                    <div v-for="room in floor.rooms" :key="room.name"
                        class="bg-gray-800 rounded-lg p-6 shadow-lg w-full max-w-md">
                        <h4 class="text-xl font-semibold text-white mb-4 text-center">
                            Sala {{ room.name }}
                        </h4>
                        <div class="grid grid-cols-4 gap-4 justify-center">
                            <div v-for="bed in room.beds" :key="bed.id" class="relative">
                                <div class="relative w-20 h-32 rounded-lg transition-all duration-300 hover:scale-110 flex flex-col items-center"
                                    :class="{
                                        'bg-orange-500': bed.admission_id,
                                        'bg-red-600': bed.out_of_service,
                                        'bg-green-500': !bed.out_of_service
                                    }">
                                    <!-- Header con número fijo -->
                                    <div class="absolute top-0 w-full h-6 bg-gray-700 rounded-t-lg flex items-center justify-center">
                                        <span class="text-white text-xs">{{ bed.number }}</span>
                                    </div>

                                    <!-- Icono de cama centrado -->
                                    <div class="flex-1 flex items-center justify-center mt-5">
                                        <svg viewBox="0 0 24 14" class="w-12 h-12 text-white">
                                            <path fill="currentColor"
                                                d="M3 11h18v3H3zm1.5-3h15c.83 0 1.5.67 1.5 1.5V11H3V9.5C3 8.67 3.67 8 4.5 8zm2-2h10c.83 0 1.5.67 1.5 1.5V8H5v-.5C5 6.67 5.67 6 6.5 6z" />
                                        </svg>
                                    </div>

                                    <!-- Contenedor de botones fijo -->
                                    <div class="w-full p-1 space-y-1">
                                        <!-- Botón Ver Ingreso -->
                                        <div v-if="bed.admission_id" class="w-full">
                                            <Link :href="route('admissions.show', { id: bed.admission_id, bedsRoute: true })"
                                                class="w-full block text-center bg-gray-700 text-white py-1 rounded-md text-xs hover:bg-gray-600 transition-colors">
                                                Ver Ingreso
                                            </Link>
                                        </div>

                                        <!-- Botones Editar y Crear -->
                                        <div v-if="!bed.admission_id" class="w-full space-y-1 flex justify-evenly items-end">

                                            <Link v-if="!bed.out_of_service" :href="route('admissions.create', {bed_id: bed.id})"
                                                class="text-center h-6 w-8 bg-gray-700 hover:bg-blue-600 text-white rounded-md text-xs transition-colors flex items-center justify-center">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" class="">
                                                    <path d="M12 5v14M5 12h14" />
                                                </svg>

                                            </Link>

                                            <button @click="onBedClick(bed)" class="h-6 w-8 bg-gray-600 hover:bg-orange-500 text-white rounded-md text-xs transition-colors flex items-center justify-center">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" class="">
                                                    <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                    <path d="M13.5 6.5l4 4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        <DialogModal :show="showEditModal != null" @close="showEditModal = null">
            <template #title>
                Cambiar estado de la cama
            </template>

            <template #content>
                <form @submit.prevent="submitUpdate">
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <input v-model="selectedBed.out_of_service" id="out_of_service" type="checkbox"
                                class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                :true-value="1" :false-value="0">
                            <label for="out_of_service"
                                class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Fuera de
                                servicio</label>
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
    </AppLayout>
</template>

<script>
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

export default {
    components: {
        AppLayout,
        Link,
        DialogModal,
        PrimaryButton,
        SecondaryButton,
    },
    props: {
        beds: Array
    },
    data() {
        return {
            showEditModal: ref(null),
            selectedBed: null,
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
            this.selectedBed = { ...bed };
            this.showEditModal = true;
        },
        submitUpdate() {
            this.$inertia.put(route('beds.update', this.selectedBed.id), this.selectedBed);
            this.showEditModal = null;
        }
    }
}
</script>
