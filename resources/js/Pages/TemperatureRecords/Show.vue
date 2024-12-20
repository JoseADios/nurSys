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
                    <TemperatureChart :temperatureData="details" :height="100" />
                </div>

                <!-- Formulario para agregar nuevo detalle -->
                <div hidden class="p-8 ">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Temperatura</h3>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label for="medication"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Medicamento
                                </label>
                                <input type="text" id="medication" v-model="formDetail.medication" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               dark:bg-gray-800 dark:text-white" placeholder="Nombre del medicamento" />
                            </div>

                            <div>
                                <label for="comment"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Comentario
                                </label>
                                <input type="text" id="comment" v-model="formDetail.comment" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               dark:bg-gray-800 dark:text-white" placeholder="Comentarios adicionales" />
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md
                           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                           transition-colors duration-300">
                                Agregar Evento
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
                nurse_record_id: this.temperatureRecord.id,
                medication: null,
                comment: null,
            }
        }
    },
    methods: {
        submit() {
            this.$inertia.post(route('temperatureRecordDetails.store'),
                this.formDetail,
                {
                    onSuccess: () => {
                        this.formDetail = {
                            nurse_record_id: this.temperatureRecord.id,
                            medication: '',
                            comment: '',
                        };
                    }
                });
        },
        goBack() {
            window.history.back()
        },
    }
}
</script>
