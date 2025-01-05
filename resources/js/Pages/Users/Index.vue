<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
                Usuarios
            </h2>
        </template>

        <!-- <div class="text-white">Datos: {{ admission_id }}</div> -->


        <div class="flex flex-col items-center justify-center mt-10">
            <Link :href="route('users.create')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
            Crear Usuario
            </Link>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Nombre
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Rol
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Especialidad
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Position
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Area
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ user.name }} {{ user.last_name }}
                        </th>
                        <td class="px-6 py-4">
                            Role
                        </td>
                        <td class="px-6 py-4">
                            {{ user.specialty }}
                        </td>
                        <td class="px-6 py-4">
                            {{ user.position }}
                        </td>
                        <td class="px-6 py-4">
                            {{ user.area }}
                        </td>
                        <td class="px-6 py-4">
                            <Link class="ml-2 text-green-500 hover:text-green-800" :href="route('users.show', user.id)"
                                as="button">
                            Abrir
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </AppLayout>
</template>
<script>

import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        users: Array,
    },
    components: {
        AppLayout,
        Link,
    },
    methods: {
        goBack() {
            window.history.back()
        }
    }

}
</script>
