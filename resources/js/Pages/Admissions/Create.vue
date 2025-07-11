<template>
    <AppLayout title="Crear Ingresos">
        <!--BreadCrumb -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                <BreadCrumb :items="[
                    {
                        text: 'Ingresos',
                        route: route('admissions.index'),
                    },
                ]" />
            </h2>
        </template>

        <!--Errores -->
        <div v-if="errors.length" class="px-3 sm:px-0 mb-4 flex flex-col items-center">
            <p v-for="error in errors" :key="error" class="text-red-500 text-[13px] sm:text-sm mb-2 sm:mb-0">
                {{ error }}
            </p>
        </div>

        <!-- Formulario -->
       <div
            class="relative overflow-hidden border-gray-200 dark:border-gray-700/60 sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
            <form @submit.prevent="submit" class="max-w-md sm:max-w-xl mx-auto space-y-6">
                <!-- Cama -->
                <BedSelector :beds="beds" :errors="form.errors" :initialBedId="form.bed_id"
                    @update:bedId="updateBedId" />

                <!-- Paciente -->
                <div>
                    <label for="patient"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Paciente</label>
                    <PatientSelector @update:patient="form.patient_id = $event" :selectedPatientId="selectedPatient" />
                    <InputError :message="form.errors.patient_id" class="mt-2" />
                </div>

                <!-- Doctor -->
                <div>
                    <label for="doctor"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Doctor</label>
                    <UserSelector @update:user="form.doctor_id = $event" roles="doctor" />
                    <InputError :message="form.errors.doctor_id" class="mt-2" />
                </div>

                <!-- Dx ingreso -->
                <div>
                    <label for="admission_dx"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                        de ingreso
                        <span class="text-red-500">*</span></label>
                    <textarea id="admission_dx" rows="4" required v-model="form.admission_dx"
                        class="block w-full p-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Escribe el diagnóstico de ingreso..." />
                    <InputError :message="form.errors.admission_dx" class="mt-2" />
                </div>

                <!-- Observaciones -->
                <div>
                    <label for="comment"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                    <textarea id="comment" rows="4" v-model="form.comment"
                        class="block w-full p-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Escribe las observaciones..." />
                    <InputError :message="form.errors.comment" class="mt-2" />
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-2 sm:gap-4">
                    <Link :href="route('admissions.index')">
                    <SecondaryButton>Cancelar</SecondaryButton>
                    </Link>

                    <PrimaryButton @click="admissionBeingCreated = true" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing" :is-loading="form.processing">
                        Guardar
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <!-- Modal confirmación -->
        <ConfirmationModal :show="admissionBeingCreated != null" @close="admissionBeingCreated = null">
            <template #title>Crear Ingreso</template>

            <template #content>
                <p class="text-sm">
                    ¿Estás seguro de que deseas crear este ingreso?
                </p>
            </template>

            <template #footer>
                <SecondaryButton @click="admissionBeingCreated = null">Cancelar</SecondaryButton>

                <PrimaryButton class="ms-3" @click="submit">Crear</PrimaryButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import BedSelector from '@/Components/BedSelector.vue';
import UserSelector from '@/Components/UserSelector.vue';
import PatientSelector from '@/Components/PatientSelector.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { ref } from 'vue';
export default {
    components: {
        AppLayout,
        Link,
        BedSelector,
        InputError,
        UserSelector,
        PatientSelector,
        BreadCrumb,
        ConfirmationModal,
        PrimaryButton,
        SecondaryButton
    },
    props: {
        doctors: Array,
        beds: Object,
        patients: Object,
        errors: [Array, Object],
        selectedPatient: Number,
        selectedbed: Number,
    },
    data() {
        return {
            form: useForm({
                bed_id: this.selectedbed || null,
                patient_id: this.selectedPatient || null,
                doctor_id: null,
                admission_dx: null,
                comment: null,
            }),
            admissionBeingCreated: ref(null)
        }
    },
    methods: {
        updateBedId(bedId) {
            this.form.bed_id = bedId;
        },
        submit() {
            this.admissionBeingCreated = null;
            this.form.post(route('admissions.store'), {
                onError: (errors) => {
                    this.form.errors = errors;
                }
            })
        },

    }
}
</script>

