<template>
    <AppLayout title="Crear Ingresos>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                <BreadCrumb :items="[
                     {
                        text: 'Ingresos',
                        route: route('admissions.index')

                    },
                ]" />
            </h2>
        </template>

        <!-- Mostrar errores -->
        <div v-if="errors.length > 0" class="mb-4 flex flex-col items-center">
            <div class="mb-4 text-red-500" v-for="error in errors" :key="error">
                {{ error }}
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-xl mx-auto">

                <BedSelector :beds="beds" :errors="form.errors" :initialBedId="form.bed_id"
                    @update:bedId="updateBedId" />

                <label for="patient"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Paciente</label>
                <PatientSelector @update:patient="form.patient_id = $event" :selectedPatientId="selectedPatient"/>
                <InputError :message="form.errors.patient_id" class="mt-2" />

                <label for="doctor"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Doctor</label>
                <UserSelector @update:user="form.doctor_id = $event" roles="doctor" />
                <InputError :message="form.errors.doctor_id" class="mt-2" />

                <label for="admission_dx"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico de ingreso <span class="text-red-500">*</span></label>
                <textarea required id="admission_dx" rows="4" v-model="form.admission_dx"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico de ingreso..."></textarea>
                <InputError :message="form.errors.admission_dx" class="mt-2" />

                <label for="comment"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                <textarea id="comment" rows="4" v-model="form.comment"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe las observaciones..."></textarea>
                <InputError :message="form.errors.comment" class="mt-2" />

                <div class="flex justify-end mt-6 mb-2 gap-2">
                    <Link :href="route('admissions.index')" >
                     <SecondaryButton         >
                Cancelar</SecondaryButton>
                    </Link>

                    <PrimaryButton type="submit"  :class="{ 'opacity-25': form.processing }"  :is-loading="form.processing" :disabled="form.processing"
                        >Guardar</PrimaryButton>
                </div>
            </form>
        </div>
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
export default {
    components: {
        AppLayout,
        Link,
        BedSelector,
        InputError,
        UserSelector,
        PatientSelector,
        BreadCrumb,
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
            })
        }
    },
    methods: {
        updateBedId(bedId) {
            this.form.bed_id = bedId;
        },
        submit() {
            this.form.post(route('admissions.store'), {
                onError: (errors) => {
                    this.form.errors = errors;
                }
            })
        }
    }
}
</script>
