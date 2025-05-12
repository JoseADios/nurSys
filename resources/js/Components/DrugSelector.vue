<template>
<div>
    <!-- Aquí todo tu contenido actual (incluyendo el segundo elemento raíz DialogModal) -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
        <!-- Contenido del filtro de búsqueda -->
        <!-- Filtros de búsqueda -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">
                Buscar Medicamento
            </h3>
            <div class="space-y-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" v-model="filters.name" @input="debounceSearch" class="pl-10 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Nombre del Medicamento...">
                </div>
            </div>
        </div>
    </div>
    <div class="space-y-2">
        <!-- Contenido de la lista de medicamentos -->
        <!-- Lista de Medicamentos -->
        <div class="space-y-2">
            <h3 class="text-base font-medium text-gray-900 dark:text-white">
                Seleccionar Medicamento ({{ drugs.total }} resultados)
            </h3>
            <div class="max-h-[250px] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                <div v-for="drug in drugs.data"
     :key="drug.id"
     @mouseenter="handleTooltip(drug.id)"
     @mouseleave="handleTooltip(null)"
     @click="selectDrug(drug)"
     :class="[
        'relative p-3 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all',
        selectedDrugLocal === drug.id ? 'bg-purple-100 dark:bg-purple-900/30' : ''
     ]">

    <!-- Contenido del medicamento -->
    <div class="flex justify-between items-center">
        <div>
            <span class="font-medium text-gray-900 dark:text-white text-sm">
                {{ drug.name }}
                {{ drug.id }}
            </span>
            <div v-if="selectedDrugId === drug.id" class="text-xs text-green-500 dark:text-green-400">
                Medicamento actual
            </div>
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400">
            {{ formatDate(drug.created_at) }}
        </div>
    </div>

    <!-- Tooltip elegante -->
    <div v-if="showTooltip === drug.id"
         :ref="el => tooltipRefs[drug.id] = el"
         :class="{
            'top-auto bottom-full mb-3': shouldShowAbove(drug.id),
            'top-full mt-3': !shouldShowAbove(drug.id)
         }"
         class="hidden lg:block absolute z-50 w-64 text-sm bg-white/95 dark:bg-gray-800/95 rounded-md border border-gray-200/60 dark:border-gray-700/60 backdrop-blur-sm left-1/2 -translate-x-1/2 overflow-hidden transform origin-top scale-100 transition-all duration-200">

        <!-- Encabezado del tooltip -->
        <div class="h-1.5 bg-gradient-to-r from-purple-400 to-pink-400 dark:from-purple-500 dark:to-pink-500"></div>

        <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 dark:text-gray-200 truncate">
                {{ drug.name }}
            </h3>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ formatDate(drug.created_at) }}
            </span>
        </div>

        <div class="px-5 py-4">
            <p class="text-gray-700 dark:text-gray-300 text-xs">
                {{ drug.description || 'Sin descripción disponible.' }}
            </p>
        </div>

        <!-- Flecha del tooltip -->
        <div class="absolute left-1/2 -translate-x-1/2 -top-2.5 w-0 h-0 border-l-6 border-r-6 border-b-6 border-transparent border-b-white dark:border-b-gray-800 filter drop-shadow-sm"></div>
    </div>
</div>

            </div>
            <div class="flex justify-start mt-4 space-x-2">
                <button type="button" @click="prevPage" :disabled="!drugs.prev_page_url" class="px-3 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Anterior
                </button>
                <button type="button" @click="nextPage" :disabled="!drugs.next_page_url" class="px-3 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Siguiente
                </button>
                <button type="button" @click="openCreateModal" class="px-2 py-1 bg-gray-500 text-white rounded shadow hover:bg-gray-600">
                    <span class="font-medium">Crear Medicamento</span>
                </button>
            </div>
        </div>
    </div>
</div>
<DialogModal :show="isVisible" @close="isVisible = false" class="">

    <!-- Header del modal -->
    <template #title>
        Crear Medicamentos
    </template>
    <!-- Contenido del modal -->
    <template #content>
        <div>
            <form>
                <div class="grid gap-4">
                    <!-- Campo Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre
                        </label>
                        <input type="text" id="name" v-model="modalform.name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white" placeholder="Nombre del Medicamento" />
                    </div>
                    <!-- Campo Descripción -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descripción
                        </label>
                        <textarea id="description" v-model="modalform.description" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white" placeholder="Descripción del Medicamento"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </template>
    <!-- Footer del modal -->
    <template #footer>
        <button @click="submitModal" class="mr-6 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
            Guardar
        </button>
        <button @click="isVisible = false" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
            Cerrar
        </button>
    </template>
</DialogModal>
</template>

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
