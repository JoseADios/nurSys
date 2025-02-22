<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Registro de Enfermería
            </h2>
        </template>

        <!-- <div class="text-white">Datos {{ adm_id }}</div> -->

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden">
                <!-- Navigation -->
                <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <Link :href="route('nurseRecords.index')"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Volver</span>
                    </Link>
                    <AccessGate :permission="['nurseRecord.delete']">
                        <button v-if="nurseRecord.active" @click="recordBeingDeleted = true"
                            class="flex items-center space-x-2 text-red-600 hover:text-red-800 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H3a1 1 0 100 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm2 4a1 1 0 011 1v7a1 1 0 11-2 0V7a1 1 0 011-1zm4 0a1 1 0 011 1v7a1 1 0 11-2 0V7a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium">Eliminar</span>
                        </button>
                        <button v-else @click="restoreRecord"
                            class="flex items-center space-x-2 text-green-600 hover:text-green-800 transition-colors">
                            <span class="font-medium">Restaurar</span>
                        </button>
                    </AccessGate>
                </div>

                <!-- Patient and Record Information -->
                <div class="grid md:grid-cols-2 gap-6 p-8 bg-gray-50 dark:bg-gray-700">
                    <div class="space-y-4">

                        <!-- admission -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ingreso</h3>
                                <Link :href="route('admissions.show', nurseRecord.admission_id)" as="button"
                                    class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                                ING-00{{ nurseRecord.admission_id }}
                                </Link>
                            </div>
                            <AccessGate :permission="['nurseRecord.delete']">
                                <button @click="showEditAdmission = true" class="text-blue-500 ml-3">Edit</button>
                            </AccessGate>
                        </div>

                        <!-- patient name -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <Link :href="route('patients.show', nurseRecord.admission.patient.id)" as="button"
                                class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                            {{ nurseRecord.admission.patient.first_name }} {{
                                nurseRecord.admission.patient.first_surname }} {{
                                nurseRecord.admission.patient.second_surname }}
                            </Link>
                        </div>

                        <!-- Bed info -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Sala</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                Sala {{ bed.room }}, Cama {{ bed.number }}
                            </p>
                        </div>

                    </div>

                    <!-- Rigth col -->
                    <div class="space-y-4">

                        <!-- Nurse -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Enfermero</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ nurse.name }} {{ nurse.last_name }}
                            </p>
                        </div>

                        <!-- Created date -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de Registro</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ formatDate(nurseRecord.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Errors -->
                <div v-if="errors.length > 0" class="bg-red-50 border-l-4 border-red-500 p-4 mx-8 my-4">
                    <div class="text-red-700" v-for="error in errors" :key="error">
                        {{ error }}
                    </div>
                </div>

                <!-- Form -->
                <!-- Formulario para agregar nuevo detalle -->
                <AccessGate :permission="['nurseRecordDetail.create']">
                    <div class="p-8">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Nuevos Eventos</h3>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label for="medication"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Medicamento
                                    </label>
                                    <input type="text" id="medication" v-model="formDetail.medication" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    dark:bg-gray-800 dark:text-white" placeholder="Nombre del medicamento" />
                                </div>

                                <div>
                                    <label for="comment"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Comentario
                                    </label>
                                    <input type="text" id="comment" v-model="formDetail.comment" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    dark:bg-gray-800 dark:text-white" placeholder="Comentarios adicionales" />
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md
                                hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                transition-colors duration-300">
                                    Agregar Evento
                                </button>
                            </div>
                        </form>
                    </div>
                </AccessGate>

                <AccessGate :except-permission="['nurseRecordDetail.create']">
                    <hr class="my-2 border-transparent dark:border-transparent">
                </AccessGate>

                <!-- Nurse Record Details -->
                <div class="p-8 space-y-4  bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Eventos del Registro</h3>

                    <div v-for="detail in details" :key="detail.id"
                        class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md flex justify-between items-center hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors">
                        <div class="flex-grow">
                            <div class="font-semibold text-gray-900 dark:text-white">
                                {{ detail.medication }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ detail.comment }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ formatDate(detail.created_at) }}
                            </div>
                        </div>
                        <AccessGate :permission="['nurseRecordDetail.edit']">
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <!-- Editar -->
                                <Link :href="route('nurseRecordDetails.edit', detail.id)"
                                    class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Editar</span>
                                </Link>
                            </div>
                        </AccessGate>
                    </div>

                    <div v-if="details.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No hay eventos de registro disponibles
                    </div>
                </div>


                <section class=" p-8 space-y-4 ">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Firma</h3>

                    <!-- mostrar imagen firma -->
                    <div v-show="!isVisibleEditSign">
                        <div class="flex items-center flex-col justify-center">

                            <img v-if="nurseRecord.nurse_sign" :src="`/storage/${nurseRecord.nurse_sign}`" alt="Firma">
                            <div v-else>
                                <div class="text-gray-500 dark:text-gray-400 my-16">
                                    No hay firma disponible
                                </div>
                            </div>

                            <AccessGate :permission="['nurseRecord.edit']">
                                <button @click="isVisibleEditSign = true"
                                    class="mt-4 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                                    Editar</button>
                            </AccessGate>
                        </div>
                    </div>

                    <!-- Campo de firma -->
                    <AccessGate :permission="['nurseRecord.edit']">
                        <div v-show="isVisibleEditSign" class="my-4">
                            <form @submit.prevent="submitSignature" class="flex items-center flex-col justify-center">
                                <!-- <label for="nurse_sign"
                                     class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                     Firma
                                 </label> -->

                                <SignaturePad v-model="formSignature.nurse_sign" input-name="nurse_sign" />
                                <div v-if="signatureError" class="text-red-500 text-sm mt-2">La firma es obligatoria.
                                </div>

                                <div class="my-4">
                                    <button type="button"
                                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                        @click="isVisibleEditSign = false">Cancelar</button>
                                    <button
                                        class="mr-6 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900"
                                        type="submit">Guardar firma</button>
                                </div>
                            </form>
                        </div>
                    </AccessGate>
                </section>
            </div>
        </div>

        <!-- Change admission modal -->
        <Modal :closeable="true" :show="showEditAdmission != null" @close="showEditAdmission == null">
            <div class="relative overflow-hidden shadow-lg sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
                <form @submit.prevent="submitAdmission" class="max-w-3xl mx-auto">
                    <!-- Filtros de búsqueda -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4 shadow-sm">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">
                            Buscar Paciente
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            <div class="space-y-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" v-model="filters.name" @input="debounceSearch"
                                        class="pl-10 w-full rounded-lg border-gray-200 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:text-white"
                                        placeholder="Nombre del paciente...">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0h8v12H6V4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" v-model="filters.room" @input="debounceSearch"
                                        class="pl-10 w-full rounded-lg border-gray-200 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:text-white"
                                        placeholder="Número de sala...">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2z" />
                                        </svg>
                                    </div>
                                    <input type="number" v-model="filters.bed" @input="debounceSearch"
                                        class="pl-10 w-full rounded-lg border-gray-200 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:text-white"
                                        placeholder="Número de cama...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de ingresos  -->
                    <div class="space-y-2">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">
                            Seleccionar Ingreso ({{ admissions.length }} resultados)
                        </h3>
                        <div
                            class="max-h-[250px] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            <div v-for="admission in filteredAdmissions" :key="admission.id"
                                @click="selectAdmission(admission)" :class="[
                                    'p-3 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20 transition',
                                    formAdmission.admission_id === admission.id ? 'bg-purple-100 dark:bg-purple-900/30' : ''
                                ]">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white text-sm">
                                            {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                                                admission.patient.second_surname }}
                                        </span>
                                        <span
                                            class="text-xs ml-2 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-800 dark:text-gray-300">
                                            ING-00{{ admission.id }}
                                        </span>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                            Sala {{ admission.bed.room }} - Cama {{ admission.bed.number }}
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatDate(admission.created_at) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end mt-4 space-x-3">
                        <button type="button" @click="showEditAdmission = null"
                            class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800 transition"
                            :disabled="!formAdmission.admission_id">
                            Aceptar
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete modal -->
        <AccessGate :permission="['nurseRecord.delete']">
            <ConfirmationModal :show="recordBeingDeleted != null" @close="recordBeingDeleted == null">
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
        </AccessGate>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue'
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import moment from 'moment';
import AccessGate from '@/Components/Access/AccessGate.vue';
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce';


