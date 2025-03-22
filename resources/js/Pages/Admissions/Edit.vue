<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Editar ingreso
            </h2>
        </template>

        <div class="ml-10 mt-4 lg:mx-10 flex justify-between">
            <Link :href="route('admissions.index')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-full">
            Volver
            </Link>

            <div class="flex">
                <div v-if="admission.discharged_date == null">
                    <button type="button" @click="discharge"
                        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-3 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">
                        Dar de Alta
                    </button>
                </div>
                <div v-if="admission.discharged_date != null">
                    <button type="button" @click="charge"
                        class="focus:outline-none text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-900">
                        Poner en progreso
                    </button>
                </div>
                <button type="button" v-if="admission.active" @click="admissionBeingDeleted = true"
                    class="inline-flex ml-2 items-center px-5 py-2.5 font-medium bg-gradient-to-r from-red-500 to-red-700 text-white text-sm rounded-lg hover:from-red-600 hover:to-red-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Eliminar
                </button>
                <button v-if="!admission.active" @click="restoreAdmission"
                    class="inline-flex items-center px-6 bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold rounded-lg hover:from-green-600 hover:to-green-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                    Restaurar
                </button>

            </div>
        </div>

        <!-- <div class="text-white">
            {{ beds }}
        </div> -->

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <BedSelector :beds="beds" :errors="form.errors" :initialBedId="form.bed_id"
                    @update:bedId="form.bed_id = $event" />

                <AccessGate :except-role="['nurse']">
                    <label for="patient"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Paciente</label>
                    <select required id="patient" v-model="form.patient_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                        <option :value="patient.id" v-for="patient in patients" :key="patient.id"
                            :selected="patient.id === admission.patient_id">
                            {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                        </option>
                    </select>
                    <InputError :message="form.errors.patient_id" class="mt-2" />

                    <label for="doctor"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Doctor</label>
                    <select required id="doctor" v-model="form.doctor_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                        <option :value="doctor.id" v-for="doctor in doctors" :key="doctor.id"
                            :selected="doctor.id === admission.doctor_id">
                            {{ doctor.name }} {{ doctor.last_name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.doctor_id" class="mt-2" />

                    <label for="admission_dx"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                        de
                        ingreso</label>
                    <textarea required id="admission_dx" rows="4" v-model="form.admission_dx"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Escribe el diagnóstico de ingreso..."></textarea>
                    <InputError :message="form.errors.admission_dx" class="mt-2" />

                    <label for="final_dx"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                        final</label>
                    <textarea id="final_dx" rows="4" v-model="form.final_dx"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Escribe el diagnóstico final..."></textarea>
                    <InputError :message="form.errors.final_dx" class="mt-2" />

                    <label for="comment"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                    <textarea id="comment" rows="4" v-model="form.comment"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Escribe las observaciones..."></textarea>
                    <InputError :message="form.errors.comment" class="mt-2" />
                </AccessGate>

                <div class="flex justify-end mt-6 mb-2">

                    <Link :href="route('admissions.index')"
                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cancelar
                    </Link>

                    <button type="submit"
                        class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Guardar</button>
                </div>
            </form>
        </div>
        <!-- modal para eliminar -->
        <ConfirmationModal :show="admissionBeingDeleted != null" @close="admissionBeingDeleted = null">
            <template #title>
                Eliminar Ingreso
            </template>

            <template #content>
                ¿Estás seguro de que deseas eliminar este ingreso?
            </template>

            <template #footer>
                <SecondaryButton @click="admissionBeingDeleted = null">
                    Cancelar
                </SecondaryButton>

                <DangerButton class="ms-3" @click="deleteAdmission">
                    Eliminar
                </DangerButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script>
import AccessGate from '@/Components/Access/AccessGate.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useGoBack } from '@/composables/useGoBack';
import InputError from '@/Components/InputError.vue';
import BedSelector from '@/Components/BedSelector.vue';
import { ref } from "vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

export default {
    props: {
        admission: Object,
        patients: [Array, Object],
        doctors: [Array, Object],
        beds: Array,
        previousUrl: String,
    },
    components: {
        AppLayout,
        Link,
        AccessGate,
        InputError,
        BedSelector,
        ConfirmationModal,
        DangerButton,
        SecondaryButton
    },
    data() {
        return {
            form: useForm({
                bed_id: this.admission.bed_id,
                patient_id: this.admission.patient_id,
                doctor_id: this.admission.doctor_id,
                admission_dx: this.admission.admission_dx,
                final_dx: this.admission.final_dx,
                comment: this.admission.comment,
            }),
            admissionBeingDeleted: ref(null),
            isVisible: false,
            goBack: useGoBack().goBack
        }
    },
    methods: {
        submit() {
            this.$inertia.put(route('admissions.update', this.admission.id), this.form, {
                onError: (errors) => {
                    this.form.errors = errors;
                }
            })
        },

        submitProcess(value) {
            this.$inertia.put(route('admissions.update', this.admission.id), {
                patient_id: this.admission.patient_id,
                discharged_date: value
            }, {
                onError: (errors) => {
                    this.form.errors = errors;
                }
            })
        },
        discharge() {
            this.submitProcess(new Date().toISOString())
        },
        charge() {
            this.submitProcess(null)
        },
        deleteAdmission() {
            this.admissionbeingDeleted = null;
            this.$inertia.delete(route('admissions.destroy', this.admission.id), {
                onSuccess: (response) => {

                    this.admissionbeingDeleted = null;
                },
                preserveScroll: true
            });
        },
        restoreAdmission() {
            this.$inertia.put(route('admissions.restore', this.admission.id));
        }
    },
}
</script>
