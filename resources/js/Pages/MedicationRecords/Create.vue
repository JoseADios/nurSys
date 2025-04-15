<template>
<AppLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-white leading-tight text-center">
            <BreadCrumb :items="[
                    {
                        text: 'Fichas de Medicamentos',
                        route: route('medicationRecords.index')

                    },

                    {
                        text: 'Crear'
                    },
                ]" />
        </h2>
    </template>

    <!-- Formulario -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <form @submit.prevent="submit" class="max-w-xl mx-auto">
            <!-- Diagnóstico -->
            <!-- Selector -->

            <label for="diet-select" class="block mb-2 mt-4 text-sm font-medium text-gray-900 dark:text-white">
                Ingreso
            </label>
            <AdmissionSelector :doesnt-have-medication-r=true @update:admission="form.admission_id = $event" :selected-admission-id="admission_id" />

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

</AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link
} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
export default {
    props: {
        errors: [Array, Object],
        diet: Array,
        admission_id: Number
    },
    components: {
        AppLayout,
        Link,
        DialogModal,
        AdmissionSelector,
        BreadCrumb
    },
    data() {
        return {
            isVisible: false,
            form: {
                admission_id: this.admission_id,
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
