<template>
    <AppLayout>
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
                        formattedId: { id: admission.id, prefix: 'ING' }
                    }
                ]" />
            </h2>
        </template>
        <div class="container mx-auto px-4 py-8">

            <div
                class="max-w-5xl mx-auto bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/60 rounded-2xl overflow-hidden">

                <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <Link :href="route('admissions.index')"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <BackIcon class="size-5" />Volver
                    </Link>


                </div>

                <!-- Estado de Ingreso -->
                <div class="m-8 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-md mb-6">
                    <div class="flex items-center justify-between space-x-2">
                        <div class="flex space-x-2 items-center">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300">Estado:</h3>
                            <span v-bind:class="admission.discharged_date == null ? 'bg-green-500' : 'bg-gray-500'"
                                class="text-white text-sm font-semibold px-2 py-1 rounded-full">
                                {{ admission.discharged_date == null ? 'Ingresado' : 'Dado de alta' }}
                            </span>
                        </div>
                        <div v-if="admission.discharged_date == null" class="flex space-x-2 items-center">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300">Días ingresado:</h3>
                            <span class="text-gray-900 dark:text-white text-sm font-semibold">
                                {{ daysIngressed }}
                            </span>
                        </div>
                        <div v-else class="flex space-x-2 items-center">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300">Fecha:</h3>
                            <span class="text-gray-900 dark:text-white text-sm font-semibold">
                                {{ formatDate(admission.discharged_date) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ubicación</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            <div v-if="admission.bed">
                                Sala: {{ admission.bed.room }}, Cama: {{ admission.bed.number }}
                            </div>
                            <div v-else>
                                No asignada
                            </div>
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                                    admission.patient.second_surname }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Doctor</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ admission.doctor.name }} {{ admission.doctor.last_name }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de Ingreso</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ formatDate(admission.created_at) }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Creado por</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ admission.receptionist.name }} {{ admission.receptionist.last_name }}
                            </p>
                        </div>

                    </div>
                </div>

                <div class="p-8 space-y-8">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico de Ingreso
                        </h3>
                        <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                            {{ admission.admission_dx || 'No se proporcionó diagnóstico de ingreso' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico Final</h3>
                        <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                            {{ admission.final_dx || 'No se ha proporcionado diagnóstico final' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Observaciones</h3>
                        <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                            {{ admission.comment || 'No hay observaciones' }}
                        </p>
                    </div>
                    <AccessGate :except-role="['receptionist']"
                        class="bg-gray-100 dark:bg-gray-700 rounded-lg p-6 shadow-md">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Acciones Adicionales</h3>
                        <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4">

                            <div class="flex flex-col space-y-2 items-center">
                                <Link :href="route('medicalOrders.index', { admission_id: admission.id })"
                                    class="flex w-full items-center justify-center bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg p-4 hover:from-blue-600 hover:to-blue-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                                Órdenes Médicas
                                </Link>
                                <Link v-if="can.createOrder"
                                    :href="route('medicalOrders.create', { admission_id: admission.id })"
                                    class="flex w-24 items-center justify-center bg-blue-400 text-white font-semibold rounded-lg p-2 hover:bg-blue-500 transition duration-300 ease-in-out">
                                Nuevo +
                                </Link>
                            </div>

                            <div class="flex flex-col space-y-2 items-center">
                                <Link :href="route('nurseRecords.index', { admission_id: admission.id })"
                                    class="flex w-full items-center justify-center bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold rounded-lg p-4 hover:from-green-600 hover:to-green-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                                    </path>
                                </svg>
                                Hojas de Enfermería
                                </Link>
                                <AccessGate :role="['admin', 'nurse']">
                                    <Link :href="route('nurseRecords.create', { admission_id: admission.id })"
                                        class="flex w-24 items-center justify-center bg-green-400 text-white font-semibold rounded-lg p-2 hover:bg-green-500 transition duration-300 ease-in-out">
                                    Nuevo +
                                    </Link>
                                </AccessGate>
                            </div>

                            <div class="flex flex-col space-y-2 items-center">
                                <div v-if="temperatureRecordId !== null" class=" w-full">
                                    <Link
                                        :href="`${route('temperatureRecords.show', temperatureRecordId)}?admission_id=${admission.id}`"
                                        class="flex w-full items-center justify-center bg-gradient-to-r from-purple-500 to-purple-700 text-white font-semibold rounded-lg p-4 hover:from-purple-600 hover:to-purple-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Hoja de Temperaturas
                                    </Link>
                                </div>
                                <div v-else class=" w-full">
                                    <Link :href="route('temperatureRecords.create', { admission_id: admission.id })"
                                        class="flex w-full items-center justify-center bg-gradient-to-r from-purple-500 to-purple-700 text-white font-semibold rounded-lg p-4 hover:from-purple-600 hover:to-purple-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Nueva hoja de Temperaturas
                                    </Link>
                                </div>

                            </div>

                            <div>
                                <div v-if="medicationRecord">
                                    <Link :href="route('medicationRecords.show', medicationRecord)"
                                        class="flex w-full items-center justify-center bg-gradient-to-r from-yellow-500 to-yellow-700 text-white font-semibold rounded-lg p-4 hover:from-yellow-600 hover:to-yellow-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Ficha de Medicamentos
                                    </Link>
                                </div>
                                <div v-else>
                                    <Link :href="route('medicationRecords.create', { admission_id: admission.id })"
                                        class="flex w-full items-center justify-center bg-gradient-to-r from-yellow-500 to-yellow-700 text-white font-semibold rounded-lg p-4 hover:from-yellow-600 hover:to-yellow-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Ficha de Medicamentos
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </AccessGate>

                    <div class="flex justify-end space-x-4">
                        <div v-if="can.update">
                            <div v-if="admission.discharged_date == null">
                                <button type="button" @click="admissionUpdateCharge = true"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold rounded-lg hover:from-green-600 hover:to-green-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                    Dar de Alta
                                </button>
                            </div>
                            <div v-if="admission.discharged_date != null">
                                <button type="button" @click="admissionBeingPutInProgress = true"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-yellow-700 text-white font-semibold rounded-lg hover:from-yellow-600 hover:to-yellow-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                    Poner en progreso
                                </button>
                            </div>
                        </div>

                        <Link v-if="can.update" :href="route('admissions.edit', admission.id)"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-blue-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Editar
                        </Link>

                        <button v-if="can.delete && admission.active" @click="admissionBeingDeleted = true"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-700 text-white font-semibold rounded-lg hover:from-red-600 hover:to-red-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Eliminar
                        </button>
                        <button v-if="can.delete && !admission.active" @click="restoreAdmission"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold rounded-lg hover:from-green-600 hover:to-green-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                            Restaurar
                        </button>
                    </div>
                </div>

            </div>
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

                <DangerButton class="ms-3" @click="confirmDelete">
                    Eliminar
                </DangerButton>
            </template>
        </ConfirmationModal>

        <!-- modal para eliminar -->
        <ConfirmationModal :show="admissionBeingPutInProgress != null" @close="admissionBeingPutInProgress = null">
            <template #title>
                poner en progreso
            </template>

            <template #content>
                ¿Estás seguro de que deseas poner en progreso este ingreso?
            </template>

            <template #footer>
                <SecondaryButton @click="admissionBeingPutInProgress = null">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ms-3" @click="charge">
                    Aceptar
                </PrimaryButton>
            </template>
        </ConfirmationModal>

        <!-- modal para dar de alta -->
        <ConfirmationModal :show="admissionUpdateCharge != null" @close="admissionUpdateCharge = null">
            <template #title>
                <div v-if="admission.discharged_date == null">Dar de alta</div>
                <div v-if="admission.discharged_date != null">Poner en progreso</div>
                <div>
                    <label for="final_dx"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                        final</label>
                    <textarea required id="final_dx" rows="4" v-model="formDischarge.final_dx"
                        class="block p-2.5 mb-4 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Escribe el diagnóstico final..."></textarea>

                    <SignaturePad class="w-full max-w-lg lg:max-w-md" v-model="formDischarge.doctor_sign"
                        input-name="doctor_sign" />
                    <div v-if="signatureError" class="text-red-500 text-sm mt-2">La firma es obligatoria.</div>

                </div>

            </template>

            <template #content>
                <div v-if="admission.discharged_date == null">¿Estás seguro de que deseas dar de alta a este ingreso?
                </div>
                <div v-if="admission.discharged_date != null">¿Estás seguro de que deseas poner en progreso este
                    ingreso?</div>
            </template>

            <template #footer>
                <SecondaryButton @click="admissionUpdateCharge = null">
                    Cancelar
                </SecondaryButton>

                <div v-if="admission.discharged_date == null">
                    <PrimaryButton class="ms-3" @click="discharge">
                        Dar de alta
                    </PrimaryButton>
                </div>
                <div v-if="admission.discharged_date != null">
                    <PrimaryButton class="ms-3" @click="charge">
                        Poner en progreso
                    </PrimaryButton>
                </div>

            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script>
import AccessGate from '@/Components/Access/AccessGate.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import FormatId from '@/Components/FormatId.vue';
import BackIcon from '@/Components/Icons/BackIcon.vue';
import ReportIcon from '@/Components/Icons/ReportIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import {
    Link
} from '@inertiajs/vue3';
import {
    ref
} from 'vue';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue';

export default {
    props: {
        admission: Object,
        daysIngressed: Number,
        temperatureRecordId: {
            type: [Number, Object],
            default: null
        },
        can: [Array, Object],
        medicationRecord: Object
    },
    components: {
        AppLayout,
        Link,
        AccessGate,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        PrimaryButton,
        BackIcon,
        ReportIcon,
        FormatId,
        SignaturePad,
        BreadCrumb,
    },
    data() {
        return {
            admissionBeingDeleted: ref(null),
            admissionUpdateCharge: ref(null),
            admissionBeingPutInProgress: ref(null),
            signatureError: false,
            signatureError: false,
            form: {
                charge: false,
                patient_id: this.admission.patient_id,
                bed_id: this.admission.bed_id,
                doctor_id: this.admission.doctor_id,
                admission_dx: this.admission.admission_dx,
                doctor_sign: this.admission.doctor_sign,
                final_dx: this.admission.final_dx,
                comment: this.admission.comment,
                discharged_date: this.admission.discharged_date
            },
            formDischarge: {
                discharge: true,
                doctor_sign: this.admission.doctor_sign,
                final_dx: this.admission.final_dx,
                discharged_date: this.admission.discharged_date
            },
        }
    },
    methods: {
        submit() {
            this.$inertia.put(route('admissions.update', this.admission.id), this.form, {
                preserveScroll: true
            })
        },
        discharge() {
            this.formDischarge.discharged_date = new Date().toISOString()
            this.submitDischarge();
        },
        submitDischarge() {
            if (!this.formDischarge.doctor_sign) {
                this.signatureError = true;
                return false
            }
            this.signatureError = false;
            this.$inertia.put(route('admissions.update', this.admission.id), this.formDischarge, {
                preserveScroll: true
            });
            this.admissionUpdateCharge = null
        },

        charge() {
            this.form.charge = true,
                this.form.discharged_date = null
            this.form.doctor_sign = null
            this.form.final_dx = null
            this.admissionBeingPutInProgress = null
            this.submit()
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        confirmDelete() {
            this.$inertia.delete(route('admissions.destroy', this.admission.id));
            this.admissionBeingDeleted = false;
        },

        restoreAdmission() {
            this.$inertia.put(route('admissions.restore', this.admission.id));
        },
    }
}
</script>
