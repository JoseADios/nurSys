<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Hoja de Temperatura
            </h2>
        </template>

        <div class="ml-4 my-2 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">

            <div v-if="admission_id" class="inline-flex items-center">
                <Link :href="route('admissions.show', admission_id)"
                    class="inline-flex items-center hover:text-blue-600  dark:hover:text-white">
                <FormatId :id="admission_id" prefix="ING"></FormatId>
                </Link>
                <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" fill="none"
                    viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 9 4-4-4-4" />
                </svg>
            </div>

            <div class="ml-2 inline-flex items-center">
                Hoja de temperatura
                <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 9 4-4-4-4" />
                </svg>
                <FormatId :id="temperatureRecord.id" prefix="ENF"></FormatId>
            </div>
        </div>

        <!-- <div class="text-white">Datos {{ temperatureRecord.id }}</div> -->

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden">
                <!-- Navigation -->
                <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <Link v-if="admission_id" :href="route('admissions.show', admission_id)"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Volver</span>
                    </Link>

                    <Link v-else :href="route('temperatureRecords.index')"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Volver</span>
                    </Link>
                    <div class="flex items-center">
                        <button v-if="temperatureRecord.active" @click="downloadRecordReport"
                            class="inline-flex mr-8 items-center px-4 py-2 bg-emerald-500 text-white text-sm rounded-lg hover:to-emerald-600 transition-all duration-200">
                            📄 Crear Reporte </button>
                        <AccessGate :permission="['temperatureRecord.delete']">
                            <button v-if="temperatureRecord.active" @click="recordBeingDeleted = true"
                                class="flex items-center space-x-2 text-red-600 hover:text-red-800 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H3a1 1 0 100 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm2 4a1 1 0 011 1v7a1 1 0 11-2 0V7a1 1 0 011-1zm4 0a1 1 0 011 1v7a1 1 0 11-2 0V7a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Eliminar</span>
                            </button>
                            <button v-else @click="restoreRecord"
                                class="flex items-center space-x-2 text-green-600 hover:text-green-800 transition-colors">
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
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ingreso</h3>
                                <Link :href="route('admissions.show', temperatureRecord.admission_id)" as="button"
                                    class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                                <FormatId :id="temperatureRecord.admission_id" prefix="ING"></FormatId>
                                </Link>
                            </div>
                            <AccessGate :permission="['temperatureRecord.delete']">
                                <button @click="showEditAdmission = true" class="text-blue-500 ml-3">Edit</button>
                            </AccessGate>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <Link :href="route('patients.show', temperatureRecord.admission.patient.id)" as="button"
                                class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                            {{ temperatureRecord.admission.patient.first_name }} {{
                                temperatureRecord.admission.patient.first_surname }} {{
                                temperatureRecord.admission.patient.second_surname }}
                            </Link>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
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
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Enfermera</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ temperatureRecord.nurse.name }} {{ temperatureRecord.nurse.last_name }}
                            </p>
                        </div>


                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de creación</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ formatDate(temperatureRecord.created_at) }}
                            </p>
                        </div>

                        <AccessGate :permission="['temperatureRecord.update']">
                            <div v-if="!isVisibleEditDiagnosis"
                                class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md flex justify-between">
                                <div class="">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico de
                                        impresión
                                    </h3>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ temperatureRecord.impression_diagnosis }}
                                    </p>
                                </div>
                                <button @click="toggleEditRecord" class="text-blue-500 mr-3">Edit</button>
                            </div>

                            <div v-if="isVisibleEditDiagnosis"
                                class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                                <form @submit.prevent="submitUpdateRecord">

                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico de
                                        impresión
                                    </h3>
                                    <textarea v-model="formRecord.impression_diagnosis"
                                        class="w-full text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </textarea>
                                    <div class="mt-3">
                                        <button
                                            class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                            @click="toggleEditRecord">Cancelar</button>

                                        <button type="submit"
                                            class="focus:outline-none text-white bg-green-800 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">
                                            Aceptar</button>
                                    </div>
                                </form>
                            </div>
                        </AccessGate>
                        <AccessGate :except-permission="['temperatureRecord.update']">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico de
                                    impresión
                                </h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ temperatureRecord.impression_diagnosis }}
                                </p>
                            </div>
                        </AccessGate>
                    </div>
                </div>

                <!-- Chart -->
                <div class="p-4 mx-8 my-4">
                    <TemperatureChart ref="chart" :temperatureData="details" :key="chartKey" :height="100" />
                </div>

                <!-- ultima temperatura -->

                <!-- Formulario para actualizar ultimo detalle -->
                <AccessGate :permission="['temperatureDetail.update']">
                    <div v-if="lastTemperature" class="p-8 ">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Ultima temperatura</h3>
                        <form @submit.prevent="updateDetail" class="space-y-4">
                            <div class="grid md:grid-cols-3 gap-4">
                                <div>
                                    <label for="temperature"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Temperatura
                                    </label>
                                    <input type="number" step="0.1" id="temperature"
                                        v-model="formDetailUpdate.temperature" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    dark:bg-gray-800 dark:text-white" placeholder="Temperatura del paciente (°C)" />
                                </div>

                                <div>
                                    <label for="evacuations"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Evacuaciones
                                    </label>
                                    <input type="number" id="evacuations" v-model="formDetailUpdate.evacuations"
                                        required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    dark:bg-gray-800 dark:text-white"
                                        placeholder="Num. de evacuaciones del paciente" />
                                </div>
                                <div>
                                    <label for="urinations"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Micciones
                                    </label>
                                    <input type="text" id="urinations" v-model="formDetailUpdate.urinations" required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    dark:bg-gray-800 dark:text-white" placeholder="Num. de micciones del paciente" />
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md
                                hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2
                                transition-colors duration-300">
                                    Actualizar Temperatura
                                </button>
                            </div>
                        </form>
                    </div>
                </AccessGate>

                <!-- Formulario para agregar nuevo detalle -->
                <AccessGate :permission="['temperatureDetail.create']">
                    <div v-if="canCreateDetail" class="p-8 ">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Temperatura</h3>

                        <form @submit.prevent="submitCreateDetail" class="space-y-4">
                            <div class="grid md:grid-cols-3 gap-4">
                                <div>
                                    <label for="temperature"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Temperatura
                                    </label>
                                    <input type="number" step="0.1" id="temperature" v-model="formDetail.temperature"
                                        required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    dark:bg-gray-800 dark:text-white" placeholder="Temperatura del paciente (°C)" />
                                </div>

                                <div>
                                    <label for="evacuations"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Evacuaciones
                                    </label>
                                    <input type="number" id="evacuations" v-model="formDetail.evacuations" required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    dark:bg-gray-800 dark:text-white"
                                        placeholder="Num. de evacuaciones del paciente" />
                                </div>
                                <div>
                                    <label for="urinations"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Micciones
                                    </label>
                                    <input type="text" id="urinations" v-model="formDetail.urinations" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    dark:bg-gray-800 dark:text-white" placeholder="Num. de micciones del paciente" />
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md
                                hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                transition-colors duration-300">
                                    Agregar Temperatura
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TODO: MODIFICAR ESTA PARTE PARA USAR POLICIES -->
                    <!-- si no puede crear ni actualizar mostrar que ya otro enfermero ha registrado una firma en este turno que no puede hacer nada -->
                    <div v-if="!canCreateDetail && !lastTemperature" class="p-8">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Información</h3>
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Ya otro enfermero ha registrado una firma en este turno. No puede realizar ninguna acción.
                        </p>
                    </div>
                </AccessGate>


                <section class=" p-8 space-y-4  bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Firma</h3>

                    <!-- mostrar imagen firma -->
                    <div v-show="!isVisibleEditSign">
                        <div class="flex items-center flex-col justify-center">

                            <img v-if="temperatureRecord.nurse_sign" :src="`/storage/${temperatureRecord.nurse_sign}`"
                                alt="Firma">
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
                                <!-- <label for="nurse_sign"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Firma
                                </label> -->

                                <SignaturePad v-model="formSignature.nurse_sign" input-name="nurse_sign" />
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
                    class="relative overflow-hidden shadow-lg sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
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

