<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                <BreadCrumb :items="[
                    // Condicionar el primer elemento (solo se muestra si hay admission_id)
                    ...(admission_id ? [{
                        formattedId: { id: admission_id, prefix: 'ING' },
                        route: route('admissions.show', admission_id)
                    }] : []),

                    // Segundo elemento (depende si hay admission_id o no)
                    {
                        text: 'Hojas de temperatura',
                        route: admission_id
                            ? route('temperatureRecords.index', { admission_id: admission_id })
                            : route('temperatureRecords.index')
                    },

                    {
                        text: 'Crear'
                    },
                ]" />
            </h2>
        </template>

        <div
            class="relative mb-4 overflow-x-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 sm:rounded-lg mt-8 p-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-2xl mx-auto">
                <AdmissionSelector @update:admission="form.admission_id = $event" :selected-admission-id="admission_id"
                    :doesnt-have-temperature-r="true" />

                <div class="flex justify-end mt-6 mb-2">
                    <Link v-if="admission_id" :href="route('temperatureRecords.index', {admission_id: admission_id})"
                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cancelar
                    </Link>
                    <Link v-else :href="route('temperatureRecords.index')"
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
import BreadCrumb from '@/Components/BreadCrumb.vue';
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
        AdmissionSelector,
        BreadCrumb
    },
    data() {
        return {
            form: {
                admission_id: this.admission_id || null,
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
