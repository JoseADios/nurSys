<template>
    <div>
        <!-- Filtros de búsqueda -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">
                Buscar Usuario
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" v-model="filters.name" @input="debounceSearch"
                            class="pl-10 w-full rounded-lg border-gray-200 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Nombre del usuario...">
                    </div>
                </div>
                <div v-if="!fixedRole" class="space-y-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 818 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" v-model="filters.role" @input="debounceSearch"
                            class="pl-10 w-full rounded-lg border-gray-200 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Rol del usuario...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de usuarios -->
        <div class="space-y-2">
            <h3 class="text-base font-medium text-gray-900 dark:text-white">
                Seleccionar Usuario ({{ users.total }} resultados)
            </h3>
            <div
                class="max-h-[250px] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                <div v-for="user in users.data" :key="user.id" @click="selectUser(user)"
                    :class="['p-3 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20', selectedUser === user.id ? 'bg-purple-100 dark:bg-purple-900/30' : '']">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white text-sm">
                                {{ user.name }} {{ user.last_name }}
                            </span>
                            <span
                                class="text-xs ml-2 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-800 dark:text-gray-300">
                                <span v-if="user.roles[0]">
                                   <FormatRole :role="user.roles[0].name" />
                                </span>
                                <span v-else>
                                    Sin rol asignado
                                </span>
                            </span>
                            <div v-if="selectedUserId === user.id" class="text-xs text-green-500 dark:text-green-400">
                                Usuario actual
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ formatDate(user.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-start mt-4 space-x-2">
                <button type="button" @click="prevPage" :disabled="!users.prev_page_url"
                    class="px-3 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Anterior
                </button>
                <button type="button" @click="nextPage" :disabled="!users.next_page_url"
                    class="px-3 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</template>

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
