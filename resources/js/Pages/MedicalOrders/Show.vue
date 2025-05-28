<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                <BreadCrumb :items="[
                    ...(admission_id ? [{
                        formattedId: { id: admission_id, prefix: 'ING' },
                        route: route('admissions.show', admission_id)
                    }] : []),
                    {
                        text: 'Órdenes Médicas',
                        route: medicalOrder.id
                            ? route('medicalOrders.index', { id: medicalOrder.id })
                            : route('medicalOrders.index')
                    },

                    {
                        formattedId: { id: medicalOrder.id, prefix: 'ORD' }
                    }
                ]" />
            </h2>

        </template>


        <div class="container mx-auto px-4 py-8">

            <div
                class="max-w-6xl mx-auto bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/60 rounded-2xl overflow-hidden">
                <!-- Navigation -->
                <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <Link :href="route('medicalOrders.index')"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <BackIcon class="size-5" />Volver
                    </Link>
                   <div class="flex items-center gap-2">
                        <PersonalizableButton v-if="medicalOrder.active" @click="downloadRecordReport" color="emerald">
                        <ReportIcon class="size-5 " />
                        Crear Reporte
                        </PersonalizableButton>
                          <AccessGate :permission="['medicalOrder.delete']">
                        <DangerButton v-if="medicalOrder.active" @click="recordBeingDeleted = true">

                            <TrashIcon class="size-5 mr-2" />
                            <span class="font-medium ">Eliminar</span>

                        </DangerButton>
                            <PersonalizableButton v-else @click="restoreRecord" class="gap-2" color="green"
                            >
                            <RestoreIcon class="size-5" />
                            <span class="hidden sm:inline-flex">Restaurar</span>
                            </PersonalizableButton>
                            </AccessGate>
                    </div>
                </div>

                <!-- Patient and Record Information -->
                <div class="grid md:grid-cols-2 gap-6 p-8 bg-gray-50 dark:bg-gray-700">
                    <div class="space-y-4">
                        <div v-if="!isVisibleAdm"
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60 flex justify-between">
                            <div class="">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ingreso</h3>
                                <Link :href="route('admissions.show', medicalOrder.admission_id)" as="button"
                                    class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                                <FormatId :id="medicalOrder.admission_id" prefix="ING"></FormatId>
                                </Link>

                            </div>
                            <AccessGate :role="['admin']">
                                <button @click="showEditAdmission = true" class="text-blue-500 mt-6  ">
                                    <EditIcon class="size-5" />
                                </button>
                            </AccessGate>

                        </div>

                        <div v-if="isVisibleAdm"
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <form @submit.prevent="submitAdmission">

                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Seleccionar
                                    Ingreso</h3>
                                <select v-model="formAdmission.admission_id"
                                    class="w-full text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option :value="admission.id" v-for="admission in admissions" :key="admission.id">
                                        {{ admission.created_at }}
                                        {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                                            admission.patient.second_surname }}
                                        Cama {{ admission.bed?.number || "N/A" }}, Sala {{ admission.bed?.room ||
                                            "N/A" }}
                                    </option>
                                </select>
                                <div class="mt-3">
                                    <button
                                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                        @click="toggleEditAdmission">Cancelar</button>

                                    <button type="submit"
                                        class="focus:outline-none text-white bg-green-800 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">
                                        Aceptar</button>
                                </div>

                            </form>

                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <Link :href="route('patients.show', medicalOrder.admission.patient.id)" as="button"
                                class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                            {{ medicalOrder.admission.patient.first_name }} {{
                                medicalOrder.admission.patient.first_surname
                            }} {{ medicalOrder.admission.patient.second_surname }}
                            </Link>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Sala</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                Sala {{ medicalOrder.admission.bed?.room || "N/A" }}, Cama {{ medicalOrder.admission.bed?.number || "N/A" }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div
                            class="flex justify-between items-center bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Doctor/a</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ doctor.name }} {{ doctor.last_name
                                    }}
                                </p>
                            </div>
                            <AccessGate :role="['admin']">
                                <button @click="showEditDoctor = true" class="text-blue-500 flex">
                                    <EditIcon class="size-5" />
                                </button>
                            </AccessGate>
                        </div>
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de Registro
                            </h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ formatDate(medicalOrder.created_at) }}
                            </p>
                        </div>

                        <div v-if="$page.props.errors.message" class="alert alert-danger">
                            {{ $page.props.errors.message }}
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <!-- Formulario para agregar nuevo detalle -->
                    <AccessGate :permission="['medicalOrder.delete']">
                <div class="p-8 ">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Nuevos Detalles
                    </h3>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid md:grid-cols-[2fr_1fr] gap-4">
                            <div>
                                <label for="orden"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Órden <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="order" v-model="formDetail.order" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                    placeholder="Orden médica" />
                            </div>

                            <div>
                                <label for="regime"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Régimen
                                </label>
                                <select id="regime" v-model="formDetail.regime"
                                    class="w-full text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option :value="regime.name" v-for="regime in regimes" :key="regime.id">
                                        {{ regime.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-4">
                             <PersonalizableButton   type="submit"  :class="{ 'opacity-25': formDetail.processing }" :loading="formDetail.processing"  :disabled="formDetail.processing"  size="large" class="w-full">
                            Agregar Detalle
                        </PersonalizableButton>
                        </div>
                    </form>
                </div>
                </AccessGate>

                <!-- Nurse Record Details -->
                <div class="p-8 space-y-4  bg-gray-50 dark:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Detalles del Registro
                        </h3>
                        <div v-if="medicalOrder.active">
                            <AccessGate :permission="['medicalOrder.delete']">
                            <button @click="toggleShowDeleted"
                                class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap ml-auto"
                                :class="{
                                    'bg-red-500 hover:bg-red-600 text-white': showDeleted,
                                    'bg-gray-100 hover:bg-gray-100 text-gray-800 dark:bg-gray-800 dark:hover:bg-gray-600 dark:text-gray-200': !showDeleted
                                }">
                                {{ showDeleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                                <svg class="ml-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path v-if="showDeleted" fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                                        clip-rule="evenodd" />
                                    <path v-else fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            </AccessGate>
                        </div>
                    </div>
                    <div v-for="detail in details" :key="detail.id" :class="[
                        'rounded-lg p-4 shadow-md flex justify-between items-center transition-colors',
                        detail.suspended_at
                            ? 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 hover:bg-red-100 dark:hover:bg-red-900/30'
                            : 'bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-900'
                    ]">
                        <div class="flex-grow">
                            <div class="font-semibold text-gray-900 dark:text-white">
                                {{ detail.order }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ detail.regime }}
                            </div>
                            <div v-if="detail.suspended_at"
                                class="text-sm text-red-600 dark:text-red-400 mt-1 font-medium flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                Suspendido: {{ detail.suspended_at }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ formatDate(detail.created_at) }}
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                              <AccessGate :permission="['medicalOrder.update']">
                            <button @click="openEditModal(detail)"
                                class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                               <EditIcon class="size-5" />
                                <span class="font-medium">Editar</span>
                            </button>
                            </AccessGate>
                        </div>
                    </div>

                    <div v-if="details.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No hay eventos de ordenes disponibles
                    </div>
                </div>
                    <AccessGate :permission="['medicalOrder.update']">
                <section id="bottom" class="p-8 space-y-4  bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Firma</h3>

                <!-- mostrar imagen firma -->
                <div v-show="!isVisibleEditSign" class="my-4 flex items-center flex-col justify-center">
                    <div>

                        <img v-if="medicalOrder.doctor_sign" :src="`/storage/${medicalOrder.doctor_sign}`" alt="Firma">
                        <div v-else>
                            <div class="text-gray-500 dark:text-gray-400 my-16">
                                No hay firma disponible
                            </div>
                        </div>
                    </div>
                    <PersonalizableButton @click="isVisibleEditSign = true">
                        Editar</PersonalizableButton>
                </div>

                <!-- Campo de firma -->
                <div v-show="isVisibleEditSign" class="my-4">
                    <form @submit.prevent="submitSignature" class=" flex items-center flex-col justify-center">
                        <label for="doctor_sign"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Firma
                        </label>

                        <SignaturePad class="w-full max-w-lg lg:max-w-md" v-model="formSignature.doctor_sign"
                            input-name="doctor_sign" />
                        <div v-if="signatureError" class="text-red-500 text-sm mt-2">La firma es obligatoria.</div>

                        <div class="my-4 ">
                             <SecondaryButton class="mr-2"  @click="isVisibleEditSign = false"
                                        >
                                        Cerrar
                                    </SecondaryButton>

                             <PrimaryButton type="submit" :class="{ 'opacity-25': formSignature.processing }":is-loading="formSignature.processing"
                                        :disabled="formSignature.processing"
                                       >
                                       Aceptar
                                    </PrimaryButton>


                        </div>
                    </form>
                </div>
            </section>
            </AccessGate>
            </div>

        </div>

        <!-- Change admission modal -->
        <AccessGate :permission="['medicalOrder.delete']">
            <Modal :closeable="true" :show="showEditAdmission != null" @close="showEditAdmission == null">
                <div
                    class="relative overflow-hidden shadow-lg sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
                    <form @submit.prevent="submitUpdateRecord" class="max-w-3xl mx-auto">

                        <AdmissionSelector @update:admission="formRecord.admission_id = $event"
                            :selected-admission-id="medicalOrder.admission_id" :doesnt-have-medical-order="true" />

                        <!-- Botones -->
                        <div class="flex justify-end mt-4 space-x-3">

                              <SecondaryButton  @click="showEditAdmission = null"
                                        >
                                        Cerrar
                                    </SecondaryButton>

                             <PrimaryButton type="submit" :class="{ 'opacity-25': formRecord.processing }":is-loading="formRecord.processing"
                                        :disabled="formRecord.processing"
                                       >
                                       Aceptar
                                    </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AccessGate>

        <DialogModal :show="isVisibleDetail" @close="isVisibleDetail = false">
            <!-- Header del modal -->
            <template #title>
                Editar orden
            </template>

            <!-- Contenido del modal -->
            <template #content>
                <div class="">
                    <form>
                        <div class="grid md:grid-cols-[2fr_1fr] gap-4">
                            <div>
                                <label for="orden"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Órden <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="order" v-model="selectedDetail.order" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                    placeholder="Orden médica" />
                            </div>

                            <div>
                                <label for="regime"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Régimen
                                </label>
                                <select id="regime" v-model="selectedDetail.regime"
                                    class="w-full text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option :value="regime.name" v-for="regime in regimes" :key="regime.id">
                                        {{ regime.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="flex items-center me-4">
                                <input :checked="selectedDetail.suspended_at"
                                    @change="selectedDetail.suspended_at = $event.target.checked ? new Date().toISOString().slice(0, 19).replace('T', ' ') : null"
                                    id="suspended_at" type="checkbox" value=""
                                    class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="red-checkbox"
                                    class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Suspender</label>
                            </div>

                        </div>
                    </form>
                </div>
            </template>

            <!-- Footer del modal -->
            <template #footer>
                <div class="flex items-center gap-2">
                <AccessGate :permission="['medicalOrder.delete']">
                <DangerButton v-if="selectedDetail.active"  type="button" @click="detailBeingDeleted = true">
                      <TrashIcon class="size-5 mr-2" />
                     Eliminar
                </DangerButton>

                <PersonalizableButton v-if="!selectedDetail.active":class="{ 'opacity-25': modalform.processing }":is-loading="modalform.processing"
                                        :disabled="modalform.processing" type="button" @click="restoreDetail" color="green">
                     <RestoreIcon class="size-5 mr-2" />
                    Restaurar
                </PersonalizableButton>
                 </AccessGate>
                 <PrimaryButton type="submit" @click="submitUpdateDetail" :class="{ 'opacity-25': modalform.processing }":is-loading="modalform.processing"
                                        :disabled="modalform.processing" >
                    Actualizar
                </PrimaryButton>
                <SecondaryButton type="button" @click="isVisibleDetail = false">
                    Cerrar
                </SecondaryButton>

                </div>
            </template>
        </DialogModal>

        <ConfirmationModal :show="recordBeingDeleted != null || detailBeingDeleted != null"
            @close="recordBeingDeleted = null; detailBeingDeleted = null">
            <template #title>
                Eliminar registro
            </template>

            <template v-if="recordBeingDeleted" #content>
                ¿Estás seguro de que deseas eliminar este registro?
            </template>
            <template v-else #content>
                ¿Estás seguro de que deseas eliminar este detalle?
            </template>

            <template #footer>
                <SecondaryButton @click="recordBeingDeleted = null; detailBeingDeleted = null">
                    Cancelar
                </SecondaryButton>

                <DangerButton v-if="recordBeingDeleted" class="ms-3" @click="deleteRecord">
                      <TrashIcon class="size-5 mr-2" />
                    Eliminar registro
                </DangerButton>
                <DangerButton v-else class="ms-3" @click="deleteDetail(); detailBeingDeleted = null;">
                      <TrashIcon class="size-5 mr-2" />
                    Eliminar detalle
                </DangerButton>
            </template>
        </ConfirmationModal>
        <Modal :closeable="true" :show="showEditDoctor != null" @close="showEditDoctor = null">
            <div class="relative overflow-hidden sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
                <form @submit.prevent="submitAdmission" class="max-w-3xl mx-auto">

                    <UserSelector roles="nurse" :selected-user-id="medicalOrder.doctor_id"
                        @update:user="formRecord.doctor_id = $event" />
                    <!-- Botones -->
                    <div class="flex justify-end mt-4 space-x-3">

                        <SecondaryButton  @click="showEditDoctor = null"
                                        >
                                        Cerrar
                                    </SecondaryButton>

                             <PrimaryButton type="submit" :class="{ 'opacity-25': formRecord.processing }":is-loading="formRecord.processing"
                                        :disabled="formRecord.processing"
                                       >
                                       Aceptar
                                    </PrimaryButton>

                    </div>
                </form>
            </div>

        </Modal>

    </AppLayout>
</template>

<script>
import DialogModal from '@/Components/DialogModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link, useForm
} from '@inertiajs/vue3';
import {
    ref
} from 'vue';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import FormatId from '@/Components/FormatId.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import Modal from '@/Components/Modal.vue';
import BackIcon from '@/Components/Icons/BackIcon.vue';
import ReportIcon from '@/Components/Icons/ReportIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import moment from 'moment/moment';
import UserSelector from '@/Components/UserSelector.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import RestoreIcon from '@/Components/Icons/RestoreIcon.vue';
import 'moment/locale/es';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
export default {
    components: {
        AppLayout,
        Link,
        PersonalizableButton,
        DialogModal,
        SignaturePad,
        ConfirmationModal,
        PrimaryButton,
        DangerButton,
        SecondaryButton,
        FormatId,
        EditIcon,
        AccessGate,
        AdmissionSelector,
        Modal,
        BackIcon,
        ReportIcon,
        BreadCrumb,
        TrashIcon,
        RestoreIcon,
        UserSelector

    },
    props: {
        medicalOrder: Object,
        errors: [Array, Object],
        admissions: Array,
        details: Array,
        regimes: Array,
        filters: Object,
         doctor: Object,
          admission_id: Number,

    },
    data() {
        return {
            recordBeingDeleted: ref(null),
            detailBeingDeleted: ref(null),
            selectedDetail: ref(null),
             modalform: useForm( {
                selectedDetail: this.selectedDetail,

            }),
            isVisibleDetail: ref(false),
            originalSuspendedState: ref(null),
            isVisibleEditSign: ref(null),
            signatureError: false,
            showEditAdmission: ref(null),
            showEditDoctor: ref(null),
            isVisibleAdm: false,
            formAdmission: {
                admission_id: this.medicalOrder.admission_id,
                active: this.medicalOrder.active
            },
            formRecord: useForm({
                admission_id: this.medicalOrder.admission_id,
                doctor_id: this.medicalOrder.doctor_id,
                impression_diagnosis: this.medicalOrder.impression_diagnosis,
                active: this.medicalOrder.active
            }),
            formDetail: useForm ({
                medical_order_id: this.medicalOrder.id,
                order: null,
                regime: null,
                active: null,
            }),
            formSignature: useForm({
                doctor_sign: this.medicalOrder.doctor_sign,
                signature: true,
            }),
            showDeleted: this.filters.show_deleted,
        }
    },
    methods: {
        toggleShowDeleted() {
            this.showDeleted = !this.showDeleted;
            this.$inertia.get(route('medicalOrders.show', {
                medicalOrder: this.medicalOrder
            }), {
                showDeleted: this.showDeleted
            }, {
                preserveState: true,
                preserveScroll: true
            });
        },
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY HH:mm');
        },
        submitUpdateRecord() {
            this.showEditAdmission = null;
            this.showEditDoctor = null
            this.formRecord.put(route('medicalOrders.update', this.medicalOrder.id))
            this.isVisibleEditDiagnosis = false
        },
        toggleEditAdmission() {
            this.isVisibleAdm = !this.isVisibleAdm;
        },
        submitAdmission() {
            this.showEditDoctor = null;
            this.formRecord.put(route('medicalOrders.update', this.medicalOrder.id), {
                preserveScroll: true
            })
            this.isVisibleAdm = false;

        },
        async downloadRecordReport() {
            window.open(route('reports.medicalOrder', {
                id: this.medicalOrder.id
            }), '_blank');
        },
        submit() {
            this.formDetail.post(route('medicalOrderDetails.store'),

                 {
                      preserveScroll: true,
                onSuccess: () => {
              this.formDetail.reset();

                }

            });
        },
        submitCreateRecord() {

            if (this.medicalOrder.admission.medication_record) {

                this.$inertia.get(route('medicationRecordDetails.create', {
                    medicationRecordId: this.medicalOrder.admission.medication_record.id
                }))
            } else {

                this.$inertia.get(route('medicationRecords.create', {
                    admission: this.medicalOrder.admission.id
                }))
            }

        },

        submitUpdateDetail() {
            this.modalform.put(route('medicalOrderDetails.update', this.selectedDetail.id), this.selectedDetail, {
                preserveScroll: true,
                preserveState: true
            }),
                this.isVisibleAdm = false
            this.isVisibleDetail = false
        },
        submitSignature() {
            if (!this.formSignature.doctor_sign) {
                this.signatureError = true;
                return;
            }
            this.signatureError = false;
            this.formSignature.put(route('medicalOrders.update', this.medicalOrder.id), {
                preserveScroll: true,
                preserveState: true
            });
            this.isVisibleEditSign = false
        },
        openEditModal(detail) {
            this.selectedDetail = {
                ...detail
            };
            this.originalSuspendedState = detail.suspended_at;
            this.isVisibleDetail = true;
        },
        deleteRecord() {
            this.recordBeingDeleted = null;
            this.$inertia.delete(route('medicalOrders.destroy', this.medicalOrder.id));
        },
        deleteDetail() {
            this.detailBeingDeleted = false
            this.isVisibleAdm = false
            this.isVisibleDetail = false
            this.$inertia.delete(route('medicalOrderDetails.destroy', this.selectedDetail.id), {
                preserveScroll: true,
                preserveState: true
            });
        },
        restoreDetail() {
            this.selectedDetail.active = true
            this.submitUpdateDetail()
        },
        restoreRecord() {
            this.formAdmission.active = true
            this.submitAdmission()
        }
    }
}
</script>

<style scoped>
.alert {
    color: white;
    background: red;
    padding: 10px;
    margin: 10px 0;
    border-radius: 10;
}
</style>
