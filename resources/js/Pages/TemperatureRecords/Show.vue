<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Hoja de Temperatura
            </h2>
        </template>

        <!-- <div class="text-white">Datos {{ admissions }}</div> -->

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden">
                <!-- Navigation -->
                <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <Link :href="route('temperatureRecords.index', temperatureRecord.admission_id)"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Volver</span>
                    </Link>
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

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnóstico de impresión</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ temperatureRecord.impression_diagnosis }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Errors -->
                <div v-if="errors.length > 0" class="bg-red-50 border-l-4 border-red-500 p-4 mx-8 my-4">
                    <div class="text-red-700" v-for="error in errors" :key="error">
                        {{ error }}
                    </div>
                </div>

                <!-- Chart -->
                <div class="p-4 mx-8 my-4">
                    <TemperatureChart :temperatureData="details" :key="chartKey" :height="100" />
                </div>

                <!-- Formulario para agregar nuevo detalle -->
                <div class="p-8 ">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Temperatura</h3>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label for="temperature"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Temperatura
                                </label>
                                <input type="number" step="0.1" id="temperature" v-model="formDetail.temperature" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
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

            </div>
        </div>
    </AppLayout>
</template>

<script>
import TemperatureChart from '@/Components/Charts/TemperatureChart.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        temperatureRecord: Object,
        admissions: Array,
        details: Array,
        errors: {
            type: Array,
            default: () => []
        },
    },
    components: {
        AppLayout,
        Link,
        TemperatureChart
    },
    data() {
        return {
            formDetail: {
                temperature_record_id: this.temperatureRecord.id,
                temperature: 37,
                evacuations: 1,
                urinations: null,
            },
            chartKey: 0,
        }
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
                            urinations: '',
                        };
                        this.chartKey++;
                    }
                });
        },
        goBack() {
            window.history.back()
        },
    }
}
</script>
