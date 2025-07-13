            <template>
                <AppLayout>
                    <template #header>
                        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                            <BreadCrumb :items="[
                                ...(admission_id
                                    ? [
                                        {
                                            formattedId: { id: admission_id, prefix: 'ING' },
                                            route: route('admissions.show', admission_id),
                                        },
                                    ]
                                    : []),
                                {
                                    text: 'Ficha de Medicamentos',
                                    route: admission_id
                                        ? route('medicationRecords.index', { admission_id })
                                        : route('medicationRecords.index'),
                                },
                                {
                                    formattedId: { id: medicationRecord.id, prefix: 'FICH' },
                                },
                            ]" />
                        </h2>
                    </template>

                    <div class="container mx-auto px-4 py-8">
                        <div
                            class="max-w-5xl mx-auto bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/60 rounded-2xl overflow-hidden">

                            <div class="p-4 dark:bg-gray-900
                        flex items-center flex-nowrap gap-2"> <!-- fila fija, sin wrap -->

                                <!-- Volver (misma altura que los otros botones) -->
                                <Link
                                    :href="admission_id ? route('admissions.show', admission_id) : route('medicationRecords.index')"
                                    class="inline-flex items-center gap-2
                    px-3 py-2 rounded-md text-sm font-medium
                    text-blue-600 hover:text-blue-800 transition-colors">
                                <BackIcon class="size-5" />
                                <span>Volver</span>
                                </Link>

                                <!-- Botones de acción (derecha) -->
                                <div class="ml-auto flex items-center gap-2"> <!-- empujado al borde derecho -->
                                    <PersonalizableButton v-if="medicationRecord.active" @click="downloadRecordReport"
                                        color="emerald">
                                        <ReportIcon class="size-5" />
                                        <span class="hidden sm:inline-flex">Crear Reporte</span>
                                    </PersonalizableButton>

                                    <AccessGate :permission="['medicationRecord.delete']" v-if="canUpdateRecord">
                                        <DangerButton v-if="medicationRecord.active" @click="recordBeingDeleted = true">
                                            <TrashIcon class="size-5" />
                                            <span class="font-medium hidden sm:inline-flex">Eliminar</span>
                                        </DangerButton>

                                        <PersonalizableButton v-else @click="restoreRecord(medicationRecord)"
                                            class="gap-2" color="green" :loading="recordActiveChanging">
                                            <RestoreIcon class="size-5" />
                                            <span class="hidden sm:inline-flex">Restaurar</span>
                                        </PersonalizableButton>
                                    </AccessGate>
                                </div>
                            </div>
                            <!-- Alerta de errores responsive -->
                            <div v-if="errors.length > 0"
                                class="bg-red-50 border-l-4 border-red-500 p-4 mx-4 sm:mx-8 my-4 rounded-md">
                                <!-- Lista de errores -->
                                <ul class="list-disc list-inside space-y-1 text-sm sm:text-base text-red-700">
                                    <li v-for="error in errors" :key="error">
                                        {{ error }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Información Principal -->
                            <div class="p-4 sm:p-8 space-y-6 sm:space-y-8 bg-gray-50 dark:bg-gray-700">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

                                    <!-- Paciente -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
                                            Paciente
                                        </h3>
                                        <Link :href="route('patients.show', medicationRecord.admission.patient.id)"
                                            as="button"
                                            class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                                        {{ medicationRecord.admission.patient.first_name }}
                                        {{ medicationRecord.admission.patient.first_surname }}
                                        {{ medicationRecord.admission.patient.second_surname }}
                                        </Link>
                                    </div>

                                    <!-- Doctor -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
                                            Doctor
                                        </h3>
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ medicationRecord.admission.doctor.name }}
                                            {{ medicationRecord.admission.doctor.last_name }}
                                        </p>
                                    </div>

                                    <!-- Diagnóstico -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
                                            Diagnóstico
                                        </h3>
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ medicationRecord.admission.admission_dx }}
                                        </p>
                                    </div>

                                    <!-- Ubicación (Cama / Habitación) -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
                                            Ubicación
                                        </h3>
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                            Cama {{ medicationRecord?.admission?.bed?.number || 'N/A' }},
                                            Habitación {{ medicationRecord?.admission?.bed?.room || 'N/A' }}
                                        </p>
                                    </div>

                                    <!-- Dieta (editable) -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
                                                Dieta
                                            </h3>
                                            <p
                                                class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                                                {{ medicationRecord.diet }}
                                            </p>
                                        </div>
                                        <AccessGate :permission="['medicationRecord.update']" v-if="canUpdateRecord">
                                            <button class="text-blue-500 hover:text-blue-800 self-start sm:self-auto"
                                                @click="openCreateModal()">
                                                <EditIcon class="size-5" />
                                            </button>
                                        </AccessGate>
                                    </div>

                                    <!-- Fecha -->
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
                                            Fecha de Registro
                                        </h3>
                                        <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ formatDate(medicationRecord.created_at) }}
                                        </p>
                                    </div>

                                    <!-- Errores (solo si existen) -->
                                    <div v-if="$page.props.errors.message" class="alert alert-danger md:col-span-2">
                                        {{ $page.props.errors.message }}
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

                            <div class="p-2 sm:p-4">

                                <AccessGate :permission="['medicationRecordDetail.create']" v-if="canUpdateRecord">
                                    <h3
                                        class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-white mb-2 sm:mb-3 px-2">
                                        Agregar Nuevo Detalle
                                    </h3>

                                    <PersonalizableButton v-if="!showCreateDetailForm" @click="OpenFormCreateRecord"
                                        id="add_detail" size="large" class="w-full px-2">
                                        Agregar Detalle
                                    </PersonalizableButton>

                                    <PersonalizableButton v-else @click="closeform" id="add_detail" size="large"
                                        class="w-full px-2">
                                        Cerrar
                                    </PersonalizableButton>
                                </AccessGate>

                                <!-- Formulario -->
                                <div v-if="showCreateDetailForm"
                                    class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4 border rounded-2xl mt-3 sm:mt-4 mx-1 sm:mx-3">
                                    <!-- Tarjeta Órdenes -->
                                    <div class="rounded-2xl bg-white dark:bg-gray-800 overflow-hidden">

                                        <!-- Contenedor scrollable -->
                                        <div
                                            class="overflow-y-auto max-h-[70vh] sm:max-h-none space-y-2 sm:space-y-1 p-2 sm:p-4">
                                            <h3
                                                class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-white mb-2 sm:mb-3 px-2">
                                                Órdenes médicas <span class="text-red-500">*</span>
                                            </h3>

                                            <!-- Sin órdenes -->
                                            <div v-if="!orders || orders.length === 0 || !orders.some(o => o.medical_order_detail?.length)"
                                                class="text-center text-sm sm:text-base text-gray-500 dark:text-gray-400 py-2 px-2">
                                                No hay órdenes médicas disponibles.
                                            </div>

                                            <!-- Acordeón -->
                                            <div v-else class="space-y-2">
                                                <div v-for="(order, index) in orders" :key="order.id">
                                                    <div v-if="order.medical_order_detail.length"
                                                        class="border border-gray-200 dark:border-gray-700 rounded-2xl sm:rounded-xl overflow-hidden">
                                                        <!-- Header acordeón -->
                                                        <div @click="toggleAccordion(index)"
                                                            class="flex justify-between items-center p-2 sm:p-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                                            <div class="flex justify-between w-full gap-1 px-2">
                                                                <span
                                                                    class="font-semibold text-gray-800 dark:text-white">
                                                                    <Link :href="route('medicalOrders.show', order.id)">
                                                                    <FormatId :id="order.id" prefix="ORD" />
                                                                    </Link>
                                                                </span>
                                                                <span
                                                                    class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                                                    {{ formatDateFromNow(order.created_at) }}
                                                                </span>
                                                            </div>

                                                            <ChevronDown
                                                                class="h-4 w-4 sm:h-5 sm:w-5 text-gray-800 dark:text-white transform transition-transform duration-300"
                                                                :class="{ 'rotate-180': openAccordion === index }" />
                                                        </div>

                                                        <!-- Contenido acordeón -->
                                                        <div v-if="openAccordion === index"
                                                            class="p-2 sm:p-3 bg-white dark:bg-gray-900 rounded-b-2xl accordion-content">
                                                            <div v-for="(detail, detailIndex) in order.medical_order_detail"
                                                                :key="detailIndex" @click="selectOrder(detail.id)"
                                                                :class="[
                                                                    'border rounded-xl p-2 sm:p-2.5 m-0.5 sm:m-1 shadow cursor-pointer transition',
                                                                    detail.suspended_at
                                                                        ? 'opacity-50 pointer-events-none'
                                                                        : selectedOrderId === detail.id
                                                                            ? 'bg-indigo-100 dark:bg-indigo-500 text-white'
                                                                            : 'bg-white dark:bg-gray-800'
                                                                ]">
                                                                <div class="flex flex-col justify-between items-start">
                                                                    <div class="w-full flex flex-col">
                                                                        <div
                                                                            class="flex justify-between items-center w-full gap-2">
                                                                            <p
                                                                                class="text-sm font-semibold text-gray-800 dark:text-white break-words w-full">
                                                                                {{ detail.order }} </p>

                                                                        </div>
                                                                        <div
                                                                            class="flex justify-between items-center mt-2 w-full gap-2">
                                                                            <p
                                                                                class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                                                                {{ detail.regime }}
                                                                            </p>
                                                                            <p
                                                                                class="text-xs text-gray-500 dark:text-gray-400 text-end">
                                                                                {{ formatDateFromNow(detail.created_at)
                                                                                }}
                                                                            </p>


                                                                        </div>

                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Acordeón -->
                                        </div>
                                        <!-- /Overflow -->

                                        <!-- Espacio del detalle de Medical Orders -->

                                    </div>
                                    <div class="relative overflow-x-auto sm:rounded-lg mx-2 sm:mx-10">

                                        <!-- Formulario -->
                                        <form @submit.prevent="submit"
                                            class="w-full sm:max-w-sm mx-auto px-3 sm:px-0 space-y-6">

                                            <!-- Medicamento -->
                                            <div class="flex flex-col space-y-1 mt-4">
                                                <label class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Medicamento <span class="text-red-500">*</span>
                                                </label>
                                                <DrugSelector v-model:drug="form.drug" />
                                                <InputError :message="form.errors.drug" />
                                            </div>

                                            <!-- Nebulizado -->
                                            <div class="flex items-center justify-between">
                                                <label class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Nebulizado:
                                                </label>
                                                <Checkbox class="ml-2" v-model:checked="form.nebulized" />
                                            </div>

                                            <!-- Vía -->
                                            <div v-if="!form.nebulized" class="flex flex-col space-y-1">
                                                <label class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Vía <span class="text-red-500">*</span>
                                                </label>
                                                <select v-model="form.route" required class="w-full p-2.5 bg-gray-50 dark:bg-gray-700 border
                   border-gray-300 dark:border-gray-600 rounded-lg
                   text-sm text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                                    <option v-for="r in routeOptions" :key="r.id" :value="r.name">
                                                        {{ r.name }} – {{ r.description }}
                                                    </option>
                                                </select>
                                                <InputError :message="form.errors.route" />
                                            </div>

                                            <!-- Dosis + Métrica -->
                                            <div class="flex flex-col sm:flex-row sm:gap-4">
                                                <!-- Dosis -->
                                                <div class="flex-1 flex flex-col space-y-1">
                                                    <label class="text-sm font-medium text-gray-900 dark:text-white">
                                                        Dosis <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="number" min="1" step="1" required v-model="form.dose"
                                                        class="w-full p-2.5 bg-gray-50 dark:bg-gray-700 border
                    border-gray-300 dark:border-gray-600 rounded-lg
                    text-sm text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" />
                                                    <InputError :message="form.errors.dose" />
                                                </div>

                                                <!-- Métrica -->
                                                <div class="flex-1 flex flex-col space-y-1 mt-4 sm:mt-0">
                                                    <label class="text-sm font-medium text-gray-900 dark:text-white">
                                                        Métrica <span class="text-red-500">*</span>
                                                    </label>
                                                    <select v-model="form.dose_metric" required class="w-full p-2.5 bg-gray-50 dark:bg-gray-700 border
                     border-gray-300 dark:border-gray-600 rounded-lg
                     text-sm text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                                        <template v-if="!form.nebulized">
                                                            <option v-for="d in dose" :key="d.id" :value="d.name">
                                                                {{ d.name }} – {{ d.description }}
                                                            </option>
                                                        </template>
                                                        <template v-else>
                                                            <option value="ML">Ml – Mililitro</option>
                                                            <option value="MG">MG – Miligramo</option>
                                                            <option value="MCG">MCG – Microgramo</option>
                                                        </template>
                                                    </select>
                                                    <InputError :message="form.errors.dose_metric" />
                                                </div>
                                            </div>

                                            <!-- Frecuencia -->
                                            <div class="flex flex-col space-y-1" v-if="form.nebulized">
                                                <label class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Solución Salina (ml) <span class="text-red-500">*</span>
                                                </label>
                                                 <input type="text" v-model="form.saline_solution"
                                                    class="w-full p-2.5 bg-gray-50 dark:bg-gray-700 border
                                                border-gray-300 dark:border-gray-600 rounded-lg
                                                text-sm text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" />

                                            </div>
                                            <!-- Frecuencia -->
                                            <div class="flex flex-col space-y-1">
                                                <label class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Frecuencia <span class="text-red-500">*</span>
                                                </label>
                                                <input type="number" min="1" step="1" required v-model="form.fc"
                                                    class="w-full p-2.5 bg-gray-50 dark:bg-gray-700 border
                                                border-gray-300 dark:border-gray-600 rounded-lg
                                                text-sm text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" />
                                                <InputError :message="form.errors.fc" />
                                            </div>

                                            <!-- Intervalo (horas) -->
                                            <div class="flex flex-col space-y-1">
                                                <label class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Intervalo (horas) <span class="text-red-500">*</span>
                                                </label>
                                                <input type="number" min="1" step="1" required
                                                    v-model="form.interval_in_hours"
                                                    class="w-full p-2.5 bg-gray-50 dark:bg-gray-700 border
                                                border-gray-300 dark:border-gray-600 rounded-lg
                                                text-sm text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" />
                                                <InputError :message="form.errors.interval_in_hours" />
                                            </div>

                                            <!-- Tiempo de nebulización -->
                                            <div v-if="form.nebulized" class="flex flex-col space-y-1">
                                                <label class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Tiempo de Nebulización (min) <span class="text-red-500">*</span>
                                                </label>
                                                <input type="number" min="1" step="1" required
                                                    v-model="form.nebulization_time"
                                                    class="w-full p-2.5 bg-gray-50 dark:bg-gray-700 border
                                                    border-gray-300 dark:border-gray-600 rounded-lg
                                                    text-sm text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" />
                                                <InputError :message="form.errors.nebulization_time" />
                                            </div>

                                            <!-- Hora de inicio -->
                                            <div class="flex flex-col space-y-1">
                                                <label class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Hora de Inicio <span class="text-red-500">*</span>
                                                </label>
                                                <input type="time" :min="currentTime" required v-model="form.start_time"
                                                    class="w-full p-2.5 bg-gray-50 dark:bg-gray-700 border
                                                border-gray-300 dark:border-gray-600 rounded-lg
                                                text-sm text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" />
                                                <InputError :message="form.errors.start_time" />
                                            </div>

                                            <!-- Botones -->
                                            <div class="flex justify-end gap-3 px-4 py-4 !mt-0">
                                                <SecondaryButton @click="closeform">Cerrar</SecondaryButton>
                                                <PrimaryButton :disabled="form.processing"
                                                    :is-loading="form.processing">
                                                    Guardar
                                                </PrimaryButton>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                            <!-- Mensaje de error global -->
                            <div v-if="errorMessage"
                                class="bg-red-100 text-red-700 text-center p-2 mx-2 sm:mx-0 rounded-md mb-2">
                                {{ errorMessage }}
                            </div>


                            <!-- Detalles Ficha de medicamentos -->
                            <div class="p-4 sm:p-8 space-y-4 bg-gray-50 dark:bg-gray-700">
                                <!-- Encabezado + botón de eliminados -->
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                                        Detalles del Registro
                                    </h3>
                                    <AccessGate :permission="['medicationRecord.delete']" v-if="canUpdateRecord">
                                        <PersonalizableButton custom-class="whitespace-nowrap w-full sm:w-auto"
                                            @click="toggleShowDeleted" :color="form.showDeleted ? 'red' : 'gray'">
                                            {{ filters.show_deleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                                            <CirclePlusIcon v-if="form.showDeleted" class="ml-1 h-5 w-5" />
                                            <CircleXIcon v-else class="ml-1 h-5 w-5" />
                                        </PersonalizableButton>
                                    </AccessGate>
                                </div>

                                <!-- Tarjetas de detalle -->
                                <div v-for="detail in details" :key="detail.id" :class="[
                                    'flex flex-col md:flex-row md:justify-between md:items-start bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700/60',
                                    detail.suspended_at
                                        ? 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 hover:bg-red-100 dark:hover:bg-red-900/30'
                                        : 'hover:bg-gray-100 dark:hover:bg-gray-900'
                                ]">
                                    <!-- Bloque de texto -->
                                    <div class="flex-1 space-y-1">
                                        <div v-if="detail.nebulized"
                                            class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                            <Checkbox disabled :checked="true" class="pointer-events-none" />
                                            <span>Nebulizado</span>

                                        </div>
                                         <div v-if="detail.nebulized" class="text-sm text-gray-500 dark:text-gray-400">
                                            Solución Salina: {{ detail.saline_solution }} ml
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            Dosis: {{ detail.dose }} {{ detail.dose_metric }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            Fecha de Creación: {{ formatDate(detail.created_at) }}
                                        </div>
                                        <div v-if="detail.interval_in_hours"
                                            class="text-sm text-gray-500 dark:text-gray-400">
                                            Aplicar cada: {{ detail.interval_in_hours }} horas
                                        </div>
                                        <div v-if="detail.nebulized && detail.nebulization_time"
                                            class="text-sm text-gray-500 dark:text-gray-400">
                                            Tiempo de aplicación: {{ detail.nebulization_time }} minutos
                                        </div>
                                        <template v-if="detail.fc > 1">
                                            <div v-for="notifications in detail.medication_notification"
                                                :key="notifications.id"
                                                class="text-sm text-gray-500 dark:text-gray-400">
                                                <div v-if="Firstnoapplied(notifications)">
                                                    Siguiente: {{ formatDate(notifications.scheduled_time) }}
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Bloque de acciones (dos columnas en móvil, columna en escritorio) -->
                                    <div class="mt-4 md:mt-0
             grid grid-cols-2 gap-1
             md:flex md:flex-col md:items-end md:space-y-1
             text-sm">
                                        <!-- Notificaciones -->
                                        <AccessGate :permission="['medicationNotification.view']"
                                            v-if="canUpdateRecord">
                                            <Link v-if="detail.active && detail.suspended_at === null"
                                                :href="route('medicationNotification.show', { medicationNotification: detail.id, admission_id })"
                                                class="inline-flex items-center justify-center px-2 py-1 rounded-md text-blue-600 hover:text-blue-800 transition-colors">
                                            <NotificationIcon class="size-5 mr-1" />
                                            Notificaciones
                                            </Link>
                                        </AccessGate>

                                        <!-- Editar -->
                                        <AccessGate :permission="['medicationRecordDetail.update']"
                                            v-if="canUpdateRecord">
                                            <Link v-if="detail.active && !detail.suspended_at && !hasApplied(detail)"
                                                :href="route('medicationRecordDetails.edit', { medicationRecordDetail: detail.id, admission_id })"
                                                class="inline-flex items-center justify-center px-2 py-1 rounded-md text-yellow-600 hover:text-yellow-800 transition-colors">
                                            <EditIcon class="size-5 mr-1" />
                                            Editar
                                            </Link>
                                        </AccessGate>

                                        <!-- Suspender / Habilitar -->
                                        <AccessGate :permission="['medicationRecord.update']" v-if="canUpdateRecord">
                                            <button v-if="medicationRecord.active" @click="ToggleSuspend(detail)"
                                                :disabled="recordDetailSupendChange"
                                                class="inline-flex items-center justify-center px-2 py-1 rounded-md transition-colors text-indigo-500 hover:text-indigo-700 disabled:opacity-25">
                                                <component :is="detail.suspended_at ? 'RestoreIcon' : 'SuspendIcon'"
                                                    class="size-5 mr-1" />
                                                {{ detail.suspended_at ? 'Habilitar' : 'Suspender' }}
                                            </button>
                                        </AccessGate>

                                        <!-- Eliminar / Restaurar -->

                                        <AccessGate :permission="['medicationRecord.delete']" v-if="canUpdateRecord">
                                            <button v-if="detail.active"
                                                @click="detailBeingDeleted = true; selectedDetail = detail"
                                                class="inline-flex items-center justify-center px-2 py-1 rounded-md text-red-500 hover:text-red-700 transition-colors">
                                                <TrashIcon class="size-5 mr-1" />
                                                Eliminar
                                            </button>
                                            <button v-else @click="selectedDetail = detail; restoreDetail()"
                                                class="inline-flex items-center justify-center px-2 py-1 rounded-md text-green-500 hover:text-green-700 transition-colors">
                                                <RestoreIcon class="size-5 mr-1" />
                                                Restaurar
                                            </button>
                                        </AccessGate>
                                    </div>
                                </div>

                                <!-- Sin detalles -->
                                <div v-if="details.length === 0"
                                    class="text-center text-gray-500 dark:text-gray-400 py-4">
                                    No hay detalles de registro disponibles.
                                </div>
                            </div>

                            <!-- Confirmación de eliminación -->
                            <ConfirmationModal :show="recordBeingDeleted !== null || detailBeingDeleted !== null"
                                @close="recordBeingDeleted = null; detailBeingDeleted = null"
                                class="w-[92vw] max-w-sm sm:max-w-md">
                                <!-- Título -->
                                <template #title>Eliminar registro</template>

                                <!-- Contenido -->
                                <template v-if="recordBeingDeleted" #content>
                                    ¿Estás seguro de que deseas eliminar este registro?
                                </template>
                                <template v-else #content>
                                    ¿Estás seguro de que deseas eliminar este detalle?
                                </template>

                                <!-- Acciones -->
                                <template #footer>
                                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                                        <SecondaryButton @click="recordBeingDeleted = null; detailBeingDeleted = null"
                                            class="w-full sm:w-auto">
                                            Cancelar
                                        </SecondaryButton>

                                        <DangerButton v-if="recordBeingDeleted" class="w-full sm:w-auto"
                                            @click="deleteRecord">
                                            <TrashIcon class="size-5 mr-2" />
                                            Eliminar registro
                                        </DangerButton>

                                        <DangerButton v-else class="w-full sm:w-auto"
                                            @click="deleteDetail(); detailBeingDeleted = null">
                                            <TrashIcon class="size-5 mr-2" />
                                            Eliminar detalle
                                        </DangerButton>
                                    </div>
                                </template>
                            </ConfirmationModal>

                            <!--Modal de edición de dieta -->
                            <DialogModal :show="isVisible" @close="isVisible = false"
                                class="w-[92vw] max-w-sm sm:max-w-lg">
                                <!-- Título -->
                                <template #title>Editar Dieta</template>

                                <!-- Formulario -->
                                <template #content>
                                    <form class="space-y-4">
                                        <!-- Campo Dieta -->
                                        <div>
                                            <label for="diet-select"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Dieta <span class="text-red-500">*</span>
                                            </label>

                                            <select id="diet-select" v-model="modalform.diet" required class="w-full text-sm rounded-lg border-gray-200 dark:border-gray-600
                                                bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-400
                                                focus:outline-none p-2.5 hover:bg-gray-100 dark:hover:bg-gray-700
                                                hover:text-blue-700 dark:hover:text-white focus:ring-4
                                                focus:ring-gray-100 dark:focus:ring-gray-700">
                                                <option v-for="diets in diet" :key="diets.id" :value="diets.name">
                                                    {{ diets.name }} - {{ diets.description }}
                                                </option>
                                            </select>
                                        </div>
                                    </form>
                                </template>

                                <!-- Acciones -->
                                <template #footer>
                                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                                        <SecondaryButton @click="isVisible = false" class="w-full sm:w-auto">
                                            Cerrar
                                        </SecondaryButton>

                                        <PrimaryButton @click="submitModal"
                                            :class="{ 'opacity-25': modalform.processing }"
                                            :disabled="modalform.processing" :is-loading="modalform.processing"
                                            class="w-full sm:w-auto">
                                            Aceptar
                                        </PrimaryButton>
                                    </div>
                                </template>
                            </DialogModal>
                        </div>
                    </div>
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
        canUpdateRecord: Boolean,
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
            this.dose_metric = newVal ? 'ML' : '';
        },
    },
    data() {
        return {
            form: useForm({
                medication_record_id: this.medicationRecord.id,
                drug: this.selectedDrug || null,
                saline_solution: '',
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
                    data: {
                        admission_id: this.admission_id
                    },
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

            this.errorMessage = "";
            if (!this.form.selectedOrderId) {
                this.errorMessage = "Debe seleccionar una orden antes de guardar.";
                return;
            }
            if (!this.form.drug) {
                this.form.errors.drug = "Debe seleccionar un medicamennto.";
                return;
            }
            if (this.form.nebulized) {
                this.form.route = 'inhalatoria';
            }
            if (!this.form.route && !this.form.nebulized) {
                console.log("Debe seleccionar una vía.");
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
             if (!this.form.saline_solution) {
                this.form.errors.saline_solution = "Debe ingresar  una solucion salina para la dosis.";
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
                if (this.form.interval_in_hours <= 0 && this.form.nebulization_time <= 0) {
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
            if (this.form.fc === 1 && !this.form.nebulized) {
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
            console.log(this.canUpdateRecord)
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
