<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight text-center">
              Medicamentos
            </h2>
        </template>

        <div class="flex flex-col items-center justify-center mt-10">
            <Link :href="route('Drugs.create')"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                Crear Nuevo Medicamento
            </Link>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-10 lg:mx-10">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">#</th>
                        <th scope="col" class="px-6 py-3">Nombre</th>
                        <th scope="col" class="px-6 py-3">Descripcion</th>

                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="drug in Drugs" :key="drug.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ drug.id  }}
                        </td>
                        <td class="px-6 py-4">{{ drug.name }}</td>
                        <td class="px-6 py-4">{{ drug.description }}</td>
                        <td class="px-6 py-4">
                            <Link class="ml-2 text-blue-500 hover:text-blue-800"
                                :href="route('Drugs.show', drug.id)">
                                Ver
                            </Link>
                            <Link class="text-green-500 hover:text-green-800"
                                :href="route('Drugs.edit', drug.id)">
                                Editar
                            </Link>
                             <!-- <Link method="delete" class="ml-2 text-red-500 hover:text-red-800"
                                :href="route('Drugs.destroy', drug.id)" as="button">
                                Eliminar
                            </Link> -->
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
        Drugs: Array,
    },
    components: {
        AppLayout,
        Link,
    },
}
</script>
