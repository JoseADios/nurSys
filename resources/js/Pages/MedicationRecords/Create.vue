<template>
<AppLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
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
            <InputError :message="error" class="mt-2" />

            <!-- Contenedor para la dieta y el selector -->
            <div class="flex items-center space-x-4 mt-6">
                <!-- Dieta -->

                <!-- Selector -->
                <div class="flex-1">
                    <label for="diet-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Dieta  <span class="text-red-500">*</span>
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
                <Link :href="route('medicationRecords.index')" >
              <SecondaryButton class="py-2.5 px-5 me-2 mb-2  ">
                  Cancelar
              </SecondaryButton>
                </Link>

                <PrimaryButton type="submit" class="py-2.5 px-5 me-2 mb-2  "  :class="{ 'opacity-25': form.processing }":is-loading="form.processing"  :disabled="form.processing" >
                    Guardar
                </PrimaryButton>
            </div>
        </form>
    </div>

</AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link, useForm
} from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';
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
        BreadCrumb,
        PrimaryButton,
        SecondaryButton,
        InputError
    },
    data() {
        return {
            isVisible: false,
            form:  useForm({
                admission_id: this.admission_id,

                diet: '',
            }),
            error: ref(null)
        };
    },
    methods: {
        submit() {
             console.log(this.form);
            if (!this.form.admission_id ) {
                this.error = 'Por favor, seleccione un ingreso.';
                return;
            }

            this.error = null;
             this.form.post(route('medicationRecords.store'));
        },

    },

};
</script>
