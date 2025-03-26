<template>
    <AppLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight text-center">
                <BreadCrumb :items="[
                    {
                        text: 'Usuarios',
                        route: route('users.index')
                    },
                    {
                        text: 'Crear'
                    },
                ]" />
            </h2>
        </template>

        <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">
            <form @submit.prevent="submit"
                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">

                <!-- Información Personal Section -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Información Personal
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <InputLabel for="name" value="Nombres" />
                            <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required
                                autocomplete="name" />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <InputLabel for="last_name" value="Apellidos" />
                            <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full"
                                required autocomplete="last_name" />
                            <InputError :message="form.errors.last_name" class="mt-2" />
                        </div>

                        <!-- Cédula -->
                        <div>
                            <InputLabel for="identification_card" value="Cédula" />
                            <CedulaInput v-model="form.identification_card" class="mt-1" />
                            <InputError :message="form.errors.identification_card" class="mt-2" />
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div>
                            <InputLabel for="birthdate" value="Fecha de Nacimiento" />
                            <DateInput v-model="form.birthdate" />
                            <InputError :message="form.errors.birthdate" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto Section -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Información de Contacto
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Correo Electrónico -->
                        <div>
                            <InputLabel for="email" value="Correo Electrónico" />
                            <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
                                autocomplete="email" />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <InputLabel for="phone" value="Teléfono" />
                            <PhoneInput v-model="form.phone" />
                            <InputError :message="form.errors.phone" class="mt-2" />
                        </div>

                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <InputLabel for="address" value="Dirección" />
                            <TextAreaInput id="address" v-model="form.address" rows="3" class="mt-1 block w-full"
                                required />
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Información Profesional Section -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Información Profesional
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rol -->
                        <div>
                            <InputLabel for="role" value="Rol" />
                            <select @change="setExequaturVisibily" required id="role" v-model="form.role"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option v-for="role in roles" :key="role" :value="role.name">
                                    <FormatRole :role="role.name" />
                                </option>
                            </select>
                            <InputError :message="form.errors.role" class="mt-2" />
                        </div>

                        <!-- Especialidad -->
                        <div>
                            <InputLabel for="specialty" value="Especialidad" />
                            <TextInput id="specialty" v-model="form.specialty" type="text" class="mt-1 block w-full"
                                required autocomplete="specialty" />
                            <InputError :message="form.errors.specialty" class="mt-2" />
                        </div>

                        <!-- Área -->
                        <div>
                            <InputLabel for="area" value="Área" />
                            <SelectInput v-model:model-value="form.area" :options="areas" />
                            <InputError :message="form.errors.area" class="mt-2" />
                        </div>

                        <!-- Posición -->
                        <div>
                            <InputLabel for="position" value="Posición" />
                            <TextInput id="position" v-model="form.position" type="text" class="mt-1 block w-full"
                                required autocomplete="position" />
                            <InputError :message="form.errors.position" class="mt-2" />
                        </div>

                        <!-- Exequatur (Condicional) -->
                        <div v-if="exequaturVisible">
                            <InputLabel for="exequatur" value="Exequatur" />
                            <TextInput id="exequatur" v-model="form.exequatur" type="text" class="mt-1 block w-full"
                                autocomplete="exequatur" />
                            <InputError :message="form.errors.exequatur" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Credenciales Section -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Credenciales
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contraseña -->
                        <div>
                            <InputLabel for="password" value="Contraseña" />
                            <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full"
                                required autocomplete="password" />
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div>
                            <InputLabel for="password_confirmation" value="Confirmar Contraseña" />
                            <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password"
                                class="mt-1 block w-full" required autocomplete="password_confirmation" />
                            <InputError :message="form.errors.password_confirmation" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Observaciones Section -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Observaciones
                    </h3>

                    <div>
                        <InputLabel for="comment" value="Comentarios Adicionales" />
                        <TextAreaInput id="comment" v-model="form.comment" rows="3" class="mt-1 block w-full" />
                        <InputError :message="form.errors.comment" class="mt-2" />
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-200 dark:bg-gray-700 flex justify-end space-x-4 rounded-b-lg">
                    <Link :href="route('users.index')" as="button"
                        class="px-4 py-2 text-sm font-medium text-gray-100 bg-slate-600 dark:text-white border border-gray-200 dark:border-gray-600 rounded-md hover:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Cancelar
                    </Link>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Guardar
                    </button>
                    <button @click="saveAndNew" type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Guardar y Crear Otro
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<script>
import BirthDateInput from '@/Components/DateInput.vue';
import CedulaInput from '@/Components/CedulaInput.vue';
import ExequaturInput from '@/Components/ExequaturInput.vue';
import FormatRole from '@/Components/FormatRole.vue';
import InputError from '@/Components/InputError.vue';
import PhoneInput from '@/Components/PhoneInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import { ref } from 'vue';
import DateInput from '@/Components/DateInput.vue';

export default {
    components: {
        AppLayout,
        Link,
        InputError,
        PhoneInput,
        ExequaturInput,
        CedulaInput,
        BirthDateInput,
        FormatRole,
        InputLabel,
        TextInput,
        SelectInput,
        TextAreaInput,
        BreadCrumb,
        DateInput
    },
    props: {
        roles: Array,
        previousUrl: String,
        areas: Array,
    },
    data() {
        return {
            exequaturVisible: ref(false),
            form: useForm({
                name: '',
                last_name: '',
                email: '',
                role: '',
                password: '',
                password_confirmation: '',
                identification_card: '',
                exequatur: '',
                specialty: '',
                area: '',
                phone: '',
                address: '',
                birthdate: '',
                position: '',
                comment: '',
                saveAndNew: false,
            }),
        }
    },
    methods: {
        submit() {
            this.$inertia.post(route('users.store'), this.form, {
                onSuccess: () => {
                    this.form.reset(),
                    this.form.errors = null;
                },
                onError: (errors) => {
                    this.form.errors = errors;
                }
            });
        },
        save() {
            this.form.saveAndNew = false;
            this.submit()
        },
        saveAndNew() {
            this.form.saveAndNew = true;
            this.submit()
        },
        setExequaturVisibily() {
            if (this.form.role === 'doctor' || this.form.role === 'nurse') {
                this.exequaturVisible = true;
            } else {
                this.exequaturVisible = false;
            }
        }
    }
}
</script>
