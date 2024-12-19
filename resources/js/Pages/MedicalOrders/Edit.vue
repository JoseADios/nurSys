<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Editar Orden Médica
            </h2>
        </template>

        <!-- <div class="text-white">Datos {{ adm_id }}</div> -->

        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden">
                <!-- Navigation -->
                <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <Link :href="route('medicalOrders.index', medicalOrder.admission_id)"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Volver</span>
                    </Link>
                </div>

                <!-- Patient and Record Information -->
                <div class="grid md:grid-cols-2 gap-6 p-8 bg-gray-50 dark:bg-gray-700">
                    <div class="space-y-4">
                        <div v-if="!isVisible"
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md flex justify-between">
                            <div class="">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ingreso</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ medicalOrder.admission_id }}
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
                                {{ medicalOrder.admission.patient.first_name }} {{ medicalOrder.admission.patient.first_surname }} {{ medicalOrder.admission.patient.second_surname }}
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Sala</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                Sala {{ medicalOrder.admission.bed.room }}, Cama {{ medicalOrder.admission.bed.number }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Enfermera</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ medicalOrder.admission.doctor.name }} {{ medicalOrder.admission.doctor.last_name }}
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha de Registro</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ medicalOrder.created_at }}
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
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Nuevos Detalles</h3>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid md:grid-cols-[2fr_1fr] gap-4">
                            <div>
                                <label for="orden"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Órden
                                </label>
                                <input type="text" id="order" v-model="formDetail.order" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                    placeholder="Orden médica" />
                            </div>

                            <div>
                                <label for="regime"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Régimen
                                </label>
                                <select id="regime" v-model="formDetail.regime"
                                    class="w-full text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option :value="regime.name" v-for="regime in regimes" :key="regime.id">
                                        {{ regime.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300">
                                Agregar Detalle
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Nurse Record Details -->
                <div class="p-8 space-y-4  bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Detalles del Registro</h3>

                    <div v-for="detail in details" :key="detail.id" :class="[
                        'rounded-lg p-4 shadow-md flex justify-between items-center transition-colors',
                        detail.suspended_at
                            ? 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 hover:bg-red-100 dark:hover:bg-red-900/30'
                            : 'bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-900'
                    ]">
                        <div class="flex-grow">
                            <div class="font-semibold text-gray-900 dark:text-white">
                                {{ detail.order }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ detail.regime }}
                            </div>
                            <div v-if="detail.suspended_at"
                                class="text-sm text-red-600 dark:text-red-400 mt-1 font-medium flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                Suspendido: {{ detail.suspended_at }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ detail.created_at }}
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <button @click="openEditModal(detail)"
                                class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Editar</span>
                            </button>
                        </div>
                    </div>

                    <div v-if="details.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No hay eventos de ordenes disponibles
                    </div>
                </div>


            </div>
        </div>


        <DialogModal :show="isOpen" @close="isOpen = false">
            <!-- Header del modal -->
            <template #title>
                Editar orden
            </template>

            <!-- Contenido del modal -->
            <template #content>
                <div class="">
                    <form>
                        <div class="grid md:grid-cols-[2fr_1fr] gap-4">
                            <div>
                                <label for="orden"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Órden
                                </label>
                                <input type="text" id="order" v-model="selectedDetail.order" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                    placeholder="Orden médica" />
                            </div>

                            <div>
                                <label for="regime"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Régimen
                                </label>
                                <select id="regime" v-model="selectedDetail.regime"
                                    class="w-full text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option :value="regime.name" v-for="regime in regimes" :key="regime.id">
                                        {{ regime.name }}
                                    </option>
                                </select>
                            </div>

                            <div v-if="!originalSuspendedState">
                                <div class="flex items-center me-4">
                                    <input :checked="!!selectedDetail.suspended_at"
                                        @change="selectedDetail.suspended_at = $event.target.checked ? new Date().toISOString().slice(0, 19).replace('T', ' ') : null"
                                        id="suspended_at" type="checkbox" value=""
                                        class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="red-checkbox"
                                        class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Suspender</label>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </template>

            <!-- Footer del modal -->
            <template #footer>
                <button @click="submitUpdateDetail"
                    class="mr-6 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                    Actualizar
                </button>
                <button @click="isOpen = false"
                    class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cerrar
                </button>
            </template>
        </DialogModal>

    </AppLayout>
</template>

<script>
import DialogModal from '@/Components/DialogModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';


export default {
    components: {
        AppLayout,
        Link,
        DialogModal,
    },
    props: {
        medicalOrder: Object,
        errors: {
            type: Array,
            default: () => []
        },
        admissions: Array,
        details: Array,
        regimes: Array,
        // datos: Object
    },
    data() {
        return {
            selectedDetail: ref(null),
            isOpen: ref(false),
            originalSuspendedState: ref(null),

            isVisible: false,
            formAdmission: {
                admission_id: this.medicalOrder.admission_id
            },
            formDetail: {
                medical_order_id: this.medicalOrder.id,
                order: null,
                regime: null,
            }
        }
    },
    methods: {
        toggleEditAdmission() {
            this.isVisible = !this.isVisible;
        },
        submitAdmission() {
            this.$inertia.put(route('medicalOrders.update', this.medicalOrder.id), this.formAdmission)
            this.toggleEditAdmission()
        },
        submit() {
            this.$inertia.post(route('medicalOrderDetails.store'),
                this.formDetail,
                {
                    onSuccess: () => {
                        this.formDetail = {
                            medical_order_id: this.medicalOrder.id,
                            medication: '',
                            comment: '',
                        };
                    }
                });
        },
        submitUpdateDetail() {
            this.$inertia.put(route('medicalOrderDetails.update', this.selectedDetail.id), this.selectedDetail)
            this.isVisible = false
            this.isOpen = false

        },
        openEditModal(detail) {
            this.selectedDetail = { ...detail };
            this.originalSuspendedState = detail.suspended_at;
            this.isOpen = true;
        },
    }


}
</script>
