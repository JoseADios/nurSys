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
                                {{ doctor.name }} {{ patient.last_name }}
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
                                Agregar Detalle
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Nurse Record Details -->
                <div class="p-8 space-y-4  bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Detalles del Registro</h3>

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
                            <Link :href="route('medicalOrderDetails.edit', detail.id)"
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
                        No hay eventos de ordenes disponibles
                    </div>
                </div>


            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        medicalOrder: Object,
        errors: {
            type: Array,
            default: () => []
        },
        admissions: Array,
        patient: Object,
        doctor: Object,
        bed: Object,
        details: Array,
        // datos: Object
    },
    components: {
        AppLayout,
        Link,
    },
    data() {
        return {
            isVisible: false,
            formAdmission: {
                admission_id: this.medicalOrder.admission_id
            },
            formDetail: {
                medicalOrder_id: this.medicalOrder.id,
                medication: null,
                comment: null,
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
                            medicalOrder_id: this.medicalOrder.id,
                            medication: '',
                            comment: '',
                        };
                    }
                });
        },

    }
}
</script>
