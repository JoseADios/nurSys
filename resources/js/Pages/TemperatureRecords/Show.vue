<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                <BreadCrumb :items="[
                    ...(admission_id ? [{
                        formattedId: { id: admission_id, prefix: 'ING' },
                        route: route('admissions.show', admission_id)
                    }] : []),
                    {
                        text: 'Hojas de temperatura',
                        route: admission_id
                            ? route('temperatureRecords.index', { admission_id: admission_id })
                            : route('temperatureRecords.index')
                    },

                    {
                        formattedId: { id: temperatureRecord.id, prefix: 'TEMP' }
                    }
                ]" />
            </h2>
        </template>

        <div class="container mx-auto px-4 py-8">
            <div
                class="max-w-6xl mx-auto bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/60 rounded-2xl overflow-hidden">
                <!-- Navigation -->
                <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <Link v-if="admission_id" :href="route('admissions.show', admission_id)"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <BackIcon class="size-5" />
                    <span class="font-medium">Volver</span>
                    </Link>

                    <Link v-else :href="route('temperatureRecords.index')"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <BackIcon class="size-5" />
                    <span class="font-medium">Volver</span>
                    </Link>
                    <div class="flex items-center">
                        <button v-if="temperatureRecord.active" @click="downloadRecordReport"
                            class="inline-flex mr-8 items-center px-4 py-2 bg-emerald-500 text-white text-sm rounded-lg hover:to-emerald-600 transition-all duration-200">
                            <ReportIcon class="size-5 mr-1" />
                            <span class="hidden sm:inline-flex">Crear Reporte</span>
                        </button>
                        <AccessGate :permission="['temperatureRecord.delete']">
                            <button v-if="temperatureRecord.active" @click="recordBeingDeleted = true"
                                class="flex items-center space-x-2 text-red-600 hover:text-red-800 transition-colors">
                                <TrashIcon class="size-5" />
                                <span class="font-medium">Eliminar</span>
                            </button>
                            <button v-else @click="restoreRecord"
                                class="flex items-center space-x-2 text-green-600 hover:text-green-800 transition-colors">
                                <RestoreIcon class="size-5" />
                                <span class="font-medium">Restaurar</span>
                            </button>
                        </AccessGate>
                    </div>
                </div>

                <!-- Patient and Record Information -->
                <div class="grid md:grid-cols-2 gap-6 p-8 bg-gray-50 dark:bg-gray-700">
                    <div class="space-y-4">
                        <!-- admission -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60 flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ingreso</h3>
                                <Link :href="route('admissions.show', temperatureRecord.admission_id)" as="button"
                                    class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                                <FormatId :id="temperatureRecord.admission_id" prefix="ING"></FormatId>
                                </Link>
                            </div>
                            <AccessGate :permission="['temperatureRecord.delete']">
                                <button @click="showEditAdmission = true" class="text-blue-500 ml-3">
                                    <EditIcon class="size-5" />
                                </button>
                            </AccessGate>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <Link :href="route('patients.show', temperatureRecord.admission.patient.id)" as="button"
                                class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                            {{ temperatureRecord.admission.patient.first_name }} {{
                                temperatureRecord.admission.patient.first_surname }} {{
                                temperatureRecord.admission.patient.second_surname }}
                            </Link>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ubicación</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            <div v-if="temperatureRecord.admission.bed">
                                Sala {{ temperatureRecord.admission.bed.room }}, Cama {{
                                    temperatureRecord.admission.bed.number
                                }}
                            </div>
                            <div v-else>N/A</div>
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div
                            class="flex justify-between items-center bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Enfermera</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ temperatureRecord.nurse.name }} {{ temperatureRecord.nurse.last_name }}
                                </p>
                            </div>
                            <AccessGate :permission="['temperatureRecord.delete']">
                                <button @click="showEditUser = true" class="text-blue-500 ml-3">
                                    <EditIcon class="size-5" />
                                </button>
                            </AccessGate>
                        </div>


                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de creación</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ formatDate(temperatureRecord.created_at) }}
                            </p>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico de
                                impresión
                            </h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ temperatureRecord.admission.admission_dx }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Chart -->
                <div class="p-4 mx-8 my-4">
                    <TemperatureChart ref="chart" :temperatureData="details" :key="chartKey" :height="100" />
                </div>

                <!-- forms temperatura -->
                <div class="flex flex-col md:flex-row justify-center items-center">
                    <!-- Formulario para actualizar ultimo detalle -->
                    <AccessGate :permission="['temperatureDetail.update']" v-if="lastTemperature" class="w-full p-8">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Ultima temperatura</h3>
                        <form @submit.prevent="updateDetail" class="space-y-4">
                            <div>
                                <InputLabel for="temperature" value="Temperatura" />
                                <input type="number" step="0.1" id="temperature" v-model="formDetailUpdate.temperature"
                                    required
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    placeholder="Temperatura del paciente (°C)" />

                                <div class="pt-8">
                                    <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md
                                                                        hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2
                                                                        transition-colors duration-300">
                                        Actualizar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </AccessGate>

                    <!-- Formulario para agregar nuevo detalle -->
                    <AccessGate :permission="['temperatureDetail.create']"
                        :class="['w-full p-8', { 'w-[50%]': !lastTemperature }]">

                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Temperatura</h3>
                        <form @submit.prevent="submitCreateDetail" :class="['space-y-4', {
                            'md:grid-cols-1 place-items-center': !lastTemperature
                        }]">
                            <div>
                                <InputLabel for="temperature" value="Temperatura" />
                                <input type="number" step="0.1" id="temperature" v-model="formDetail.temperature"
                                    required
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    placeholder="Temperatura del paciente (°C)" />

                                <div class="pt-8 w-full">
                                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md
                                            hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                            transition-colors duration-300">
                                        Agregar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </AccessGate>
                </div>

                <!-- forms eliminaciones -->
                <div class="flex justify-center">
                    <!-- formulario para actualizar ultimas eliminaciones -->
                    <div v-if="lastEliminations && canUpdateElimination" class="p-8 ">
                        <h3 class="text-xl text-center font-semibold text-gray-800 dark:text-white mb-6">Actualizar
                            eliminaciones</h3>
                        <form @submit.prevent="updateEliminations" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label for="evacuations"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Evacuaciones
                                    </label>
                                    <input type="number" id="evacuations" v-model="formEliminationsUpdate.evacuations"
                                        required class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        placeholder="Num. de evacuaciones del paciente" />
                                </div>
                                <div>
                                    <label for="urinations"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Micciones
                                    </label>
                                    <input type="text" id="urinations" v-model="formEliminationsUpdate.urinations"
                                        required class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        placeholder="Num. de micciones del paciente" />
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md
                                    hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2
                                    transition-colors duration-300">
                                    Actualizar
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- formulario para crear elimination records -->
                    <AccessGate v-if="canCreateElimination" :permission="['temperatureDetail.create']">
                        <div class="p-8 ">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar registro de
                                eliminación
                            </h3>

                            <form @submit.prevent="submitCreateEliminations" class="space-y-4">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="evacuations"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Evacuaciones
                                        </label>
                                        <input type="number" id="evacuations" v-model="formEliminations.evacuations"
                                            required class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            placeholder="Num. de evacuaciones del paciente" />
                                    </div>
                                    <div>
                                        <label for="urinations"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Micciones
                                        </label>
                                        <input type="text" id="urinations" v-model="formEliminations.urinations"
                                            required class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            placeholder="Num. de micciones del paciente" />
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md
                                    hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                    transition-colors duration-300">
                                        Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </AccessGate>

                    <!-- si no puede crear ni actualizar mostrar que ya otro enfermero ha registrado una firma en este turno que no puede hacer nada -->
                    <div v-if="!lastTemperature && !canCreateElimination && !canUpdateElimination" class="p-8">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Información</h3>
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            No puede realizar ninguna acción.
                        </p>
                    </div>

                </div>


                <section class=" p-8 space-y-4  bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Firma</h3>

                    <!-- mostrar imagen firma -->
                    <div v-show="!isVisibleEditSign">
                        <div class="flex items-center flex-col justify-center">
                            <img class="w-full max-w-md" v-if="temperatureRecord.nurse_sign"
                                :src="`/storage/${temperatureRecord.nurse_sign}`" alt="Firma">
                            <div v-else>
                                <div class="text-gray-500 dark:text-gray-400 my-16">
                                    No hay firma disponible
                                </div>
                            </div>
                            <AccessGate :permission="['temperatureRecord.update']" v-if="canUpdateSignature">
                                <button @click="isVisibleEditSign = true"
                                    class="mt-4 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                                    Editar</button>
                            </AccessGate>
                        </div>
                    </div>
                    <!-- Campo de firma -->
                    <AccessGate :permission="['temperatureRecord.update']">
                        <div v-show="isVisibleEditSign" class="my-4">
                            <form @submit.prevent="submitSignature" class="flex items-center flex-col justify-center">

                                <SignaturePad v-model="formSignature.nurse_sign" input-name="nurse_sign"
                                    class="w-full max-w-lg lg:max-w-md" />
                                <div v-if="signatureError" class="text-red-500 text-sm mt-2">La firma es obligatoria.
                                </div>

                                <div class="my-4">
                                    <button type="button"
                                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                        @click="isVisibleEditSign = false">Cancelar</button>
                                    <button
                                        class="mr-6 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900"
                                        type="submit">Guardar firma</button>
                                </div>
                            </form>
                        </div>
                    </AccessGate>
                </section>

            </div>
        </div>


        <!-- Change admission modal -->
        <AccessGate :permission="['temperatureRecord.delete']">
            <Modal :closeable="true" :show="showEditAdmission != null" @close="showEditAdmission == null">
                <div
                    class="relative overflow-hidden border border-gray-200 dark:border-gray-700/60 sm:rounded-xl my-8 lg:mx-8 bg-white dark:bg-gray-800 p-4">
                    <form @submit.prevent="submitUpdateRecord" class="max-w-3xl mx-auto">

                        <AdmissionSelector @update:admission="formRecord.admission_id = $event"
                            :selected-admission-id="temperatureRecord.admission_id" :doesnt-have-temperature-r="true" />

                        <!-- Botones -->
                        <div class="flex justify-end mt-4 space-x-3">
                            <button type="button" @click="showEditAdmission = null"
                                class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800 transition"
                                :disabled="!formRecord.admission_id">
                                Aceptar
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>
        </AccessGate>

        <!-- Change patient modal -->
        <!-- Change admission modal -->
        <AccessGate :permission="['temperatureRecord.delete']">
            <Modal :closeable="true" :show="showEditUser != null" @close="showEditUser == null">
                <div
                    class="relative overflow-hidden border border-gray-200 dark:border-gray-700/60 sm:rounded-xl my-8 lg:mx-8 bg-white dark:bg-gray-800 p-4">
                    <form @submit.prevent="submitUpdateRecord" class="max-w-3xl mx-auto">

                        <UserSelector roles="nurse" :selected-user-id="temperatureRecord.nurse_id"
                            @update:user="formRecord.nurse_id = $event" />

                        <!-- Botones -->
                        <div class="flex justify-end mt-4 space-x-3">
                            <button type="button" @click="showEditUser = null"
                                class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800 transition"
                                :disabled="!formRecord.nurse_id">
                                Aceptar
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>
        </AccessGate>

        <!-- delete modal -->
        <AccessGate :permission="['temperatureRecord.delete']">
            <ConfirmationModal :show="recordBeingDeleted != null" @close="recordBeingDeleted = null">
                <template #title>
                    Eliminar Ingreso
                </template>

                <template #content>
                    ¿Estás seguro de que deseas eliminar este ingreso?
                </template>

                <template #footer>
                    <SecondaryButton @click="recordBeingDeleted = null">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton class="ms-3" @click="deleteRecord">
                        Eliminar
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </AccessGate>

    </AppLayout>
