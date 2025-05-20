<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <BreadCrumb :items="[
                    {
                        text: 'Usuarios',
                        route: route('users.index')
                    },
                    {
                        text: 'Editar'
                    },
                ]" />
            </h2>
        </template>

        <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">

            <form @submit.prevent="submitProfile"
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">

                <!-- Información Personal Section -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <UserIcon class="h-5 w-5 mr-2 text-[#696CFF]" />
                        Información Personal
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <InputLabel for="name" value="Nombres" :required="true" />
                            <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required
                                autocomplete="name" />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <InputLabel for="last_name" value="Apellidos" :required="true" />
                            <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full"
                                required autocomplete="last_name" />
                            <InputError :message="form.errors.last_name" class="mt-2" />
                        </div>

                        <!-- Cédula -->
                        <div>
                            <InputLabel for="identification_card" value="Cédula" :required="true" />
                            <CedulaInput v-model="form.identification_card" class="mt-1" />
                            <InputError :message="form.errors.identification_card" class="mt-2" />
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div>
                            <InputLabel for="birthdate" value="Fecha de Nacimiento" :required="true" />
                            <DateInput class="mt-1" v-model="form.birthdate" />
                            <InputError :message="form.errors.birthdate" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto Section -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <MailIcon class="h-5 w-5 mr-2 text-[#696CFF]" />
                        Información de Contacto
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Correo Electrónico -->
                        <div>
                            <InputLabel for="email" value="Correo Electrónico" :required="true" />
                            <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
                                autocomplete="email" />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <InputLabel for="phone" value="Teléfono" :required="true" />
                            <PhoneInput class="mt-1" v-model="form.phone" required />
                            <InputError :message="form.errors.phone" class="mt-2" />
                        </div>

                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <InputLabel for="address" value="Dirección" :required="true" />
                            <TextAreaInput id="address" v-model="form.address" rows="3" class="mt-1 block w-full"
                                required />
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Información Profesional Section -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <BriefCaseIcon class="h-5 w-5 mr-2 text-[#696CFF]" />
                        Información Profesional
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rol -->
                        <div>
                            <InputLabel for="role" value="Rol" :required="true" />
                            <select @change="setExequaturVisibily" required id="role" v-model="form.role"
                                class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option v-for="role in roles" :key="role" :value="role.name">
                                    <FormatRole :role="role.name" />
                                </option>
                            </select>
                            <InputError :message="form.errors.role" class="mt-2" />
                        </div>

                        <!-- Especialidad -->
                        <div>
                            <InputLabel for="specialty" value="Especialidad" :required="true" />
                            <TextInput id="specialty" v-model="form.specialty" type="text" class="mt-1 block w-full"
                                required autocomplete="specialty" />
                            <InputError :message="form.errors.specialty" class="mt-2" />
                        </div>

                        <!-- Área -->
                        <div>
                            <InputLabel for="area" value="Área" />
                            <SelectInput class="mt-1" v-model:model-value="form.area" :options="areas" />
                            <InputError :message="form.errors.area" class="mt-2" />
                        </div>

                        <!-- Posición -->
                        <div>
                            <InputLabel for="position" value="Posición" />
                            <TextInput id="position" v-model="form.position" type="text" class="mt-1 block w-full"
                                autocomplete="position" />
                            <InputError :message="form.errors.position" class="mt-2" />
                        </div>

                        <!-- Exequatur (Condicional) -->
                        <div v-if="exequaturVisible">
                            <InputLabel for="exequatur" value="Exequatur" :required="exequaturVisible" />
                            <TextInput id="exequatur" v-model="form.exequatur" :required="exequaturVisible" type="text"
                                class="mt-1 block w-full" autocomplete="exequatur" />
                            <InputError :message="form.errors.exequatur" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Observaciones Section -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <FileTexIcon class="h-5 w-5 mr-2 text-[#696CFF]" />
                        Observaciones
                    </h3>

                    <div>
                        <InputLabel for="comment" value="Comentarios Adicionales" />
                        <TextAreaInput id="comment" v-model="form.comment" rows="3" class="mt-1 block w-full" />
                        <InputError :message="form.errors.comment" class="mt-2" />
                    </div>
                </div>

                <!-- Form Actions -->
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex flex-col sm:flex-row gap-2 justify-between items-center rounded-b-lg">
                    <div class="space-y-2 flex flex-col sm:block md:space-x-2">
                        <DangerButton v-if="user.active == 1" @click="userBeingDeleted = true" type="button">
                            Deshabilitar
                        </DangerButton>
                        <PersonalizableButton v-else @click="restoreUser" color="green" type="button"
                            :loading="isProcessingActive">
                            Habilitar
                        </PersonalizableButton>
                        <PersonalizableButton @click="userChangingPass = true" color="blue" shade="600" type="button">Cambiar
                            contraseña
                        </PersonalizableButton>
                    </div>
                    <div class="flex space-x-4">
                        <PersonalizableLink :href="route('users.index')" color="gray"> Volver
                        </PersonalizableLink>
                        <PrimaryButton :is-loading="form.processing"> Actualizar
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </div>

        <DialogModal :show="userChangingPass" @close="userChangingPass = false">
            <!-- Header del modal -->
            <template #title>
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center space-x-3">
                        <PasswordUserIcon class="w-6 h-6 text-blue-500" />
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Cambiar Contraseña
                        </h3>
                    </div>
                    <button @click="userChangingPass = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <XIcon class="w-5 h-5" />
                    </button>
                </div>
            </template>

            <!-- Contenido del modal -->
            <template #content>
                <form @submit.prevent="submitPassword" class="px-4 space-y-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="text" class="hidden" name="username" id="username" :value="user.email"
                            autocomplete="username">
                        <div>
                            <InputLabel for="password" value="Nueva Contraseña"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" />
                            <TextInput id="password" v-model="passwordForm.password" type="password"
                                autocomplete="new-password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-800 dark:border-gray-700" />
                            <InputError :message="passwordForm.errors.password" class="mt-2 text-sm text-red-500" />
                        </div>

                        <div class="md:col-span-1">
                            <InputLabel for="password_confirmation" value="Confirmar Nueva Contraseña"
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" />
                            <TextInput id="password_confirmation" v-model="passwordForm.password_confirmation"
                                type="password" autocomplete="new-password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-800 dark:border-gray-700" />
                            <InputError :message="passwordForm.errors.password_confirmation"
                                class="mt-2 text-sm text-red-500" />
                        </div>
                    </div>
                </form>
            </template>

            <!-- Footer del modal -->
            <template #footer>
                <div class="flex justify-end p-4">
                    <SecondaryButton type="button" @click="userChangingPass = false" class="mr-2">
                        Cancelar
                    </SecondaryButton>

                    <PrimaryButton type="submit" @click="submitPassword" :is-loading="passwordForm.processing">
                        Cambiar Contraseña
                    </PrimaryButton>
                </div>
            </template>
        </DialogModal>
        <!-- Modal para confirmar eliminación -->
        <ConfirmationModal :show="userBeingDeleted != null" @close="userBeingDeleted = null">
            <template #title>
                Deshabilitar Usuario
            </template>

            <template #content>
                ¿Estás seguro de que deseas deshabilitar este usuario? <br>
                El usuario no podrá acceder al sistema.
            </template>

            <template #footer>
                <SecondaryButton @click="userBeingDeleted = null">
                    Cancelar
                </SecondaryButton>

                <DangerButton class="ms-3" @click="deleteUser" :class="{ 'opacity-25': isProcessingActive }"
                    :disabled="isProcessingActive">
                    Deshabilitar
                </DangerButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PhoneInput from '@/Components/PhoneInput.vue';