export default {
    props: {
        temperatureRecord: Object,
        details: Array,
        admission_id: String,
        lastTemperature: Object,
        canCreateDetail: Boolean,
        previousUrl: String,
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
        FormatId
    },
    data() {
        return {
            isVisibleEditAdm: ref(null),
            recordBeingDeleted: ref(null),
            showEditAdmission: ref(null),
            isVisibleEditSign: ref(null),
            isVisibleEditDiagnosis: false,
            signatureError: false,
            chartKey: 0,

            formDetail: {
                temperature_record_id: this.temperatureRecord.id,
                temperature: 37,
                evacuations: 1,
                urinations: 1,
            },
            formSignature: {
                nurse_sign: this.temperatureRecord.nurse_sign,
                signature: true,
            },
            formRecord: {
                admission_id: this.temperatureRecord.admission_id,
                impression_diagnosis: this.temperatureRecord.impression_diagnosis,
                active: this.temperatureRecord.active
            },
        }
    },

    computed: {
        formDetailUpdate() {
            return {
                temperature_record_id: this.temperatureRecord.id,
                temperature: this.lastTemperature ? this.lastTemperature.temperature : null,
                evacuations: this.lastTemperature ? this.lastTemperature.evacuations : null,
                urinations: this.lastTemperature ? this.lastTemperature.urinations : null,
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
        updateDetail() {
            this.$inertia.put(route('temperatureDetails.update', this.lastTemperature.id), this.formDetailUpdate, {
                onSuccess: () => {
                    this.chartKey++;
                },
                preserveScroll: true,
            });
        },
        submitUpdateRecord() {
            this.showEditAdmission = null
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
            this.recordBeingDeleted = false
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
