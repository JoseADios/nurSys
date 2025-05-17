<template>
    <div
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
        <!-- Encabezado más compacto -->
        <div class="px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-medium text-gray-900 dark:text-white flex items-center">
                <span
                    class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-800 text-indigo-600 dark:text-indigo-300 mr-2">
                    <UserIcon class="h-4 w-4 text-gray-400" />
                </span>
                Selección de Usuario
            </h3>
        </div>

        <div class="p-4">
            <!-- Filtros de búsqueda optimizados -->
            <div class="space-y-2 mb-3">
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <label class="text-xs font-medium text-gray-900 dark:text-white">
                                Buscar por Nombre
                            </label>
                        </div>
                        <button v-if="filters.name" @click="filters.name = ''; debounceSearch()" type="button"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <CircleXIcon class="h-4 w-4 text-gray-400" />
                        </button>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <SearchIcon class="h-4 w-4 text-gray-400" />
                        </div>
                        <input type="text" v-model="filters.name" @input="debounceSearch"
                            class="pl-10 w-full rounded-md border-0 py-2 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6"
                            placeholder="Nombre del usuario...">
                    </div>
                </div>

                <div v-if="!fixedRole"
                    class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 transition-all duration-300">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <label class="text-xs font-medium text-gray-900 dark:text-white">
                                Filtrar por Rol
                            </label>
                        </div>
                        <button v-if="filters.role" @click="filters.role = ''; debounceSearch()" type="button"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <CircleXIcon class="h-4 w-4 text-gray-400" />
                        </button>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <EyeIcon class="h-4 w-4 text-gray-400" />
                        </div>
                        <select v-model="filters.role" @change="debounceSearch"
                            class="pl-10 w-full rounded-md border-0 py-2 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6 appearance-none">
                            <option value="">Todos los roles</option>
                            <option v-for="role in availableRoles" :key="role" :value="role">
                                <FormatRole :role="role" />
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Lista de usuarios optimizada -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <h3 class="text-xs font-medium text-gray-900 dark:text-white">
                            Seleccionar Usuario <span class="text-xs text-gray-500 dark:text-gray-400">({{ users.total
                            }})</span>
                            <span class="text-red-500">*</span>
                        </h3>
                    </div>
                </div>

                <div
                    class="max-h-[180px] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 mb-2">
                    <div v-for="user in users.data" :key="user.id" @click="selectUser(user)"
                        :class="['py-2 px-3 cursor-pointer border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors duration-200', selectedUser === user.id ? 'bg-indigo-100 dark:bg-indigo-900/30' : '']">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white text-sm">
                                    {{ user.name }} {{ user.last_name }}
                                </span>
                                <span
                                    class="text-xs ml-2 px-1.5 py-0.5 bg-indigo-100 dark:bg-indigo-800/50 rounded-md text-indigo-700 dark:text-indigo-300">
                                    <span v-if="user.roles[0]">
                                        <FormatRole :role="user.roles[0].name" />
                                    </span>
                                    <span v-else>
                                        Sin rol
                                    </span>
                                </span>
                                <div v-if="selectedUserId === user.id"
                                    class="text-xs text-green-500 dark:text-green-400 flex items-center">
                                    <CheckCircleIcon class="h-3 w-3 text-green-500" />
                                    Actual
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ formatDate(user.created_at) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación optimizada -->
                <div class="flex justify-between items-center">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ users.data.length }}/{{ users.total }}
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" @click="prevPage" :disabled="!users.prev_page_url"
                            class="px-2 py-1 text-xs font-medium rounded-md transition-colors duration-200"
                            :class="users.prev_page_url ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed'">
                            <span class="flex items-center">
                                <ChevronLeftIcon class="h-3 w-3 mr-1" />
                                Anterior
                            </span>
                        </button>
                        <button type="button" @click="nextPage" :disabled="!users.next_page_url"
                            class="px-2 py-1 text-xs font-medium rounded-md transition-colors duration-200"
                            :class="users.next_page_url ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed'">
                            <span class="flex items-center">
                                Siguiente
                                <ChevronRightIcon class="h-3 w-3" />
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Información de selección actual optimizada -->
            <transition name="fade" mode="out-in">
                <div v-if="selectedUser"
                    class="mt-3 flex items-center justify-between bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-2 border border-indigo-100 dark:border-indigo-800/50">
                    <div class="flex items-center space-x-2">
                        <div class="p-1 bg-indigo-100 dark:bg-indigo-800 rounded-full">
                            <CheckIcon class="h-4 w-4 text-indigo-600 dark:text-indigo-300" />
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-900 dark:text-white">Usuario seleccionado</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{users.data.find(user => user.id === selectedUser)?.name}} {{users.data.find(user =>
                                    user.id === selectedUser)?.last_name}}
                            </p>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>


<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
<script>
import debounce from 'lodash/debounce';
import axios from 'axios';
import moment from "moment/moment";
import 'moment/locale/es';
import FormatRole from './FormatRole.vue';
import EyeIcon from './Icons/EyeIcon.vue';
import UserIcon from './Icons/UserIcon.vue';
import SearchIcon from './Icons/SearchIcon.vue';
import CircleXIcon from './Icons/CircleXIcon.vue';
import CheckCircleIcon from './Icons/CheckCircleIcon.vue';
import ChevronRightIcon from './Icons/ChevronRightIcon.vue';
import ChevronLeftIcon from './Icons/ChevronLeftIcon.vue';
import CheckIcon from './Icons/CheckIcon.vue';

export default {
    components: {
    FormatRole,
    EyeIcon,
    UserIcon,
    SearchIcon,
    CircleXIcon,
    CheckCircleIcon,
    ChevronRightIcon,
    ChevronLeftIcon,
    CheckIcon
},
    props: {
        selectedUserId: Number,
        // Prop para especificar roles específicos
        roles: {
            type: [String, Array],
            default: null
        }
    },
    data() {
        return {
            users: {
                data: [],
                total: 0,
                prev_page_url: null,
                next_page_url: null,
            },
            selectedUser: this.selectedUserId || null,
            // Determinar si los roles están fijos
            fixedRole: this.roles ?
                (Array.isArray(this.roles) ? this.roles : [this.roles]) :
                null,
            filters: {
                name: '',
                role: this.roles && !Array.isArray(this.roles) ? this.roles : '',
            },
            availableRoles: ['admin', 'doctor', 'nurse', 'receptionist'],
            debouncedSearch: null,
        };
    },
    created() {
        this.debouncedSearch = debounce(this.applyFilters, 300);
    },
    mounted() {
        this.applyFilters();
    },
    methods: {
        selectUser(user) {
            this.selectedUser = user.id;
            this.$emit('update:user', user.id);  // Envía el ID al padre
        },
        debounceSearch() {
            this.debouncedSearch();
        },
        async applyFilters(pageUrl = null) {
            try {
                const response = await axios.get(pageUrl || route('users.filter'), {
                    params: {
                        filters: {
                            ...this.filters,
                            // Añadir soporte para múltiples roles
                            roles: this.fixedRole || this.filters.role ?
                                (this.fixedRole || [this.filters.role]) :
                                null
                        },
                        user_id: this.selectedUserId
                    }
                });
                this.users = response.data;
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        },
        formatDate(date) {
            return moment(date).format('DD MMM YYYY, HH:mm');
        },
        prevPage() {
            if (this.users.prev_page_url) {
                this.applyFilters(this.users.prev_page_url)
            }
        },
        nextPage() {
            if (this.users.next_page_url) {
                this.applyFilters(this.users.next_page_url)
            }
        }
    }
}
</script>
