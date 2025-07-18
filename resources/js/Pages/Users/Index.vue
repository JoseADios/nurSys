<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Usuarios
            </h2>
        </template>

        <!-- Filtros con vista móvil mejorada -->
        <div class="px-4 lg:px-10 mt-4">
            <div class="mb-4">
                <!-- Barra de búsqueda principal y botones de acción siempre visibles -->
                <div class="flex flex-col md:flex-row gap-3 mb-3">
                    <!-- Búsqueda general - siempre visible -->
                    <div class="relative h-fit flex-grow">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <SearchIcon class="size-4 text-gray-500 dark:text-gray-400" />
                        </div>
                        <TextInput v-model="form.search" placeholder="Buscar por nombre" class="pl-10 w-full"
                            @input="applyFilters" />
                        <button v-if="form.search" @click="form.search = ''; applyFilters()" type="button"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500">
                            <XIcon class="h-5 w-5" />
                        </button>
                    </div>

                    <!-- Botones de acción - siempre visibles -->
                    <div class="flex justify-center items-center flex-col sm:flex-row md:flex-wrap gap-2">
                        <!-- Botón para ver registros Desactivados -->
                        <AccessGate :permission="['user.update']">
                            <PersonalizableButton custom-class="whitespace-nowrap" @click="toggleShowDeleted" :color="form.show_deleted ? 'red' : 'gray'">
                                {{ filters.show_deleted ? 'Ocultar Desactivados' : 'Ver Desactivados' }}
                                <CirclePlusIcon v-if="form.show_deleted" class="ml-1 h-5 w-5" />
                                <CircleXIcon v-else class="ml-1 h-5 w-5" />
                            </PersonalizableButton>
                        </AccessGate>

                        <AccessGate :permission="['user.create']">
                            <PrimaryLink class="py-2.5 whitespace-nowrap" :href="route('users.create')">
                                <PlusIcon class="h-4 w-4 mr-1" />
                                Nuevo usuario
                            </PrimaryLink>
                        </AccessGate>
                    </div>
                </div>

                <!-- Botón para mostrar/ocultar filtros en móvil -->
                <button @click="showFilters = !showFilters"
                    class="md:hidden w-full flex items-center justify-center space-x-2 bg-gray-100 dark:bg-gray-700 p-2 rounded-lg mb-2">
                    <span class=" text-gray-700 dark:text-white">{{ showFilters ? 'Ocultar filtros' : 'Mostrar filtros'
                    }}</span>
                    <span v-if="showFilters">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5  text-gray-700 dark:text-white"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span v-else>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5  text-gray-700 dark:text-white"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>

                <!-- Filtros adicionales - colapsables en móvil -->
                <div :class="{ 'hidden': !showFilters }" class="md:block">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- Filtro por rol -->
                        <div class="relative">
                            <select v-model="form.role" name="role" id="role" @change="applyFilters"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Todos los roles</option>
                                <option value="admin">Administrador</option>
                                <option value="nurse">Enfermero</option>
                                <option value="doctor">Doctor</option>
                                <option value="receptionist">Recepcionista</option>
                            </select>

                            <button v-if="form.role" @click="form.role = ''; applyFilters()" type="button"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500">
                                <XIcon class="h-5 w-5" />
                            </button>
                        </div>

                        <!-- Filtro por especialidad -->
                        <div class="relative">
                            <TextInput v-model="form.specialty" placeholder="Filtrar por especialidad" class="w-full"
                                @input="applyFilters" />
                            <button v-if="form.specialty" @click="form.specialty = ''; applyFilters()" type="button"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500">
                                <XIcon class="h-5 w-5" />
                            </button>
                        </div>

                        <!-- Filtro por posición -->
                        <div class="relative">
                            <TextInput v-model="form.position" placeholder="Filtrar por posición" class="w-full"
                                @input="applyFilters" />
                            <button v-if="form.position" @click="form.position = ''; applyFilters()" type="button"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500">
                                <XIcon class="h-5 w-5" />
                            </button>
                        </div>

                        <!-- Filtro por email -->
                        <div class="relative">
                            <TextInput v-model="form.email" placeholder="Filtrar por email" class="w-full"
                                @input="applyFilters" />
                            <button v-if="form.email" @click="form.email = ''; applyFilters()" type="button"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500">
                                <XIcon class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla con scroll horizontal en móvil -->
        <div class="px-4 lg:px-10">
            <div class="relative overflow-x-auto border border-gray-200 dark:border-gray-700/60 rounded-lg my-4">
                <div class="min-w-full overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap hidden sm:table-cell"
                                    @click="sort('id')">
                                    ID <span v-if="form.sortField === 'id'">{{ form.sortDirection === 'asc' ? '↑' : '↓'
                                    }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap"
                                    @click="sort('name')">
                                    Nombre <span v-if="form.sortField === 'name'">{{ form.sortDirection === 'asc' ? '↑'
                                        :
                                        '↓'
                                    }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap"
                                    @click="sort('role')">
                                    Rol <span v-if="form.sortField === 'role'">{{ form.sortDirection === 'asc' ? '↑' :
                                        '↓'
                                    }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap"
                                    @click="sort('specialty')">
                                    Especialidad <span v-if="form.sortField === 'specialty'">{{ form.sortDirection ===
                                        'asc'
                                        ? '↑' :
                                        '↓' }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap"
                                    @click="sort('position')">
                                    Posición <span v-if="form.sortField === 'position'">{{ form.sortDirection === 'asc'
                                        ?
                                        '↑' : '↓'
                                    }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 cursor-pointer whitespace-nowrap"
                                    @click="sort('email')">
                                    Correo <span v-if="form.sortField === 'email'">{{ form.sortDirection === 'asc' ? '↑'
                                        :
                                        '↓'
                                    }}</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap">Acciones</th>
                            </tr>
                        </thead>
                        <tbody v-if="users.data.length">
                            <tr v-for="(user, index) in users.data" :key="user.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    {{ user.id }}
                                </td>
                                <td
                                    class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white flex items-center">

                                    <img v-if="user.profile_photo_url" :src="user.profile_photo_url" alt="Profile Photo"
                                        class="size-6 rounded-full mr-2 sm:size-8 md:size-10 md:mr-4 object-cover">

                                    <DynamicAvatar v-else :name="user.name"
                                        size-class="size-6 mr-2 sm:size-8 md:size-10 md:mr-4" />

                                    <Link as="button" :href="route('users.show', user.id)" class="hover:text-blue-600">
                                    {{ user.name }} {{ user.last_name }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="user.roles[0]">
                                        <FormatRole :role="user.roles[0].name" />
                                    </div>
                                    <div v-else>N/A</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ user.specialty }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ user.position }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2 justify-center">
                                        <Link class="text-primary-500 hover:text-primary-800"
                                            :href="route('users.show', user.id)" as="button">
                                        Ver
                                        </Link>
                                        <AccessGate :permission="['user.update']">
                                            <Link class="text-green-500 hover:text-green-800"
                                                :href="route('users.edit', user.id)">
                                            Editar
                                            </Link>
                                        </AccessGate>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="!users.data.length" class="text-center text-gray-500 dark:text-gray-400 py-4 w-full">
                    No hay registros disponibles.
                </div>
                <Pagination :pagination="users" :filters="form" />
            </div>
        </div>
        <div class="pb-4"></div>
    </AppLayout>
</template>

<script>
import AccessGate from '@/Components/Access/AccessGate.vue';
import DynamicAvatar from '@/Components/DynamicAvatar.vue';
import FormatRole from '@/Components/FormatRole.vue';
import CirclePlusIcon from '@/Components/Icons/CirclePlusIcon.vue';
import CircleXIcon from '@/Components/Icons/CircleXIcon.vue';
import PlusIcon from '@/Components/Icons/PlusIcon.vue';
import SearchIcon from '@/Components/Icons/SearchIcon.vue';
import XIcon from '@/Components/Icons/XIcon.vue';
import Pagination from '@/Components/Pagination.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
    props: {
        users: Object,
        filters: Object,
    },
    components: {
        AppLayout,
        Link,
        Pagination,
        FormatRole,
        AccessGate,
        XIcon,
        PlusIcon,
        CirclePlusIcon,
        CircleXIcon,
        TextInput,
        SearchIcon,
        DynamicAvatar,
        PrimaryLink,
        PersonalizableButton
    },
    data() {
        return {
            showFilters: false, // Controlar visibilidad de filtros en móvil
            form: {
                search: this.filters.search || '',
                specialty: this.filters.specialty || '',
                role: this.filters.role || '',
                position: this.filters.position || '',
                email: this.filters.email || '',
                show_deleted: this.filters.show_deleted,
                sortField: this.filters.sortField || '',
                sortDirection: this.filters.sortDirection || 'asc',
            },
        };
    },
    methods: {
        applyFilters() {
            router.get(route('users.index'), this.form, {
                preserveState: true,
                replace: true,
            });
        },
        toggleShowDeleted() {
            this.form.show_deleted = !this.form.show_deleted;
            this.applyFilters();
        },
        sort(field) {
            this.form.sortField = field;
            this.form.sortDirection = this.form.sortField === field && this.form.sortDirection === 'asc' ? 'desc' : 'asc';
            this.applyFilters();
        },
    },
    mounted() {
        // Mostrar automáticamente los filtros en pantallas grandes
        this.showFilters = window.innerWidth >= 768;

        // Actualizar la visibilidad de los filtros cuando cambia el tamaño de la ventana
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                this.showFilters = true;
            }
        });
    },
};
</script>
