<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">

                <BreadCrumb :items="[
                    ...(admission_id ? [{
                        formattedId: { id: admission_id, prefix: 'ING' },
                        route: route('admissions.show', admission_id)
                    }] : []),
                    {
                       text: 'Ficha de Medicamentos',
                        route: admission_id
                            ? route('medicationRecords.index', { admission_id })
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
                      <Link
                :href="admission_id ? route('admissions.show', admission_id) : route('medicationRecords.index')"
                class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors"
            >
                <BackIcon class="size-5" />
                Volver
            </Link>

                    <div class="flex items-center gap-2">
                        <PersonalizableButton v-if="medicationRecord.active" @click="downloadRecordReport"
                            color="emerald">
                            <ReportIcon class="size-5 " />
                            Crear Reporte
                        </PersonalizableButton>
                        <AccessGate :permission="['medicationRecord.delete']">
                            <DangerButton v-if="medicationRecord.active" @click="recordBeingDeleted = true">

                                <TrashIcon class="size-5" />
                                <span class="font-medium ">Eliminar</span>

                            </DangerButton>
                            <PersonalizableButton v-else @click="restoreRecord(medicationRecord)" class="gap-2"
                                color="green" :loading="recordActiveChanging">
                                <RestoreIcon class="size-5" />
                                <span class="hidden sm:inline-flex">Restaurar</span>
                            </PersonalizableButton>
                        </AccessGate>
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
                                Cama {{ medicationRecord?.admission?.bed?.number || "N/A" }}
                                Habitación {{ medicationRecord?.admission?.bed?.room || "N/A" }}

                            </p>

                        </div>

                        <!-- Dieta -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60 flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Dieta</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                                    {{ medicationRecord.diet }}
                                </p>
                            </div>
                            <AccessGate :permission="['medicationRecord.update']" class="mt-auto">
                                <button class="text-blue-500 ml-2 hover:text-blue-800" @click="openCreateModal()">
                                    <EditIcon class="size-5" />
                                </button>
                            </AccessGate>
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
                    <AccessGate :permission="['medicationRecordDetail.create']">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Nuevo Detalle</h3>
                        <div v-if="showCreateDetailForm == false">
                            <PersonalizableButton @click="OpenFormCreateRecord" id="add_detail" size="large"
                                class="w-full">
                                Agregar Detalle
                            </PersonalizableButton>
                        </div>
                        <div v-else>
                            <PersonalizableButton @click="closeform" id="add_detail" size="large" class="w-full">
                                Cerrar
                            </PersonalizableButton>
                        </div>
                    </AccessGate>

                    <div v-if="showCreateDetailForm"
                        class="grid border grid-cols-1  lg:grid-cols-2 shadow-xl rounded-lg gap-4  lg:mx-2 mt-6 "
                        id="formcreaterecord">
                        <!-- Tarjeta para información del Medical Order -->
                        <div class="relative overflow-hidden rounded-lg pb-auto   bg-white dark:bg-gray-800 mb-5">

                            <div class="h-full overflow-y-auto    sm:rounded-lg mt-10 space-y-2 lg:mx-10">
                                <div class="col w-full md:w-[100%] p-4 md:p-2 ">
                                    <h3 class="text-xl font-semibold text-gray-800 pl-2 dark:text-white mb-6">Órdenes
                                        médicas
                                        <span class="text-red-500">*</span>
                                    </h3>

                                    <!-- Mensaje cuando no hay órdenes -->
                                    <div v-if="!orders || orders.length === 0 || !orders.some(o => o.medical_order_detail && o.medical_order_detail.length > 0)"
                                        class="text-center pb-4 text-sm text-gray-500 dark:text-gray-400">
                                        No hay órdenes médicas disponibles.
                                    </div>


                                    <!-- Acordeón de Órdenes Médicas -->
                                    <div v-else class="space-y-4 h-full overflow-y-auto">
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
                                                            'bg-indigo-600 text-white': selectedOrderId === detail.id && !detail.suspended_at,
                                                            'bg-white dark:bg-gray-800': selectedOrderId !== detail.id && !detail.suspended_at,

                                                        }"
                                                        class="border mb-2 rounded-lg p-4 m-2  shadow-md cursor-pointer transition duration-200">

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
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Medicamento
                                        </label>
                                        <DrugSelector v-model:drug="form.drug" />
                                        <InputError :message="form.errors.drug" class="mt-2" />
                                    </div>
                                </div>
                                <label for="drug"
                                    class="flex justify-between ml-1 pb-2 items-center mb-2 text-sm font-medium text-gray-900 dark:text-white">

                                    <span class="flex">
                                        Nebulizado:
                                        <Checkbox class="ml-2 mt-1" v-model:checked="form.nebulized" />
                                    </span>
                                </label>

                                <!-- Selector -->
                                <div class="flex-1">
                                    <label for="route-select"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Via <span class="text-red-500">*</span>
                                    </label>
                                    <select id="route-select" required v-model="form.route"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option v-for="routes in routeOptions" :key="routes.id" :value="routes.name">
                                            {{ routes.name }} - {{ routes.description }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.route" class="mt-2" />
                                </div>

                                <!-- Contenedor para la Dosis y el selector -->
                                <div class="flex items-center space-x-4 mt-6">
                                    <!-- Dosis -->
                                    <div class="flex-1">
                                        <label for="dose"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Dosis <span class="text-red-500">*</span>
                                        </label>
                                        <input id="dose" required type="number" v-model="form.dose"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Escribe la Dosis asignada..." />
                                        <InputError :message="form.errors.dose" class="mt-2" />
                                    </div>
                                    <!-- Selector -->
                                    <div class="flex-1">
                                        <label for="dose-select"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Métrica <span class="text-red-500">*</span>
                                        </label>
                                        <select id="dose-select" required v-model="form.dose_metric"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option v-for="doses in dose" :key="doses.id" :value="doses.name">
                                                {{ doses.name }} - {{ doses.description }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.dose_metric" class="mt-2" />

                                    </div>
                                </div>

                                <!-- Estudios Pendientes -->
                                <label for="fc"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                                    Frecuencia <span class="text-red-500">*</span>
                                </label>
                                <input id="fc" rows="4" required type="number" v-model="form.fc"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Escribe los estudios pendientes..."></input>
                                <InputError :message="form.errors.fc" class="mt-2" />
                                <!-- Contenedor para la Intervalo en horas y minutos -->
                                <div class="flex items-center space-x-4 mt-6">
                                    <!-- Dosis -->
                                    <div class="flex-1" v-if="!form.nebulized">
                                        <!-- Intervalo en Horas -->
                                        <label for="interval_in_hours"
                                            class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                                            Intervalo en Horas <span class="text-red-500">*</span>
                                        </label>
                                        <input required id="interval_in_hours" type="number"
                                            v-model="form.interval_in_hours"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder="Intervalo en Horas..." />
                                        <InputError :message="form.errors.interval_in_hours" class="mt-2" />
                                    </div>
                                    <!-- Selector -->
                                    <div class="flex-1" v-if="form.nebulized">
                                        <!-- Intervalo en Horas -->
                                        <label for="nebulization_time"
                                            class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                                            Tiempo de Nebulización (min) <span class="text-red-500">*</span>
                                        </label>
                                        <input required id="nebulization_time" type="number"
                                            v-model="form.nebulization_time"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            placeholder=" Tiempo de Nebulización..." />
                                        <InputError :message="form.errors.nebulization_time" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Hora de Inicio -->
                                <label for="start_time"
                                    class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                                    Hora de Inicio <span class="text-red-500">*</span>
                                </label>
                                <input required id="start_time" type="time" v-model="form.start_time" :min="currentTime"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Hora de Inicio..." />
                                <InputError :message="form.errors.start_time" class="mt-2" />


                            </form>
                            <!-- Botones -->
                            <div class="flex justify-end mt-6 gap-2 mb-6">
                                <SecondaryButton @click="closeform">
                                    Cerrar
                                </SecondaryButton>

                                <PrimaryButton @click="recordDetailBeingCreated = true"
                                    :class="{ 'opacity-25': form.processing }" :is-loading="form.processing"
                                    :disabled="form.processing">
                                    Guardar
                                </PrimaryButton>

                            </div>
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
                        <AccessGate :permission="['medicationRecord.delete']">
                            <PersonalizableButton custom-class="whitespace-nowrap" @click="toggleShowDeleted"
                                :color="form.showDeleted ? 'red' : 'gray'">
                                {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                                <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-5 w-5" />
                                <CircleXIcon v-else class="ml-1 h-5 w-5" />
                            </PersonalizableButton>
                        </AccessGate>

                    </div>

                    <div v-for="detail in details" :key="detail.id" :class="[
                        'rounded-lg p-4 shadow-md flex justify-between items-center transition-colors',
                        detail.suspended_at
                            ? 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 hover:bg-red-100 dark:hover:bg-red-900/30'
                            : 'bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-900'
                    ]">
                        <div class="flex-grow">
                            <div class="font-semibold text-gray-900 dark:text-white">
                                Medicamento: {{ detail.drug }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Fecha de Creación: {{ formatDate(detail.created_at) }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1" v-if="detail.nebulization_time">
                                Intervalo de cada: {{ detail.nebulization_time }} minutos
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1" v-if="detail.interval_in_hours">
                                Intervalo de cada: {{ detail.interval_in_hours }} horas
                            </div>

                            <div v-for="notifications in detail.medication_notification" :key=notifications.id
                                class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <div v-if="Firstnoapplied(notifications)">
                                    Siguiente: {{ formatDate(notifications.scheduled_time) }}
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 ">
                            <div v-if="detail.active && detail.suspended_at == null">
                                <AccessGate :permission="['medicationNotification.view']">
                                    <!-- NOTIF -->
                                    <Link :href="route('medicationNotification.show',{ medicationNotification: detail.id, admission_id: admission_id })"
                                        class="flex items-center space-x-2 space-y-2 text-blue-600 hover:text-blue-800 transition-colors">
                                    <NotificationIcon class="size-5 " />
                                    <span class="font-medium">Notificaciones</span>
                                    </Link>
                                </AccessGate>
                                <AccessGate :permission="['medicationRecordDetail.update']">
                                    <!-- Editar -->
                                    <Link v-if="!hasApplied(detail)"
                                        :href="route('medicationRecordDetails.edit', detail.id)"
                                        class="flex items-center space-x-2 space-y-2 text-yellow-600 hover:text-yellow-800 transition-colors">
                                    <EditIcon class="size-5" />
                                    <span class="font-medium">Editar</span>
                                    </Link>
                                </AccessGate>

                            </div>
                            <AccessGate :permission="['medicationRecord.delete']">
                                <div v-if="detail.active" class="mt-1">
                                    <button v-if="detail.active"
                                        @click="detailBeingDeleted = true, selectedDetail = detail"
                                        class="flex items-center space-x-2 space-y-2   text-red-500 hover:text-red-70">
                                        <TrashIcon class="size-5 mr-2" /> Eliminar
                                    </button>

                                </div>
                                <div v-else>
                                    <button @click="selectedDetail = detail, restoreDetail()"
                                        class="flex items-center space-x-2 space-y-2   text-green-500 hover:text-green-700">
                                        <RestoreIcon class="size-5 mr-2" />Restarurar
                                    </button>

                                </div>
                            </AccessGate>
                            <AccessGate :permission="['medicationRecord.update']">
                                <div v-if="medicationRecord.active">
                                    <!-- Disable -->
                                    <button @click="ToggleSuspend(detail)"
                                        :class="[!detail.suspended_at ? 'text-indigo-500 ' : 'text-green-500 hover:text-green-700'], { 'opacity-25': recordDetailSupendChange }"
                                        class="flex items-center space-x-2 space-y-2  transition-colors"
                                        :disabled="recordDetailSupendChange">

                                        <div v-if="detail.suspended_at">
                                            <RestoreIcon class="size-5" />
                                        </div>
                                        <div v-else>
                                            <SuspendIcon class="size-5" />
                                        </div>
                                        <span>{{ !detail.suspended_at ? 'Suspender' : 'Habilitar' }}</span>
                                    </button>
                                </div>
                            </AccessGate>

                        </div>
                    </div>

                    <div v-if="details.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No hay detalles de registro disponibles.
                    </div>

                </div>

            </div>
        </div>

        <ConfirmationModal :show="recordBeingDeleted != null || detailBeingDeleted != null"
            @close="recordBeingDeleted = null; detailBeingDeleted = null">
            <template #title>
                Eliminar registro
            </template>

            <template v-if="recordBeingDeleted" #content>
                ¿Estás seguro de que deseas eliminar este registro?
            </template>
            <template v-else #content>
                ¿Estás seguro de que deseas eliminar este detalle?
            </template>

            <template #footer>
                <SecondaryButton @click="recordBeingDeleted = null; detailBeingDeleted = null">
                    Cancelar
                </SecondaryButton>

                <DangerButton v-if="recordBeingDeleted" class="ms-3" @click="deleteRecord">
                    <TrashIcon class="size-5 mr-2" />
                    Eliminar registro
                </DangerButton>
                <DangerButton v-else class="ms-3" @click="deleteDetail(); detailBeingDeleted = null;">
                    <TrashIcon class="size-5 mr-2" />
                    Eliminar detalle
                </DangerButton>
            </template>
        </ConfirmationModal>
        <!-- modal para crear -->
        <ConfirmationModal :show="recordDetailBeingCreated != null" @close="recordDetailBeingCreated = null">
            <template #title>
                Crear Detalle Ficha de Medicamentos
            </template>

            <template #content>
                ¿Estás seguro de que deseas crear este detalle ficha?
            </template>

            <template #footer>
                <SecondaryButton @click="recordDetailBeingCreated = null">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ms-3" @click="submit()">
                    Crear
                </PrimaryButton>
            </template>
        </ConfirmationModal>

        <DialogModal :show="isVisible" @close="isVisible = false" class="">

            <!-- Header del modal -->
            <template #title>
                Editar Dieta
            </template>
            <!-- Contenido del modal -->
            <template #content>
                <div>
                    <form>
                        <div class="grid gap-4">
                            <!-- Campo Nombre -->
                            <div>
                                <label for="diet"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Dieta <span class="text-red-500">*</span>
                                </label>
                                <select id="diet-select" v-model="modalform.diet" required
                                    class="text-sm font-medium w-full text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100  p-2.5 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                    <option v-for="diets in diet" :key="diets.id" :value="diets.name">
                                        {{ diets.name }} - {{ diets.description }}
                                    </option>
                                </select>
                            </div>


                        </div>
                    </form>
                </div>
            </template>
            <!-- Footer del modal -->
            <template #footer>
                <div class="flex justify-end  space-x-3">

                    <SecondaryButton @click="isVisible = false">
                        Cerrar
                    </SecondaryButton>

                    <PrimaryButton @click="submitModal" :class="{ 'opacity-25': modalform.processing }"
                        :disabled="modalform.processing" :is-loading="modalform.processing">
                        Aceptar
                    </PrimaryButton>

                </div>


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
import UserSelector from '@/Components/UserSelector.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import Modal from '@/Components/Modal.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import RestoreIcon from '@/Components/Icons/RestoreIcon.vue';
import NotificationIcon from '@/Components/Icons/NotificationIcon.vue';
import SuspendIcon from '@/Components/Icons/SuspendIcon.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CircleXIcon from '@/Components/Icons/CircleXIcon.vue';
import CirclePlusIcon from '@/Components/Icons/CirclePlusIcon.vue';
import Checkbox from '@/Components/Checkbox.vue';
import FormatRole from '@/Components/FormatRole.vue';
export default {

    props: {
        medicationRecord: Object,
        details: Array,
        orders: Object,
        drug: Array,
        routeOptions: Array,
        dose: Array,
        diet: Array,
        filters: Object,
        selectedDrug: Array,
        admission_id: Number,
        errors: {
            type: Array,
            default: () => []
        },
    },
    components: {
        AppLayout,
        Link,
        PrimaryButton,
        PersonalizableButton,
        UserSelector,
        TrashIcon,
        RestoreIcon,
        Checkbox,
        NotificationIcon,
        EditIcon,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        BackIcon,
        DialogModal,
        AccessGate,
        SignaturePad,
        FormatId,
        DrugSelector,
        Modal,
        InputError,
        ReportIcon,
        ChevronDown,
        BreadCrumb,
        SuspendIcon,
        CirclePlusIcon,
        CircleXIcon,
    },
    watch: {
        'form.nebulized'(newVal) {
            this.form.fc = newVal ? 1 : '';
        },
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
                nebulization_time: '',
                start_time: '',
                dose_metric: '',
                selectedOrderId: null,
                showDeleted: this.filters.show_deleted,
                nebulized: false,
            }),
            showCreateDetailForm: ref(false),
            recordBeingDeleted: ref(null),
            selectedOrderId: null,
            recordActiveChanging: ref(false),
            recordDetailActiveChange: ref(false),
            recordDetailSupendChange: ref(false),
            recordDetailBeingCreated: ref(null),
            selectedDetail: ref(null),
            errorMessage: "",
            detailBeingDeleted: ref(null),
            isVisible: false,
            isVisibleEditSign: ref(null),
            openAccordion: ref(null),
            modalform: useForm({
                diet: this.medicationRecord.diet,
            }),

        }
    },
    mounted() {
        this.form.start_time = moment().format('HH:mm');
    },
    computed: {

        currentTime() {
            return moment().format('HH:mm');
        },

        formattedId() {
            return 'FICH' + this.medicationRecord.id;
        }
    },

    methods: {

        formatDate(date) {
            return moment(date).format('DD MMMM YYYY HH:mm');
        },
       toggleShowDeleted() {
            const params = {
                medicationRecord: this.medicationRecord.id,
                admission_id: this.admission_id,
                showDeleted: !this.filters?.show_deleted
            };
            this.$inertia.get(route('medicationRecords.show', params), {}, {
                preserveScroll: true
            });
        },

        openCreateModal() {
            this.isVisible = true;
        },
        submitModal() {
            this.modalform.put(route('medicationRecords.update', this.medicationRecord), {
                preserveScroll: true
            });
            this.isVisible = false;


        },
        deleteDetail() {
            this.detailBeingDeleted = null;
            this.isVisibleDetail = false;

            this.$inertia.delete(
                route('medicationRecordDetails.destroy', this.selectedDetail.id),
                {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        // Redirigir para recargar los detalles con admission_id
                        const params = {
                            medicationRecord: this.medicationRecord.id,
                            admission_id: this.admission_id,
                        };

                        if (this.filters?.show_deleted) {
                            params.showDeleted = true;
                        }

                        this.$inertia.get(route('medicationRecords.show', params), {
                            preserveScroll: true,
                        });
                    }
                }
            );
        },
        restoreRecord(record) {
            this.recordActiveChanging = true;
            this.$inertia.put(
                route('medicationRecords.update', record.id), {
                active: true,
                preserveScroll: true
            }, {

                onSuccess: (response) => {
                    this.form.showDeleted = null;

                },
                onError: (errors) => {
                    console.error('Errores:', errors);
                    alert('Error al intentar habilitar el registro.');
                },
                onFinish: () => {

                    this.recordActiveChanging = false
                }
            }
            );
        },
        submit() {
            this.recordDetailBeingCreated = null;
            this.errorMessage = "";
            if (!this.form.selectedOrderId) {
                this.errorMessage = "Debe seleccionar una orden antes de guardar.";
                return;
            }
            if (!this.form.drug) {
                this.form.errors.drug = "Debe seleccionar un medicamennto.";
                return;
            }
            if (!this.form.route) {
                this.form.errors.route = "Debe seleccionar una vía.";
                return;
            }
            if (this.form.dose <= 0) {
                this.form.errors.dose = "La Dosis debe ser mayor a 0";
                return;
            }
            if (!this.form.dose_metric) {
                this.form.errors.dose_metric = "Debe seleccionar una métrica para la dosis.";
                return;
            }
            if (this.form.fc > 20) {
                this.form.errors.fc = "Frecuencia debe ser menor de 20 veces";
                return;
            }
            if (this.form.fc <= 0) {
                this.form.errors.fc = "La frecuencia debe  ser mayor a 0";
                return;
            }
            if (this.form.fc > 1) {
                if (this.form.interval_in_hours <= 0 && this.form.nebulization_time <= 0 ) {
                    this.form.errors.interval_in_hours = "El Intervalo debe ser mayor a 0";
                    return;
                }
                if (this.form.interval_in_hours > 24) {
                    this.form.errors.interval_in_hours = "El Intervalo en horas debe ser menor de 24 horas (1 día)";
                    return;
                }
                if (this.form.nebulization_time > 59) {
                    this.form.errors.nebulization_time = "El Intervalo en horas debe ser menor a 60(1 hora)";
                    return;
                }
            }
            if (this.form.fc = 1) {
                this.form.interval_in_hours = 0;
            }



            this.form.post(route('medicationRecordDetails.store'), {
                onSuccess: () => {
                    this.form.reset();
                    this.selectedOrderId = null;
                    this.showCreateDetailForm = false;
                }
            });

        },
        toggleAccordion(index) {
            if (this.openAccordion === index) {
                this.openAccordion = null // Cierra si ya está abierto
            } else {
                this.openAccordion = index // Abre el acordeón seleccionado
            }
        },
        restoreDetail() {

            this.$inertia.put(route('medicationRecordDetails.update', this.selectedDetail.id), {
                active: true,
                preserveScroll: true,
                preserveState: true
            });
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
            this.recordActiveChanging = true;
            this.$inertia.delete(route('medicationRecords.destroy', this.medicationRecord.id), {
                preserveScroll: true,
                onFinish: () => {

                    this.recordActiveChanging = false
                }

            });

        },
        ToggleActivate(detail) {
            this.recordDetailActiveChange = true;

            if (detail.active == 1) {

                this.$inertia.delete(route('medicationRecordDetails.destroy', detail.id), {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (response) => {
                        this.recordDetailActiveChange = false;


                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                    onFinish: () => {

                        this.recordDetailActiveChange = false;
                    }
                });
            } else {


                this.$inertia.put(route('medicationRecordDetails.update', detail.id), {
                    active: true,
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (response) => {

                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                    onFinish: () => {

                        this.recordDetailActiveChange = false;
                    }
                });
                this.recordDetailActiveChange = false;
            }

        },
        async downloadRecordReport() {
            window.open(route('reports.medicationRecord', {
                id: this.medicationRecord.id
            }), '_blank');
        },

        ToggleSuspend(detail) {
            this.recordDetailSupendChange = true;
            if (detail.suspended_at) {

                this.$inertia.put(route('medicationRecordDetails.update', detail.id), {
                    suspended_at: true,
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (response) => {

                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                    onFinish: () => {

                        this.recordDetailSupendChange = false;
                    }
                });
            } else {

                this.$inertia.put(route('medicationRecordDetails.update', detail.id), {
                    suspended_at: false,
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (response) => {

                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                    onFinish: () => {

                        this.recordDetailSupendChange = false;
                    }
                });
            }
            this.recordDetailSupendChange = false;
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
