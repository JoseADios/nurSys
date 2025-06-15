<template>
<AppLayout title="Editar Ingresos">
    <template #header>
        <h2 class="font-semibold text-xl text-white leading-tight text-center">
            <BreadCrumb :items="[
                              ...(admission.id ? [{
                                  text: 'Ingresos',
                                  route: admission.id
                                      ? route('admissions.index', { id: admission.id })
                                      : route('admissions.index')
                              }] : []),


                              {
                                  formattedId: { id: admission.id, prefix: 'ING' },
                                   route: route('admissions.show', admission.id)
                              },
                              {
                                 text: 'Edit',
                              }
                          ]" />
        </h2>
    </template>


    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
        <form @submit.prevent="submit" class="max-w-xl mx-auto">

            <BedSelector :beds="beds" :errors="form.errors" :initialBedId="form.bed_id" @update:bedId="form.bed_id = $event" />

            <AccessGate :except-role="['nurse']">
                <label for="patient" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Paciente</label>
                <select required id="patient" v-model="form.patient_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                    <option :value="patient.id" v-for="patient in patients" :key="patient.id" :selected="patient.id === admission.patient_id">
                        {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                    </option>
                </select>
                <InputError :message="form.errors.patient_id" class="mt-2" />

                <label for="doctor" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Doctor</label>
                <select required id="doctor" v-model="form.doctor_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                    <option :value="doctor.id" v-for="doctor in doctors" :key="doctor.id" :selected="doctor.id === admission.doctor_id">
                        {{ doctor.name }} {{ doctor.last_name }}
                    </option>
                </select>
                <InputError :message="form.errors.doctor_id" class="mt-2" />

                <label for="admission_dx" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                    de
                    ingreso</label>
                <textarea required id="admission_dx" rows="4" v-model="form.admission_dx" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escribe el diagnóstico de ingreso..."></textarea>
                <InputError :message="form.errors.admission_dx" class="mt-2" />

                <div v-if="admission.discharged_at">


                <label for="final_dx" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                    final</label>
                <textarea id="final_dx" rows="4" v-model="form.final_dx" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escribe el diagnóstico final..."></textarea>
                <InputError :message="form.errors.final_dx" class="mt-2" />
</div>
                <label for="comment" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                <textarea id="comment" rows="4" v-model="form.comment" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escribe las observaciones..."></textarea>
                <InputError :message="form.errors.comment" class="mt-2" />
            </AccessGate>

            <div class="flex justify-end mt-6 mb-2 gap-2">

                <Link :href="route('admissions.show',admission.id)" >

                 <SecondaryButton class="py-2.5 px-5 " >
                Cancelar
            </SecondaryButton>
                </Link>

                 <PrimaryButton type="submit"  :class="{ 'opacity-25': form.processing }" :is-loading="form.processing" :disabled="form.processing" >
                    Guardar
                </PrimaryButton>
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
import {
    Link,
    useForm
} from '@inertiajs/vue3';

import InputError from '@/Components/InputError.vue';
import BedSelector from '@/Components/BedSelector.vue';
import {
    ref
} from "vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CheckCircleIcon from '@/Components/Icons/CheckCircleIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';

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
        PrimaryButton,
        InputError,
        BedSelector,
        ConfirmationModal,
        DangerButton,
        CheckCircleIcon,
        SecondaryButton,
        PersonalizableButton,
        TrashIcon,
        BreadCrumb
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

        }
    },
    methods: {
        submit() {
            this.form.put(route('admissions.update', this.admission.id), {
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
