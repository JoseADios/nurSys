<template>
    <AppLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight text-center">
                Nuevo Usuario
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

                <!-- <div class="text-white dark:text-white">{{  }}</div> -->

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Grid container -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white">Nombres</label>
                                <input type="text" id="name" v-model="form.name"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="last_name" class="block text-sm font-medium text-gray-900 dark:text-white">Apellidos</label>
                                <input type="text" id="last_name" v-model="form.last_name"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.last_name" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white">Correo</label>
                                <input type="email" id="email" v-model="form.email"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="password" class="block text-sm font-medium text-gray-900 dark:text-white">Contraseña</label>
                                <input type="password" id="password" v-model="form.password"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-900 dark:text-white">Confirmar
                                    Contraseña</label>
                                <input type="password" id="password_confirmation" v-model="form.password_confirmation"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.password_confirmation" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="role" class="block text-sm font-medium text-gray-900 dark:text-white">Rol</label>
                                <select required id="role" v-model="form.role"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500">
                                    <option v-for="role in roles" :key="role" :value="role.id">
                                        <FormatRole :role="role.name" />
                                    </option>
                                </select>
                                <InputError :message="form.errors.role" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <CedulaInput v-model="form.identification_card" />
                                <InputError :message="form.errors.identification_card" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <ExequaturInput v-model="form.exequatur" />
                                <InputError :message="form.errors.exequatur" class="mt-2" />
                            </div>

                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="specialty" class="block text-sm font-medium text-gray-900 dark:text-white">Especialidad</label>
                                <input type="text" id="specialty" v-model="form.specialty"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.specialty" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="area" class="block text-sm font-medium text-gray-900 dark:text-white">Áreas</label>
                                <select required id="area" v-model="form.area"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500">
                                    <option v-for="area in areas" :key="area" :value="area.name">{{ area.name }}</option>
                                </select>
                                <InputError :message="form.errors.area" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <PhoneInput v-model="form.phone" />
                                <InputError :message="form.errors.phone" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <BirthDateInput v-model="form.birthdate" />
                                <InputError :message="form.errors.birthdate" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="position" class="block text-sm font-medium text-gray-900 dark:text-white">Posición</label>
                                <input type="text" id="position" v-model="form.position"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.position" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="comment" class="block text-sm font-medium text-gray-900 dark:text-white">Observación</label>
                                <textarea id="comment" v-model="form.comment" rows="4"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"></textarea>
                                <InputError :message="form.errors.comment" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="address" class="block text-sm font-medium text-gray-900 dark:text-white">Dirección</label>
                                <textarea id="address" v-model="form.address" rows="4"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required></textarea>
                                <InputError :message="form.errors.address" class="mt-2" />
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
import BirthDateInput from '@/Components/BirthDateInput.vue';
import CedulaInput from '@/Components/CedulaInput.vue';
import ExequaturInput from '@/Components/ExequaturInput.vue';
import FormatRole from '@/Components/FormatRole.vue';
import InputError from '@/Components/InputError.vue';
import PhoneInput from '@/Components/PhoneInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

export default {
    components: {
        AppLayout,
        Link,
        InputError,
        PhoneInput,
        ExequaturInput,
        CedulaInput,
        BirthDateInput,
        FormatRole
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
