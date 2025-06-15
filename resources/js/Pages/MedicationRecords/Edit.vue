<template>
<AppLayout title="Editar Ficha Medicamentos ">
    <template #header>


        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                <BreadCrumb :items="[
                     {
                        text: 'Fichas de Medicamentos',
                        route: route('medicationRecords.index')

                    },
                ]" />
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

            <!-- Selector -->
            <div class="flex-1">
                <label for="diet-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Dieta <span class="text-red-500">*</span>
                </label>
                <select id="diet-select" v-model="form.diet" required class="text-sm font-medium w-full text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100  p-2.5 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <option v-for="diets in diet" :key="diets.id" :value="diets.description">
                        {{ diets.name }} - {{ diets.description }}
                    </option>
                </select>

            </div>

            <div class="flex justify-end mt-6 mb-2">
                <Link :href="route('medicationRecords.index')" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
               <SecondaryButton class="py-2.5 px-5 me-2 mb-2  ">
                  Cancelar
              </SecondaryButton>
                </Link>

                <PrimaryButton type="submit" >
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
    Link
} from '@inertiajs/vue3';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
export default {
    props: {

        medicationRecord: Object,
        errors: Object,
        diet: Array,
    },
    components: {
        AppLayout,
        Link,
        BreadCrumb,
        PrimaryButton
    },
    data() {
        return {
            form: {

                diet: this.medicationRecord.diet,

                doctor_sign: this.medicationRecord.doctor_sign,
            },
        };
    },
    methods: {
        submit() {
            this.$inertia.put(
                route('medicationRecords.update', this.medicationRecord.id),
                this.form
            );
        },
    },
};
</script>
