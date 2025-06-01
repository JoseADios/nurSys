<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pacientes
            </h2>
        </template>

        <!-- Filtros avanzados -->
        <div class="bg-gray-100 dark:bg-gray-900 overflow-hidden sm:rounded-lg mt-4 px-4 lg:px-10">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 w-full">
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                        <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-300">Filtros Avanzados</h3>
                        <div class="flex items-center justify-center">
                            <div v-if="showAdditionalFilters" class="mr-4 flex justify-end space-x-3">
                                <button @click="clearFilters"
                                    class="px-3 py-1 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-md hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                                    Limpiar
                                </button>
                            </div>
                            <button @click="toggleAdditionalFilters"
                                class="text-primary-500 hover:text-primary-600 flex items-center justify-center transition-colors">
                                <PlusIcon v-if="!showAdditionalFilters" class="size-5 mr-2" />
                                <XIcon v-else class="size-5 mr-2" />
                                {{ showAdditionalFilters ? 'Ocultar filtros' : 'Mostrar filtros' }}
                            </button>
                        </div>
                    </div>

                    <!-- Filtros adicionales -->
                    <transition enter-active-class="transition ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-[-10px]" enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition ease-in duration-200"
                        leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-[-10px]">
                        <div v-if="showAdditionalFilters"
                            class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                            <div>
                                <InputLabel for="name" value="Nombre" />
                                <TextInput id="name" v-model="form.name" @input="submitFilter()" class="w-full"
                                    placeholder="Buscar por nombre" />
                            </div>
                            <div>
                                <InputLabel for="identification_card" value="Cédula" />
                                <TextInput id="identification_card" v-model="form.identificationCard"
                                    @input="submitFilter()" class="w-full" placeholder="Número de identificación" />
                            </div>
                            <div>
                                <InputLabel for="nationality" value="Nacionalidad" />
                                <TextInput id="nationality" v-model="form.nationality" @input="submitFilter()"
                                    class="w-full" placeholder="País de origen" />
                            </div>
                            <div>
                                <InputLabel for="phone" value="Teléfono" />
                                <TextInput id="phone" v-model="form.phone" @input="submitFilter()" class="w-full"
                                    placeholder="Número de contacto" />
                            </div>
                            <div>
                                <InputLabel for="email" value="Correo" />
                                <TextInput id="email" v-model="form.email" @input="submitFilter()" class="w-full"
                                    placeholder="Correo electrónico" />
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>

        <!-- Barra de búsqueda y acciones -->
        <div class="bg-gray-100 dark:bg-gray-900 px-4 lg:px-10 mt-2">
            <div class="flex flex-col xl:flex-row gap-4 py-2">
                <!-- Búsqueda -->
                <div class="relative w-full xl:w-1/3">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <SearchIcon class="size-4 text-gray-500 dark:text-gray-400" />
                    </div>
                    <TextInput type="text" v-model="form.search" @input="submitFilter()" class="pl-10 w-full"
                        placeholder="Buscar..." />
                    <button v-if="form.search" @click="form.search = ''; submitFilter()"
                        class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                        <XIcon class="h-5 w-5" />
                    </button>
                </div>

                <!-- Filtros y acciones -->
                <div class="flex flex-col lg:flex-row items-center w-full">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 w-full">
                        <select @change="submitFilter()"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            name="hospitalized" id="hospitalized" v-model="form.hospitalized">
                            <option value="true">Hospitalizados</option>
                            <option value="false">No hospitalizados</option>
                            <option value="">Todos</option>
                        </select>

                        <select @change="submitFilter()"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            name="days" id="days" v-model="form.days">
                            <option value="">Siempre</option>
                            <option value="1">Último día</option>
                            <option value="7">Últimos 7 días</option>
                            <option value="30">Últimos 30 días</option>
                            <option value="90">Últimos 90 días</option>
                            <option value="180">Últimos 180 días</option>
                            <option value="365">Último año</option>
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto mt-2 sm:mt-0 justify-center sm:justify-end items-center">
                        <AccessGate :permission="['patient.delete']">
                            <button @click="toggleShowDeleted"
                                class="flex items-center justify-center space-x-1 px-3 py-2 rounded-lg transition-colors text-sm whitespace-nowrap"
                                :class="{
                                    'bg-red-500 hover:bg-red-600 text-white': form.showDeleted,
                                    'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200': !form.showDeleted
                                }">
                                {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                                <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-4 w-4" />
                                <CircleXIcon v-else class="ml-1 h-4 w-4" />
                            </button>
                        </AccessGate>

                        <AccessGate :permission="['patient.create']">
                            <PrimaryLink class="py-2.5 whitespace-nowrap" :href="route('patients.create')">
                                <PlusIcon class="h-4 w-4 mr-1" />
                                Nuevo paciente
                            </PrimaryLink>
                        </AccessGate>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de pacientes -->
        <div class="px-4 lg:px-10 mt-2 mb-6">
            <div class="relative overflow-x-auto border border-gray-200 dark:border-gray-700/60 rounded-lg my-4">
                <div class="min-w-full overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3 cursor-pointer hidden sm:table-cell"
                                    @click="sort('id')">
                                    ID <span v-if="form.sortField === 'id'">{{ form.sortDirection === 'asc' ? '↑' : '↓'
                                        }}</span>
                                </th>
                                <th scope="col" class="px-4 py-3 cursor-pointer" @click="sort('is_hospitalized')">
                                    <span class="hidden md:inline">Ingresado</span>
                                    <span class="md:hidden">Ing.</span>
                                    <span v-if="form.sortField === 'is_hospitalized'">{{ form.sortDirection === 'asc' ?
                                        '↑' :
                                        '↓' }}</span>
                                </th>
                                <th scope="col" class="px-4 py-3 cursor-pointer" @click="sort('first_name')">
                                    Nombre <span v-if="form.sortField === 'first_name'">{{ form.sortDirection === 'asc'
                                        ? '↑' :
                                        '↓' }}</span>
                                </th>
                                <th scope="col" class="px-4 py-3 cursor-pointer hidden sm:table-cell"
                                    @click="sort('phone')">
                                    Teléfono <span v-if="form.sortField === 'phone'">{{ form.sortDirection === 'asc' ?
                                        '↑' : '↓'
                                        }}</span>
                                </th>
                                <th scope="col" class="px-4 py-3 cursor-pointer hidden md:table-cell"
                                    @click="sort('identification_card')">
                                    Cédula <span v-if="form.sortField === 'identification_card'">{{ form.sortDirection
                                        === 'asc'
                                        ? '↑' : '↓' }}</span>
                                </th>
                                <th scope="col" class="px-4 py-3 cursor-pointer hidden lg:table-cell"
                                    @click="sort('nationality')">
                                    Nacionalidad <span v-if="form.sortField === 'nationality'">{{ form.sortDirection ===
                                        'asc' ?
                                        '↑' : '↓' }}</span>
                                </th>
                                <th scope="col" class="px-4 py-3 cursor-pointer hidden lg:table-cell"
                                    @click="sort('email')">
                                    Correo <span v-if="form.sortField === 'email'">{{ form.sortDirection === 'asc' ? '↑'
                                        : '↓'
                                        }}</span>
                                </th>
                                <th scope="col" class="px-4 py-3"> Acciones </th>
                            </tr>
                        </thead>
                        <tbody v-if="patients.data.length">
                            <tr v-for="(patient, index) in patients.data" :key="patient.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                                    {{ patient.id }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span v-if="patient.is_hospitalized"
                                        class="block w-4 h-4 bg-green-500 rounded-full mx-auto"></span>
                                    <span v-else class="block w-4 h-4 bg-orange-500 rounded-full mx-auto"></span>
                                </td>
                                <th scope="row"
                                    class="px-4 py-3 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                    <Link as="button" :href="route('patients.show', patient.id)"
                                        class="hover:text-blue-600">
                                    {{ patient.first_name }} {{ patient.first_surname }} {{ patient.second_surname }}
                                    </Link>
                                </th>
                                <td class="px-4 py-3 whitespace-nowrap hidden sm:table-cell">
                                    {{ patient.phone }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell">
                                    {{ patient.identification_card }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">
                                    {{ patient.nationality }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap hidden lg:table-cell">
                                    {{ patient.email }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2 justify-center">
                                        <Link class="text-primary-500 hover:text-primary-800"
                                            :href="route('patients.show', patient.id)">
                                        Ver
                                        </Link>
                                        <AccessGate :permission="['patient.update']">
                                            <Link class="text-green-500 hover:text-green-800"
                                                :href="route('patients.edit', patient.id)">
                                            Editar
                                            </Link>
                                        </AccessGate>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="!patients.data.length" class="w-full text-center text-gray-500 dark:text-gray-400 py-4">
                        No hay registros disponibles.
                    </div>
                </div>
                <Pagination :pagination="patients" :filters="form" />
            </div>
        </div>

    </AppLayout>
</template>

<script>
import AccessGate from '@/Components/Access/AccessGate.vue';
import CirclePlusIcon from '@/Components/Icons/CirclePlusIcon.vue';
import CircleXIcon from '@/Components/Icons/CircleXIcon.vue';
import PlusIcon from '@/Components/Icons/PlusIcon.vue';
import SearchIcon from '@/Components/Icons/SearchIcon.vue';
import XIcon from '@/Components/Icons/XIcon.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

export default {
    props: {
        patients: Object,
        filters: Object,
    },
    components: {
        AppLayout,
        Link,
        Pagination,
        AccessGate,
        CirclePlusIcon,
        CircleXIcon,
        PlusIcon,
        SearchIcon,
        XIcon,
        TextInput,
        InputLabel,
        PrimaryLink
    },
    data() {
        return {
            form: {
                search: this.filters.search || '',
                name: this.filters.name || '',
                phone: this.filters.phone || '',
                identificationCard: this.filters.identificationCard || '',
                nationality: this.filters.nationality || '',
                email: this.filters.email || '',
                showDeleted: this.filters.showDeleted,
                days: this.filters.days || '',
                hospitalized: this.filters.hospitalized || '',
                sortField: this.filters.sortField || '',
                sortDirection: this.filters.sortDirection || 'asc',
            },
            showAdditionalFilters: ref(false),
        };
    },
    methods: {
        toggleShowDeleted() {
            this.form.showDeleted = !this.form.showDeleted;
            this.$inertia.get(route('patients.index', this.form));
        },
        toggleAdditionalFilters() {
            this.showAdditionalFilters = !this.showAdditionalFilters
        },
        clearFilters() {
            this.form = {
                ...this.form,
                name: '',
                phone: '',
                identificationCard: '',
                nationality: '',
                email: '',
            }
            this.submitFilter();
        },
        submitFilter() {
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
            this.timeout = setTimeout(() => {
                this.$inertia.get(route('patients.index'), this.form, {
                    preserveState: true,
                    preserveScroll: true
                });
            }, 300);
        },
        sort(field) {
            this.form.sortField = field;
            this.form.sortDirection = this.form.sortDirection === 'asc' ? 'desc' : 'asc';
            this.submitFilter();
        }
    },
}
</script>
