<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Nueva Hoja de Temperatura
            </h2>
        </template>

        <div class="ml-4 my-2 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">
            <div v-if="admission_id" class="inline-flex items-center">
                <Link :href="route('admissions.show', admission_id)"
                    class="inline-flex items-center hover:text-blue-600  dark:hover:text-white">
                <FormatId :id="admission_id" prefix="ING"></FormatId>
                </Link>
                <ChevronRightIcon class="size-3 text-gray-400 mx-1" />
            </div>

            <div v-if="admission_id">
                <Link :href="route('temperatureRecords.index', { admission_id: admission_id })"
                    class="inline-flex items-center hover:text-blue-600 dark:hover:text-white">
                Hojas de temperatura
                </Link>
            </div>
            <div v-if="!admission_id">
                <Link :href="route('temperatureRecords.index')"
                    class="inline-flex items-center hover:text-blue-600 dark:hover:text-white">
                Hojas de temperatura
                </Link>
            </div>
            <ChevronRightIcon class="size-3 text-gray-400 mx-1" />
            <div class="ml-2 inline-flex items-center">
                Crear
            </div>
        </div>

        <div
            class="relative mb-4 overflow-x-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 sm:rounded-lg mt-4 p-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-2xl mx-auto">

                <AdmissionSelector @update:admission="form.admission_id = $event"
                    :selected-admission-id="admission_id" />

                <label for="impression_diagnosis"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico de
                    impresión</label>

                <textarea required id="impression_diagnosis" v-model="form.impression_diagnosis"
                    class="bg-gray-50 resize-none border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>

                <div class="flex justify-end mt-6 mb-2">
                    <Link v-if="admission_id" :href="route('admissions.show', admission_id)"
                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cancelar
                    </Link>

                    <button type="submit"
                        class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">Guardar</button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<script>
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import FormatId from '@/Components/FormatId.vue';
import ChevronRightIcon from '@/Components/Icons/ChevronRightIcon.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        admission_id: [Number],
    },
    components: {
        AppLayout,
        Link,
        ChevronRightIcon,
        FormatId,
        AdmissionSelector
    },
    data() {
        return {
            form: {
                admission_id: this.admission_id || null,
                impression_diagnosis: null,
                has_admission_id: this.admission_id ? true : false
            }
        }
    },
    methods: {
        submit() {
            this.$inertia.post(route('temperatureRecords.store'), this.form)
        }
    }
}
</script>
