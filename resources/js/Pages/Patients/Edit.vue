<template>
    <AppLayout title="Editar Paciente">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <BreadCrumb :items="[
                    {
                        text: 'Pacientes',
                        route: route('patients.index')
                    },
                    {
                        text: 'Editar'
                    },
                ]" />
            </h2>
        </template>

        <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">
            <form @submit.prevent="submit"
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <!-- Personal Information Section -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <UserIcon class="h-5 w-5 mr-2 text-[#696CFF]" />
                        Información Personal
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombres -->
                        <div>
                            <InputLabel for="first_name" value="Nombres" :required="true" />
                            <TextInput id="first_name" v-model="form.first_name" class="mt-1 block w-full" required
                                autocomplete="first_name" />
                            <InputError :message="form.errors.first_name" class="mt-2" />
                        </div>

                        <!-- Primer Apellido -->
                        <div>
                            <InputLabel for="first_surname" value="Primer Apellido" :required="true" />
                            <TextInput id="first_surname" v-model="form.first_surname" class="mt-1 block w-full"
                                required autocomplete="first_surname" />
                            <InputError :message="form.errors.first_surname" class="mt-2" />
                        </div>

                        <!-- Segundo Apellido -->
                        <div>
                            <InputLabel for="second_surname" value="Segundo Apellido" />
                            <TextInput id="second_surname" v-model="form.second_surname" class="mt-1 block w-full"
                                required autocomplete="second_surname" />
                            <InputError :message="form.errors.second_surname" class="mt-2" />
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <InputLabel for="phone" value="Teléfono" :required="true" />
                            <PhoneInput class="mt-1" id="phone" v-model="form.phone" />
                            <InputError :message="form.errors.phone" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Identification Section -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <Id2Icon class="h-5 w-5 mr-2 text-[#696CFF]" />
                        Identificación y Nacionalidad
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cédula -->
                        <div>
                            <InputLabel for="identification_card" value="Cédula" :required="cedulaVisibility" />
                            <CedulaInput v-model="form.identification_card" class="mt-1" :required="cedulaVisibility" />
                            <InputError :message="form.errors.identification_card" class="mt-2" />
                        </div>

                        <!-- Nacionalidad -->
                        <div>
                            <InputLabel for="nationality" value="Nacionalidad" :required="true" />
                            <input list="options" id="nationality" v-model="form.nationality"
                                class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required>
                            <datalist id="options">
                                <option v-for="nationality in nationalities" :key="nationality.id">
                                    {{ nationality.name }}
                                </option>
                            </datalist>
                            <InputError :message="form.errors.nationality" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <MailIcon class="h-5 w-5 mr-2 text-[#696CFF]" />
                        Información de Contacto
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div>
                            <InputLabel for="email" value="Correo Electrónico" />
                            <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full"
                                autocomplete="email" />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <InputLabel for="address" value="Dirección" :required="true" />
                            <TextAreaInput id="address" v-model="form.address" rows="3" maxlength="255"
                                class="mt-1 block w-full" required />
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <FileTexIcon class="h-5 w-5 mr-2 text-[#696CFF]" />
                        Información Adicional
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Fecha de nacimiento -->
                        <div>
                            <InputLabel for="birthdate" value="Fecha de Nacimiento" :required="true" />
                            <DateInput v-model="form.birthdate" @change="setCedulaVisibility()" required />
                            <InputError :message="form.errors.birthdate" class="mt-2" />
                        </div>

                        <!-- Estado Civil -->
                        <div>
                            <InputLabel for="marital_status" value="Estado Civil" :required="true" />
                            <SelectInput id="marital_status" v-model="form.marital_status" :options="maritalSatuses"
                                class="mt-1 block w-full" required />
                            <InputError :message="form.errors.marital_status" class="mt-2" />
                        </div>

                        <!-- Cargo -->
                        <div>
                            <InputLabel for="position" value="Cargo" />
                            <TextInput id="position" v-model="form.position" type="text" class="mt-1 block w-full" />
                            <InputError :message="form.errors.position" class="mt-2" />
                        </div>

                        <!-- ARS -->
                        <div>
                            <InputLabel for="ars" value="ARS" />
                            <SelectInput id="ars" v-model="form.ars" :options="arss" class="mt-1 block
                                w-full" />
                            <InputError :message="form.errors.ars" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-4 rounded-b-lg">
                    <AccessGate :permission="['patient.delete']">
                        <DangerButton type="button" v-if="patient.active == 1" @click="patientBeingDeleted = true">
                            Desactivar
                        </DangerButton>
                        <PersonalizableButton type="button" v-else @click="restorePatient"
                            :loading="isProcessingActive">
                            Activar
                        </PersonalizableButton>
                    </AccessGate>
                    <SecondaryLink :href="route('patients.index')"> Volver
                    </SecondaryLink>
                    <PrimaryButton :is-loading="form.processing"> Actualizar
                    </PrimaryButton>
                </div>
            </form>
        </div>
        <AccessGate :permission="['patient.delete']">
            <ConfirmationModal :show="patientBeingDeleted != null" @close="patientBeingDeleted = null">
                <template #title>
                    Desactivar Paciente
                </template>

                <template #content>
                    ¿Estás seguro de que deseas Desactivar este paciente?
                </template>

                <template #footer>
                    <SecondaryButton @click="patientBeingDeleted = null">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton class="ms-3" @click="deletePatient" :class="{ 'opacity-25': isProcessingActive }"
                        :disabled="isProcessingActive">
                        Desactivar
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
import CedulaInput from '@/Components/CedulaInput.vue';
import PhoneInput from '@/Components/PhoneInput.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import Id2Icon from '@/Components/Icons/Id2Icon.vue';
import MailIcon from '@/Components/Icons/MailIcon.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import DateInput from '@/Components/DateInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import FileTexIcon from '@/Components/Icons/FileTexIcon.vue';
import moment from 'moment/moment';
import 'moment/locale/es';
import SecondaryLink from '@/Components/SecondaryLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';

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
        AccessGate,
        UserIcon,
        Id2Icon,
        MailIcon,
        FileTexIcon,
        InputLabel,
        SelectInput,
        DateInput,
        TextAreaInput,
        TextInput,
        BreadCrumb,
        SecondaryLink,
        PrimaryButton,
        PersonalizableButton
    },
    data() {
        return {
            patientBeingDeleted: ref(null),
            isProcessingActive: false,
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
    setup() {
        const cedulaVisibility = ref(false)
        return {
            cedulaVisibility
        };
    },
    mounted() {
        if (this.form.birthdate) {
            let birthdate = moment(this.form.birthdate);
            this.cedulaVisibility = moment().diff(birthdate, 'years') >= 18;
        }
    },
    methods: {
        submit() {
            Object.keys(this.form.errors).forEach((key) => {
                if (this.form[key]) {
                    delete this.form.errors[key];
                }
            });
            this.form.put(route('patients.update', this.patient.id), {
                preserveScroll: true,
                onError: (errors) => {
                    this.form.errors = errors;
                }
            })
        },
        deletePatient() {
            this.patientBeingDeleted = null
            this.isProcessingActive = true;
            this.$inertia.delete(route('patients.destroy', this.patient.id), {
                preserveScroll: true,
                onFinish: () => {
                    this.isProcessingActive = false;
                }
            });
        },
        restorePatient() {
            this.isProcessingActive = true;
            this.$inertia.put(route('patients.update', this.patient.id), { active: true }, {
                preserveScroll: true,
                onFinish: () => {
                    this.isProcessingActive = false;
                }
            });
        },
        setCedulaVisibility() {
            let birthdate = moment(this.form.birthdate);
            this.cedulaVisibility = moment().diff(birthdate, 'years') >= 18;
        }
    }
}
</script>
