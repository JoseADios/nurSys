<template>
    <AppLayout title="Registro de enfermería">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                <BreadCrumb :items="[
                    // Condicionar el primer elemento (solo se muestra si hay admission_id)
                    ...(admission_id ? [{
                        formattedId: { id: nurseRecord.admission_id, prefix: 'ING' },
                        route: route('admissions.show', nurseRecord.admission_id)
                    }] : []),

                    // Segundo elemento (depende si hay nurseRecord.admission_id o no)
                    {
                        text: 'Registros de enfermería',
                        route: admission_id
                            ? route('nurseRecords.index', { admission_id: nurseRecord.admission_id })
                            : route('nurseRecords.index')
                    },

                    {
                        formattedId: { id: nurseRecord.id, prefix: 'ENF' }
                    }
                ]" />
            </h2>
        </template>
        <div class="container mx-auto px-4 py-8">
            <div
                class="max-w-6xl mx-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-2xl overflow-hidden">
                <!-- Navigation: Mejorar responsividad -->
                <div class="p-4 dark:bg-gray-900 flex flex-row justify-between items-center sm:space-y-0">
                    <div v-if="nurseRecord.admission_id">
                        <Link :href="route('nurseRecords.index', { admission_id: admission_id })"
                            class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                        <BackIcon class="size-5" />
                        <span class="font-medium ">Volver</span>
                        </Link>
                    </div>
                    <div v-else>
                        <Link :href="route('nurseRecords.index')"
                            class="flex px-4 sm:px-0 items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                        <BackIcon class="size-5" />
                        <span class="font-medium">Volver</span>
                        </Link>
                    </div>
                    <div class="flex items-center gap-2">
                        <PersonalizableButton v-if="nurseRecord.active" @click="downloadRecordReport" color="emerald">
                            <ReportIcon class="size-5" />
                            <span class="hidden md:inline-flex ml-2">Crear Reporte</span>
                        </PersonalizableButton>

                        <AccessGate :permission="['nurseRecord.delete']" v-if="canUpdateRecord">
                            <DangerButton v-if="nurseRecord.active" @click="recordBeingDeleted = true"
                                class="space-x-2">
                                <TrashIcon class="size-5" />
                                <span class="hidden sm:inline-flex">Eliminar</span>
                            </DangerButton>
                            <PersonalizableButton v-else @click="restoreRecord" class="gap-2" color="green"
                                :loading="recordActiveChanging">
                                <RestoreIcon class="size-5" />
                                <span class="hidden sm:inline-flex">Restaurar</span>
                            </PersonalizableButton>
                        </AccessGate>
                    </div>

                </div>

                <!-- Patient and Record Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 p-4 sm:p-8 bg-gray-50 dark:bg-gray-700">
                    <div class="space-y-4">

                        <!-- admission -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60 flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ingreso</h3>
                                <Link :href="route('admissions.show', nurseRecord.admission_id)" as="button"
                                    class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                                <FormatId :id="nurseRecord.admission_id" prefix="ING" />
                                </Link>
                            </div>
                            <AccessGate :role="['admin']" v-if="canUpdateRecord">
                                <button @click="showEditAdmission = true" class="text-primary-500 flex">
                                    <EditIcon class="size-5" />
                                </button>
                            </AccessGate>
                        </div>

                        <!-- patient name -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <Link :href="route('patients.show', nurseRecord.admission.patient.id)" as="button"
                                class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                            {{ nurseRecord.admission.patient.first_name }} {{
                                nurseRecord.admission.patient.first_surname }} {{
                                nurseRecord.admission.patient.second_surname }}
                            </Link>
                        </div>

                        <!-- Bed info -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Sala</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                Sala {{ nurseRecord.admission.bed?.room || 'N/A' }},
                                    Cama {{ nurseRecord.admission.bed?.number || 'N/A' }}
                            </p>
                        </div>

                    </div>

                    <!-- Rigth col -->
                    <div class="space-y-4">

                        <!-- Nurse -->
                        <div
                            class="flex justify-between items-center bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Enfermero</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ nurse.name }} {{ nurse.last_name }}
                                </p>
                            </div>
                            <AccessGate :role="['admin']" v-if="canUpdateRecord">
                                <button @click="showEditNurse = true" class="text-primary-500 flex">
                                    <EditIcon class="size-5" />
                                </button>
                            </AccessGate>
                        </div>
                        <!-- Doctor info -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Doctor</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ nurseRecord.admission.doctor.name }} {{ nurseRecord.admission.doctor.last_name }}
                            </p>
                        </div>
                        <!-- Created date -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de Registro</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ formatDate(nurseRecord.created_at) }}
                            </p>
                        </div>

                    </div>
                </div>

                <AccessGate v-if="canUpdateRecord" :permission="['nurseRecord.update']"
                    class="flex flex-col md:flex-row">
                    <div class="col w-full md:w-[50%] p-4 md:p-8">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Órdenes médicas</h3>
                        <!-- Mensaje cuando no hay órdenes -->
                        <div v-if="!medicalOrders || medicalOrders.length === 0"
                            class="text-center text-sm text-gray-500 dark:text-gray-400">
                            No hay órdenes médicas disponibles
                        </div>

                        <!-- Acordeón de Órdenes Médicas -->
                        <div v-else class="space-y-4 max-h-72 overflow-y-auto">
                            <div v-for="(order, index) in medicalOrders" :key="order.id">
                                <div
                                    class="accordion-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    <!-- Cabecera del Acordeón -->
                                    <div @click="toggleAccordion(index)"
                                        class="accordion-header cursor-pointer flex justify-between items-center p-4 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-center justify-between w-full space-x-2">
                                            <span class="font-semibold text-gray-800 dark:text-white">
                                                <Link :href="route('medicalOrders.show', order.id)">
                                                <FormatId :id="order.id" prefix="ORD" />
                                                </Link>
                                                <!-- - {{ order.doctor.name }} {{ order.doctor.last_name }} -->
                                            </span>
                                            <span class="font-normal pr-1 text-sm text-gray-500 dark:text-gray-400">{{
                                                formatDateFromNow(order.created_at)
                                                }}</span>
                                        </div>
                                        <ChevronDown
                                            class="h-5 w-5 transform transition-transform duration-300 text-gray-800 dark:text-white"
                                            :class="{ 'rotate-180': openAccordion === index }" />
                                    </div>

                                    <!-- Contenido del Acordeón -->
                                    <div v-if="openAccordion === index && order.medical_order_detail.length !== 0"
                                        class="accordion-content p-4 bg-white dark:bg-gray-900">
                                        <div v-for="(detail, detailIndex) in order.medical_order_detail"
                                            :key="detailIndex"
                                            class="mb-3 pb-3 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                            <div class="flex flex-col justify-between items-start">
                                                <div class="w-full flex flex-col">
                                                    <div class="flex justify-between items-center w-full gap-2">
                                                        <p class="text-sm font-semibold text-gray-800 dark:text-white break-all">
                                                            {{ detail.order }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 text-end">
                                                            {{ formatDateFromNow(detail.created_at) }}
                                                        </p>
                                                    </div>
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                                        {{ detail.regime }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="openAccordion === index && order.medical_order_detail.length === 0"
                                        class="accordion-content p-4 text-xs text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-900">

                                        No hay detalles disponibles para esta orden.

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Form -->
                    <!-- Formulario para agregar nuevo detalle -->
                    <AccessGate :permission="['nurseRecordDetail.create']" class="col w-full md:w-[50%] p-4 md:p-8">
                        <div v-if="canUpdateRecord">
                            <div class="">
                                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Nuevos
                                    Eventos
                                </h3>

                                <form @submit.prevent="submit" class="space-y-4">
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <InputLabel for="medication" value="Medicación" :required="true"
                                                class="mb-2" />
                                            <TextAreaInput maxlength="255" class="w-full h-16 resize-none"
                                                v-model="formDetail.medication" id="medication" placeholder="Medicación"
                                                required />
                                        </div>

                                        <div>
                                            <InputLabel for="comment" value="Observaciones" :required="true"
                                                class="mb-2" />
                                            <TextAreaInput maxlength="255" class="w-full resize-none h-24"
                                                v-model="formDetail.comment" id="comment"
                                                placeholder="Comentarios adicionales" required />
                                        </div>
                                    </div>
                                    <div class="pt-4">
                                        <PersonalizableButton size="large" class="w-full"
                                            :loading="formDetail.processing">
                                            Agregar Evento
                                        </PersonalizableButton>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div v-else>
                            <hr class="my-2 border-transparent dark:border-transparent">
                        </div>
                    </AccessGate>
                </AccessGate>

                <div v-if="!canUpdateRecord">
                    <hr class="my-2 border-transparent dark:border-transparent">
                </div>
                <AccessGate :except-permission="['nurseRecordDetail.create']">
                    <div v-if="canUpdateRecord">
                        <hr class="my-2 border-transparent dark:border-transparent">
                    </div>
                </AccessGate>

                <!-- Nurse Record Details: Mejorar controles -->
                <div class="p-4 sm:p-8 space-y-4 bg-gray-50 dark:bg-gray-700">
                    <div
                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-2 sm:space-y-0">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2 sm:mb-0">Eventos del
                            Registro</h3>

                        <div v-if="canUpdateRecord">
                            <button @click="toggleShowDeleted"
                                class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap w-full sm:w-auto justify-center sm:justify-start"
                                :class="{
                                    'bg-red-500 hover:bg-red-600 text-white': showDeletedLocal,
                                    'bg-gray-200 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200': !showDeletedLocal
                                }">
                                {{ showDeletedLocal ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                                <CirclePlusIcon v-if="showDeletedLocal" class="ml-1 h-5 w-5" />
                                <CircleXIcon v-else class="ml-1 h-5 w-5" />
                            </button>

                        </div>
                    </div>

                    <div class="max-h-[50rem] overflow-y-auto space-y-4">
                        <div v-for="detail in details" :key="detail.id"
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60 backdrop-blur-sm flex flex-col sm:flex-row justify-between items-start sm:items-center hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                            <div class="flex-grow pr-4 w-full sm:w-auto">
                                <div class="font-semibold text-gray-900 dark:text-white text-wrap break-all">
                                    {{ detail.medication }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1 break-all">
                                    {{ detail.comment }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ formatDate(detail.created_at) }}
                                </div>
                            </div>
                            <div class="flex mt-3 sm:mt-0 space-x-4 w-full sm:w-auto">
                                <AccessGate :permission="['nurseRecordDetail.edit']" v-if="canUpdateRecord">
                                    <div class="sm:text-right">
                                        <!-- Editar -->
                                        <button @click="openEditDetailModal(detail)"
                                            class="flex items-center space-x-2 text-primary-500 hover:text-primary-600 transition-colors">
                                            <EditIcon class="size-5" />
                                            <span class="font-medium sm:hidden md:inline-flex">Editar</span>
                                        </button>
                                    </div>
                                </AccessGate>
                            </div>
                        </div>
                    </div>

                    <div v-if="details.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No hay eventos de registro disponibles
                    </div>
                </div>

                <!-- Firma -->
                <section id="bottom" class=" p-8 space-y-4 ">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Firma</h3>

                    <!-- mostrar imagen firma -->
                    <div v-show="!isVisibleEditSign">
                        <div class="flex items-center flex-col justify-center">

                            <img v-if="nurseRecord.nurse_sign" :src="`/storage/${nurseRecord.nurse_sign}`" alt="Firma"
                                class="w-full max-w-md">
                            <div v-else>
                                <div class="text-gray-500 dark:text-gray-400 my-16">
                                    No hay firma disponible
                                </div>
                            </div>

                            <AccessGate :permission="['nurseRecord.update']" v-if="canUpdateRecord">
                                <PrimaryButton @click="isVisibleEditSign = true" class="mt-4">
                                    Editar
                                </PrimaryButton>
                            </AccessGate>
                        </div>
                    </div>

                    <!-- Campo de firma -->
                    <AccessGate :permission="['nurseRecord.update']" v-if="canUpdateRecord">
                        <div v-show="isVisibleEditSign" class="my-4">
                            <form @submit.prevent="submitSignature" class="flex items-center flex-col justify-center">

                                <SignaturePad v-model="formSignature.nurse_sign" input-name="nurse_sign"
                                    class="w-full max-w-lg lg:max-w-md" />
                                <div v-if="signatureError" class="text-red-500 text-sm mt-2">La firma es obligatoria.
                                </div>

                                <div class="my-4 space-x-4">
                                    <SecondaryButton type="button" @click="isVisibleEditSign = false">
                                        Cancelar
                                    </SecondaryButton>
                                    <PrimaryButton @click="isVisibleEditSign = true"
                                        :is-loading="formSignature.processing" class="mt-4">
                                        Guardar firma
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </AccessGate>
                </section>
            </div>
        </div>

        <!-- Change admission modal -->
        <AccessGate :permission="['nurseRecord.delete']" v-if="canUpdateRecord">
            <Modal :closeable="true" :show="showEditAdmission != null" @close="showEditAdmission == null">
                <div class="relative overflow-hidden sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
                    <form @submit.prevent="submitAdmission" class="max-w-3xl mx-auto">

                        <AdmissionSelector @update:admission="formRecord.admission_id = $event"
                            :selected-admission-id="nurseRecord.admission_id" />

                        <!-- Botones -->
                        <div class="flex justify-end mt-4 space-x-3">
                            <SecondaryButton type="button" @click="showEditAdmission = null">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton :disabled="!formRecord.admission_id" :is-loading="formRecord.processing">
                                Actualizar
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

            </Modal>
        </AccessGate>


        <!-- Change nurse modal -->
        <AccessGate :permission="['nurseRecord.delete']" v-if="canUpdateRecord">
            <Modal :closeable="true" :show="showEditNurse != null" @close="showEditNurse == null">
                <div class="relative overflow-hidden sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
                    <form @submit.prevent="submitAdmission" class="max-w-3xl mx-auto">

                        <UserSelector roles="nurse" :selected-user-id="nurseRecord.nurse_id"
                            @update:user="formRecord.nurse_id = $event" />
                        <!-- Botones -->
                        <div class="flex justify-end mt-4 space-x-3">
                            <SecondaryButton type="button" @click="showEditNurse = null">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton :disabled="!formRecord.nurse_id" :is-loading="formRecord.processing">
                                Actualizar
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

            </Modal>
        </AccessGate>

        <!-- Edit event -->
        <DialogModal :show="isVisibleDetail" @close="isVisibleDetail = false" class="p-1 sm:p-4">
            <!-- Header del modal -->
            <template #title>
                Editar evento
            </template>

            <!-- Contenido del modal -->
            <template #content>
                <div>
                    <form>
                        <div class="flex flex-col gap-6">
                            <div>
                                <InputLabel for="medication" value="Medicación" :required="true" />
                                <TextInput id="medication" v-model="selectedDetail.medication" type="text"
                                    class="mt-1 block w-full" required autocomplete="medication" />
                                <InputError v-if="!selectedDetail.medication" message="Este campo es obligatorio." />
                            </div>

                            <div>
                                <InputLabel for="comment" value="Observaciones" :required="true" />
                                <TextAreaInput class="mt-1 w-full" v-model="selectedDetail.comment" required
                                    id="comment" />
                                <InputError v-if="!selectedDetail.comment" message="Este campo es obligatorio." />
                            </div>

                        </div>
                    </form>
                </div>
            </template>

            <!-- Footer del modal -->
            <template #footer>
                <div class="flex flex-col justify-center w-full sm:flex-row sm:justify-end items-center gap-3">
                    <DangerButton v-if="selectedDetail.active" type="button" @click="detailBeingDeleted = true">
                        Eliminar
                    </DangerButton>
                    <PersonalizableButton v-if="!selectedDetail.active" color="green" type="button"
                        @click="restoreDetail(selectedDetail)">
                        Restaurar
                    </PersonalizableButton>
                    <SecondaryButton type="button" @click="isVisibleDetail = false">
                        Cerrar
                    </SecondaryButton>
                    <PrimaryButton @click="submitUpdateDetail" :is-loading="formDetail.processing">
                        Actualizar
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>

        <!-- Delete record modal -->
        <AccessGate :permission="['nurseRecord.delete']" v-if="canUpdateRecord">
            <ConfirmationModal :show="recordBeingDeleted != null" @close="recordBeingDeleted == null">
                <template #title>
                    Eliminar registro
                </template>

                <template #content>
                    ¿Estás seguro de que deseas eliminar este registro?
                </template>

                <template #footer>
                    <SecondaryButton @click="recordBeingDeleted = null">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton class="ms-3" @click="deleteRecord" :class="{ 'opacity-25': recordActiveChanging }"
                        :disabled="recordActiveChanging">
                        Eliminar
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </AccessGate>

        <!-- Delete detail modal -->
        <AccessGate :permission="['nurseRecord.delete']" v-if="canUpdateRecord">
            <ConfirmationModal :show="detailBeingDeleted != null" @close="detailBeingDeleted == null">
                <template #title>
                    Eliminar evento
                </template>

                <template #content>
                    ¿Estás seguro de que deseas eliminar este evento?
                </template>

                <template #footer>
                    <SecondaryButton @click="detailBeingDeleted = null">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton class="ms-3" @click="deleteDetail(selectedDetail.id)">
                        Eliminar
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </AccessGate>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue'
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import moment from "moment/moment";
import 'moment/locale/es';
import AccessGate from '@/Components/Access/AccessGate.vue';
import Modal from '@/Components/Modal.vue';
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import FormatId from '@/Components/FormatId.vue';
import UserSelector from '@/Components/UserSelector.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import ReportIcon from '@/Components/Icons/ReportIcon.vue';
import RestoreIcon from '@/Components/Icons/RestoreIcon.vue';
import ChevronRightIcon from '@/Components/Icons/ChevronRightIcon.vue';
import BackIcon from '@/Components/Icons/BackIcon.vue';
import CirclePlusIcon from '@/Components/Icons/CirclePlusIcon.vue';
import CircleXIcon from '@/Components/Icons/CircleXIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import DialogModal from '@/Components/DialogModal.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import ChevronDown from '@/Components/Icons/ChevronDown.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

export default {
    props: {
        nurseRecord: Object,
        admission_id: Number,
        errors: {
            type: Array,
            default: () => []
        },
        admissions: Array,
        patient: Object,
        nurse: Object,
        bed: Object,
        details: Array,
        canUpdateRecord: Boolean,
        showDeleted: Boolean,
        medicalOrders: {
            type: Array,
            default: () => []
        }
    },
    components: {
        AppLayout,
        Link,
        SignaturePad,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        AccessGate,
        Modal,
        AdmissionSelector,
        FormatId,
        UserSelector,
        TrashIcon,
        EditIcon,
        ReportIcon,
        RestoreIcon,
        ChevronRightIcon,
        BackIcon,
        CirclePlusIcon,
        CircleXIcon,
        BreadCrumb,
        RestoreIcon,
        DialogModal,
        TextAreaInput,
        InputLabel,
        TextInput,
        InputError,
        ChevronDown,
        PersonalizableButton,
        PrimaryButton,
    },
    data() {
        return {
            openAccordion: ref(0),
            recordBeingDeleted: ref(null),
            detailBeingDeleted: ref(null),
            isVisibleEditSign: ref(null),
            showEditAdmission: ref(null),
            showEditNurse: ref(null),
            selectedDetail: ref(null),
            isVisibleDetail: ref(false),
            recordActiveChanging: ref(false),
            signatureError: false,
            showDeletedLocal: this.showDeleted || false,

            formRecord: {
                admission_id: this.nurseRecord.admission_id,
                nurse_id: this.nurseRecord.nurse_id,
                active: this.nurseRecord.active,
            },

            formDetail: useForm({
                nurse_record_id: this.nurseRecord.id,
                medication: null,
                comment: null,
            }),
            formSignature: useForm({
                nurse_sign: '',
                signature: true,
            }),
        }
    },
    methods: {
        submitAdmission() {
            this.$inertia.put(route('nurseRecords.update', this.nurseRecord.id), this.formRecord, {
                preserveScroll: true,
                onFinish: () => {
                    this.recordActiveChanging = false;
                }
            })
            this.showEditAdmission = null
            this.showEditNurse = null
        },
        toggleAccordion(index) {
            if (this.openAccordion === index) {
                this.openAccordion = null // Cierra si ya está abierto
            } else {
                this.openAccordion = index // Abre el acordeón seleccionado
            }
        },
        applyFilters() {
            this.$inertia.get(
                route('nurseRecords.show', this.nurseRecord.id),
                {
                    name: this.filters.name,
                    room: this.filters.room,
                    bed: this.filters.bed
                },
                {
                    preserveScroll: true,
                }
            );
        },
        submit() {
            this.formDetail.post(route('nurseRecordDetails.store'),
                {
                    onSuccess: () => {
                        this.formDetail.reset({
                            nurse_record_id: this.nurseRecord.id,

                        }, 'medication',
                            'comment');
                    },
                    preserveScroll: true,

                });
        },
        toggleShowDeleted() {
            this.showDeletedLocal = !this.showDeletedLocal;
            this.$inertia.get(route('nurseRecords.show', this.nurseRecord.id),
                {
                    showDeleted: this.showDeletedLocal,
                    admission_id: this.admission_id !== 0 ? this.nurseRecord.admission_id : null
                }, {
                preserveScroll: true,
                preserveState: true
            });
        },
        submitSignature() {
            if (!this.formSignature.nurse_sign) {
                this.signatureError = true;
                return;
            }
            this.signatureError = false;
            this.formSignature.put(route('nurseRecords.update', this.nurseRecord.id), {
                preserveScroll: true
            });

            this.formSignature.reset('nurse_sign');
            this.isVisibleEditSign = false;
        },
        submitUpdateDetail() {
            if (!this.selectedDetail.medication || !this.selectedDetail.comment) {
                return;
            }

            this.$inertia.put(route('nurseRecordDetails.update', this.selectedDetail.id), this.selectedDetail, {
                preserveScroll: true
            }),
                this.isVisibleDetail = false
        },
        openEditDetailModal(detail) {
            this.selectedDetail = { ...detail };
            this.originalSuspendedState = detail.suspended_at;
            this.isVisibleDetail = true;
        },
        deleteRecord() {
            this.recordBeingDeleted = null;
            this.recordActiveChanging = true;
            this.$inertia.delete(route('nurseRecords.destroy', this.nurseRecord.id), {
                preserveScroll: true,
                onFinish: () => {
                    this.recordActiveChanging = false;
                }
            });
        },
        restoreRecord() {
            this.recordActiveChanging = true;
            this.formRecord.active = true
            this.submitAdmission();
        },
        deleteDetail(id) {
            this.detailBeingDeleted = null;
            this.isVisibleDetail = false;
            this.$inertia.delete(route('nurseRecordDetails.destroy', id), {
                preserveScroll: true
            });
        },
        restoreDetail(detail) {
            detail.active = true
            this.$inertia.put(route('nurseRecordDetails.update', detail.id),
                {
                    ...detail,
                    showDeleted: this.showDeletedLocal
                }, {
                preserveScroll: true,
                // preserveState: true
            });
            this.isVisibleDetail = false;
        },
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY HH:mm');
        },
        formatDateFromNow(date) {
            return moment(date).fromNow();
        },
        async downloadRecordReport() {
            window.open(route('reports.nurseRecord', { id: this.nurseRecord.id }), '_blank');
        }
    }
}
</script>
