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

                <!-- Dieta -->
                <label for="diet" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Dieta
                </label>
                <input required id="diet" type="text" v-model="form.diet"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe la dieta asignada..." />

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
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        errors: Array,
        admissions: Array,  // Recibir los admissions disponibles
    },
    components: {
        AppLayout,
        Link,
    },
    data() {
        return {
            form: {
                admission_id: '',  // Nuevo campo para seleccionar el admission_id
                diagnosis: '',
                diet: '',
                referrals: '',
                pending_studies: '',
                doctor_sign: '',
            },
        };
    },
    methods: {
        submit() {
            // Enviar los datos, incluyendo admission_id seleccionado
            this.$inertia.post(route('medicationRecords.store'), this.form);
        },
    },
};
</script>
