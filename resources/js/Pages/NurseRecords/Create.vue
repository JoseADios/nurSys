<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-lg text-white leading-tight text-center">
                Nuevo Registro de Enfermería
            </h2>
        </template>

        <div class="ml-4 my-2 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">
            <div v-if="admission_id" class="inline-flex items-center">
                <Link :href="route('admissions.show', admission_id)"
                    class="inline-flex items-center hover:text-blue-600  dark:hover:text-white">
                <FormatId :id="admission_id" prefix="ING"></FormatId>
                </Link>
                <ChevronRightIcon class="size-3 text-gray-400 mx-1" />
            </div>

             <div v-if="admission_id">
                 <Link :href="route('nurseRecords.index', { admission_id: admission_id })"
                     class="inline-flex items-center hover:text-blue-600 dark:hover:text-white">
                 Registros de enfermería
                 </Link>
             </div>
             <div v-if="!admission_id">
                 <Link :href="route('nurseRecords.index')"
                     class="inline-flex items-center hover:text-blue-600 dark:hover:text-white">
                 Registros de enfermería
                 </Link>
             </div>
            <ChevronRightIcon class="size-3 text-gray-400 mx-1" />
            <div class="ml-2 inline-flex items-center">
                Crear
            </div>
        </div>

        <div class="relative overflow-hidden border-gray-200 dark:border-gray-700/60 sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
            <form @submit.prevent="submit" class="max-w-3xl mx-auto">

                <AdmissionSelector @update:admission="form.admission_id = $event"
                    :selected-admission-id="admission_id" />

                <!-- Mostrar mensaje de error si no se selecciona ninguna admisión -->
                <p v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</p>

                <!-- Botones -->
                <div class="flex justify-end mt-4 space-x-3">
                    <Link :href="route('nurseRecords.index')"
                        class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                    Cancelar
                    </Link>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800 transition">
                        Aceptar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import FormatId from '@/Components/FormatId.vue';
import ChevronRightIcon from '@/Components/Icons/ChevronRightIcon.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        admission_id: {
            type: Number,
            default: null
        }
    },
    components: {
        AppLayout,
        Link,
        AdmissionSelector,
        ChevronRightIcon,
        FormatId
    },
    data() {
        return {
            form: {
                admission_id: this.admission_id || null
            },
            error: null
        };
    },
    methods: {
        submit() {
            if (!this.form.admission_id) {
                this.error = 'Por favor, seleccione un ingreso.';
                return;
            }
            this.error = null;
            this.$inertia.post(route('nurseRecords.store'), this.form);
        }
    }
};
</script>
