<template>
    <AppLayout title="Crear Ordenes Médicas">
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
                    <Link :href="route('medicalOrders.index')">
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
import { Link, useForm } from '@inertiajs/vue3';
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
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
        BreadCrumb,
        SecondaryButton,
        PrimaryButton
    },
    data() {
        return {
            form: useForm({
                admission_id: this.admission_id
            }),
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
            this.form.post(route('medicalOrders.store'));
        }
    }
}
</script>
