<template>
<AppLayout>
    <template #header>

        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
            <BreadCrumb :items="[
                    ...(medicationRecordDetail.medication_record_id ? [{
                        formattedId: { id: medicationRecordDetail.medication_record_id, prefix: 'ING' },
                        route: route('admissions.show', medicationRecordDetail.medication_record_id)
                    }] : []),
                    {
                        text: 'Fichas de Medicamentos',
                        route: medicationRecordDetail.medication_record_id
                            ? route('medicationRecords.show', { id: medicationRecordDetail.medication_record_id })
                            : route('medicationRecords.show')
                    },

                    {
                        formattedId: { id: medicationRecordDetail.medication_record_id, prefix: 'FICH' },
                        route: medicationRecordDetail.medication_record_id
                            ? route('medicationRecords.show', { id: medicationRecordDetail.medication_record_id })
                            : route('medicationRecords.show')
                    },
                    {
                        formattedId: { id: medicationRecordDetail.id, prefix: 'DET' }
                    }
                ]" />
            </h2>

    </template>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
        <form @submit.prevent="submit" class="max-w-sm mx-auto">

            <label for="drug" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Medicamento</label>
            <input type="text" id="drug" rows="4" v-model="form.drug" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escribe el diagnóstico final..."></input>

            <!-- Selector -->
            <div class="flex-1">
                <label for="route-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Via
                </label>
                <select id="route-select" required v-model="form.route" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option v-for="routeOptions in routes" :key="routeOptions.id" :value="routeOptions.name">
                        {{ routeOptions.name }} - {{ routeOptions.description }}
                    </option>
                </select>

            </div>

            <!-- Contenedor para la Dosis y el selector -->
            <div class="flex items-center space-x-4 mt-6">
                <!-- Dosis -->
                <div class="flex-1">
                    <label for="dose" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Dosis
                    </label>
                    <input id="dose" required type="number" v-model="form.dose" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escribe la Dosis asignada..." />
                </div>
                <!-- Selector -->
                <div class="flex-1">
                    <label for="dose-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Dosis
                    </label>
                    <select id="dose-select" required v-model="form.dose_metric" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option v-for="doses in dose" :key="doses.id" :value="doses.name">
                            {{ doses.name }} - {{ doses.description }}
                        </option>
                    </select>

                </div>
            </div>

            <label for="fc" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Frecuencia</label>
            <input type="text" id="fc" rows="4" v-model="form.fc" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escribe el diagnóstico final..."></input>

            <label for="interval_in_hours" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Intervalo en Horas</label>
            <input type="text" id="interval_in_hours" rows="4" v-model="form.interval_in_hours" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Escribe el diagnóstico final..."></input>

            <!-- Hora de Inicio -->
            <label for="start_time" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                Hora de Inicio
            </label>
            <input required id="start_time" type="time" v-model="form.start_time" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Selecciona la hora de inicio..." />
            <div class="flex justify-end mt-6 mb-2">
                <Link :href="route('medicationRecords.show', medicationRecordDetail.medication_record_id)" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                Cancelar
                </Link>

                <button type="submit" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Guardar</button>
            </div>
        </form>
    </div>
</AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link
} from '@inertiajs/vue3';
import BreadCrumb from '@/Components/BreadCrumb.vue';
export default {
    components: {
        AppLayout,
        Link,
        BreadCrumb
    },
    props: {
        medicationRecordDetail: Object,
        routes: Object,
        dose: Object,
    },
    watch: {
        'medicationRecordDetail.start_time': function (newStartTime) {
            this.form.start_time = this.formatStartTime(newStartTime);
        },
    },
    data() {
        return {
            form: {
                drug: this.medicationRecordDetail.drug,
                dose: this.medicationRecordDetail.dose,
                dose_metric: this.medicationRecordDetail.dose_metric,
                route: this.medicationRecordDetail.route,
                fc: this.medicationRecordDetail.fc,
                interval_in_hours: this.medicationRecordDetail.interval_in_hours,
                start_time: this.formatStartTime(this.medicationRecordDetail.start_time),
            }
        }
    },

    methods: {
        submit() {
            this.$inertia.put(route('medicationRecordDetails.update', this.medicationRecordDetail.id), this.form)
        },
        formatStartTime(datetime) {
            if (!datetime) return '';
            return new Date(datetime).toTimeString().slice(0, 5);
        },
    }
}
</script>
