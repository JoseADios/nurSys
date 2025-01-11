<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Nuevo Registro de Enfermería
            </h2>
        </template>


        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <label for="admission" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Ingreso</label>

                <select id="admission" v-model="form.admission_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option v-for="admission in admissions" :key="admission.id" :value="admission.id">
                        {{ admission.created_at }}
                        {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                            admission.patient.second_surname }}
                        Cama {{ admission.bed.number }}, Sala {{ admission.bed.room }}
                    </option>
                </select>
                <div class="flex justify-end mt-6 mb-2">
                    <button @click="goBack" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cancelar
                    </button>

                    <button type="submit" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Guardar</button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        admissions: Array,
        admission_id: Number,
    },
    components: {
        AppLayout,
    },
    data() {
        return {
            form: {
                admission_id: this.admission_id
            }
        }
    },
    methods: {
        submit() {
            this.$inertia.post(route('nurseRecords.store'), this.form)
        },
        goBack() {
            this.$inertia.visit(document.referrer)
        }
    }
}
</script>
