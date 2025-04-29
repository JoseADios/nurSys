<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <BreadCrumb :items="[
                    // Condicionar el primer elemento (solo se muestra si hay admission_id)
                    ...(admission_id ? [{
                        formattedId: { id: admission_id, prefix: 'ING' },
                        route: route('admissions.show', admission_id)
                    }] : []),

                    // Segundo elemento (depende si hay admission_id o no)
                    {
                        text: 'Registros de enfermería',
                        route: admission_id
                            ? route('nurseRecords.index', { admission_id: admission_id })
                            : route('nurseRecords.index')
                    },

                    {
                        text: 'Crear'
                    },
                ]" />
            </h2>
        </template>


        <div
            class="relative overflow-hidden border-gray-200 dark:border-gray-700/60 sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
            <form @submit.prevent="submit" class="max-w-3xl mx-auto">

                <AdmissionSelector @update:admission="form.admission_id = $event"
                    :selected-admission-id="admission_id" />

                <!-- Mostrar mensaje de error si no se selecciona ninguna admisión -->
                <p v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</p>

                <!-- Botones -->
                <div class="flex justify-end mt-4 space-x-3">
                    <div v-if="admission_id" class="self-center">
                        <Link :href="route('nurseRecords.index', { admission_id: admission_id })"
                            class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                        Cancelar
                        </Link>
                    </div>

                    <div v-else class="self-center">
                        <Link :href="route('nurseRecords.index')"
                            class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                        Cancelar
                        </Link>
                    </div>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 transition">
                        Aceptar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
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
        FormatId,
        BreadCrumb
    },
    data() {
        return {
            form: {
                admission_id: this.admission_id || null,
                has_admission_id: this.admission_id ? true : false
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
