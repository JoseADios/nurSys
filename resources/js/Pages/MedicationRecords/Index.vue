<template>
<AppLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-white leading-tight text-center">
            Fichas Medicamentos
        </h2>
    </template>

    <div class="flex flex-col items-center justify-center mt-10">
        <button @click="openCreateModal(record)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
            Crear Nueva Ficha Medica
        </button>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
        <form @submit.prevent="submit" class="mb-2">
            <input @input="submit()" class="rounded-lg" type="text" name="search" id="search" v-model="form.search" placeholder="Buscar ..." />

        </form>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Admisión</th>
                    <th scope="col" class="px-6 py-3">Diagnóstico</th>
                    <th scope="col" class="px-6 py-3">Dieta</th>
                    <th scope="col" class="px-6 py-3">Referencias</th>
                    <th scope="col" class="px-6 py-3">Estudios Pendientes</th>
                    <th scope="col" class="px-6 py-3">Firma del Doctor</th>
                    <th scope="col" class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>

                <tr v-for="record in medicationRecords.data.filter(record => record.id)" :key="record.id" :class="[
        'bg-white border-b dark:bg-gray-800 dark:border-gray-700',
        !record.active && 'transition-colors bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 hover:bg-red-100 dark:hover:bg-red-900/30'
    ]">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ record.admission ? record.admission.id : 'N/A' }}
                    </td>
                    <td class="px-6 py-4">{{ record.diagnosis }}</td>
                    <td class="px-6 py-4">{{ record.diet }}</td>
                    <td class="px-6 py-4">{{ record.referrals }}</td>
                    <td class="px-6 py-4">{{ record.pending_studies }}</td>
                    <td class="px-6 py-4">{{ record.doctor_sign }}</td>
                    <td class="px-6 py-4 flex items-center space-x-4">
                        <div v-if="record.active">
                            <Link class="text-blue-500 hover:text-blue-800" @click="MedicationRecordShow(record.id)">
                            Ver
                            </Link>

                            <Link class="text-yellow-500 ml-2 hover:text-yellow-800" @click="MedicationRecordEdit(record.id)">
                            Editar
                            </Link>
                        </div>
                        <Link method="post" :class="[
            'transition-colors',
            record.active
                ? 'text-red-500 hover:text-red-800'
                : 'text-green-500 hover:text-green-800'
        ]" @click="record.active ? openDisableModal(record) : Enable(record)">
                        {{ record.active ? 'Deshabilitar' : 'Habilitar' }}
                        </Link>
                    </td>

                </tr>

            </tbody>
        </table>
        <Pagination :pagination="medicationRecords" />
    </div>
    <ConfirmationModal :show="recordBeingDisabled != null" @close="recordBeingDisabled = null">
        <template #title>
            Eliminar Ingreso
        </template>
        <template #content>
            ¿Estás seguro de que deseas eliminar este ingreso?
        </template>
        <template #footer>
            <SecondaryButton @click="recordBeingDisabled = null">
                Cancelar
            </SecondaryButton>
            <DangerButton class="ms-3" @click="confirmDisable">
                Eliminar
            </DangerButton>
        </template>
    </ConfirmationModal>

    <DialogModal :show="isVisibleRecord" @close="isVisibleRecord = false">
        <!-- Header del modal -->
        <template #title>
            Crear Ficha de Medicamento
        </template>

        <!-- Contenido del modal -->
        <template #content>
            <div class="">
                <form>
                    <div class="grid md:grid-cols-[2fr_1fr] gap-4">
                        <div>
                            <label for="orden" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Admisión
                            </label>
                            <select id="order" v-model="selectedRecord.admission" class="w-full text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option :value="admissions.id" v-for="admissions in admission" :key="admissions.id">
                                    {{ admissions.id }} - {{ admissions.patient.first_name }} {{ admissions.patient.last_name }}
                                </option>
                            </select>

                        </div>

                    </div>
                </form>
            </div>
        </template>

        <!-- Footer del modal -->
        <template #footer>
            <button type="submit" @click="submitCreateRecord" class="ml-6 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                Crear
            </button>
            <button type="button" @click="isVisibleRecord = false" class="py-2.5  ml-2 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                Cerrar
            </button>
        </template>
    </DialogModal>

</AppLayout>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import {
    Link
} from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import {
    ref
} from 'vue';
export default {
    props: {
        medicationRecords: Array,
        filters: Object,
        admission: Array,
    },
    components: {
        AppLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        Pagination,
        DialogModal,
    },
    data() {
        return {
            recordBeingDisabled: null,
            form: {
                search: this.filters.search || '',
            },
            timeout: 1000,
            selectedRecord: ref(null),
            isVisibleRecord: ref(false),

        }
    },
    methods: {

        submit() {

            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            this.timeout
            this.$inertia.get(route('medicationRecords.index'), this.form, {
                preserveState: true,
            });
        },
        submitCreateRecord() {
            this.$inertia.get(route('medicationRecords.create', this.selectedRecord))
        },
        openCreateModal(record) {
            this.selectedRecord = {
                ...record
            };
            this.isVisibleRecord = true;
        },
        MedicationRecordShow(id) {
            this.$inertia.get(route('medicationRecords.show', id));
        },
        MedicationRecordEdit(id) {
            this.$inertia.get(route('medicationRecords.edit', id));
        },
        openDisableModal(record) {
            this.recordBeingDisabled = record;
        },
        confirmDisable() {
            if (this.recordBeingDisabled) {
                this.$inertia.delete(
                    route('medicationRecords.destroy', this.recordBeingDisabled.id), {
                        onSuccess: () => {

                            this.recordBeingDisabled = null;
                        },
                        onError: ($error) => {
                            alert($error, 'Error al intentar deshabilitar el registro.');
                        },
                    }
                );
            }
        },
        Enable(record) {
            this.$inertia.put(
                route('medicationRecords.update', record.id), {
                    active: true
                }, {
                    onSuccess: (response) => {

                    },
                    onError: (errors) => {
                        console.error('Errores:', errors);
                        alert('Error al intentar habilitar el registro.');
                    },
                }
            );
        },

    }

}
</script>
