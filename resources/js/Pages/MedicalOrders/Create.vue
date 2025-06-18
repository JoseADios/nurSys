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

               <InputError :message="form.errors.admission_id" class="mt-2" />
                 </form>
                <!-- Botones -->
                <div class="flex justify-end mt-4 space-x-3 max-w-3xl mx-auto">
                    <Link :href="route('medicalOrders.index')">
                          <SecondaryButton class="py-2.5 px-5 me-2 mb-2  ">
                  Cancelar
              </SecondaryButton>
                    </Link>
                   <PrimaryButton @click="orderBeingCreated = true" class="py-2.5 px-5 me-2 mb-2  "  :class="{ 'opacity-25': form.processing }":is-loading="form.processing"  :disabled="form.processing" >
                    Guardar
                </PrimaryButton>
                </div>

        </div>
         <ConfirmationModal :show="orderBeingCreated != null" @close="orderBeingCreated = null">
            <template #title>
                Crear Órden Médica
            </template>

            <template #content>
                ¿Estás seguro de que deseas crear esta órden?
            </template>

            <template #footer>
                <SecondaryButton @click="orderBeingCreated = null">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ms-3" @click="submit()">
                    Crear
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
export default {
    props: {
        admissions: Array,
        admission_id: Number,
        errors: {
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
        ConfirmationModal,
        PrimaryButton,
        InputError
    },
    data() {
        return {
            form: useForm({
                admission_id: null,
            }),
            orderBeingCreated: ref(null),
            error: ref(null)
        }
    },
    methods: {
        submit() {

            this.orderBeingCreated =null;
            this.error = null;
            this.form.post(route('medicalOrders.store'),{
                onError: (errors) =>{
                    this.form.errors = errors;
                }
            });
        }
    }
}
</script>
