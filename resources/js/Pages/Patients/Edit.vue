<template>
    <AppLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-white leading-tight text-center">
                Editar Ingreso de Paciente
            </h2>
        </template>

        <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">
            <form @submit.prevent="submit" class="rounded-lg shadow-lg bg-gray-800">
                <!-- Personal Information Section -->
                <div class="p-6 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Información Personal
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombres -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-white">Nombres</label>
                            <input type="text" id="first_name" v-model="form.first_name"
                                class="block p-2.5 w-full text-sm text-white bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                            <InputError :message="form.errors.first_name" class="mt-2" />
                        </div>

                        <!-- Primer Apellido -->
                        <div>
                            <label for="first_surname" class="block text-sm font-medium text-white">Primer
                                Apellido</label>
                            <input type="text" id="first_surname" v-model="form.first_surname"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                            <InputError :message="form.errors.first_surname" class="mt-2" />
                        </div>

                        <!-- Segundo Apellido -->
                        <div>
                            <label for="second_surname" class="block text-sm font-medium text-white">Segundo
                                Apellido</label>
                            <input type="text" id="second_surname" v-model="form.second_surname"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                            <InputError :message="form.errors.second_surname" class="mt-2" />
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <PhoneInput v-model="form.phone" />
                            <InputError :message="form.errors.phone" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Identification Section -->
                <div class="p-6 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        Identificación y Nacionalidad
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cédula -->
                        <div>
                            <CedulaInput v-model="form.identification_card" />
                            <InputError :message="form.errors.identification_card" class="mt-2" />
                        </div>

                        <!-- Nacionalidad -->
                        <div>
                            <label for="nationality" class="block text-sm font-medium text-white">Nacionalidad</label>
                            <input list="nationalityOptions" id="nationality" v-model="form.nationality"
                                class="block p-2.5 w-full text-sm text-white bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                            <datalist id="nationalityOptions">
                                <option v-for="nationality in nationalities" :key="nationality.id">
                                    {{ nationality.name }}
                                </option>
                            </datalist>
                            </input>
                            <InputError :message="form.errors.nationality" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="p-6 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Información de Contacto
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-white">Correo Electrónico</label>
                            <input type="email" id="email" v-model="form.email"
                                class="block p-2.5 w-full text-sm text-white bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-white">Dirección</label>
                            <textarea id="address" v-model="form.address" rows="3"
                                class="block p-2.5 w-full text-sm text-white bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required></textarea>
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="p-6 ">
                    <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Información Adicional
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Fecha de nacimiento -->
                        <div>
                            <label for="birthdate" class="block text-sm font-medium text-white">Fecha de
                                Nacimiento</label>
                            <input type="date" id="birthdate" v-model="form.birthdate"
                                class="block p-2.5 w-full text-sm text-white bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                            <InputError :message="form.errors.birthdate" class="mt-2" />
                        </div>

                        <!-- Estado Civil -->
                        <div>
                            <label for="marital_status" class="block text-sm font-medium text-white">Estado
                                Civil</label>
                            <select id="marital_status" v-model="form.marital_status"
                                class="block p-2.5 w-full text-sm text-white bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                                <option v-for="status in maritalSatuses" :key="status.id">
                                    {{ status.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.marital_status" class="mt-2" />
                        </div>

                        <!-- Cargo -->
                        <div>
                            <label for="position" class="block text-sm font-medium text-white">Cargo</label>
                            <input type="text" id="position" v-model="form.position"
                                class="block p-2.5 w-full text-sm text-white bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                            <InputError :message="form.errors.position" class="mt-2" />
                        </div>

                        <!-- ARS -->
                        <div>
                            <label for="ars" class="block text-sm font-medium text-white">ARS</label>
                            <select id="ars" v-model="form.ars"
                                class="block p-2.5 w-full text-sm text-white bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">Ninguno</option>
                                <option v-for="ars in arss" :key="ars.id">
                                    {{ ars.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.ars" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-700 flex justify-end space-x-4 rounded-b-lg">
                    <AccessGate :permission="['patient.delete']">
                        <button type="button" v-if="patient.active == 1" @click="patientBeingDeleted = true"
                            class="inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:to-red-600 transition-all duration-200">
                            Eliminar
                        </button>
                        <button type="button" v-else @click="restorePatient"
                            class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:to-green-600 transition-all duration-200">
                            Restaurar
                        </button>
                    </AccessGate>
                    <Link :href="route('patients.index')" as="button"
                        class="px-4 py-2 text-sm font-medium text-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Volver
                    </Link>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
        <AccessGate :permission="['patient.delete']">
            <ConfirmationModal :show="patientBeingDeleted != null" @close="patientBeingDeleted = null">
                <template #title>
                    Eliminar Ingreso
                </template>

                <template #content>
                    ¿Estás seguro de que deseas eliminar este ingreso?
                </template>

                <template #footer>
                    <SecondaryButton @click="patientBeingDeleted = null">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton class="ms-3" @click="deletePatient">
                        Eliminar
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </AccessGate>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { useGoBack } from '@/composables/useGoBack';
import CedulaInput from '@/Components/CedulaInput.vue';
import PhoneInput from '@/Components/PhoneInput.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';

export default {
    props: {
        patient: Object,
        nationalities: Array,
        maritalSatuses: Array,
        arss: Array,
        previousUrl: String,
    },
    components: {
        AppLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        InputError,
        CedulaInput,
        PhoneInput,
        AccessGate
    },
    data() {
        return {
            patientBeingDeleted: ref(null),
            form: useForm({
                first_name: this.patient.first_name,
                first_surname: this.patient.first_surname,
                second_surname: this.patient.second_surname,
                phone: this.patient.phone,
                identification_card: this.patient.identification_card,
                nationality: this.patient.nationality,
                email: this.patient.email,
                birthdate: this.patient.birthdate,
                position: this.patient.position,
                marital_status: this.patient.marital_status,
                address: this.patient.address,
                ars: this.patient.ars,
            })
        }
    },
    methods: {
        submit() {
            this.$inertia.put(route('patients.update', this.patient.id), this.form, {
                onError: (errors) => {
                    this.form.errors = errors;
                }
            })
        },
        deletePatient() {
            this.patientBeingDeleted = false
            this.$inertia.delete(route('patients.destroy', this.patient.id));
        },
        restorePatient() {
            this.$inertia.put(route('patients.update', this.patient.id), { active: true });
        }
    },
    setup() {
        const { goBack } = useGoBack()
        return { goBack }
    }
}
</script>
