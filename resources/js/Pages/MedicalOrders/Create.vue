<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">

                <BreadCrumb :items="[
                     {
                        text: 'Órdenes Médicas',
                        route: route('medicalOrders.index')

                    },
                ]" />
            </h2>
        </template>

        <div class="relative overflow-hidden shadow-lg sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
            <form @submit.prevent="submit" class="max-w-3xl mx-auto">

                <AdmissionSelector @update:admission="form.admission_id = $event" :selected-admission-id="admission_id" />

                <!-- Mostrar mensaje de error si no se selecciona ninguna admisión -->
                <p v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</p>

                <!-- Botones -->
                <div class="flex justify-end mt-4 space-x-3">
                    <Link :href="route('medicalOrders.index')"
                        class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                    Cancelar
                    </Link>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800 transition"
                        >
                        Aceptar
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
export default {
    props: {
        admissions: Array,
        admission_id: Number,
        error: {
            type: Object,
            default: null
        }
    },
    components: {
        AppLayout,
        Link,
        AdmissionSelector,
        BreadCrumb
    },
    data() {
        return {
            form: {
                admission_id: this.admission_id
            },
            errorMessage: this.error || null,
        }
    },
    methods: {
        submit() {
            if (!this.form.admission_id) {
                this.errorMessage = 'Por favor, seleccione un ingreso.';
                return;
            }
            this.errorMessage = null;
            this.$inertia.post(route('medicalOrders.store'), this.form);
        }
    }
}
</script>