export default {
    props: {
        nurseRecord: Object,
        adm_id: Number,
        errors: {
            type: Array,
            default: () => []
        },
        admissions: Array,
        patient: Object,
        nurse: Object,
        bed: Object,
        details: Array,
    },
    components: {
        AppLayout,
        Link,
        SignaturePad,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        AccessGate,
        Modal
    },
    data() {
        return {
            recordBeingDeleted: ref(null),
            isVisibleEditSign: ref(null),
            showEditAdmission: ref(null),
            signatureError: false,

            formAdmission: {
                admission_id: this.nurseRecord.admission_id,
                active: this.nurseRecord.active
            },
            formDetail: {
                nurse_record_id: this.nurseRecord.id,
                medication: null,
                comment: null,
            },
            formSignature: {
                nurse_sign: this.nurseRecord.nurse_sign,
                signature: true,
            },
            filters: {
                name: '',
                room: '',
                bed: '',
            },
            debouncedSearch: null,
        }
    },
    created() {
        this.debouncedSearch = debounce(this.applyFilters, 300)
    },
    computed: {
        filteredAdmissions() {
            return this.admissions;
        }
    },
    methods: {
        submitAdmission() {
            this.$inertia.put(route('nurseRecords.update', this.nurseRecord.id), this.formAdmission)
            this.showEditAdmission = null
        },
        selectAdmission(admission) {
            this.formAdmission.admission_id = admission.id;
        },
        debounceSearch() {
            this.debouncedSearch();
        },
        applyFilters() {
            this.$inertia.get(
                route('nurseRecords.show', this.nurseRecord.id),
                {
                    name: this.filters.name,
                    room: this.filters.room,
                    bed: this.filters.bed
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['admissions']
                }
            );
        },
        submit() {
            this.$inertia.post(route('nurseRecordDetails.store'),
                this.formDetail,
                {
                    onSuccess: () => {
                        this.formDetail = {
                            nurse_record_id: this.nurseRecord.id,
                            medication: '',
                            comment: '',
                        };
                    }
                });
        },
        submitSignature() {
            if (!this.formSignature.nurse_sign) {
                this.signatureError = true;
                return;
            }
            this.signatureError = false;
            this.$inertia.put(route('nurseRecords.update', this.nurseRecord.id), this.formSignature, {
                preserveScroll: true
            });
            this.isVisibleEditSign = false
        },
        deleteRecord() {
            this.recordBeingDeleted = false
            this.$inertia.delete(route('nurseRecords.destroy', this.nurseRecord.id));
        },
        restoreRecord() {
            this.formAdmission.active = true
            this.submitAdmission();
        },
        formatDate(date) {
            return moment(date).format('DD MMM YYYY HH:mm');
        },
    }
}
</script>
