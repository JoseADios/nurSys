<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Hoja de Temperatura
            </h2>
        </template>

        <!-- <div class="text-white">Datos {{ temperatureRecord.id }}</div> -->

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden">
                <!-- Navigation -->
                <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <button @click="goBack"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Volver</span>
                    </button>
                    <button v-if="temperatureRecord.active" @click="recordBeingDeleted = true"
                        class="flex items-center space-x-2 text-red-600 hover:text-red-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
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
                </div>

                <!-- Patient and Record Information -->
                <div class="grid md:grid-cols-2 gap-6 p-8 bg-gray-50 dark:bg-gray-700">
                    <div class="space-y-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md flex justify-between">
                            <div class="">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ingreso</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ temperatureRecord.admission_id }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ temperatureRecord.admission.patient.first_name }} {{
                                    temperatureRecord.admission.patient.first_surname }} {{
                                    temperatureRecord.admission.patient.second_surname }}
                            </p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Enfermera</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ temperatureRecord.nurse.name }} {{ temperatureRecord.nurse.last_name }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Sala</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                Sala {{ temperatureRecord.admission.bed.room }}, Cama {{
                                    temperatureRecord.admission.bed.number
                                }}
                            </p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de Registro</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ temperatureRecord.created_at.toLocaleString() }}
                            </p>
                        </div>

                        <div v-if="!isVisible"
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

                        <div v-if="isVisible" class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
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
                    </div>
                </div>

                <!-- Chart -->
                <div class="p-4 mx-8 my-4">
                    <TemperatureChart :temperatureData="details" :key="chartKey" :height="100" />
                </div>

                <!-- ultima temperatura -->

                <!-- Formulario para actualizar ultimo detalle -->
                <div v-if="lastTemperature" class="p-8 ">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Ultima temperatura</h3>
                    <form @submit.prevent="updateDetail" class="space-y-4">
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label for="temperature"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Temperatura
                                </label>
                                <input type="number" step="0.1" id="temperature" v-model="formDetailUpdate.temperature"
                                    required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               dark:bg-gray-800 dark:text-white" placeholder="Temperatura del paciente (°C)" />
                            </div>

                            <div>
                                <label for="evacuations"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Evacuaciones
                                </label>
                                <input type="number" id="evacuations" v-model="formDetailUpdate.evacuations" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               dark:bg-gray-800 dark:text-white" placeholder="Num. de evacuaciones del paciente" />
                            </div>
                            <div>
                                <label for="urinations"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Micciones
                                </label>
                                <input type="text" id="urinations" v-model="formDetailUpdate.urinations" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
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

                <!-- Formulario para agregar nuevo detalle -->
                <div v-if="canCreateDetail" class="p-8 ">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Temperatura</h3>

                    <form @submit.prevent="submit" class="space-y-4">
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
                                <input type="number" id="evacuations" v-model="formDetail.evacuations" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               dark:bg-gray-800 dark:text-white" placeholder="Num. de evacuaciones del paciente" />
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
                <!-- si no puede crear ni actualizar mostrar que ya otro enfermero ha registrado una firma en este turno que no puede hacer nada -->
                <div v-if="!canCreateDetail && !lastTemperature" class="p-8">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Información</h3>
                    <p class="text-lg text-gray-700 dark:text-gray-300">
                        Ya otro enfermero ha registrado una firma en este turno. No puede realizar ninguna acción.
                    </p>
                </div>


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
                            <button @click="isVisibleEditSign = true"
                                class="mt-4 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                                Editar</button>
                        </div>
                    </div>
                    <!-- Campo de firma -->
                    <div v-show="isVisibleEditSign" class="my-4">
                        <form @submit.prevent="submitSignature" class="flex items-center flex-col justify-center">
                            <!-- <label for="nurse_sign"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Firma
                            </label> -->

                            <SignaturePad v-model="formSignature.nurse_sign" input-name="nurse_sign" />
                            <div v-if="signatureError" class="text-red-500 text-sm mt-2">La firma es obligatoria.</div>

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
                </section>

            </div>
        </div>
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

export default {
    props: {
        temperatureRecord: Object,
        admissions: Array,
        details: Array,
        lastTemperature: Object,
        canCreateDetail: Boolean,
        previousUrl: String,
    },
    components: {
        AppLayout,
        Link,
        TemperatureChart,
        SignaturePad,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
    },
    data() {
        return {
            recordBeingDeleted: ref(null),
            isVisible: false,
            isVisibleEditSign: ref(null),
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
        submit() {
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
                    }
                });
        },
        updateDetail() {
            this.$inertia.put(route('temperatureDetails.update', this.lastTemperature.id), this.formDetailUpdate, {
                onSuccess: () => {
                    this.chartKey++;
                }
            });

        },
        submitUpdateRecord() {
            this.$inertia.put(route('temperatureRecords.update', this.temperatureRecord.id), this.formRecord)
            this.isVisible = false
        },
        submitSignature() {
            if (!this.formSignature.nurse_sign) {
                this.signatureError = true;
                return;
            }
            this.signatureError = false;
            this.$inertia.put(route('temperatureRecords.update', this.temperatureRecord.id), this.formSignature);
            this.isVisibleEditSign = false
        },
        toggleEditRecord() {
            this.isVisible = !this.isVisible
        },
        deleteRecord() {
            this.recordBeingDeleted = false
            this.$inertia.delete(route('temperatureRecords.destroy', this.temperatureRecord.id));
        },
        restoreRecord() {
            this.formRecord.active = true
            this.submitUpdateRecord()
        },
        goBack() {
            if (this.previousUrl) {
                this.$inertia.visit(this.previousUrl);
            } else {
                window.history.back();
            }
        }
    }
}
</script>
