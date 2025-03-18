<template>
<AppLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-white leading-tight text-center">
            Crear Ficha Medicamentos
        </h2>
    </template>







        <!-- Formulario -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">
                   <!-- Diagnóstico -->
                  <!-- Selector -->

                        <label for="diet-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Admission ID
                        </label>
                        <AdmissionSelector :doesnt-have-medication-r = true @update:admission="form.admission_id = $event" :selected-admission-id="admission_id" />
                        <p v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</p>


                <!-- Diagnóstico -->
                <label for="diagnosis" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Diagnóstico
                </label>
                <textarea required id="diagnosis" rows="4" v-model="form.diagnosis" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escribe el diagnóstico..."></textarea>

                <!-- Contenedor para la dieta y el selector -->
                <div class="flex items-center space-x-4 mt-6">
                    <!-- Dieta -->

                    <!-- Selector -->
                    <div class="flex-1">
                        <label for="diet-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Dieta
                        </label>
                        <select id="diet-select" v-model="form.diet" required class="text-sm font-medium w-full text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100  p-2.5 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            <option v-for="diets in diet" :key="diets.id" :value="diets.name">
                                {{ diets.name }} - {{ diets.description }}
                            </option>
                        </select>

                    </div>
                    <div class="flex-1 mt-5">

                        <button @click="openCreateModal" class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium">Crear Dieta</span>
                        </button>

                    </div>
                </div>




                <!-- Botones -->
                <div class="flex justify-end mt-6 mb-2">
                    <Link :href="route('medicationRecords.index')" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cancelar
                    </Link>

                    <button type="submit" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
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
                            <input type="text" id="name" v-model="modalform.name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white" placeholder="Nombre de la dieta" />
                        </div>

                        <!-- Campo Descripción -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Descripción
                            </label>
                            <textarea id="description" v-model="modalform.description" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white" placeholder="Descripción de la dieta"></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </template>

        <!-- Footer del modal -->
        <template #footer>
            <button @click="submitModal" class="mr-6 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                Guardar
            </button>
            <button @click="isVisible = false" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                Cerrar
            </button>
        </template>
    </DialogModal>

</AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link
} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
export default {
    props: {
        errors: Array,
        admission: Array,
        diet: Array,

    },
    components: {
        AppLayout,
        Link,
        DialogModal,
        AdmissionSelector
    },
    data() {
        return {
            isVisible: false,
            form: {
                admission_id: this.admission.id,
                diagnosis: '',
                diet: '',



            },
            modalform: {
                description: '',
                name: '',
            }
        };
    },
    methods: {
        submit() {
            if (!this.form.admission_id) {
                this.error = 'Por favor, seleccione un ingreso.';
                return;
            }
            this.error = null;
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
