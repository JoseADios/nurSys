<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Nuevo ingreso
            </h2>
        </template>

        <!-- show errors -->
        <div v-if="errors.length > 0" class="mb-4 flex flex-col items-center">
            <div class="mb-4 text-red-500" v-for="error in errors" :key="error">
                {{ error }}
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4 lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                <label for="first_name" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Nombres</label>
                <input type="text" required id="first_name" rows="4" v-model="form.first_name"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></input>

                <label for="first_surname"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Primer
                    apellido</label>
                <input type="text" required id="first_surname" rows="4" v-model="form.first_surname"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></input>

                <label for="second_surname"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Segundo
                    apellido</label>
                <input type="text" required id="second_surname" rows="4" v-model="form.second_surname"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></input>

                <label for="phone-input" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Número de
                    teléfono:</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor"
                            viewBox="0 0 19 18">
                            <path
                                d="M18 13.446a3.02 3.02 0 0 0-.946-1.985l-1.4-1.4a3.054 3.054 0 0 0-4.218 0l-.7.7a.983.983 0 0 1-1.39 0l-2.1-2.1a.983.983 0 0 1 0-1.389l.7-.7a2.98 2.98 0 0 0 0-4.217l-1.4-1.4a2.824 2.824 0 0 0-4.218 0c-3.619 3.619-3 8.229 1.752 12.979C6.785 16.639 9.45 18 11.912 18a7.175 7.175 0 0 0 5.139-2.325A2.9 2.9 0 0 0 18 13.446Z" />
                        </svg>
                    </div>
                    <input v-model="form.phone" type="text" id="phone-input" aria-describedby="helper-text-explanation"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        pattern="[0-9]{3}[0-9]{3}[0-9]{4}" placeholder="123-456-7890" required />
                </div>

                <label for="identification_card"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Cédula</label>
                <input type="text" required id="identification_card" rows="4" v-model="form.identification_card"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></input>

                <label for="nationality"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Nacionalidad</label>
                <select required id="nationality" v-model="form.nationality"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option v-for="nationality in nationalities" :key="nationality.id">
                        {{ nationality.name }}
                    </option>
                </select>

                <label for="email"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Correo</label>
                <input type="email" required id="email" rows="4" v-model="form.email" placeholder="email@company.com"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></input>

                <label for="birthdate"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Fecha de nacimiento</label>
                <input v-model="form.birthdate" id="birthdate" type="date"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Selecciona la fecha">

                <label for="position" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Cargo de
                    trabajo</label>
                <input type="text" required id="position" rows="4" v-model="form.position"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></input>

                <label for="marital_status"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Estado
                    civil</label>
                <select required id="marital_status" v-model="form.marital_status"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option v-for="marital_status in maritalSatuses" :key="marital_status.id">
                        {{ marital_status.name }}
                    </option>
                </select>

                <label for="address"
                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">Dirección</label>
                <textarea required id="address" rows="4" v-model="form.address"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe la dirección del paciente..."></textarea>

                <label for="ars" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">ARS</label>
                <select id="ars" v-model="form.ars"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Ninguno</option>
                    <option v-for="ars in arss" :key="ars.id">
                        {{ ars.name }}
                    </option>
                </select>

                <div class="flex justify-end mt-6 mb-2">
                    <button @click="goBack"
                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Cancelar
                    </button>

                    <button type="submit"
                        class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Guardar</button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        nationalities: Array,
        maritalSatuses: Array,
        arss: Array,
        errors: Array,
    },
    components: {
        AppLayout,
        Link,
    },
    data() {
        return {
            form: {
                first_name: null,
                first_surname: null,
                second_surname: null,
                phone: null,
                identification_card: null,
                nationality: null,
                email: null,
                birthdate: null,
                position: null,
                marital_status: null,
                address: null,
                ars: null,
            }
        }
    },
    methods: {
        submit() {
            this.$inertia.post(route('patients.store'), this.form)
        },
        goBack() {
            window.history.back()
        }
    }
}
</script>
