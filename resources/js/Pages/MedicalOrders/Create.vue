<template>
    <AppLayout title="Crear Órdenes Médicas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                <BreadCrumb
                    :items="[
                        // Condicionar el primer elemento (solo se muestra si hay admission_id)
                        ...(admission_id ? [{
                            formattedId: { id: admission_id, prefix: 'ING' },
                            route: route('admissions.show', admission_id)
                        }] : []),

                        // Segundo elemento (depende si hay admission_id o no)
                        {
                            text: 'Órdenes Médicas',
                            route: admission_id ?
                                route('medicalOrders.index', { admission_id: admission_id }) :
                                route('medicalOrders.index')
                        },

                        {
                            text: 'Crear'
                        },
                    ]" />
            </h2>
        </template>

        <div class="relative overflow-hidden  sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
            <form @submit.prevent="submit" class="max-w-3xl mx-auto">

                <AdmissionSelector @update:admission="form.admission_id = $event"
                    :selected-admission-id="admission_id" />

               <InputError :message="form.errors.admission_id" class="mt-2" />

                <!-- Botones -->
                <div class="flex justify-end mt-4 space-x-3">
                    <div v-if="admission_id" class="self-center">
                        <Link :href="route('medicalOrders.index', { admission_id: admission_id })"
                            class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                        Cancelar
                        </Link>
                    </div>

                    <div v-else class="self-center">
                        <SecondaryLink :href="route('medicalOrders.index')">
                            Cancelar
                        </SecondaryLink>
                    </div>
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }":is-loading="form.processing"
                        :disabled="form.processing">
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
        Link,
        useForm
    } from '@inertiajs/vue3';
    import AdmissionSelector from '@/Components/AdmissionSelector.vue';
    import BreadCrumb from '@/Components/BreadCrumb.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
   import {
    ref
} from "vue";
import InputError from '@/Components/InputError.vue';
import SecondaryLink from '@/Components/SecondaryLink.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
    export default {
        props: {
            admissions: Array,
            admission_id: {
            type: Number,
            default: null
        },
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
            PrimaryButton,
            InputError,
            SecondaryLink,
            ConfirmationModal
        },
        data() {
            return {
                form: useForm({
                    admission_id: this.admission_id || null,
                    has_admission_id: this.admission_id ? true : false
                }),
                errorMessage: this.error || null,

            }
        },
        methods: {
            submit() {

                if (!this.form.admission_id) {
                    this.form.errors.admission_id = 'Por favor, seleccione un ingreso.';
                    return;
                }
                this.form.post(route('medicalOrders.store'));
            }
        }
    }
</script>
