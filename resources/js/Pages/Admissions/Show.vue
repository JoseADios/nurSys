<template>
    <AppLayout title="Ingresos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
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
                <div class="m-8 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg border dark:border-gray-700/60   mb-6">
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
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border dark:border-gray-700/60   ">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                                    admission.patient.second_surname }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border dark:border-gray-700/60   ">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Recepcionista</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ admission.receptionist.name }} {{ admission.receptionist.last_name }}
                            </p>


                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border dark:border-gray-700/60   ">
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



                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border dark:border-gray-700/60   ">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Doctor</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ admission.doctor.name }} {{ admission.doctor.last_name }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border dark:border-gray-700/60   ">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de Ingreso</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ formatDate(admission.created_at) }}
                            </p>
                        </div>


                    </div>
                </div>

                <div class="p-8 space-y-8">
                    <AccessGate :role="['doctor','admin']">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 dark:border-gray-700/60   ">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico de Ingreso
                        </h3>
                        <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                            {{ admission.admission_dx || 'No se proporcionó diagnóstico de ingreso' }}
                        </p>
                        <div class="flex justify-end"></div>
                    </div>
                    </AccessGate>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 dark:border-gray-700/60   ">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico Final</h3>
                        <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                            {{ admission.final_dx || 'No se ha proporcionado diagnóstico final' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 dark:border-gray-700/60   ">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Observaciones</h3>
                        <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                            {{ admission.comment || 'No hay observaciones' }}
                        </p>
                    </div>

                    <div v-if="admission.doctor_sign" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 dark:border-gray-700/60  ">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Firma del Doctor/a</h3>
                        <p class="text-base text-gray-800 dark:text-gray-200 min-h-[100px]">
                            <img :src="`/storage/${admission.doctor_sign}`" width="250" alt="Firma">
                        </p>
                    </div>

                    <AccessGate :except-role="['receptionist']"
                        class="bg-gray-100 dark:bg-gray-700 rounded-lg p-6 border dark:border-gray-700/60  ">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Acciones Adicionales </h3>
                        <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-4">

                            <div class="flex flex-col space-y-2 items-center">

                                  <div class=" w-full">
                                    <PersonalizableLink :href="route('medicalOrders.index', { admission_id: admission.id })"
                                    class="flex w-full items-center justify-center py-4 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg p-4 hover:from-blue-600 hover:to-blue-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                <MedicalOrderIcon class="size-5 mr-2" />
                                Órdenes Médicas
                                </PersonalizableLink>
                                  </div>
                                <PersonalizableLink v-if="can.createOrder"
                                    :href="route('medicalOrders.create', { admission_id: admission.id })"
                                    class="flex w-24 items-center justify-center  bg-blue-400 text-white font-semibold rounded-lg p-2 hover:bg-blue-500 transition duration-300 ease-in-out">
                                Nuevo +
                                </PersonalizableLink>
                            </div>

                            <div class="flex flex-col space-y-2 items-center">
                                <PersonalizableLink :href="route('nurseRecords.index', { admission_id: admission.id })"
                                    class="flex w-full items-center justify-center py-4 bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold rounded-lg p-4 hover:from-green-600 hover:to-green-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                <NurseRecordIcon class="size-5 mr-2" />
                                Hojas de Enfermería
                                </PersonalizableLink>
                                <AccessGate :role="['admin', 'nurse']">
                                    <PersonalizableLink :href="route('nurseRecords.create', { admission_id: admission.id })"
                                        class="flex w-24 items-center justify-center  bg-green-400 text-white font-semibold rounded-lg p-2 hover:bg-green-500 transition duration-300 ease-in-out">
                                    Nuevo +
                                    </PersonalizableLink>
                                </AccessGate>
                            </div>

                            <div class="flex flex-col space-y-2 items-center">
                                <div v-if="temperatureRecordId !== null" class=" w-full">
                                    <PersonalizableLink
                                        :href="`${route('temperatureRecords.show', temperatureRecordId)}?admission_id=${admission.id}`"
                                        class="flex w-full items-center text-center justify-center py-4 bg-gradient-to-r from-purple-500 to-purple-700 text-white font-semibold rounded-lg p-4 hover:from-purple-600 hover:to-purple-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                    <ChartIcon class="size-5 mr-1" />
                                    Hoja de Temperaturas
                                    </PersonalizableLink>
                                </div>
                                <div v-else class=" w-full">
                                    <PersonalizableLink :href="route('temperatureRecords.create', { admission_id: admission.id })"
                                        class="flex w-full items-center text-center justify-center py-4 bg-gradient-to-r from-purple-500 to-purple-700 text-white font-semibold rounded-lg p-4 hover:from-purple-600 hover:to-purple-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                    <ChartIcon class="size-5 " />
                                    Nueva hoja de Temperatura
                                    </PersonalizableLink>
                                </div>

                            </div>

                            <div>
                                <div v-if="medicationRecordId !== null">
                                    <PersonalizableLink  :href="`${route('medicationRecords.show', medicationRecordId)}?admission_id=${admission.id}`"

                                        class="flex w-full items-center text-center   justify-center py-4  bg-gradient-to-r from-yellow-500 to-yellow-700 text-white font-semibold rounded-lg p-4 hover:from-yellow-600 hover:to-yellow-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                    <MedicationIcon class="size-5 mr-1 " />
                                    Ficha de Medicamentos
                                    </PersonalizableLink>
                                </div>
                                <div v-else>
                                    <PersonalizableLink :href="route('medicationRecords.create', { admission_id: admission.id })"
                                        class="flex w-full items-center text-center justify-center py-4 bg-gradient-to-r from-yellow-500 to-yellow-700 text-white font-semibold rounded-lg p-4 hover:from-yellow-600 hover:to-yellow-800 transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105">
                                    <MedicationIcon class="size-5 mr-1 " />
                                    Ficha de Medicamentos
                                    </PersonalizableLink>
                                </div>
                            </div>
                        </div>
                    </AccessGate>


                    <div class="flex flex-wrap justify-end gap-2 sm:flex-row flex-col text-center">
                        <AccessGate :role="['doctor', 'admin']">
                            <div v-if="can.update">
                                <div v-if="admission.discharged_date == null">
                                    <PersonalizableButton @click="admissionUpdateCharge = true"  :class="{ 'opacity-25': formDischarge.processing }":is-loading="formDischarge.processing"  :disabled="formDischarge.processing"
                                        color="green">
                                        <CheckCircleIcon class="size-5 mr-2 " />
                                        <span class="">Dar de Alta </span>
                                    </PersonalizableButton>
                                </div>
                                <div v-if="admission.discharged_date != null">
                                    <PersonalizableButton @click="admissionBeingPutInProgress = true"  :class="{ 'opacity-25': form.processing }":is-loading="form.processing"  :disabled="form.processing"
                                        color="yellow">
                                        <RestoreIcon class="size-5 mr-2" />
                                        <span class="">Poner en progreso</span>
                                    </PersonalizableButton>
                                </div>
                            </div>
                        </AccessGate>

                        <AccessGate :role="['admin']">

                            <Link v-if="can.update"  :href="route('admissions.edit', admission.id)">
                            <PersonalizableButton class=" bg-primary-500 mr-2" :class="{ 'opacity-25': formDischarge.processing }":is-loading="formDischarge.processing"  :disabled="formDischarge.processing">
                                <EditIcon class="size-5 mr-2" />
                                <span class="">Editar</span>
                            </PersonalizableButton>
                            </Link>


                        <DangerButton v-if="can.delete && admission.active" @click="admissionBeingDeleted = true" :class="{ 'opacity-25': formDischarge.processing }":is-loading="formDischarge.processing"  :disabled="formDischarge.processing"
                            class="gap-2" color="red">
                            <TrashIcon class="size-5" />
                            <span class="">Eliminar</span>
                        </DangerButton>
                        <PersonalizableButton v-if="can.delete && !admission.active" @click="restoreAdmission"
                            class="gap-2" color="green">
                            <RestoreIcon class="size-5 " />
                            <span class="">Restaurar</span>
                        </PersonalizableButton>
                          </AccessGate>
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

                <PrimaryButton class="ms-3" @click="charge" :class="{ 'opacity-25': form.processing }":is-loading="form.processing"  :disabled="form.processing">
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
                        class="block p-2.5 mb-2 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Escribe el diagnóstico final..."></textarea>
                        <InputError :message="formDischarge.errors.final_dx" class="mb-4"/>

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

                <div v-if="admission.discharged_date == null"  :class="{ 'opacity-25': formDischarge.processing }":is-loading="formDischarge.processing"  :disabled="formDischarge.processing">
                    <PrimaryButton class="ms-3" @click="discharge">
                        Dar de alta
                    </PrimaryButton>
                </div>
                <div v-if="admission.discharged_date != null" :class="{ 'opacity-25': form.processing }":is-loading="form.processing"  :disabled="form.processing">
                    <PrimaryButton class="ms-3" @click="charge">
                        Poner en progreso
                    </PrimaryButton>
                </div>

            </template>
        </ConfirmationModal>
        <Modal :closeable="true" :show="showEditReceptionist != null" @close="showEditReceptionist == null">
            <div class="relative overflow-hidden sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
                <form @submit.prevent="submitAdmission" class="max-w-3xl mx-auto">

                    <UserSelector roles="nurse" :selected-user-id="admission.receptionist_id"
                        @update:user="formRecord.receptionist_id = $event" />
                    <!-- Botones -->
                    <div class="flex justify-end mt-4 space-x-3">
                        <SecondaryButton type="button" @click="showEditReceptionist = null"
                            class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit" :disabled="!formRecord.receptionist_id">
                            Aceptar
                        </PrimaryButton>
                    </div>
                </form>
            </div>

        </Modal>

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
import EditIcon from '@/Components/Icons/EditIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import UserSelector from '@/Components/UserSelector.vue';
import Modal from '@/Components/Modal.vue';
import RestoreIcon from '@/Components/Icons/RestoreIcon.vue';
import MedicalOrderIcon from '@/Components/Icons/MedicalOrderIcon.vue';
import NurseRecordIcon from '@/Components/Icons/NurseRecordIcon.vue';
import ChartIcon from '@/Components/Icons/ChartIcon.vue';
import MedicationIcon from '@/Components/Icons/MedicationIcon.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';
import {
    Link,
    useForm
} from '@inertiajs/vue3';
import {
    ref
} from 'vue';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue';
import CheckCircleIcon from '@/Components/Icons/CheckCircleIcon.vue';
import InputError from '@/Components/InputError.vue';
import PersonalizableLink from '@/Components/PersonalizableLink.vue';
export default {
    props: {
        admission: Object,
        daysIngressed: Number,
        temperatureRecordId: {
            type: [Number, Object],
            default: null
        },
        can: [Array, Object],
        medicationRecordId: {
            type: [Number, Object],
            default: null
        },
        medicalOrderId:{
            type: [Number, Object],
            default: null
        },
    },
    components: {
        AppLayout,
        Link,
        Modal,
        InputError,
        EditIcon,
        CheckCircleIcon,
        TrashIcon,
        RestoreIcon,
        MedicationIcon,
        NurseRecordIcon,
        ChartIcon,
        MedicalOrderIcon,
        UserSelector,
        PersonalizableButton,
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
        PersonalizableLink
    },
    data() {
        return {
            admissionBeingDeleted: ref(null),
            processing: ref(false),
            admissionUpdateCharge: ref(null),
            showEditReceptionist: ref(null),
            showEditDiagnosis: ref(null),
            admissionBeingPutInProgress: ref(null),

            signatureError: false,
            modalform: useForm({
                 admission_dx: this.admission.admission_dx,
            }),
            form: useForm ({
                charge: false,
                patient_id: this.admission.patient_id,
                bed_id: this.admission.bed_id,
                doctor_id: this.admission.doctor_id,
                admission_dx: this.admission.admission_dx,
                doctor_sign: this.admission.doctor_sign,
                final_dx: this.admission.final_dx,
                comment: this.admission.comment,
                discharged_date: this.admission.discharged_date
            }),
            formDischarge: useForm ({
                discharge: true,
                doctor_sign: this.admission.doctor_sign,
                final_dx: this.admission.final_dx,
                discharged_date: this.admission.discharged_date
           }),
            formRecord: {
                admission_id: this.admission.id,
                receptionist_id: this.admission.receptionist_id,
                active: this.admission.active,
            },
        }
    },
    methods: {
        submit() {
            this.form.put(route('admissions.update', this.admission.id), {
                preserveScroll: true
            })
        },
        updateDiagnosis(){
             this.modalform.put(route('admissions.update', this.admission.id), {
                preserveScroll: true
            })
             this.showEditDiagnosis = null;
        },
        submitAdmission() {
            this.$inertia.put(route('admissions.update', this.admission.id), this.formRecord, {
                preserveScroll: true
            })
            this.showEditReceptionist = null;
        },
        discharge() {
            this.formDischarge.discharged_date = new Date().toISOString()
            this.submitDischarge();
        },
        submitDischarge() {
                if (!this.formDischarge.final_dx) {
                this.formDischarge.errors.final_dx = "Debe Ingresar un diagnóstico final."
                return false
            }
            if (!this.formDischarge.doctor_sign) {
                this.signatureError = true;
                return false
            }

            this.signatureError = false;
            this.formDischarge.put(route('admissions.update', this.admission.id), {
                preserveScroll: true,
                  onSuccess: () => {

                        this.formDischarge.reset();
                        this.formDischarge.errors = '';
                    },
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
