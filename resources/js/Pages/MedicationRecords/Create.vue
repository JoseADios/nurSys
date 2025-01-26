<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Crear Ficha Medicamentos
            </h2>
        </template>

        <div class="ml-10 mt-4 lg:mx-10">
            <Link :href="route('medicationRecords.index')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                Volver
            </Link>
        </div>

        <!-- Mostrar errores -->
        <div class="mb-4 flex flex-col items-center">
            <div class="mb-4 text-red-500" v-for="error in errors" :key="error">
                {{ error }}
            </div>
        </div>



        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <!-- Select para elegir un admission_id -->
                <label for="admission_id" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Selecciona Admisión
                </label>
                <select id="admission_id" v-model="form.admission_id" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option v-for="admission in admissions" :key="admission.id" :value="admission.id">
                        #{{ admission.id }} - {{ admission.patient_id }}
                    </option>
                </select>

                <!-- Diagnóstico -->
                <label for="diagnosis" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Diagnóstico
                </label>
                <textarea required id="diagnosis" rows="4" v-model="form.diagnosis"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico..."></textarea>

                            <!-- Contenedor para la dieta y el selector -->
                <div class="flex items-center space-x-4 mt-6">
                    <!-- Dieta -->

                    <!-- Selector -->
                    <div class="flex-1">
                        <label for="diet-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Dieta
                        </label>
                        <select id="diet-select" v-model="form.diet" required
                        class="text-sm font-medium w-full text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100  p-2.5 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        <option v-for="diets in diet" :key="diets.id" :value="diets.name">
                            {{ diets.name }} - {{ diets.description }}
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
                        <span class="font-medium">Crear Dieta</span>
                    </button>


                        </div>
                </div>


                <!-- Referencias -->
                <label for="referrals" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Referencias
                </label>
                <input required id="referrals" type="text" v-model="form.referrals"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe las referencias..." />

                <!-- Estudios Pendientes -->
                <label for="pending_studies" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Estudios Pendientes
                </label>
                <textarea id="pending_studies" rows="4" v-model="form.pending_studies"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe los estudios pendientes..."></textarea>

                <!-- Firma del Doctor -->
                <label for="doctor_sign" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Firma del Doctor
                </label>
                <input required id="doctor_sign" type="text" v-model="form.doctor_sign"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Firma del doctor..." />

                <!-- Botones -->
                <div class="flex justify-end mt-6 mb-2">
                    <Link :href="route('medicationRecords.index')"
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
        Crear Dieta
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
                            placeholder="Nombre de la dieta" />
                    </div>

                    <!-- Campo Descripción -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descripción
                        </label>
                        <textarea id="description" v-model="modalform.description" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Descripción de la dieta"></textarea>
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
        admissions: Array,  // Recibir los admissions disponibles
        diet: Array,
    },
    components: {
        AppLayout,
        Link,
        DialogModal,
    },
    data() {
        return {
            isVisible: false,
            form: {
                admission_id: '',  // Nuevo campo para seleccionar el admission_id
                diagnosis: '',
                diet: '',
                referrals: '',
                pending_studies: '',
                doctor_sign: '',
            },
            modalform:{
                description: '',
                name: '',
            }
        };
    },
    methods: {
        submit() {

            this.$inertia.post(route('medicationRecords.store'), this.form);
        },
        openCreateModal() {
            this.isVisible = true;
        },
        submitModal() {

            this.$inertia.post(route('Diet.store'), this.modalform);
            this.isVisible = false;
            this.form = {
                name: '',
                description: '',
            };
        },
    },

};
</script>
