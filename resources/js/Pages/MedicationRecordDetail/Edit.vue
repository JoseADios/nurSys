
<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Editar Detalle de Ficha de Medicamentos
            </h2>
        </template>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <label for="drug"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Medicamento</label>
                <input type="text" id="drug" rows="4" v-model="form.drug"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico final..."></input>

                <label for="dose"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Dosis</label>
                <input type="text" id="dose" rows="4" v-model="form.dose"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico final..."></input>

                <label for="route"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Via</label>
                <input type="text" id="route" rows="4" v-model="form.route"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico final..."></input>

                <label for="fc"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Frecuencia</label>
                <input type="text" id="fc" rows="4" v-model="form.fc"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico final..."></input>

                <label for="interval_in_hours"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Intervalo en Horas</label>
                <input type="text" id="interval_in_hours" rows="4" v-model="form.interval_in_hours"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico final..."></input>

                <div class="flex justify-end mt-6 mb-2">
                    <Link :href="route('medicationRecords.show', medicationRecordDetail.medication_record_id)"
                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cancelar
                    </Link>

                    <button type="submit"
                        class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Guardar</button>
                </div>
             </form>
        </div>
    </AppLayout>

</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        AppLayout,
        Link
    },
    props: {
        medicationRecordDetail: Object,
    },
    data() {
        return {
            form: {
                drug: this.medicationRecordDetail.drug,
                dose: this.medicationRecordDetail.dose,
                route: this.medicationRecordDetail.route,
                fc: this.medicationRecordDetail.fc,
                interval_in_hours: this.medicationRecordDetail.interval_in_hours,
            }
        }
    },
    methods: {
        submit() {
            this.$inertia.put(route('medicationRecordDetails.update', this.medicationRecordDetail.id), this.form)
        }
    }
}
</script>
