<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
import FormatRole from '@/Components/FormatRole.vue';
import Toast from '@/Components/Toast.vue';
import BedIcon from '@/Components/Icons/BedIcon.vue';
import DashboardIcon from '@/Components/Icons/DashboardIcon.vue';
import AdmissionIcon from '@/Components/Icons/AdmissionIcon.vue';
import MedicalOrderIcon from '@/Components/Icons/MedicalOrderIcon.vue';
import NurseRecordIcon from '@/Components/Icons/NurseRecordIcon.vue';
import TemperatureIcon from '@/Components/Icons/TemperatureIcon.vue';
import MedicationIcon from '@/Components/Icons/MedicationIcon.vue';
import PatientIcon from '@/Components/Icons/PatientIcon.vue';
import UserIcon from '@/Components/Icons/UserIcon.vue';
import SidebarIcon from '@/Components/Icons/SidebarIcon.vue';
import SidebarFilledIcon from '@/Components/Icons/SidebarFilledIcon.vue';
import Tooltip from '@/Components/Tooltip.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const sidebarExpanded = ref(true);

const toggleSidebar = () => {
    sidebarExpanded.value = !sidebarExpanded.value;
    localStorage.setItem('sidebarExpanded', sidebarExpanded.value);
};

onMounted(() => {
    const savedSidebarState = localStorage.getItem('sidebarExpanded');
    if (savedSidebarState !== null) {
        sidebarExpanded.value = savedSidebarState === 'true';
    }
});

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>

        <Head :title="title" />

        <Banner />
        <Toast />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Sidebar - visible solo en escritorio -->
            <aside
                class="hidden sm:flex flex-col h-screen fixed left-0 top-0 bottom-0 bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 shadow-md transition-all duration-300 ease-in-out z-20"
                :class="{ 'w-64': sidebarExpanded, 'w-20': !sidebarExpanded }">
                <!-- Logo -->
                <div
                    :class="['flex items-center p-4 border-b border-gray-100 dark:border-gray-700', { 'justify-between': sidebarExpanded, 'justify-center': !sidebarExpanded }]">
                    <Link :href="route('dashboard')" class="flex items-center">
                    <ApplicationMark class="h-9 w-auto" />
                    <span v-if="sidebarExpanded"
                        class="ml-3 text-xl font-semibold text-gray-800 dark:text-white transition-opacity duration-300">
                        NurSys
                    </span>
                    </Link>

                </div>

                <!-- Navigation Links -->
                <div class="flex-1 overflow-y-auto py-6 space-y-1">
                    <div class="px-3">
                        <Tooltip class="w-full" text="Dashboard" position="right">
                            <Link :href="route('dashboard')" :class="[
                                route().current('dashboard') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                'group flex items-center px-3 py-3 text-sm font-medium rounded-md transition-all',
                                { 'justify-center': !sidebarExpanded }
                            ]">
                            <DashboardIcon
                                class="h-5 w-5 text-gray-500 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300" />
                            <span v-if="sidebarExpanded" class="truncate ml-3">Dashboard</span>
                            </Link>
                        </Tooltip>
                    </div>

                    <div class="px-3">
                        <Tooltip class="w-full" text="Camas" position="right">
                            <Link :href="route('beds.index')" :class="[
                                route().current('beds.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                'group flex items-center px-3 py-3 text-sm font-medium rounded-md transition-all',
                                { 'justify-center': !sidebarExpanded }
                            ]">
                            <BedIcon
                                class="h-5 w-5 text-gray-500 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300" />
                            <span v-if="sidebarExpanded" class="truncate ml-3">Camas</span>
                            </Link>
                        </Tooltip>
                    </div>

                    <div class="px-3">
                        <Tooltip class="w-full" text="Ingresos" position="right">
                            <Link :href="route('admissions.index')" :class="[
                                route().current('admissions.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                'group flex items-center px-3 py-3 text-sm font-medium rounded-md transition-all',
                                { 'justify-center': !sidebarExpanded }
                            ]">
                            <AdmissionIcon
                                class="h-5 w-5 text-gray-500 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300" />
                            <span v-if="sidebarExpanded" class="truncate ml-3">Ingresos</span>
                            </Link>
                        </Tooltip>
                    </div>

                    <div class="px-3">
                        <Tooltip class="w-full" text="Órdenes Médicas" position="right">
                            <Link :href="route('medicalOrders.index')" :class="[
                                route().current('medicalOrders.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                'group flex items-center px-3 py-3 text-sm font-medium rounded-md transition-all',
                                { 'justify-center': !sidebarExpanded }
                            ]">
                            <MedicalOrderIcon
                                class="h-5 w-5 text-gray-500 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300" />
                            <span v-if="sidebarExpanded" class="truncate ml-3">Órdenes Médicas</span>
                            </Link>
                        </Tooltip>
                    </div>

                    <div class="px-3">
                        <Tooltip class="w-full" text="Registros de Enfermería"
                            position="right">
                            <Link :href="route('nurseRecords.index')" :class="[
                                route().current('nurseRecords.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                'group flex items-center px-3 py-3 text-sm font-medium rounded-md transition-all',
                                { 'justify-center': !sidebarExpanded }
                            ]">
                            <NurseRecordIcon
                                class="h-5 w-5 text-gray-500 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300" />
                            <span v-if="sidebarExpanded" class="truncate ml-3">Registros de Enfermería</span>
                            </Link>
                        </Tooltip>
                    </div>

                    <AccessGate :permission="['temperatureRecord.view']" class="px-3">
                        <Tooltip class="w-full" text="Hojas de Temperatura" position="right">
                            <Link :href="route('temperatureRecords.index')" :class="[
                                route().current('temperatureRecords.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                'group flex items-center px-3 py-3 text-sm font-medium rounded-md transition-all',
                                { 'justify-center': !sidebarExpanded }
                            ]">
                            <TemperatureIcon
                                class="h-5 w-5 text-gray-500 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300" />
                            <span v-if="sidebarExpanded" class="truncate ml-3">Hojas de Temperatura</span>
                            </Link>
                        </Tooltip>
                    </AccessGate>

                    <div class="px-3">
                        <Tooltip class="w-full" text="Ficha de Medicamentos" position="right">
                            <Link :href="route('medicationRecords.index')" :class="[
                                route().current('medicationRecords.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                'group flex items-center px-3 py-3 text-sm font-medium rounded-md transition-all',
                                { 'justify-center': !sidebarExpanded }
                            ]">
                            <MedicationIcon
                                class="h-5 w-5 text-gray-500 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300" />
                            <span v-if="sidebarExpanded" class="truncate ml-3">Ficha de Medicamentos</span>
                            </Link>
                        </Tooltip>
                    </div>

                    <div class="px-3">
                        <Tooltip class="w-full" text="Pacientes" position="right">
                            <Link :href="route('patients.index')" :class="[
                                route().current('patients.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                'group flex items-center px-3 py-3 text-sm font-medium rounded-md transition-all',
                                { 'justify-center': !sidebarExpanded }
                            ]">
                            <PatientIcon
                                class="h-5 w-5 text-gray-500 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300" />
                            <span v-if="sidebarExpanded" class="truncate ml-3">Pacientes</span>
                            </Link>
                        </Tooltip>
                    </div>

                    <AccessGate :permission="['user.view']" class="px-3">
                        <Tooltip class="w-full" text="Usuarios" position="right">
                            <Link :href="route('users.index')" :class="[
                                route().current('users.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700',
                                'group flex items-center px-3 py-3 text-sm font-medium rounded-md transition-all',
                                { 'justify-center': !sidebarExpanded }
                            ]">
                            <UserIcon
                                class="h-5 w-5 text-gray-500 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-300" />
                            <span v-if="sidebarExpanded" class="truncate ml-3">Usuarios</span>
                            </Link>
                        </Tooltip>
                    </AccessGate>
                </div>

            </aside>

            <!-- Contenido principal - ajustado para acomodar el sidebar -->
            <div class="flex flex-col flex-1 transition-all duration-300"
                :class="{ 'sm:ml-64': sidebarExpanded, 'sm:ml-20': !sidebarExpanded }">

                <!-- Top bar (solo para móviles) - mantiene el menú hamburguesa existente -->
                <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sm:hidden">

                    <!-- Primary Navigation Menu -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <!-- Logo -->
                                <div class="shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                    <ApplicationMark class="block h-9 w-auto" />
                                    </Link>
                                </div>
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <!-- Componentes del menú superior que ya no usamos en escritorio pero mantenemos para móvil -->
                            </div>

                            <!-- Hamburger -->
                            <div class="-me-2 flex items-center sm:hidden">
                                <button
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                                    @click="showingNavigationDropdown = !showingNavigationDropdown">
                                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path
                                            :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                        <path
                                            :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu - mantener igual que el original -->
                    <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                        class="sm:hidden">
                        <div class="pt-2 pb-3 space-y-1">
                            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('beds.index')" :active="route().current('beds.index')">
                                Camas
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('admissions.index')"
                                :active="route().current('admissions.index')">
                                Ingresos
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('medicalOrders.index')"
                                :active="route().current('medicalOrders.index')">
                                Órdenes Médicas
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('nurseRecords.index')"
                                :active="route().current('nurseRecords.index')">
                                Registros de Enfermería
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('temperatureRecords.index')"
                                :active="route().current('temperatureRecords.index')">
                                Hojas de Temperatura
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('medicationRecords.index')"
                                :active="route().current('medicationRecords.index')">
                                Ficha de Medicamentos
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('patients.index')"
                                :active="route().current('patients.index')">
                                Pacientes
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.auth.user.roles.includes('admin')"
                                :href="route('users.index')" :active="route().current('users.index')">
                                Usuarios
                            </ResponsiveNavLink>
                        </div>
                        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center px-4">
                                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                    <img class="size-10 rounded-full object-cover"
                                        :src="$page.props.auth.user.profile_photo_url"
                                        :alt="$page.props.auth.user.name">
                                </div>

                                <div>
                                    <div
                                        class="font-medium text-base text-gray-800 dark:text-gray-200 flex items-center">
                                        {{ $page.props.auth.user.name }}
                                        <span
                                            class="bg-blue-100 ml-2 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-indigo-900 dark:text-indigo-300">
                                            <div v-if="$page.props.auth.user.roles">
                                                <FormatRole :role="$page.props.auth.user.roles[0]" />
                                            </div>
                                            <div v-else>N/A</div>
                                        </span>
                                    </div>
                                    <div class="font-medium text-sm text-gray-500">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('profile.show')"
                                    :active="route().current('profile.show')">
                                    Perfil
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures"
                                    :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                    API Tokens
                                </ResponsiveNavLink>

                                <!-- Authentication -->
                                <form method="POST" @submit.prevent="logout">
                                    <ResponsiveNavLink as="button">
                                        Cerrar sesión
                                    </ResponsiveNavLink>
                                </form>

                                <!-- Team Management -->
                                <template v-if="$page.props.jetstream.hasTeamFeatures">
                                    <div class="border-t border-gray-200 dark:border-gray-600" />

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Team
                                    </div>

                                    <!-- Team Settings -->
                                    <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)"
                                        :active="route().current('teams.show')">
                                        Team Settings
                                    </ResponsiveNavLink>

                                    <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams"
                                        :href="route('teams.create')" :active="route().current('teams.create')">
                                        Create New Team
                                    </ResponsiveNavLink>

                                    <!-- Team Switcher -->
                                    <template v-if="$page.props.auth.user.all_teams.length > 1">
                                        <div class="border-t border-gray-200 dark:border-gray-600" />

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Switch Teams
                                        </div>

                                        <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                            <form @submit.prevent="switchToTeam(team)">
                                                <ResponsiveNavLink as="button">
                                                    <div class="flex items-center">
                                                        <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                            class="me-2 size-5 text-green-400"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <div>{{ team.name }}</div>
                                                    </div>
                                                </ResponsiveNavLink>
                                            </form>
                                        </template>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Heading -->
                <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow flex items-center justify-between">
                    <div class="max-w-7xl py-6 px-4 sm:px-6 lg:px-8 flex items-center">

                        <div class="hidden sm:flex sm:items-center sm:justify-center mr-8">
                            <button @click="toggleSidebar"
                                class="p-1 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700">
                                <SidebarIcon v-if="sidebarExpanded" class="h-5 w-5" />
                                <SidebarFilledIcon v-else class="h-5 w-5" />
                            </button>
                        </div>

                        <slot name="header" />
                    </div>

                    <!-- Perfil en la esquina superior derecha cuando el sidebar está expandido -->
                    <div class="hidden sm:flex sm:items-center sm:justify-end p-4">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center text-sm">
                                    <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                        <img class="size-8 rounded-full object-cover"
                                            :src="$page.props.auth.user.profile_photo_url"
                                            :alt="$page.props.auth.user.name">
                                    </div>

                                </button>
                            </template>
                            <template #content>
                                <!-- Account Management -->

                                <div class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200">
                                    <div class="font-medium">{{
                                        $page.props.auth.user.name }} {{
                                            $page.props.auth.user.last_name }}</div>
                                    <div class="mt-2 text-xs truncate">{{ $page.props.auth.user.email }}
                                        <div
                                            class="bg-indigo-200 text-indigo-900 mt-2 text-xs font-medium px-2.5 py-1 rounded-sm dark:bg-indigo-700 dark:text-indigo-100 w-max">
                                            <div v-if="$page.props.auth.user.roles">
                                                <FormatRole :role="$page.props.auth.user.roles[0]" />
                                            </div>
                                            <div v-else>N/A</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-t border-gray-200 dark:border-gray-600" />

                                <DropdownLink :href="route('profile.show')">
                                    Perfil
                                </DropdownLink>

                                <!-- Authentication -->
                                <form @submit.prevent="logout">
                                    <DropdownLink as="button">
                                        Cerrar cesión
                                    </DropdownLink>
                                </form>
                            </template>
                        </Dropdown>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
