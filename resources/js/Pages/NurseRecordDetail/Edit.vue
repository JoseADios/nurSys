<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Editar evento
            </h2>
        </template>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <label for="medication"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Medicacion</label>
                <input maxlength="255" type="text" id="medication" rows="4" v-model="form.medication"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></input>

                <label for="comment"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Observaciones</label>
                <textarea id="comment" rows="4" v-model="form.comment"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>

                <div class="flex justify-end mt-6 mb-2">
                    <button v-if="nurseRecordDetail.active" type="button" @click="recordBeingDeleted = true"
                        class="focus:outline-none mr-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Eliminar</button>
                    <button v-if="!nurseRecordDetail.active" type="button" @click="restoreRecord"
                        class="focus:outline-none mr-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">Restaurar</button>

                    <Link :href="route('nurseRecords.show', nurseRecordDetail.nurse_record_id)"
                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cancelar
                    </Link>

                    <button type="submit"
                        class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                        Actualizar</button>

                </div>
            </form>
        </div>

        <ConfirmationModal :show="recordBeingDeleted != null" @close="recordBeingDeleted = null">
            <template #title>
                Eliminar Ingreso
            </template>

            <template #content>
                ¿Estás seguro de que deseas eliminar este ingreso?
            </template>

            <template #footer>
                <SecondaryButton @click="recordBeingDeleted = null">
                    Cancelar
                </SecondaryButton>

                <DangerButton class="ms-3" @click="deleteRecord">
                    Eliminar
                </DangerButton>
            </template>
        </ConfirmationModal>
    </AppLayout>

</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

export default {
    components: {
        AppLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
    },
    props: {
        nurseRecordDetail: Object,
    },
    data() {
        return {
            recordBeingDeleted: ref(null),
            form: {
                medication: this.nurseRecordDetail.medication,
                comment: this.nurseRecordDetail.comment,
                active: this.nurseRecordDetail.active,
            }
        }
    },
    methods: {
        submit() {
            this.$inertia.put(route('nurseRecordDetails.update', this.nurseRecordDetail.id), this.form)
        },
        deleteRecord() {
            this.recordBeingDeleted = null
            this.$inertia.delete(route('nurseRecordDetails.destroy', this.nurseRecordDetail.id));
        },
        restoreRecord() {
            this.form.active = true;
            this.submit();
        }
    }
}
</script>
