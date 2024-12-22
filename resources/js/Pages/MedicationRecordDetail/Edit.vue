<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Editar Detalle de Ficha de Medicamentos
            </h2>
        </template>

        <!-- <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <label for="created_at"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Fecha</label>
                <input type="text" id="created_at" rows="4" v-model="form.created_at"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico final..."></input> -->

                <!-- <label for="comment"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                <textarea id="comment" rows="4" v-model="form.comment"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe el diagnóstico final..."></textarea> -->
<!--
                <div class="flex justify-end mt-6 mb-2">
                    <Link :href="route('nurseRecords.edit', medicationRecordDetail.medication_record_id)"
                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cancelar
                    </Link>

                    <button type="submit"
                        class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Guardar</button>
                </div> -->
            <!-- </form>
        </div> -->
    </AppLayout>

</template>

<!-- <script>
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
                created_at: this.medicationRecordDetail.created_at,
                // comment: this.nurseRecordDetail.comment,
            }
        }
    },
    methods: {
        submit() {
            this.$inertia.put(route('medicationRecordDetails.update', this.medicationRecordDetail.id), this.form)
        }
    }
}
</script> -->
