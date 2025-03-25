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
                        text: 'Crear'
                    },
                ]" />
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
                <!-- show errors -->
                <!-- <div v-if="Object.keys(errors).length"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Ups!</strong>
                    <span class="block sm:inline">Por favor, corrige los errores en el formulario.</span>
                    <ul>
                        <li v-for="error in Object.values(errors)" class="text-sm">{{ error }}</li>
                    </ul>
                </div> -->

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Grid container -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <!-- Name -->
                        <div class="space-y-4">

                            <div class="mb-6">
                                <InputLabel for="name" value="Nombre" />
                                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required
                                    autocomplete="name" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>
                            <div class="mb-6">
                                <InputLabel for="last_name" value="Apellidos" />
                                <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full"
                                    required autocomplete="last_name" />
                                <InputError :message="form.errors.last_name" class="mt-2" />
                            </div>
                            <div class="mb-6">
                                <InputLabel for="email" value="Correo" />
                                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full"
                                    required autocomplete="email" />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>
                            <div class="mb-6">
                                <InputLabel for="password" value="Contraseña" />
                                <TextInput id="password" v-model="form.password" type="password"
                                    class="mt-1 block w-full" required autocomplete="password" />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>
                            <div class="mb-6">
                                <InputLabel for="password_confirmation" value="Confirmar Contraseña" />
                                <TextInput id="password_confirmation" v-model="form.password_confirmation"
                                    type="password" class="mt-1 block w-full" required
                                    autocomplete="password_confirmation" />
                                <InputError :message="form.errors.password_confirmation" class="mt-2" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="role" value="Rol" />
                                <select required id="role" v-model="form.role"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option v-for="role in roles" :key="role" :value="role.id">
                                        <FormatRole :role="role.name" />
                                    </option>
                                </select>
                                <InputError :message="form.errors.role" class="mt-2" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="identification_card" value="Cédula" />
                                <CedulaInput v-model="form.identification_card" />
                                <InputError :message="form.errors.identification_card" class="mt-2" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="exequatur" value="Exequatur" />
                                <TextInput id="exequatur" v-model="form.exequatur" type="number" class="mt-1 block w-full"
                                    required autocomplete="exequatur" />
                                <InputError :message="form.errors.exequatur" class="mt-2" />
                            </div>

                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="mb-6">
                                <InputLabel for="specialty" value="Especialidad" />
                                <TextInput id="specialty" v-model="form.specialty" type="text" class="mt-1 block w-full"
                                    required autocomplete="specialty" />
                                <InputError :message="form.errors.specialty" class="mt-2" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="area" value="Área" />
                                <SelectInput v-model:model-value="form.area" :options="areas"   />
                                <InputError :message="form.errors.specialty" class="mt-2" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="phone" value="Teléfono" />
                                <PhoneInput id="specialty" v-model="form.phone" required autocomplete="phone" />
                                <InputError :message="form.errors.phone" class="mt-2" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="birthdate" value="Fecha de nacimiento" />
                                <BirthDateInput id="specialty" v-model="form.birthdate" required />
                                <InputError :message="form.errors.birthdate" class="mt-2" />
                            </div>
                            <div class="mb-6">
                                <InputLabel for="position" value="Posicón" />
                                <TextInput id="position" v-model="form.position" type="text" class="mt-1 block w-full"
                                    required autocomplete="position" />
                                <InputError :message="form.errors.position" class="mt-2" />
                            </div>
                            <div class="mb-6">
                                <InputLabel for="address" value="Dirección" />
                                <TextAreaInput id="address" v-model="form.address" type="text" class="mt-1 block w-full"
                                    required autocomplete="address" />
                                <InputError :message="form.errors.address" class="mt-2" />
                            </div>
                            <div class="mb-6">
                                <InputLabel for="comment" value="Observación" />
                                <TextAreaInput id="comment" v-model="form.comment" type="text" class="mt-1 block w-full"
                                     autocomplete="comment" />
                                <InputError :message="form.errors.comment" class="mt-2" />
                            </div>
                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4 pt-4">
                        <Link :href="route('users.index')"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                        Cancelar
                        </Link>
                        <button @click="save"
                            class="px-4 py-2 text-sm font-medium text-white dark:text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200">
                            Guardar
                        </button>
                        <button @click="saveAndNew"
                            class="px-4 py-2 text-sm font-medium text-white dark:text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200">
                            Guardar y Crear Otro
                        </button>
                    </div>
                </form>
            </div>
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
        BreadCrumb
    },
    props: {
        roles: Array,
        previousUrl: String,
        areas: Array,
    },
    data() {
        return {
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
                onSuccess: () => this.form.reset(),
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
        }
    }
}
</script>
