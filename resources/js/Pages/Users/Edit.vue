<template>
    <AppLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight text-center">
                Editar Usuario
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <div v-if="Object.keys(errors).length"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Ups!</strong>
                    <span class="block sm:inline">Por favor, corrige los errores en el formulario.</span>
                    <ul>
                        <li v-for="error in Object.values(errors)" class="text-sm">{{ error }}</li>
                    </ul>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="name"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Nombres</label>
                                <input type="text" id="name" v-model="form.name"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="last_name"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Apellidos</label>
                                <input type="text" id="last_name" v-model="form.last_name"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.last_name" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="email"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Correo</label>
                                <input type="email" id="email" v-model="form.email"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <CedulaInput v-model="form.identification_card" />
                                <InputError :message="form.errors.identification_card" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <ExequaturInput v-model="form.exequatur" />
                                <InputError :message="form.errors.exequatur" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="role"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Rol</label>
                                <select required id="role" v-model="form.role" name="role"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500">
                                    <option v-for="role in roles" :key="role.id" :value="role.name">
                                        <FormatRole :role="role.name" />
                                    </option>
                                </select>
                                <InputError :message="form.errors.role" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="address"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Dirección</label>
                                <textarea id="address" v-model="form.address" rows="4"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required></textarea>
                                <InputError :message="form.errors.address" class="mt-2" />
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="specialty"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Especialidad</label>
                                <input type="text" id="specialty" v-model="form.specialty"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.specialty" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="area"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Áreas</label>
                                <select required id="area" v-model="form.area"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500">
                                    <option v-for="area in areas" :key="area" :value="area.name">{{ area.name }}
                                    </option>
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
                                <label for="position"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Posición</label>
                                <input type="text" id="position" v-model="form.position"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                    required>
                                <InputError :message="form.errors.position" class="mt-2" />
                            </div>

                            <div class="space-y-2">
                                <label for="comment"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Observación</label>
                                <textarea id="comment" v-model="form.comment" rows="4"
                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"></textarea>
                                <InputError :message="form.errors.comment" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="space-x-4 pt-4">
                        <button v-show="userChangingPass == null" type="button" @click="userChangingPass = true"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:to-blue-600 transition-all duration-200">
                            Cambiar Contraseña
                        </button>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 mr-0">
                        <div v-show="userChangingPass != null"
                            class="py-2 px-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <!-- Formulario para cambiar la contraseña  -->
                            <form @submit.prevent="changePassword" class="space-y-6">
                                <!-- Left Column -->
                                <div class="space-y-4">

                                    <div class="space-y-2">
                                        <label for="password"
                                            class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Nueva
                                            contraseña</label>
                                        <input type="password" id="password" v-model="formPassword.password"
                                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                            required>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="password_confirmation"
                                            class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Confirmar
                                            Contraseña</label>
                                        <input type="password" id="password_confirmation"
                                            v-model="formPassword.password_confirmation"
                                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
                                            required>
                                    </div>

                                    <div class="flex justify-end space-x-4 pt-4">
                                        <button @click="userChangingPass = null"
                                            class="px-4 py-2 text-sm font-medium text-gray-300 bg-slate-600 dark:bg-gray-700 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200">
                                            Actualizar
                                        </button>
                                    </div>

                                </div>
                            </form>
                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4 pt-4">
                        <button v-if="user.active == 1" @click="userBeingDeleted = true" type="button"
                            class="inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:to-red-600 transition-all duration-200">
                            Deshabilitar
                        </button>
                        <button v-else @click="restoreUser" type="button"
                            class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:to-green-600 transition-all duration-200">
                            Habilitar
                        </button>
                        <Link :href="route('users.index')" type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-700 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                        Cancelar
                        </Link>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para confirmar eliminacion -->
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

                <DangerButton class="ms-3" @click="deleteUser">
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
import { useGoBack } from '@/composables/useGoBack';
import PhoneInput from '@/Components/PhoneInput.vue';
import CedulaInput from '@/Components/CedulaInput.vue';
import ExequaturInput from '@/Components/ExequaturInput.vue';
import BirthDateInput from '@/Components/BirthDateInput.vue';
import FormatRole from '@/Components/FormatRole.vue';
import InputError from '@/Components/InputError.vue';

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
        InputError
    },
    data() {
        return {
            userBeingDeleted: ref(null),
            userChangingPass: ref(null),
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
            formPassword: {
                password: null,
                password_confirmation: null,
            },
        }
    },
    methods: {
        submit() {
            this.$inertia.put(route('users.update', this.user.id), this.form, {
                onError: (errors) => {
                    this.form.errors = errors
                }
            });
        },
        deleteUser() {
            this.userBeingDeleted = null;
            this.$inertia.delete(route('users.destroy', this.user.id));
        },
        restoreUser() {
            this.$inertia.put(route('users.update', this.user.id), { active: true });
        },
        changePassword() {
            this.$inertia.put(route('users.update', this.user.id), this.formPassword, {
                onSuccess: () => {
                    this.formPassword.password = null;
                    this.formPassword.password_confirmation = null;
                },
            });
        }
    },
    setup() {
        const { goBack } = useGoBack()
        return { goBack }
    }
}
</script>
