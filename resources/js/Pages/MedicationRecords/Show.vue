<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">

                <BreadCrumb :items="[
                    ...(medicationRecord.id ? [{
                        formattedId: { id: medicationRecord.admission.id, prefix: 'ING' },
                        route: route('admissions.show', medicationRecord.id)
                    }] : []),
                    {
                        text: 'Fichas de Medicamentos',
                        route: medicationRecord.id
                            ? route('medicationRecords.index', { id: medicationRecord.id })
                            : route('medicationRecords.index')
                    },

                    {
                        formattedId: { id: medicationRecord.id, prefix: 'FICH' }
                    }
                ]" />
            </h2>

        </template>
        <div class="container mx-auto px-4   py-8">

            <div
                class="max-w-5xl mx-auto bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/60 rounded-2xl overflow-hidden">

                <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <Link :href="route('medicationRecords.index')"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <BackIcon class="size-5" />Volver
                    </Link>
                    <div class="flex items-center">
                        <button v-if="medicationRecord.active" @click="downloadRecordReport"
                            class=" mr-4 inline-flex   px-4 py-2 bg-emerald-500 text-white text-sm rounded-lg hover:to-emerald-600 transition-all duration-200">
                            <ReportIcon class="size-5 mr-1" /> Crear Reporte
                        </button>
                        <button v-if="medicationRecord.active" @click="recordBeingDeleted = true"
                            class="mr-4 flex items-center space-x-2 text-red-600 hover:text-red-800 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H3a1 1 0 100 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm2 4a1 1 0 011 1v7a1 1 0 11-2 0V7a1 1 0 011-1zm4 0a1 1 0 011 1v7a1 1 0 11-2 0V7a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium ">Eliminar</span>
                        </button>
                        <button v-else @click="restoreRecord(medicationRecord)"
                            class="flex items-center space-x-2 text-green-600 hover:text-green-800 transition-colors">
                            <span class="font-medium">Restaurar</span>
                        </button>
                    </div>

                </div>

                <!-- Mostrar errores -->
                <div v-if="errors.length > 0" class="bg-red-50 border-l-4 border-red-500 p-4 mx-8 my-4">
                    <div class="text-red-700" v-for="error in errors" :key="error">
                        {{ error }}
                    </div>
                </div>
                <!-- Información Principal -->
                <div class="p-8 space-y-8">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Paciente -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <Link :href="route('patients.show', medicationRecord.admission.patient.id)" as="button"
                                class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                            {{ medicationRecord.admission.patient.first_name }}
                            {{ medicationRecord.admission.patient.first_surname }}
                            {{ medicationRecord.admission.patient.second_surname }}
                            </Link>
                        </div>

                        <!-- Doctor -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Doctor</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                <!-- Verifica que la relación drug esté definida -->
                                {{ medicationRecord.admission.doctor.name }} {{
                                    medicationRecord.admission.doctor.last_name }}
                            </p>
                        </div>

                        <!-- Diagnostico -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnostico</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ medicationRecord.admission.admission_dx }}
                            </p>
                        </div>

                        <!-- bed -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ubicación</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                Cama {{ medicationRecord.admission.bed.number }} Habitacion {{
                                    medicationRecord.admission.bed.room }}
                            </p>
                        </div>

                        <!-- Dieta -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Dieta</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ medicationRecord.diet }}
                            </p>
                        </div>
                        <!-- Fecha Admission -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha
                            </h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ formatDate(medicationRecord.created_at) }}
                            </p>
                        </div>
                        <div v-if="$page.props.errors.message" class="alert alert-danger">
                            {{ $page.props.errors.message }}
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Nuevo Detalle</h3>
                    <div>
                        <button @click="OpenFormCreateRecord" id="add_detail" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md
                                hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                transition-colors duration-300">
                            Agregar Detalle
                        </button>
                    </div>

                    <div v-if="showCreateDetailForm"
                        class="grid border grid-cols-1 lg:grid-cols-2 shadow-xl rounded-lg gap-4  lg:mx-2 mt-6 "
                        id="formcreaterecord">
                        <!-- Tarjeta para información del Medical Order -->
                        <div class="relative overflow-hidden rounded-lg   bg-white dark:bg-gray-800 mb-5">

                            <div class="max-h-80 overflow-y-auto   shadow-md sm:rounded-lg mt-10 space-y-2 lg:mx-10">
                                <div class="col w-full md:w-[100%] p-4 md:p-8 ">
                                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Órdenes médicas
                                    </h3>
                                    <!-- Mensaje cuando no hay órdenes -->
                                    <div v-if="!orders || orders.length === 0"
                                        class="text-center text-sm text-gray-500 dark:text-gray-400">
                                        No hay órdenes médicas disponibles
                                    </div>

                                    <!-- Acordeón de Órdenes Médicas -->
                                    <div v-else class="space-y-4 max-h-72 overflow-y-auto">
                                        <div v-for="(order, index) in orders" :key="order.id">
                                            <div v-if="order.medical_order_detail.length !== 0"
                                                class="accordion-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">

                                                <div @click="toggleAccordion(index)"
                                                    class="accordion-header cursor-pointer flex justify-between items-center p-4 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                                    <div class="flex items-center justify-between w-full space-x-2">
                                                        <span class="font-semibold text-gray-800 dark:text-white">
                                                            <Link :href="route('medicalOrders.show', order.id)">
                                                            <FormatId :id="order.id" prefix="ORD" />
                                                            </Link>
                                                            <!-- - {{ order.doctor.name }} {{ order.doctor.last_name }} -->
                                                        </span>
                                                        <span
                                                            class="font-normal pr-1 text-sm text-gray-500 dark:text-gray-400">{{
                                                                formatDateFromNow(order.created_at)
                                                            }}</span>
                                                    </div>
                                                    <ChevronDown
                                                        class="h-5 w-5 transform transition-transform duration-300 text-gray-800 dark:text-white"
                                                        :class="{ 'rotate-180': openAccordion === index }" />
                                                </div>

                                                <!-- Contenido del Acordeón -->
                                                <div v-if="openAccordion === index"
                                                    class="accordion-content p-4 bg-white dark:bg-gray-900">
                                                    <div v-for="(detail, detailIndex) in order.medical_order_detail"
                                                        :key="detailIndex" @click="selectOrder(detail.id)" :class="{
                                                            'bg-blue-500 text-white': selectedOrderId === detail.id && !detail.suspended_at,
                                                            'bg-white dark:bg-gray-800': selectedOrderId !== detail.id && !detail.suspended_at,

                                                        }" class="border mb-2 rounded-lg p-4 m-2 shadow-md cursor-pointer transition duration-200">

                                                        <div class="flex flex-col justify-between items-start">
                                                            <div class="w-full flex flex-col">
                                                                <div class="flex justify-between items-center w-full">
                                                                    <p
                                                                        class="text-sm font-semibold text-gray-800 dark:text-white">
                                                                        {{ detail.order }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                        {{ formatDateFromNow(detail.created_at) }}
                                                                    </p>
                                                                </div>
                                                                <p
                                                                    class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                                                    {{ detail.regime }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Espacio del detalle de Medical Orders -->

                        </div>

                        <div class="relative overflow-x-auto   sm:rounded-lg   lg:mx-10">
                            <form @submit.prevent="submit" class="max-w-sm mx-auto">

                                <!-- Contenedor para la Medicamento y el selector -->
                                <div class="flex justify-between items-center mt-6">
                                    <input type="hidden" v-model="form.selectedOrderId" />

                                    <!-- Selector de Medicamento -->
                                    <div class="w-full mb-2">
                                        <label for="drug"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Medicamento</label>
                                        <DrugSelector v-model:drug="form.drug" />
                                    </div>
                                </div>

                                <!-- Contenedor para la Via y el selector -->

                                <!-- Selector -->
                                <div class="flex-1">
                                    <label for="route-select"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Via
                                    </label>
                                    <select id="route-select" required v-model="form.route"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option v-for="routes in routeOptions" :key="routes.id" :value="routes.name">
                                            {{ routes.name }} - {{ routes.description }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Contenedor para la Dosis y el selector -->
                                <div class="flex items-center space-x-4 mt-6">
                                    <!-- Dosis -->
                                    <div class="flex-1">
                                        <label for="dose"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Dosis
                                        </label>
                                        <input id="dose" required type="number" v-model="form.dose"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Escribe la Dosis asignada..." />
                                    </div>
                                    <!-- Selector -->
                                    <div class="flex-1">
                                        <label for="dose-select"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Dosis
                                        </label>
                                        <select id="dose-select" required v-model="form.dose_metric"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option v-for="doses in dose" :key="doses.id" :value="doses.name">
                                                {{ doses.name }} - {{ doses.description }}
                                            </option>
                                        </select>

                                    </div>
                                </div>

                                <!-- Estudios Pendientes -->
                                <label for="fc"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                                    Frecuencia
                                </label>
                                <input id="fc" rows="4" required type="number" v-model="form.fc"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Escribe los estudios pendientes..."></input>

                                <!-- Firma del Doctor -->
                                <label for="interval_in_hours"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                                    Intervalo en Horas
                                </label>
                                <input required id="interval_in_hours" type="number" v-model="form.interval_in_hours"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Intervalo en Horas..." />

                                <!-- Hora de Inicio -->
                                <label for="start_time"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                                    Hora de Inicio
                                </label>
                                <input required id="start_time" type="time" v-model="form.start_time" :min="currentTime"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Hora de Inicio..." />

                                <!-- Botones -->
                                <div class="flex justify-end mt-6 mb-2">
                                    <button @click="closeform"
                                        class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                        Cerrar
                                    </button>

                                    <button type="submit"
                                        class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                                        Guardar
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div id="errorMessage" v-if="errorMessage"
                        class="bg-red-100 text-red-700 text-center p-2 w-full rounded-md mb-2">
                        {{ errorMessage }}
                    </div>
                </div>

                <div class="p-8 space-y-4  bg-gray-50 dark:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                            Detalles del Registro
                        </h3>

                        <div v-if="medicationRecord.active">
                            <button @click="toggleShowDeleted"
                                class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors whitespace-nowrap ml-auto"
                                :class="{
                                    'bg-red-500 hover:bg-red-600 text-white': form.showDeleted,
                                    'bg-gray-100 hover:bg-gray-100 text-gray-800 dark:bg-gray-800 dark:hover:bg-gray-600 dark:text-gray-200': !form.showDeleted
                                }">
                                {{ form.showDeleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                                <svg class="ml-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path v-if="form.showDeleted" fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 001.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                                        clip-rule="evenodd" />
                                    <path v-else fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                    </div>

                    <div v-for="detail in details" :key="detail.id" :class="[
                        'rounded-lg p-4 border flex justify-between items-center transition-colors',
                        !detail.suspended_at
                            ? 'bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-900'
                            : 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 hover:bg-red-100 dark:hover:bg-red-900/30',

                    ]">
                        <div class="flex-grow">
                            <div class="font-semibold text-gray-900 dark:text-white">
                                Medicamento: {{ detail.drug }}

                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                Dosis: {{ detail.dose }} {{ detail.dose_metric }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Via: {{ detail.route }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Frecuencia: {{ detail.fc }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Intervarlo en Horas: {{ detail.interval_in_hours }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Fecha: {{ formatDate(detail.created_at) }}
                            </div>
                            <div v-for="notifications in detail.medication_notification" :key=notifications.id
                                class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <div v-if="notifications.applied === 1">
                                    Medicamento Administrado el: {{ formatDate(notifications.administered_time) }}
                                </div>
                                <div v-if="Firstnoapplied(notifications)">
                                    Siguiente: {{ formatDate(notifications.scheduled_time) }}
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <div v-if="detail.active && detail.suspended_at == null">
                                <!-- Editar -->
                                <Link v-if="!hasApplied(detail)"
                                    :href="route('medicationRecordDetails.edit', detail.id)"
                                    class="flex items-center space-x-2 text-yellow-600 hover:text-yellow-800 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Editar</span>
                                </Link>
                                <AccessGate :permission="['medicationRecordDetail.view']">
                                    <!-- NOTIF -->
                                    <Link :href="route('medicationNotification.show', detail.id)"
                                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="font-medium">Notificaciones</span>
                                    </Link>
                                </AccessGate>
                            </div>
                            <button @click="ToggleActivate(detail)"
                                :class="[detail.active ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700']"
                                class="flex items-center space-x-2 text-white-600 hover:text-white-800 transition-colors">
                                <svg xmlns="http:1//www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>{{ detail.active ? 'Eliminar' : 'Restaurar' }}</span>
                            </button>

                            <div v-if="medicationRecord.active">
                                <!-- Disable -->
                                <button @click="ToggleSuspend(detail)"
                                    :class="[!detail.suspended_at ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700']"
                                    class="flex items-center space-x-2 text-white-600 hover:text-white-800 transition-colors">
                                    <svg xmlns="http:1//www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ !detail.suspended_at ? 'Suspender' : 'Habilitar' }}</span>
                                </button>
                            </div>

                        </div>
                    </div>

                    <div v-if="details.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No hay detalles de registro disponibles
                    </div>

                </div>

            </div>
        </div>

        <ConfirmationModal :show="recordBeingDeleted != null" @close="recordBeingDeleted = null">
            <template #title>
                Eliminar Ingreso
            </template>
            <template #content>
                ¿Estás seguro de que deseas eliminar este ingreso?
            </template>
            <template #footer>
                <SecondaryButton @click="recordBeingDeleted = null">
                    Cancelar
                </SecondaryButton>
                <DangerButton class="ms-3" @click="deleteRecord">
                    Eliminar
                </DangerButton>
            </template>
        </ConfirmationModal>

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
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nombre
                                </label>
                                <input type="text" id="name" v-model="modalform.name" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                    placeholder="Nombre del Medicamento" />
                            </div>
                            <!-- Campo Descripción -->
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Descripción
                                </label>
                                <textarea id="description" v-model="modalform.description" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                    placeholder="Descripción del Medicamento"></textarea>
                            </div>

                        </div>
                    </form>
                </div>
            </template>
            <!-- Footer del modal -->
            <template #footer>
                <button @click="submitModal"
                    class="mr-6 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                    Guardar
                </button>
                <button @click="isVisible = false"
                    class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cerrar
                </button>
            </template>
        </DialogModal>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link,
    useForm
} from '@inertiajs/vue3';
import {
    ref
} from "vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue';
import FormatId from '@/Components/FormatId.vue';
import DrugSelector from '@/Components/DrugSelector.vue';
import InputError from '@/Components/InputError.vue';
import BackIcon from '@/Components/Icons/BackIcon.vue';
import ReportIcon from '@/Components/Icons/ReportIcon.vue';
import ChevronDown from '@/Components/Icons/ChevronDown.vue';
import moment from 'moment/moment';
import 'moment/locale/es';
import BreadCrumb from '@/Components/BreadCrumb.vue';
export default {

    props: {
        medicationRecord: Object,
        details: Array,
        orders: Object,
        drug: Array,
        routeOptions: Array,
        dose: Array,
        filters: Object,
        selectedDrug: Array,
        errors: {
            type: Array,
            default: () => []
        },
    },
    components: {
        AppLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        BackIcon,
        DialogModal,
        AccessGate,
        SignaturePad,
        FormatId,
        DrugSelector,
        InputError,
        ReportIcon,
        ChevronDown,
        BreadCrumb
    },
    data() {
        return {
            form: useForm({
                medication_record_id: this.medicationRecord.id,
                drug: this.selectedDrug || null,
                dose: '',
                route: '',
                fc: '',
                interval_in_hours: '',
                start_time: '',
                dose_metric: '',
                selectedOrderId: null,
                showDeleted: this.filters.show_deleted,
            }),
            showCreateDetailForm: ref(false),
            recordBeingDeleted: ref(null),
            selectedOrderId: null,
            errorMessage: "",

            isVisible: false,
            isVisibleEditSign: ref(null),
            openAccordion: ref(null),
            modalform: {
                description: '',
                name: '',
            },

        }
    },
    mounted() {
        this.form.start_time = moment().format('HH:mm');
    },
    computed: {

        currentTime() {
            return moment().format('HH:mm');
        }
    },

    methods: {
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY HH:mm');
        },
        toggleShowDeleted() {
            this.form.showDeleted = !this.form.showDeleted;
            this.$inertia.get(route('medicationRecords.show', {
                medicationRecord: this.medicationRecord
            }), {
                showDeleted: this.form.showDeleted
            }, {
                preserveState: true,
                preserveScroll: true
            });
        },

        openCreateModal() {
            this.isVisible = true;
        },
        submitModal() {
            this.$inertia.post(route('Drugs.store'), this.modalform, {
                preserveScroll: true
            });
            this.isVisible = false;
            this.modalform = {
                name: '',
                description: '',
            };

        },
        restoreRecord(record) {
            this.$inertia.put(
                route('medicationRecords.update', record.id), {
                active: true,
                preserveScroll: true
            }, {

                onSuccess: (response) => {
                    this.form.showDeleted = null;
                    toggleShowDeleted();
                },
                onError: (errors) => {
                    console.error('Errores:', errors);
                    alert('Error al intentar habilitar el registro.');
                },
            }
            );
        },
        submit() {
            if (!this.form.selectedOrderId) {
                this.errorMessage = "Debe seleccionar una orden antes de guardar.";
                return;
            }
            if (this.form.dose <= 0) {
                this.errorMessage = "La Dosis debe ser mayor a 0";
                return;
            }

            if (this.form.fc > 25) {
                this.errorMessage = "Frecuencia debe ser menor de 25 veces";
                return;
            }
            if (this.form.interval_in_hours > 24) {
                this.errorMessage = "El Intervalo en horas debe ser menor de 24 horas (1 dia)";
                return;
            }

            if (this.form.fc <= 0) {
                this.errorMessage = "Frecuencia debe  ser mayor a 0";
                return;
            }
            if (this.form.interval_in_hours <= 0) {
                this.errorMessage = "El Intervalo en horas debe ser mayor a 0";
                return;
            }

            this.errorMessage = "";

            this.$inertia.post(route('medicationRecordDetails.store'), this.form, {
                preserveScroll: true,

                onSuccess: () => {
                    this.form = {
                        medication_record_id: this.medicationRecord.id,
                        drug: '',
                        dose: '',
                        route: '',
                        dose_metric: '',
                        fc: '',
                        interval_in_hours: '',
                        selectedOrderId: null,
                    };
                    this.selectedOrderId = null;
                    this.showCreateDetailForm = false;
                },
                preserveScroll: true,
            });
        },
        toggleAccordion(index) {
            if (this.openAccordion === index) {
                this.openAccordion = null // Cierra si ya está abierto
            } else {
                this.openAccordion = index // Abre el acordeón seleccionado
            }
        },
        formatDateFromNow(date) {
            return moment(date).fromNow();
        },
        selectOrder(id) {
            this.selectedOrderId = id;
            this.form.selectedOrderId = id;
        },
        hasApplied(detail) {
            return detail.medication_notification?.some(item => item.applied === 1) ?? false;
        },
        Firstnoapplied(notifications) {
            for (const detail of this.details) {
                if (Array.isArray(detail.medication_notification)) {

                    const firstNotApplied = detail.medication_notification.find(
                        (n) => n.applied === 0
                    );

                    if (firstNotApplied && firstNotApplied.id === notifications.id) {
                        return true;
                    }
                }
            }

            return false;
        },
        OpenFormCreateRecord() {
            this.form.start_time = moment().format('HH:mm');
            this.showCreateDetailForm = true

        },
        closeform() {
            this.showCreateDetailForm = false

        },
        deleteRecord() {
            this.recordBeingDeleted = null;
            this.$inertia.delete(route('medicationRecords.destroy', this.medicationRecord.id), {
                onSuccess: (response) => {
                    console.log('eliminado correctamente', response);
                    this.recordBeingDeleted = null;
                },
                preserveScroll: true
            });
        },
        ToggleActivate(detail) {
            if (detail.active) {
                this.$inertia.delete(route('medicationRecordDetails.destroy', detail.id), {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (response) => {
                        console.log('eliminado correctamente', response);
                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                });
            } else {
                this.$inertia.put(route('medicationRecordDetails.update', detail.id), {
                    active: true,
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (response) => {
                        console.log('update', response);
                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                });
            }
        },
        async downloadRecordReport() {
            window.open(route('reports.medicationRecord', {
                id: this.medicationRecord.id
            }), '_blank');
        },

        ToggleSuspend(detail) {
            if (detail.suspended_at) {

                this.$inertia.put(route('medicationRecordDetails.update', detail.id), {
                    suspended_at: true,
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (response) => {
                        console.log('update', response);
                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                });
            } else {

                this.$inertia.put(route('medicationRecordDetails.update', detail.id), {
                    suspended_at: false,
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (response) => {
                        console.log('update', response);
                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                });
            }
        },

    }
}
</script>

<style scoped>
.max-h-80 {
    max-height: 600px;
}

.alert {
    color: white;
    background: red;
    padding: 10px;
    margin: 10px 0;
    border-radius: 10;
}
</style>
