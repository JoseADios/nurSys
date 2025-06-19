<template>
    <div class="p-8 bg-white dark:bg-gray-900  rounded-xl max-h-[30rem] overflow-y-auto">
        <ol v-if="admissions.length > 0" class="relative border-s border-gray-200 dark:border-gray-700">
            <li v-for="(admission, index) in visibleAdmissions" :key="index" class="mb-10 ms-4">
                <div :class="[
                    'absolute w-3 h-3 rounded-full mt-1.5 -start-1.5 border border-transparent',
                    admission.discharged_date ? 'bg-gray-200 dark:border-gray-900 dark:bg-gray-700' : 'bg-green-500 dark:bg-green-600'
                ]">
                </div>
                <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{
                    formatDate(admission.created_at) }}</time>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ admission.admission_dx }}</h3>
                <p v-if="admission.discharged_date" class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">
                    El paciente fue atendido por el doctor <strong class="text-gray-900 dark:text-white">{{ admission.doctor.name }} {{
                        admission.doctor.last_name }}</strong>.
                    Duró <strong class="text-gray-900 dark:text-white">{{ admission.days_admitted }}</strong> dia/s ingresado y el diagnóstico final fue
                    <strong class="text-gray-900 dark:text-white">{{ admission.final_dx }}</strong>.
                </p>
                <p v-else class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">El paciente está siendo
                    atendido
                    por el doctor <strong class="text-gray-900 dark:text-white">{{ admission.doctor.name }} {{ admission.doctor.last_name }}</strong>.
                    Tiene <strong class="text-gray-900 dark:text-white">{{ admission.days_admitted }}</strong> dia/s ingresado.
                    <span v-if="admission.bed">Está en la sala <strong class="text-gray-900 dark:text-white">{{ admission.bed.room }}, cama {{ admission.bed.number
                        }}</strong>.</span>
                    <span v-else>No se le ha asignado una cama.</span>
                </p>
                <Link :href="route('admissions.show', admission.id)" as="button"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                Ver ingreso <svg class="w-3 h-3 ms-2 rtl:rotate-180" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                </svg></Link>
            </li>
        </ol>
        <p v-else class="text-base font-normal text-gray-500 dark:text-gray-400">No hay ingresos registrados para este
            paciente.</p>
    </div>

    <div v-if="admissions.length > 3" class="mt-4">
        <button v-if="!showAll" @click="showAllAdmissions"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
            Ver más
        </button>
        <button v-if="showAll" @click="showLessAdmissions"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
            Ver menos
        </button>
    </div>
</template>

<script>
import moment from 'moment/moment';
import 'moment/locale/es';
import { Link } from '@inertiajs/vue3';

export default {
    name: 'PatientAdmissionHistoryLine',
    props: {
        admissions: Array
    },
    components: {
        Link
    },
    data() {
        return {
            showAll: false
        };
    },
    computed: {
        visibleAdmissions() {
            return this.showAll ? this.admissions : this.admissions.slice(0, 3);
        }
    },
    methods: {
        formatDate(date) {
            return moment(date).format('MMMM YYYY').replace(/^\w/, c => c.toUpperCase());
        },
        showAllAdmissions() {
            this.showAll = true;
        },
        showLessAdmissions() {
            this.showAll = false;
        }
    }
}
</script>
