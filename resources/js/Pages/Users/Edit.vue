<template>
    <AppLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-white leading-tight text-center">
                Editar Usuario
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 shadow-xl rounded-lg p-6">
                <div v-if="Object.keys(errors).length"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Ups!</strong>
                    <span class="block sm:inline">Por favor, corrige los errores en el formulario.</span>
                    <ul>
                        <li v-for="error in Object.values(errors)" class="text-sm">{{ error }}</li>
                    </ul>
                </div>

                <!-- <div class="text-white">
                    {{ hasRoles[0] }}
                </div> -->

                <form @submit.prevent="submit" class="space-y-6">
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

                            <div class="space-y-2">
                                <label for="role" class="block text-sm font-medium text-white">Role</label>
                                <select required id="role" v-model="form.role" name="role"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                    <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="address" class="block text-sm font-medium text-white">Dirección</label>
                                <textarea id="address" v-model="form.address" rows="4"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required></textarea>
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
                                <input type="date" id="birthdate" v-model="form.birthdate"
                                    class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                                    required>
                                <span v-if="birthdateError" class="text-red-500 text-sm">{{ birthdateError }}</span>
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


                        </div>

                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4 pt-4">
                        <button v-if="user.active == 1" @click="userBeingDeleted = true" type="button"
                            class="inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:to-red-600 transition-all duration-200">
                            Eliminar
                        </button>
                        <button v-else @click="restoreUser" type="button"
                            class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:to-green-600 transition-all duration-200">
                            Restaurar
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
                Eliminar Ingreso
            </template>

            <template #content>
                ¿Estás seguro de que deseas eliminar este ingreso?
            </template>

            <template #footer>
                <SecondaryButton @click="userBeingDeleted = null">
                    Cancelar
                </SecondaryButton>

                <DangerButton class="ms-3" @click="deleteUser">
                    Eliminar
                </DangerButton>
            </template>
        </ConfirmationModal>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

export default {
    props: {
        user: Object,
        roles: Array,
        hasRoles: Array,
        errors: Object,
        previousUrl: String,
    },
    components: {
        AppLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
    },
    data() {
        return {
            userBeingDeleted: ref(null),
            form: {
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
            },
            birthdateError: ''
        }
    },
    methods: {
        validateBirthdate(birthdate) {
            const date = new Date(birthdate);
            const today = new Date();
            let age = today.getFullYear() - date.getFullYear();
            const monthDifference = today.getMonth() - date.getMonth();
            if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < date.getDate())) {
                age--;
            }
            return age >= 18;
        },
        submit() {
            if (!this.validateBirthdate(this.form.birthdate)) {
                this.birthdateError = 'La fecha de nacimiento debe indicar que el usuario tiene al menos 18 años.';
                return;
            }
            this.birthdateError = '';
            this.$inertia.put(route('users.update', this.user.id), this.form);
        },
        deleteUser() {
            this.userBeingDeleted = false
            this.$inertia.delete(route('users.destroy', this.user.id));
        },
        restoreUser() {
            this.$inertia.put(route('users.update', this.user.id), { active: true });
        },
        previousUrl: String,
    }
}
</script>
