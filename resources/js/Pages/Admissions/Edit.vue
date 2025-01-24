<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Editar ingreso
            </h2>
        </template>

        <div class="ml-10 mt-4 lg:mx-10 flex justify-between">
            <button @click="goBack" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-full">
                Volver
            </button>

            <div class="flex">
                <div v-if="admission.in_process">
                    <button type="button" @click="discharge"
                        class="self-end focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">
                        Dar de Alta
                    </button>
                </div>
                <div v-if="!admission.in_process">
                    <button type="button" @click="charge"
                        class="self-end focus:outline-none text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-900">
                        Poner en progreso
                    </button>
                </div>

                <button @click="confirmDelete"
                    class="self-end flex ml-2 focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Eliminar
                </button>
            </div>
        </div>

        <!-- <div class="text-white">
            {{ admission }}
        </div> -->

        <!-- show errors -->
        <div class="mb-4 flex flex-col items-center">
            <div class="mb-4 text-red-500" v-for="error in errors" :key="error">
                {{ error }}
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <label for="bed"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Ubicación</label>
                <select id="bed" v-model="form.bed_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option :value="bed.id" v-for="bed in beds" :key="bed.id" :selected="bed.id === admission.bed_id">
                        Cama {{ bed.number }} - Cuarto {{ bed.room }}
                    </option>
                </select>
                <AccessGate :except-role="['nurse']">
                    <label for="patient"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Paciente</label>
                    <select required id="patient" v-model="form.patient_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                        <option :value="patient.id" v-for="patient in patients" :key="patient.id"
                            :selected="patient.id === admission.patient_id">
                            {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                        </option>
                    </select>

                    <label for="doctor"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Doctor</label>
                    <select required id="doctor" v-model="form.doctor_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                        <option :value="doctor.id" v-for="doctor in doctors" :key="doctor.id"
                            :selected="doctor.id === admission.doctor_id">
                            {{ doctor.name }} {{ doctor.last_name }}
                        </option>
                    </select>

                    <label for="admission_dx"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                        de
                        ingreso</label>
                    <textarea required id="admission_dx" rows="4" v-model="form.admission_dx"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Escribe el diagnóstico de ingreso..."></textarea>

                    <label for="final_dx"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                        final</label>
                    <textarea id="final_dx" rows="4" v-model="form.final_dx"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Escribe el diagnóstico final..."></textarea>

                    <label for="comment"
                        class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                    <textarea id="comment" rows="4" v-model="form.comment"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Escribe las observaciones..."></textarea>
                </AccessGate>

                <div class="flex justify-end mt-6 mb-2">

                    <Link @click="goBack"
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
import AccessGate from '@/Components/Access/AccessGate.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        admission: Object,
        errors: [Array, Object],
        patients: [Array, Object],
        doctors: [Array, Object],
        beds: [Array, Object],
        previousUrl: String,
    },
    components: {
        AppLayout,
        Link,
        AccessGate,
    },
    data() {
        return {
            form: {
                bed_id: this.admission.bed_id,
                patient_id: this.admission.patient_id,
                doctor_id: this.admission.doctor_id,
                admission_dx: this.admission.admission_dx,
                final_dx: this.admission.final_dx,
                comment: this.admission.comment,
                in_process: this.admission.in_process,
            }
        }
    },
    methods: {
        submit() {
            this.$inertia.put(route('admissions.update', this.admission.id), this.form)
        },
        discharge() {
            this.form.in_process = 0
            this.submit()
        },
        charge() {
            this.form.in_process = 1
            this.submit()
        },
        goBack() {
            if (this.previousUrl) {
                this.$inertia.visit(this.previousUrl);
            } else {
                window.history.back();
            }
        },
        confirmDelete() {
            if (confirm('¿Estás seguro de que deseas eliminar este ingreso?')) {
                this.$inertia.delete(route('admissions.destroy', this.admission.id));
            }
        },
    },
}
</script>
