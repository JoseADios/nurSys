<template>
    <div
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
        <!-- Encabezado más compacto -->
        <div class="px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-medium text-gray-900 dark:text-white flex items-center">
                <span
                    class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-800 text-indigo-600 dark:text-indigo-300 mr-2">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                </span>
                Selección de Medicamento
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
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" v-model="filters.name" @input="debounceSearch"
                            class="pl-10 w-full rounded-md border-0 py-2 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6"
                            placeholder="Nombre del medicamento...">
                    </div>
                </div>
            </div>

            <!-- Lista de medicamentos optimizada -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <h3 class="text-xs font-medium text-gray-900 dark:text-white">
                            Seleccionar Medicamento <span class="text-xs text-gray-500 dark:text-gray-400">({{ drugs.total
                            }})</span>
                            <span class="text-red-500">*</span>
                        </h3>
                    </div>
                </div>

                <div
                    class="max-h-[180px] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 mb-2">
                    <div v-for="drug in drugs.data" :key="drug.id"
                         @click="selectDrug(drug)"
                         class="group relative py-2 px-3 cursor-pointer border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors duration-200"
                         :class="[selectedDrugLocal === drug.id ? 'bg-indigo-100 dark:bg-indigo-900/30' : '']">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white text-sm">
                                    {{ drug.name }}
                                </span>
                                <div v-if="selectedDrugId === drug.id"
                                    class="text-xs text-green-500 dark:text-green-400 flex items-center">
                                    <svg class="h-3 w-3 text-green-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Actual
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ formatDate(drug.created_at) }}
                            </div>
                        </div>

                        <!-- Tooltip sencillo al pasar el mouse -->
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 absolute -top-1 left-1/2 -translate-x-1/2 -translate-y-full mt-0 z-50 pointer-events-none">
                            <div class="bg-gray-900 dark:bg-gray-700 text-white text-xs rounded py-1 px-2 max-w-xs whitespace-normal text-center shadow-lg">
                                <p class="mb-1 font-medium">{{ drug.name }}</p>
                                <p class="text-gray-200 dark:text-gray-300 text-xs">{{ drug.description || 'Sin descripción disponible.' }}</p>
                            </div>
                            <div class="w-3 h-3 bg-gray-900 dark:bg-gray-700 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Paginación optimizada -->
                <div class="flex justify-between items-center">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ drugs.data.length }}/{{ drugs.total }}
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" @click="prevPage" :disabled="!drugs.prev_page_url"
                            class="px-2 py-1 text-xs font-medium rounded-md transition-colors duration-200"
                            :class="drugs.prev_page_url ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed'">
                            <span class="flex items-center">
                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Anterior
                            </span>
                        </button>
                        <button type="button" @click="nextPage" :disabled="!drugs.next_page_url"
                            class="px-2 py-1 text-xs font-medium rounded-md transition-colors duration-200"
                            :class="drugs.next_page_url ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800/50' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed'">
                            <span class="flex items-center">
                                Siguiente
                                <svg class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </button>
                        <button type="button" @click="openCreateModal"
                            class="px-2 py-1 text-xs font-medium rounded-md bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800/50 transition-colors duration-200">
                            <span class="flex items-center">
                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Crear
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Información de selección actual optimizada -->
            <transition name="fade" mode="out-in">
                <div v-if="selectedDrugLocal"
                    class="mt-3 flex items-center justify-between bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-2 border border-indigo-100 dark:border-indigo-800/50">
                    <div class="flex items-center space-x-2">
                        <div class="p-1 bg-indigo-100 dark:bg-indigo-800 rounded-full">
                            <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-900 dark:text-white">Medicamento seleccionado</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{drugs.data.find(drug => drug.id === selectedDrugLocal)?.name}}
                            </p>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>

    <DialogModal :show="isVisible" @close="isVisible = false">
        <!-- Header del modal -->
        <template #title>
            <div class="flex items-center">
                <svg class="h-5 w-5 text-indigo-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Crear Medicamento</span>
            </div>
        </template>

        <!-- Contenido del modal -->
        <template #content>
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                <form>
                    <div class="space-y-4">
                        <!-- Campo Nombre -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                                <input type="text" id="name" v-model="modalform.name" required
                                    class="pl-10 w-full rounded-md border-0 py-2 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6"
                                    placeholder="Nombre del Medicamento" />
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Descripción <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                    </svg>
                                </div>
                                <textarea id="description" v-model="modalform.description" required
                                    class="pl-10 w-full rounded-md border-0 py-2 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-transparent sm:text-sm sm:leading-6"
                                    placeholder="Descripción del Medicamento" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </template>

        <!-- Footer del modal -->
        <template #footer>
            <div class="flex justify-end space-x-3">
                <button @click="isVisible = false"
                    class="px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button @click="submitModal"
                    class="px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Guardar Medicamento
                </button>
            </div>
        </template>
    </DialogModal>
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
import DialogModal from './DialogModal.vue';
export default {
    props: {
        selectedDrugId: Number,
        drug: String,
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
            showTooltip: null,
            tooltipRefs: {},
            tooltipTimeout: null,
            selectedDrugLocal: this.selectedDrugId || null,
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
        },
        // Observa cambios en la prop selectedDrugId
        selectedDrugId(newVal) {
            this.selectedDrugLocal = newVal;
        }
    },
    mounted() {
        this.applyFilters();
    },
    methods: {

        openCreateModal() {
            this.isVisible = true;
        },
        shouldShowAbove(bedId) {


            const tooltip = this.tooltipRefs[bedId];

            if (!tooltip) return false;

            const tooltipRect = tooltip.getBoundingClientRect();
            const bedElement = tooltip.parentElement;
            const bedRect = bedElement.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            const padding = 20; // Espacio de padding para mejor visibilidad

            // Espacio disponible debajo del elemento
            const spaceBelow = windowHeight - bedRect.bottom;
            // Altura total del tooltip
            const tooltipHeight = tooltipRect.height;

            // Solo mostrar arriba si no hay suficiente espacio abajo
            return spaceBelow < (tooltipHeight + padding + 80);

        },
        handleTooltip(bedId) {
            this.showTooltip == bedId;


            if (this.tooltipTimeout) {
                clearTimeout(this.tooltipTimeout);
            }

            if (this.showTooltip === bedId) {

                this.showTooltip = null;
                this.clearTooltipRef(bedId);
            } else {
                if (this.showTooltip !== null) {
                    this.clearTooltipRef(this.showTooltip);
                }
                this.showTooltip = bedId;
            }

        },
        clearTooltipRef(bedId) {
            if (this.tooltipRefs[bedId]) {
                delete this.tooltipRefs[bedId];
            }
        },
        beforeUnmount() {

            this.tooltipRefs = {};
            if (this.tooltipTimeout) {
                clearTimeout(this.tooltipTimeout);
            }
        },
        submitModal() {
            this.$inertia.post(route('Drugs.store'), this.modalform, {
                preserveScroll: true
            });
            this.applyFilters();
            this.isVisible = false;
            this.modalform = {
                name: '',
                description: ''
            };
        },
        selectDrug(drug) {
            this.selectedDrugLocal = drug.id;
            // Emitir solo el nombre del medicamento al componente padre
            this.$emit('update:drug', drug.name);
        },
        debounceSearch() {
            // Método para el debounce del input de búsqueda
            // Este método ya está manejado por el watcher
        },
        async applyFilters(pageUrl = null) {
            try {
                const response = await axios.get(pageUrl || route('drugs.filter'), {
                    params: {
                        filters: this.filters
                    }
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
