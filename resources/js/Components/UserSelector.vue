<template>
    <div
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
        <!-- Encabezado más compacto -->
        <div class="px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-medium text-gray-900 dark:text-white flex items-center">
                <span
                    class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-800 text-indigo-600 dark:text-indigo-300 mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
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
                            <div
                                class="w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-800/70 text-indigo-600 dark:text-indigo-300 flex items-center justify-center mr-2">
                                <span class="text-xs font-medium">1</span>
                            </div>
                            <label class="text-xs font-medium text-gray-900 dark:text-white">
                                Buscar por Nombre
                            </label>
                        </div>
                        <button v-if="filters.name" @click="filters.name = ''; debounceSearch()" type="button"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
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
                            <div
                                class="w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-800/70 text-indigo-600 dark:text-indigo-300 flex items-center justify-center mr-2">
                                <span class="text-xs font-medium">2</span>
                            </div>
                            <label class="text-xs font-medium text-gray-900 dark:text-white">
                                Filtrar por Rol
                            </label>
                        </div>
                        <button v-if="filters.role" @click="filters.role = ''; debounceSearch()" type="button"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 818 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" v-model="filters.role" @input="debounceSearch"
                            class="pl-10 w-full rounded-md border-0 py-2 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6"
                            placeholder="Rol del usuario...">
                    </div>
                </div>
            </div>

            <!-- Lista de usuarios optimizada -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <div
                            class="w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-800/70 text-indigo-600 dark:text-indigo-300 flex items-center justify-center mr-2">
                            <span class="text-xs font-medium">3</span>
                        </div>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Anterior
                            </span>
                        </button>
                        <button type="button" @click="nextPage" :disabled="!users.next_page_url"
                            class="px-2 py-1 text-xs font-medium rounded-md transition-colors duration-200"
                            :class="users.next_page_url ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed'">
                            <span class="flex items-center">
                                Siguiente
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600 dark:text-indigo-300"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-900 dark:text-white">Usuario seleccionado</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{users.data.find(user => user.id === selectedUser)?.name}} {{users.data.find(user =>
                                    user.id === selectedUser)?.last_name }}
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

export default {
    components: {
        FormatRole
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
