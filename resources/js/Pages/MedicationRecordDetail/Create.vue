<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Crear Detalle Medicamento
            </h2>
        </template>

          <!-- Mostrar errores -->
          <div class="mb-4 flex flex-col items-center">
            <div class="mb-4 text-red-500" v-for="error in errors" :key="error">
                {{ error }}
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <label for="drug" class="block hidden mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Ficha Medicamentos
                </label>
                <input required id="medication_record_id" type="text" v-model="form.medication_record_id"
                    class="bg-gray-50 border hidden border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe la dieta asignada..." />


             <!-- Contenedor para la Medicamento y el selector -->
             <div class="flex items-center space-x-4 mt-6">
                    <!-- Selector -->
                    <div class="flex-1">
                        <label for="drug-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Medicamento
                        </label>
                        <select id="drug-select" required v-model="form.drug"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option v-for="drugs in drug" :key="drugs.id" :value="drugs.description">
                            {{ drugs.name }} - {{ drugs.description }}
                        </option>
                    </select>

                    </div>
                    <div class="flex-1 mt-5">


                        <button  @click="openCreateModal"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Crear Medicamento</span>
                    </button>


                        </div>
                </div>

                  <!-- Contenedor para la Via y el selector -->

                    <!-- Selector -->
                    <div class="flex-1">
                        <label for="route-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Via
                        </label>
                        <select id="route-select" required v-model="form.route"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option v-for="routes in routeOptions" :key="routes.id" :value="routes.description">
                            {{ routes.name }} - {{ routes.description }}
                        </option>
                    </select>

                    </div>

                 <!-- Contenedor para la Dosis y el selector -->
                 <div class="flex items-center space-x-4 mt-6">
                    <!-- Dosis -->
                    <div class="flex-1">
                        <label for="dose" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Dosis
                        </label>
                        <input  id="dose" required type="text" v-model="form.dose"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Escribe la Dosis asignada..." />
                    </div>
                    <!-- Selector -->
                    <div class="flex-1">
                        <label for="dose-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Dosis
                        </label>
                        <select id="dose-select" required v-model="form.dose_metric"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option v-for="doses in dose" :key="doses.id" :value="doses.name">
                            {{ doses.name }} - {{ doses.description }}
                        </option>
                    </select>

                    </div>
                </div>

                <!-- Estudios Pendientes -->
                <label for="fc" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Frecuencia
                </label>
                <input  id="fc" rows="4" required v-model="form.fc"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe los estudios pendientes..."></input>

                <!-- Firma del Doctor -->
                <label for="interval_in_hours" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Intervalo en Horas
                </label>
                <input required id="interval_in_hours" type="text" v-model="form.interval_in_hours"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Intervalo en Horas..." />

                     <!-- Hora de Inicio -->
                <label for="start_time" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Hora de Inicio
                </label>
                <input required id="start_time" type="time" v-model="form.start_time"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Hora de Inicio..." />

                <!-- Botones -->
                <div class="flex justify-end mt-6 mb-2">
                    <Link :href="route('medicationRecords.show',id)"
                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Cancelar
                    </Link>

                    <button type="submit"
                        class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                        Guardar
                    </button>
                </div>
            </form>
        </div>

        <DialogModal :show="isVisible" @close="isVisible = false" class="">
    <!-- Header del modal -->
    <template #title>
        Crear Medicamentos
    </template>

    <!-- Contenido del modal -->
    <template #content>
        <div>
            <form>
                <div class="grid gap-4">
                    <!-- Campo Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre
                        </label>
                        <input type="text" id="name" v-model="modalform.name" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Nombre del Medicamento" />
                    </div>

                    <!-- Campo Descripción -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descripción
                        </label>
                        <textarea id="description" v-model="modalform.description" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Descripción del Medicamento"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </template>

    <!-- Footer del modal -->
    <template #footer>
        <button @click="submitModal"
            class="mr-6 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
            Guardar
        </button>
        <button @click="isVisible = false"
            class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
            Cerrar
        </button>
    </template>
</DialogModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
export default {
    props: {
        errors: Array,
        details: Array,
        medicationRecord: Object,
        id: Number,
        drug: Array,
        dose: Array,
        routeOptions: Array,
    },
    components: {
        AppLayout,
        Link,
        DialogModal,
    },
    data() {
        return {
            isVisible: false,
            modalform:{
                description: '',
                name: '',
            },
            form: {
                medication_record_id: this.id,
                drug: '',
                dose: '',
                route: '',
                fc: '',
                interval_in_hours: '',
                start_time: '',
                dose_metric: '',
            },
        };
    },
    methods: {
        submit() {
            // Enviar los datos, incluyendo admission_id seleccionado
            this.$inertia.post(route('medicationRecordDetails.store'), this.form);
        },
        openCreateModal() {
            this.isVisible = true;
        },
        submitModal() {

            this.$inertia.post(route('Drugs.store', { id: this.id }), this.modalform);
            this.isVisible = false;
            this.modalform = {
                name: '',
                description: '',
            };
        },
    },
};
</script>
