<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Nuevo ingreso
            </h2>
        </template>

        <!-- show errors -->
            <div v-if="errors.length > 0" class="mb-4 flex flex-col items-center">
                <div class="mb-4 text-red-500" v-for="error in errors" :key="error">
                    {{ error }}
                </div>
            </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <label for="bed" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Cama</label>
                <select id="bed" v-model="form.bed_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option v-for="bed in beds" :key="bed.id">
                        {{ bed.number }}
                    </option>
                </select>

                <label for="patient"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Paciente</label>
                <select required id="patient"
                    v-model="form.patient_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option :value="patient.id" v-for="patient in patients" :key="patient.id">
                        {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                    </option>
                </select>

                <label for="doctor"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Doctor</label>
                <select required id="doctor"
                    v-model="form.doctor_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option :value="doctor.id" v-for="doctor in doctors" :key="doctor.id">
                        {{ doctor.name }} {{ doctor.last_name }}
                    </option>
                </select>

                <label for="admission_dx"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                    de
                    ingreso</label>
                <textarea required id="admission_dx" rows="4"
                    v-model="form.admission_dx"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico de ingreso..."></textarea>

                <label for="final_dx"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                    final</label>
                <textarea id="final_dx" rows="4"
                    v-model="form.final_dx"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico final..."></textarea>

                <label for="comment"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                <textarea id="comment" rows="4"
                    v-model="form.comment"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe las observaciones..."></textarea>

                <div class="flex justify-end mt-6 mb-2">
                    <button @click="goBack"
                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cancelar
                    </button>

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
    props: {
        admission: Array,
        doctors: Array,
        beds: Array,
        patients: Array,
        errors: Array,
    },
    components: {
        AppLayout,
        Link,
    },
    data() {
        return {
            form: {
                bed_id: null,
                patient_id: null,
                doctor_id: null,
                admission_dx: null,
                final_dx: null,
                comment: null,
            }
        }
    },
    methods: {
        submit() {
            this.$inertia.post(route('admissions.store'), this.form)
        },
        goBack() {
            window.history.back()
        }
    }
}
</script>
