<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Editar registro de Enfermería
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
                    <button v-if="nurseRecord.active" @click="recordBeingDeleted = true"
                        class="flex items-center space-x-2 text-red-600 hover:text-red-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
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
                </div>

                <!-- Patient and Record Information -->
                <div class="grid md:grid-cols-2 gap-6 p-8 bg-gray-50 dark:bg-gray-700">
                    <div class="space-y-4">
                        <div v-if="!isVisible"
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md flex justify-between">
                            <div class="">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ingreso</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ nurseRecord.admission_id }}
                                </p>
                            </div>
                            <button @click="toggleEditAdmission" class="text-blue-500 mr-3">Edit</button>
                        </div>

                        <div v-if="isVisible" class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <form @submit.prevent="submitAdmission">

                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Seleccionar
                                    Ingreso</h3>
                                <select v-model="formAdmission.admission_id"
                                    class="w-full text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option :value="admission.id" v-for="admission in admissions" :key="admission.id">
                                        {{ admission.created_at }}
                                        {{ admission.patient.first_name }} {{ admission.patient.first_surname }} {{
                                            admission.patient.second_surname }}
                                        Cama {{ admission.bed.number }}, Sala {{ admission.bed.room }}
                                    </option>
                                </select>
                                <div class="mt-3">
                                    <button
                                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                        @click="toggleEditAdmission">Cancelar</button>

                                    <button type="submit"
                                        class="focus:outline-none text-white bg-green-800 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">
                                        Aceptar</button>
                                </div>

                            </form>

                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Sala</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                Sala {{ bed.room }}, Cama {{ bed.number }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Enfermera</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ patient.first_name }} {{ patient.last_name }}
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de Registro</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ nurseRecord.created_at }}
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
                <div class="p-8 ">
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
                                {{ detail.created_at }}
                            </div>
                        </div>
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
                            <button @click="isVisibleEditSign = true"
                                class="mt-4 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                                Editar</button>
                        </div>
                    </div>
                    <!-- Campo de firma -->
                    <div v-show="isVisibleEditSign" class="my-4">
                        <form @submit.prevent="submitSignature" class="flex items-center flex-col justify-center">
                            <!-- <label for="nurse_sign"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Firma
                            </label> -->

                            <SignaturePad v-model="formSignature.nurse_sign" input-name="nurse_sign" />
                            <div v-if="signatureError" class="text-red-500 text-sm mt-2">La firma es obligatoria.</div>

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
                </section>
            </div>
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
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue'
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

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
    },
    data() {
        return {
            recordBeingDeleted: ref(null),
            isVisible: false,
            isVisibleEditSign: ref(null),
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
            }
        }
    },
    methods: {
        toggleEditAdmission() {
            this.isVisible = !this.isVisible;
        },
        submitAdmission() {
            this.$inertia.put(route('nurseRecords.update', this.nurseRecord.id), this.formAdmission)
            this.isVisible = false
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
        }
    }
}
</script>
