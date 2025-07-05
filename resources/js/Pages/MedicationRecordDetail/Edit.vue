<template>
    <AppLayout title="Editar Detalles de Ficha Medicamentos">
        <!--BreadCrumb -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                <BreadCrumb :items="[
                    ...(admission_id
                        ? [
                            {
                                formattedId: { id: admission_id, prefix: 'ING' },
                                route: route('admissions.show', admission_id),
                            },
                        ]
                        : []),
                    {
                        text: 'Fichas de Medicamentos',
                        route: route('medicationRecords.index', { admission_id }),
                    },
                    {
                        formattedId: {
                            id: medicationRecordDetail.medication_record_id,
                            prefix: 'FICH',
                        },
                        route: route('medicationRecords.show', {
                            medicationRecord: medicationRecordDetail.medication_record_id,
                            admission_id,
                        }),
                    },
                    { formattedId: { id: medicationRecordDetail.id, prefix: 'DET' } },
                ]" />
            </h2>
        </template>

        <!--Formulario -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10 p-3 sm:p-0">
            <form @submit.prevent="submit" class="max-w-md sm:max-w-xl mx-auto space-y-6">
                <!-- Medicamento -->
                <div>
                    <label for="drug"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Medicamento</label>
                    <DrugSelector v-model:drug="form.drug" />
                </div>

                <!-- Vía -->
                <div>
                    <label for="route-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Vía <span class="text-red-500">*</span>
                    </label>
                    <select id="route-select" required v-model="form.route"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option v-for="r in routes" :key="r.id" :value="r.name">
                            {{ r.name }} - {{ r.description }}
                        </option>
                    </select>
                </div>

                <!-- Dosis + Métrica -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-4">
                    <!-- Dosis -->
                    <div class="flex-1">
                        <label for="dose" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dosis
                            <span class="text-red-500">*</span></label>
                        <input id="dose" type="number" required v-model="form.dose"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Escribe la dosis..." />
                    </div>

                    <!-- Métrica -->
                    <div class="flex-1">
                        <label for="dose-select"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Métrica
                            <span class="text-red-500">*</span></label>
                        <select id="dose-select" required v-model="form.dose_metric"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option v-for="d in dose" :key="d.id" :value="d.name">
                                {{ d.name }} - {{ d.description }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Frecuencia -->
                <div>
                    <label for="fc" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Frecuencia
                        <span class="text-red-500">*</span></label>
                    <input id="fc" type="text" v-model="form.fc"
                        class="block w-full p-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Frecuencia..." />
                </div>

                <!-- Intervalo -->
                <div>
                    <label for="interval_in_hours"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Intervalo en horas <span
                            class="text-red-500">*</span></label>
                    <input id="interval_in_hours" type="text" v-model="form.interval_in_hours"
                        class="block w-full p-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Intervalo..." />
                </div>

                <!-- Hora inicio -->
                <div>
                    <label for="start_time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hora de
                        inicio
                        <span class="text-red-500">*</span></label>
                    <input id="start_time" type="time" required v-model="form.start_time"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-2 sm:gap-4 mt-6 flex-wrap">
                    <Link :href="cancelUrl">
                    <SecondaryButton class="py-2.5 px-5">
                        Cancelar
                    </SecondaryButton>
                    </Link>

                    <PrimaryButton type="submit" class="py-2.5 px-5" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing" :is-loading="form.processing">
                        Guardar
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>


<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link, useForm
} from '@inertiajs/vue3';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import DrugSelector from '@/Components/DrugSelector.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
export default {
    components: {
        AppLayout,
        Link,
        PrimaryButton,
        SecondaryButton,
        BreadCrumb,
        DrugSelector,
    },

    props: {
        medicationRecordDetail: Object,
        routes: Object,
        dose: Object,
        admission_id: Number
    },

    data() {
        return {
            form: useForm({
                drug: this.medicationRecordDetail.drug,
                dose: this.medicationRecordDetail.dose,
                dose_metric: this.medicationRecordDetail.dose_metric,
                route: this.medicationRecordDetail.route,
                fc: this.medicationRecordDetail.fc,
                interval_in_hours: this.medicationRecordDetail.interval_in_hours,
                start_time: this.formatStartTime(this.medicationRecordDetail.start_time),
            })
        }
    },

    computed: {
        cancelUrl() {

            return this.admission_id ?
                route('medicationRecords.show', {
                    medicationRecord: this.medicationRecordDetail.medication_record_id,
                    admission_id: this.admission_id
                }) :
                route('medicationRecords.show', this.medicationRecordDetail.medication_record_id);
        }
    },

    watch: {
        'medicationRecordDetail.start_time': function (newStartTime) {
            this.form.start_time = this.formatStartTime(newStartTime);
        },
    },

    methods: {
        submit() {
            this.form.put(route('medicationRecordDetails.update', this.medicationRecordDetail.id))
        },
        formatStartTime(datetime) {
            if (!datetime) return '';
            return new Date(datetime).toTimeString().slice(0, 5);
        },
    }
};
</script>