import CedulaInput from '@/Components/CedulaInput.vue';
import ExequaturInput from '@/Components/ExequaturInput.vue';
import BirthDateInput from '@/Components/DateInput.vue';
import FormatRole from '@/Components/FormatRole.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import DialogModal from '@/Components/DialogModal.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import DateInput from '@/Components/DateInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PasswordUserIcon from '@/Components/Icons/PasswordUserIcon.vue';
import FileTexIcon from '@/Components/Icons/FileTexIcon.vue';
import XIcon from '@/Components/Icons/XIcon.vue';
import MailIcon from '@/Components/Icons/MailIcon.vue';
import BriefCaseIcon from '@/Components/Icons/BriefCaseIcon.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import SecondaryLink from '@/Components/SecondaryLink.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';
import PersonalizableLink from '@/Components/PersonalizableLink.vue';

export default {
    props: {
        user: Object,
        roles: Array,
        hasRoles: Array,
        errors: Object,
        previousUrl: String,
        areas: Array,
    },
    components: {
        AppLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        PhoneInput,
        CedulaInput,
        ExequaturInput,
        BirthDateInput,
        FormatRole,
        InputError,
        TextInput,
        SelectInput,
        InputLabel,
        TextAreaInput,
        DialogModal,
        BreadCrumb,
        DateInput,
        DialogModal,
        PrimaryButton,
        PasswordUserIcon,
        FileTexIcon,
        XIcon,
        MailIcon,
        BriefCaseIcon,
        UserIcon,
        SecondaryLink,
        PersonalizableButton,
        PersonalizableLink
    },
    data() {
        return {
            userBeingDeleted: ref(null),
            userChangingPass: ref(false),
            exequaturVisible: ref(false),
            isProcessingActive: ref(false),
            form: useForm({
                name: this.user.name,
                last_name: this.user.last_name,
                email: this.user.email,
                role: this.hasRoles[0],
                identification_card: this.user.identification_card,
                exequatur: this.user.exequatur,
                specialty: this.user.specialty,
                area: this.user.area,
                phone: this.user.phone,
                address: this.user.address,
                birthdate: this.user.birthdate,
                position: this.user.position,
                comment: this.user.comment,
            }),
            passwordForm: useForm({
                password: null,
                password_confirmation: null,
            }),
        }
    },
    mounted() {
        this.setExequaturVisibily();
    },
    methods: {
        submitProfile() {
            Object.keys(this.form.errors).forEach((key) => {
                if (this.form[key]) {
                    delete this.form.errors[key];
                }
            });
            this.form.put(route('users.update', this.user.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.form.errors = []
                },
                onError: (errors) => {
                    this.form.errors = errors
                }
            });
        },
        deleteUser() {
            this.isProcessingActive = true;
            this.userBeingDeleted = null;
            this.$inertia.delete(route('users.destroy', this.user.id), {
                preserveScroll: true,
                onFinish: () => {
                    this.isProcessingActive = false;
                }
            });
        },
        restoreUser() {
            this.isProcessingActive = true;
            this.$inertia.put(route('users.update', this.user.id), { active: true }, {
                preserveScroll: true,
                onFinish: () => {
                    this.isProcessingActive = false;
                }
            });
        },
        submitPassword() {
            this.passwordForm.put(route('users.update.password', this.user.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.passwordForm.reset();
                    this.userChangingPass = false
                },
                onError: (errors) => {
                    this.passwordForm.errors = errors
                }
            });
        },
        setExequaturVisibily() {
            if (this.form.role === 'doctor' || this.form.role === 'nurse') {
                this.exequaturVisible = true;
            } else {
                this.exequaturVisible = false;
            }
        }
    },
}
</script>
