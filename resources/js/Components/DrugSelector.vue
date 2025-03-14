<template>
    <div>
        <!-- Filtros de búsqueda -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">
                Buscar Medicamento
            </h3>
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
                        placeholder="Nombre del Medicamento...">
                </div>
            </div>
        </div>

        <!-- Lista de Medicamentos -->
        <div class="space-y-2">
            <h3 class="text-base font-medium text-gray-900 dark:text-white">
                Seleccionar Medicamento ({{ drugs.total }} resultados)
            </h3>
            <div
                class="max-h-[250px] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                <div v-for="drug in drugs.data" :key="drug.id" @click="selectDrug(drug)"
                    :class="['p-3 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20', selectedDrug === drug.id ? 'bg-purple-100 dark:bg-purple-900/30' : '']">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white text-sm">
                                {{ drug.name }}
                            </span>
                            <div v-if="selectedDrugId === drug.id"
                                class="text-xs text-green-500 dark:text-green-400">
                                Medicamento actual
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ formatDate(drug.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-start mt-4 space-x-2">
                <button type="button" @click="prevPage" :disabled="!drugs.prev_page_url"
                    class="px-3 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Anterior
                </button>
                <button type="button" @click="nextPage" :disabled="!drugs.next_page_url"
                    class="px-3 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Siguiente
                </button>
                <button type="button" @click="openCreateModal"
                    class="px-2 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600">
                    <span class="font-medium">Crear Medicamento</span>
                </button>
            </div>
        </div>
    </div>

    <DialogModal :show="isVisible" @close="isVisible = false">
        <template #title>
            Crear Medicamentos
        </template>
        <template #content>
            <form>
                <div class="grid gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre
                        </label>
                        <input type="text" id="name" v-model="modalform.name" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Nombre del Medicamento" />
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descripción
                        </label>
                        <textarea id="description" v-model="modalform.description" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Descripción del Medicamento"></textarea>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <button @click="submitModal"
                class="mr-6 text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">
                Guardar
            </button>
            <button @click="isVisible = false"
                class="py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700">
                Cerrar
            </button>
        </template>
    </DialogModal>
</template>

<script>
import debounce from 'lodash/debounce';
import axios from 'axios';
import moment from "moment/moment";
import DialogModal from '@/Components/DialogModal.vue';

export default {
    props: {
        selectedDrugId: Number
    },
    components: {
        DialogModal,
    },
    data() {
        return {
            drugs: {
                data: [],
                total: 0,
                prev_page_url: null,
                next_page_url: null,
            },
            selectedDrug: this.selectedDrugId || null,
            filters: {
                name: '',
            },
            modalform: {
                name: '',
                description: '',
            },
            isVisible: false,
        };
    },
    watch: {
        // Escucha cambios en 'filters.name' y aplica los filtros con debounce
        'filters.name': {
            handler: debounce(function () {
                this.applyFilters();
            }, 300),
            immediate: false, // No se ejecuta inmediatamente al montar el componente
        }
    },
    mounted() {
        this.applyFilters();
    },
    methods: {
        openCreateModal() {
            this.isVisible = true;
        },
        submitModal() {
            this.$inertia.post(route('Drugs.store'), this.modalform, { preserveScroll: true });
            this.isVisible = false;
            this.modalform = { name: '', description: '' };
        },
        selectDrug(drug) {
            this.selectedDrug = drug.id;
            this.$emit('update:drug', { id: drug.id, name: drug.name });
        },
        async applyFilters(pageUrl = null) {
            try {
                const response = await axios.get(pageUrl || route('drugs.filter'), {
                    params: { filters: this.filters }
                });
                this.drugs = response.data;
            } catch (error) {
                console.error('Error fetching drugs:', error);
            }
        },
        formatDate(date) {
            return moment(date).format('DD MMM YYYY, HH:mm');
        },
        prevPage() {
            if (this.drugs.prev_page_url) {
                this.applyFilters(this.drugs.prev_page_url);
            }
        },
        nextPage() {
            if (this.drugs.next_page_url) {
                this.applyFilters(this.drugs.next_page_url);
            }
        }
    }
}
</script>