</template>

<script>
import TemperatureChart from '@/Components/Charts/TemperatureChart.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue'
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
import moment from "moment/moment";
import 'moment/locale/es';
import 'moment/locale/es';
import Modal from '@/Components/Modal.vue';
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import FormatId from '@/Components/FormatId.vue';
import UserSelector from '@/Components/UserSelector.vue';
import BackIcon from '@/Components/Icons/BackIcon.vue';
import ChevronRightIcon from '@/Components/Icons/ChevronRightIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import ReportIcon from '@/Components/Icons/ReportIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import RestoreIcon from '@/Components/Icons/RestoreIcon.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

export default {
    props: {
        temperatureRecord: Object,
        details: Array,
        admission_id: String,
        lastTemperature: Object,
        lastEliminations: Object,
        previousUrl: String,
        canCreateElimination: Boolean,
        canUpdateElimination: Boolean,
        canUpdateSignature: Boolean,
    },
    components: {
        AppLayout,
        Link,
        TemperatureChart,
        SignaturePad,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        AccessGate,
        Modal,
        AdmissionSelector,
        FormatId,
        UserSelector,
        BackIcon,
        ChevronRightIcon,
        TrashIcon,
        ReportIcon,
        EditIcon,
        BreadCrumb,
        RestoreIcon,
        TextInput,
        InputLabel
    },
    data() {
        return {
            recordBeingDeleted: ref(null),
            showEditAdmission: ref(null),
            showEditUser: ref(null),
            isVisibleEditSign: ref(null),
            isVisibleEditDiagnosis: false,
            signatureError: false,
            chartKey: 0,

            formDetail: {
                temperature_record_id: this.temperatureRecord.id,
                temperature: 37,
            },
            formEliminations: {
                temperature_record_id: this.temperatureRecord.id,
                evacuations: 1,
                urinations: 1,
            },
            formSignature: {
                nurse_sign: this.temperatureRecord.nurse_sign,
                signature: true,
            },
            formRecord: {
                admission_id: this.temperatureRecord.admission_id,
                nurse_id: this.temperatureRecord.nurse_id,
                active: this.temperatureRecord.active
            },
        }
    },

    computed: {
        formDetailUpdate() {
            return {
                temperature_record_id: this.temperatureRecord.id,
                temperature: this.lastTemperature ? this.lastTemperature.temperature : null,
            };
        },
        formEliminationsUpdate() {
            return {
                temperature_record_id: this.temperatureRecord.id,
                evacuations: this.lastEliminations ? this.lastEliminations.evacuations : null,
                urinations: this.lastEliminations ? this.lastEliminations.urinations : null,
            };
        },

    },
    methods: {
        submitCreateDetail() {
            this.$inertia.post(route('temperatureDetails.store'),
                this.formDetail,
                {
                    onSuccess: () => {
                        this.formDetail = {
                            temperature_record_id: this.temperatureRecord.id,
                            temperature: 37,
                            evacuations: 1,
                            urinations: 1,
                        };
                        this.chartKey++;
                    },
                    preserveScroll: true,
                });
        },
        submitCreateEliminations() {
            this.$inertia.post(route('eliminationRecords.store'),
                this.formEliminations,
                {
                    onSuccess: () => {
                        this.formEliminations = {
                            temperature_record_id: this.temperatureRecord.id,
                            evacuations: 1,
                            urinations: 1,
                        };
                        this.chartKey++;
                    },
                    preserveScroll: true,
                });
        },
        updateDetail() {
            this.$inertia.put(route('temperatureDetails.update', this.lastTemperature.id), this.formDetailUpdate, {
                onSuccess: () => {
                    this.chartKey++;
                },
                preserveScroll: true,
            });
        },
        updateEliminations() {
            this.$inertia.put(route('eliminationRecords.update', this.lastEliminations.id), this.formEliminationsUpdate, {
                onSuccess: () => {
                    this.chartKey++;
                },
                preserveScroll: true,
            });
        },
        submitUpdateRecord() {
            this.showEditAdmission = null
            this.showEditUser = null
            this.$inertia.put(route('temperatureRecords.update', this.temperatureRecord.id), this.formRecord)
            this.isVisibleEditDiagnosis = false
        },
        submitSignature() {
            if (!this.formSignature.nurse_sign) {
                this.signatureError = true;
                return;
            }
            this.signatureError = false;
            this.$inertia.put(route('temperatureRecords.update', this.temperatureRecord.id), this.formSignature, {
                preserveScroll: true
            });
            this.isVisibleEditSign = false
        },
        toggleEditRecord() {
            this.isVisibleEditDiagnosis = !this.isVisibleEditDiagnosis
        },
        deleteRecord() {
            this.recordBeingDeleted = null
            this.$inertia.delete(route('temperatureRecords.destroy', this.temperatureRecord.id));
        },
        restoreRecord() {
            this.formRecord.active = true
            this.submitUpdateRecord()
        },
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY HH:mm');
        },

        async downloadRecordReport() {
            window.open(route('reports.temperatureRecord', { id: this.temperatureRecord.id }), '_blank');
        }
    },
    mounted() {
        moment.locale('es'); // Cambia el idioma a español
    }
}
</script>
