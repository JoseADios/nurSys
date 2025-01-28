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

        <!-- <div class="text-white">{{ bedsFilled }}</div> -->

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <label for="bed"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Ubicacion</label>
                <select id="bed" v-model="form.bed_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option v-for="bed in beds" :key="bed.id" :value="bed.id">
                        Cama: {{ bed.number }} - Sala: {{ bed.room }}
                    </option>
                </select>
                <InputError :message="form.errors.bed_id" class="mt-2" />

                <label for="patient"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Paciente</label>
                <select required id="patient" v-model="form.patient_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option :value="patient.id" v-for="patient in patients" :key="patient.id">
                        {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                    </option>
                </select>
                <InputError :message="form.errors.patient_id" class="mt-2" />

                <label for="doctor"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Doctor</label>
                <select required id="doctor" v-model="form.doctor_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option :value="doctor.id" v-for="doctor in doctors" :key="doctor.id">
                        {{ doctor.name }} {{ doctor.last_name }}
                    </option>
                </select>
                <InputError :message="form.errors.doctor_id" class="mt-2" />

                <label for="admission_dx"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                    de
                    ingreso</label>
                <textarea required id="admission_dx" rows="4" v-model="form.admission_dx"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico de ingreso..."></textarea>
                <InputError :message="form.errors.admission_dx" class="mt-2" />

                <label for="final_dx"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico
                    final</label>
                <textarea id="final_dx" rows="4" v-model="form.final_dx"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico final..."></textarea>
                <InputError :message="form.errors.final_dx" class="mt-2" />

                <label for="comment"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                <textarea id="comment" rows="4" v-model="form.comment"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe las observaciones..."></textarea>
                <InputError :message="form.errors.comment" class="mt-2" />

                <div class="flex justify-end mt-6 mb-2">
                    <Link :href="route('admissions.index')"
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
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

export default {
    props: {
        doctors: Array,
        beds: Object,
        patients: Object,
        errors: [Array, Object],
        selectedPatient: String,
        selectedbed: String,
    },
    components: {
        AppLayout,
        Link,
        InputError,
    },
    data() {
        return {
            form: useForm({
                bed_id: this.selectedbed,
                patient_id: this.selectedPatient,
                doctor_id: null,
                admission_dx: null,
                final_dx: null,
                comment: null,
            })
        }
    },
    methods: {
        submit() {
            this.$inertia.post(route('admissions.store'), this.form, {
                onError: (errors) => {
                    this.form.errors = errors;
                }
            })
        }
    }
}
</script>
