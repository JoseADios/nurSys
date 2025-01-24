<template>
    <AppLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-white leading-tight text-center">
                Nuevo Usuario
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 shadow-xl rounded-lg p-6">
                <!-- show errors -->
                <div v-if="Object.keys(errors).length"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Ups!</strong>
                    <span class="block sm:inline">Por favor, corrige los errores en el formulario.</span>
                    <ul>
                        <li v-for="error in Object.values(errors)" class="text-sm">{{ error }}</li>
                    </ul>
                </div>

                <!-- <div class="text-white">{{ reset }}</div> -->

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Grid container -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-medium text-white">Nombres</label>
                                <input type="text" id="name" v-model="form.name"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="last_name" class="block text-sm font-medium text-white">Apellidos</label>
                                <input type="text" id="last_name" v-model="form.last_name"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-white">Correo</label>
                                <input type="email" id="email" v-model="form.email"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="password" class="block text-sm font-medium text-white">Contraseña</label>
                                <input type="password" id="password" v-model="form.password"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-white">Confirmar
                                    Contraseña</label>
                                <input type="password" id="password_confirmation" v-model="form.password_confirmation"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="role" class="block text-sm font-medium text-white">Role</label>
                                <select required id="role" v-model="form.role"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                    <option v-for="role in roles" :key="role" :value="role.id">{{ role.name }}</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="identification_card"
                                    class="block text-sm font-medium text-white">Cédula</label>
                                <input type="text" id="identification_card" v-model="form.identification_card"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="exequatur" class="block text-sm font-medium text-white">Exequatur</label>
                                <input type="text" id="exequatur" v-model="form.exequatur"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="specialty" class="block text-sm font-medium text-white">Especialidad</label>
                                <input type="text" id="specialty" v-model="form.specialty"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="area" class="block text-sm font-medium text-white">Área</label>
                                <input type="text" id="area" v-model="form.area"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="phone" class="block text-sm font-medium text-white">Teléfono</label>
                                <input type="tel" id="phone" v-model="form.phone"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="birthdate" class="block text-sm font-medium text-white">Fecha de
                                    Nacimiento</label>
                                <input type="date" id="birthdate" v-model="form.birthdate" @change="validateBirthdate"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                                <div v-if="birthdateError" class="text-red-500 text-sm mt-2">{{ birthdateError }}</div>
                            </div>

                            <div class="space-y-2">
                                <label for="position" class="block text-sm font-medium text-white">Posición</label>
                                <input type="text" id="position" v-model="form.position"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label for="comment" class="block text-sm font-medium text-white">Observación</label>
                                <textarea id="comment" v-model="form.comment" rows="4"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"></textarea>
                            </div>

                            <div class="space-y-2">
                                <label for="address" class="block text-sm font-medium text-white">Dirección</label>
                                <textarea id="address" v-model="form.address" rows="4"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required></textarea>
                            </div>

                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4 pt-4">
                        <button @click="goBack" type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-700 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                            Cancelar
                        </button>
                        <button @click="save"
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200">
                            Guardar
                        </button>
                        <button @click="saveAndNew"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200">
                            Guardar y Crear Otro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        roles: Array,
        errors: {
            type: Object,
            default: () => ({})
        },
        reset: Boolean
    },
    components: {
        AppLayout,
    },
    data() {
        return {
            form: {
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
            },
            birthdateError: null,
        }
    },
    methods: {
        resetForm() {
            this.form = {
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
            }
        },
        validateBirthdate() {
            const today = new Date();
            const birthdate = new Date(this.form.birthdate);
            let age = today.getFullYear() - birthdate.getFullYear();
            const monthDifference = today.getMonth() - birthdate.getMonth();
            if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthdate.getDate())) {
                age--;
            }
            if (age < 18) {
                this.birthdateError = 'El usuario debe tener al menos 18 años.';
            } else {
                this.birthdateError = null;
            }
        },
        submit() {
            this.validateBirthdate();
            if (this.birthdateError) {
                return;
            }
            this.$inertia.post(route('users.store'), this.form);
        },
        save() {
            this.form.saveAndNew = false;
            this.submit()
        },
        saveAndNew() {
            this.form.saveAndNew = true;
            this.submit()
        },
        goBack() {
            this.$inertia.visit(document.referrer)
        },
    }
}
</script>
