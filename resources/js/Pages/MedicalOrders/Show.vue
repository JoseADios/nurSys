<template>
    <AppLayout title="Órdenes Médicas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
                <BreadCrumb :items="[
                    // Condicionar el primer elemento (solo se muestra si hay admission_id)
                    ...(admission_id ? [{
                        formattedId: { id: medicalOrder.admission_id, prefix: 'ING' },
                        route: route('admissions.show', medicalOrder.admission_id)
                    }] : []),

                    // Segundo elemento (depende si hay admission_id o no)
                    {
                        text: 'Órdenes Médicas',
                        route: admission_id
                            ? route('medicalOrders.index', { admission_id: admission_id })
                            : route('medicalOrders.index')
                    },

                    {
                        formattedId: { id: medicalOrder.id, prefix: 'ORD' }
                    }
                ]" />
            </h2>

        </template>


        <div class="container mx-auto px-4 py-8">

            <div
                class="max-w-6xl mx-auto bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/60 rounded-2xl overflow-hidden">
                <!-- Navigation -->
                <div class="p-4 bg-white dark:bg-gray-900 flex justify-between items-center">
                    <div v-if="admission_id">
                        <Link :href="route('medicalOrders.index', { admission_id: admission_id })"
                            class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                        <BackIcon class="size-5" />
                        <span class="font-medium ">Volver</span>
                        </Link>
                    </div>
                    <div v-else>
                        <Link :href="route('medicalOrders.index')"
                            class="flex px-4 sm:px-0 items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                        <BackIcon class="size-5" />
                        <span class="font-medium">Volver</span>
                        </Link>
                    </div>
                    <div class="flex items-center gap-2">
                        <PersonalizableButton v-if="medicalOrder.active" @click="downloadRecordReport" color="emerald">
                            <ReportIcon class="size-5 " />
                            <span class="hidden sm:inline-flex">Crear Reporte</span>
                        </PersonalizableButton>
                        <AccessGate :permission="['medicalOrder.delete']">
                            <DangerButton v-if="medicalOrder.active" @click="recordBeingDeleted = true">
                                <TrashIcon class="size-5" />
                                <span class="font-medium hidden sm:inline-flex">Eliminar</span>
                            </DangerButton>
                            <PersonalizableButton v-else @click="restoreRecord" class="gap-2" color="green">
                                <RestoreIcon class="size-5" />
                                <span class="hidden sm:inline-flex">Restaurar</span>
                            </PersonalizableButton>
                        </AccessGate>
                    </div>
                </div>

                <!-- Patient and Record Information -->
                <div class="p-4 sm:p-8 space-y-6 sm:space-y-8 bg-gray-50 dark:bg-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

                        <!-- Columna 1 -------------------------------------------------------->
                        <div class="space-y-4">

                            <!-- Ingreso (alineado correctamente) -->
                            <div v-if="!isVisibleAdm"
                                class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ingreso</h3>

                                <div class="flex items-center justify-between">
                                    <Link :href="route('admissions.show', medicalOrder.admission_id)" as="button"
                                        class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                                    <FormatId :id="medicalOrder.admission_id" prefix="ING" />
                                    </Link>

                                    <AccessGate :role="['admin']">
                                        <button @click="showEditAdmission = true"
                                            class="text-blue-500 hover:text-blue-800">
                                            <EditIcon class="size-5" />
                                        </button>
                                    </AccessGate>
                                </div>
                            </div>
                            <!-- Selector de ingreso (modo edición) -->
                            <div v-if="isVisibleAdm"
                                class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                <form @submit.prevent="submitAdmission">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
                                        Seleccionar Ingreso
                                    </h3>

                                    <select v-model="formAdmission.admission_id" class="w-full text-gray-900 dark:text-white bg-white dark:bg-gray-800
                                border border-gray-300 dark:border-gray-700 rounded-md p-2 shadow-sm
                                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option v-for="admission in admissions" :key="admission.id"
                                            :value="admission.id">
                                            {{ admission.created_at }}
                                            {{ admission.patient.first_name }} {{ admission.patient.first_surname }}
                                            {{ admission.patient.second_surname }}
                                            — Cama {{ admission.bed?.number || 'N/A' }}, Sala
                                            {{ admission.bed?.room || 'N/A' }}
                                        </option>
                                    </select>

                                    <div class="mt-4 flex gap-2">
                                        <button type="button" @click="toggleEditAdmission" class="py-2.5 px-5 text-sm font-medium text-gray-900 bg-white border
                                    border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700
                                    dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600
                                    dark:hover:bg-gray-700 dark:hover:text-white">
                                            Cancelar
                                        </button>

                                        <button type="submit" class="py-2.5 px-5 text-sm font-medium text-white bg-green-800 rounded-lg
                                    hover:bg-green-900 focus:outline-none focus:ring-4 focus:ring-green-300
                                    dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">
                                            Aceptar
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Paciente -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
                                    Paciente
                                </h3>
                                <Link :href="route('patients.show', medicalOrder.admission.patient.id)" as="button"
                                    class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-400">
                                {{ medicalOrder.admission.patient.first_name }}
                                {{ medicalOrder.admission.patient.first_surname }}
                                {{ medicalOrder.admission.patient.second_surname }}
                                </Link>
                            </div>

                            <!-- Sala / Cama -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
                                    Sala
                                </h3>
                                <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                    Sala {{ medicalOrder.admission.bed?.room || 'N/A' }},
                                    Cama {{ medicalOrder.admission.bed?.number || 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <!-- Columna 2 -------------------------------------------------------->
                        <div class="space-y-4">

                            <!-- Doctor/a (alineado correctamente) -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Doctor/a</h3>

                                <div class="flex items-center justify-between">
                                    <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ doctor.name }} {{ doctor.last_name }}
                                    </p>

                                    <AccessGate :role="['admin']">
                                        <button @click="showEditDoctor = true"
                                            class="text-blue-500 hover:text-blue-800">
                                            <EditIcon class="size-5" />
                                        </button>
                                    </AccessGate>
                                </div>
                            </div>

                            <!-- Fecha -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 border border-gray-200 dark:border-gray-700/60">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
                                    Fecha de Registro
                                </h3>
                                <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ formatDate(medicalOrder.created_at) }}
                                </p>
                            </div>

                            <!-- Errores -->
                            <div v-if="$page.props.errors.message" class="alert alert-danger md:col-span-2">
                                {{ $page.props.errors.message }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario para agregar nuevo detalle -->
                <AccessGate :permission="['medicalOrder.delete']">
                    <div class="p-8 ">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Nuevos Detalles
                        </h3>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="grid md:grid-cols-[2fr_1fr] gap-4">
                                <div>
                                    <label for="orden"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Órden <span class="text-red-500">*</span>
                                    </label>
                                    <TextInput type="text" id="order" maxlength="255" v-model="formDetail.order"
                                        required
                                        class="w-full px-3 py-2 border  border-gray-300 dark:border-gray-600 rounded-md shadow-none focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                        placeholder="Orden médica" />
                                </div>

                                <div>
                                    <label for="regime"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Régimen
                                    </label>
                                    <select id="regime" v-model="formDetail.regime"
                                        class="w-full text-gray-900 dark:text-white bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md p-2 shadow-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option :value="regime.name" v-for="regime in regimes" :key="regime.id">
                                            {{ regime.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="pt-4">
                            <PersonalizableButton type="" @click="medicalOrderDetailBeingCreated = true"
                                :class="{ 'opacity-25': formDetail.processing }" :loading="formDetail.processing"
                                :disabled="formDetail.processing" size="large" class="w-full">
                                Agregar Detalle
                            </PersonalizableButton>
                        </div>

                    </div>
                </AccessGate>
                <!-- Órdenes – Detalles Médicas *********************************************** -->
                <div class="p-4 sm:p-8 space-y-4 bg-gray-50 dark:bg-gray-700">
                    <!-- Encabezado + botón Mostrar/Ocultar eliminados -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-0">
                            Detalles del Registro
                        </h3>

                        <AccessGate :permission="['medicalOrder.delete']">
                            <button @click="toggleShowDeleted"
                                class="flex items-center justify-center w-full sm:w-auto px-4 py-2 rounded-lg transition-colors whitespace-nowrap"
                                :class="{
                                    'bg-red-500 hover:bg-red-600 text-white': showDeleted,
                                    'bg-gray-200 hover:bg-gray-400 text-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-gray-200':
                                        !showDeleted
                                }">
                                {{ showDeleted ? 'Ocultar Eliminados' : 'Ver Eliminados' }}
                                <CirclePlusIcon v-if="showDeleted" class="ml-1 h-5 w-5" />
                                <CircleXIcon v-else class="ml-1 h-5 w-5" />
                            </button>
                        </AccessGate>
                    </div>

                    <!-- Lista de detalles -->
                    <div v-for="detail in details" :key="detail.id" :class="[
                        // Tarjeta base
                        'grid grid-cols-[1fr_auto] items-center gap-2 p-4 sm:p-6',
                        'rounded-lg border border-gray-200 dark:border-gray-700/60 shadow-none transition-colors',
                        // Color según suspendido
                        detail.suspended_at
                            ? 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 hover:bg-red-100 dark:hover:bg-red-900/30'
                            : 'bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-900'
                    ]">
                        <!-- Columna 1: toda la información de texto -->
                        <div class="min-w-0 break-words">
                            <p class="font-semibold text-gray-900 dark:text-white break-all">
                                {{ detail.order }}
                            </p>

                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ detail.regime }}
                            </p>

                            <div v-if="detail.suspended_at"
                                class="text-sm text-red-600 dark:text-red-400 mt-1 font-medium flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                Suspendido: {{ detail.suspended_at }}
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ formatDate(detail.created_at) }}
                            </p>
                        </div>

                        <!-- Columna 2: botón Editar → verticalmente centrado por Grid -->
                        <AccessGate :permission="['medicalOrder.update']">
                            <button @click="openEditModal(detail)" class="flex items-center justify-center text-blue-600 hover:text-blue-800 transition-colors
               w-10 h-10 sm:w-auto sm:h-auto">
                                <EditIcon class="size-5" />
                                <span class="hidden sm:inline-flex ml-2 font-medium">Editar</span>
                            </button>
                        </AccessGate>
                    </div>

                    <!-- Mensaje cuando no hay detalles -->
                    <div v-if="details.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No hay eventos de órdenes disponibles
                    </div>
                </div>

                <!-- Sección de firma *********************************************************** -->
                <AccessGate :permission="['medicalOrder.update']">
                    <section id="bottom" class="p-4 sm:p-8 space-y-4 bg-gray-50 dark:bg-gray-700">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Firma</h3>

                        <!-- Imagen de firma -->
                        <div v-show="!isVisibleEditSign" class="flex flex-col items-center my-4">
                            <img v-if="medicalOrder.doctor_sign" :src="`/storage/${medicalOrder.doctor_sign}`"
                                alt="Firma" class="w-full max-w-md" />
                            <div v-else class="my-16 text-gray-500 dark:text-gray-400">
                                No hay firma disponible
                            </div>

                            <PersonalizableButton @click="isVisibleEditSign = true">
                                Editar
                            </PersonalizableButton>
                        </div>

                        <!-- Pad de firma -->
                        <div v-show="isVisibleEditSign" class="my-4">
                            <form @submit.prevent="submitSignature" class="flex flex-col items-center">
                                <label for="doctor_sign"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Firma
                                </label>

                                <SignaturePad class="w-full max-w-lg lg:max-w-md" v-model="formSignature.doctor_sign"
                                    input-name="doctor_sign" />

                                <p v-if="signatureError" class="text-red-500 text-sm mt-2">
                                    La firma es obligatoria.
                                </p>

                                <div class="my-4 flex gap-2">
                                    <SecondaryButton @click="isVisibleEditSign = false">
                                        Cerrar
                                    </SecondaryButton>

                                    <PrimaryButton type="submit" :disabled="formSignature.processing"
                                        :is-loading="formSignature.processing"
                                        :class="{ 'opacity-25': formSignature.processing }">
                                        Aceptar
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </section>
                </AccessGate>
            </div>
        </div>

        <!-- Change admission modal -->
        <AccessGate :permission="['medicalOrder.delete']">
            <Modal :closeable="true" :show="showEditAdmission != null" @close="showEditAdmission == null">
                <div
                    class="relative overflow-hidden shadow-lg sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
                    <form @submit.prevent="submitUpdateRecord" class="max-w-3xl mx-auto">

                        <AdmissionSelector @update:admission="formRecord.admission_id = $event"
                            :selected-admission-id="medicalOrder.admission_id" :doesnt-have-medical-order="true" />

                        <!-- Botones -->
                        <div class="flex justify-end mt-4 space-x-3">

                            <SecondaryButton @click="showEditAdmission = null">
                                Cerrar
                            </SecondaryButton>

                            <PrimaryButton type="submit" :class="{ 'opacity-25': formRecord.processing }"
                                :is-loading="formRecord.processing" :disabled="formRecord.processing">
                                Aceptar
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AccessGate>

       <DialogModal :show="isVisibleDetail" @close="isVisibleDetail = false">
  <!-- Header -->
  <template #title>
    Editar orden
  </template>

  <!-- Contenido -->
  <template #content>
    <form @submit.prevent="submitUpdateDetail">
      <div class="grid gap-4 md:grid-cols-[2fr_1fr]">
        <!-- Orden -->
        <div>
          <label
            for="order"
            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Órden <span class="text-red-500">*</span>
          </label>
          <input
            id="order"
            v-model="selectedDetail.order"
            type="text"
            required
            class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                   border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
            placeholder="Orden médica"
          />
        </div>

        <!-- Régimen -->
        <div>
          <label
            for="regime"
            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Régimen
          </label>
          <select
            id="regime"
            v-model="selectedDetail.regime"
            class="w-full p-2 text-gray-900 bg-white border rounded-md shadow-sm
                   dark:text-white dark:bg-gray-800 border-gray-300 dark:border-gray-700
                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option :value="regime.name" v-for="regime in regimes" :key="regime.id">
              {{ regime.name }}
            </option>
          </select>
        </div>

        <!-- Suspender -->
        <div class="flex items-center">
          <input
            id="suspended_at"
            type="checkbox"
            :checked="selectedDetail.suspended_at"
            @change="
              selectedDetail.suspended_at = $event.target.checked
                ? new Date().toISOString().slice(0, 19).replace('T', ' ')
                : null
            "
            class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded
                   focus:ring-red-500 dark:focus:ring-red-600 dark:bg-gray-700 dark:border-gray-600
                   focus:ring-2"
          />
          <label
            for="suspended_at"
            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300"
          >
            Suspender
          </label>
        </div>
      </div>
    </form>
  </template>

  <!-- Footer -->
  <template #footer>
    <div class="flex flex-col sm:flex-row sm:justify-end gap-2 w-full">
      <!-- Eliminar / Restaurar -->
      <AccessGate :permission="['medicalOrder.delete']">
        <DangerButton
          v-if="selectedDetail.active"
          class="w-full sm:w-auto"
          @click="detailBeingDeleted = true"
        >
          <TrashIcon class="size-5 mr-2" />
          Eliminar
        </DangerButton>

        <PersonalizableButton
          v-else
          color="green"
          class="w-full sm:w-auto"
          :class="{ 'opacity-25': modalform.processing }"
          :is-loading="modalform.processing"
          :disabled="modalform.processing"
          @click="restoreDetail"
        >
          <RestoreIcon class="size-5 mr-2" />
          Restaurar
        </PersonalizableButton>
      </AccessGate>

      <!-- Actualizar -->
      <PrimaryButton
        class="w-full sm:w-auto"
        :class="{ 'opacity-25': modalform.processing }"
        :is-loading="modalform.processing"
        :disabled="modalform.processing"
        @click="submitUpdateDetail"
      >
        Actualizar
      </PrimaryButton>

     <!-- Cerrar -->
<SecondaryButton
  class="w-full sm:w-auto flex justify-center"
  @click="isVisibleDetail = false"
>
  Cerrar
</SecondaryButton>
    </div>
  </template>
</DialogModal>

        <!-- modal para crear -->
        <ConfirmationModal :show="medicalOrderDetailBeingCreated != null"
            @close="medicalOrderDetailBeingCreated = null">
            <template #title>
                Crear Detalle de Órden Médica
            </template>

            <template #content>
                ¿Estás seguro de que deseas crear este detalle de órden médica?
            </template>

            <template #footer>
                <SecondaryButton @click="medicalOrderDetailBeingCreated = null">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ms-3" @click="submit">
                    Crear
                </PrimaryButton>
            </template>
        </ConfirmationModal>

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
        <Modal :closeable="true" :show="showEditDoctor != null" @close="showEditDoctor = null">
            <div class="relative overflow-hidden sm:rounded-xl mt-4 lg:mx-10 bg-white dark:bg-gray-800 p-4">
                <form @submit.prevent="submitAdmission" class="max-w-3xl mx-auto">

                    <UserSelector roles="nurse" :selected-user-id="medicalOrder.doctor_id"
                        @update:user="formRecord.doctor_id = $event" />
                    <!-- Botones -->
                    <div class="flex justify-end mt-4 space-x-3">

                        <SecondaryButton @click="showEditDoctor = null">
                            Cerrar
                        </SecondaryButton>

                        <PrimaryButton type="submit" :class="{ 'opacity-25': formRecord.processing }"
                            :is-loading="formRecord.processing" :disabled="formRecord.processing">
                            Aceptar
                        </PrimaryButton>
                    </div>
                </form>
            </div>

        </Modal>

    </AppLayout>
</template>

<script>
import DialogModal from '@/Components/DialogModal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link, useForm
} from '@inertiajs/vue3';
import {
    ref
} from 'vue';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import FormatId from '@/Components/FormatId.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
import AdmissionSelector from '@/Components/AdmissionSelector.vue';
import Modal from '@/Components/Modal.vue';
import BackIcon from '@/Components/Icons/BackIcon.vue';
import ReportIcon from '@/Components/Icons/ReportIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import moment from 'moment/moment';
import UserSelector from '@/Components/UserSelector.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import RestoreIcon from '@/Components/Icons/RestoreIcon.vue';
import 'moment/locale/es';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CircleXIcon from '@/Components/Icons/CircleXIcon.vue';
import CirclePlusIcon from '@/Components/Icons/CirclePlusIcon.vue';
import TextInput from '@/Components/TextInput.vue'
export default {
    components: {
        AppLayout,
        Link,
        TextInput,
        PersonalizableButton,
        DialogModal,
        SignaturePad,
        ConfirmationModal,
        PrimaryButton,
        DangerButton,
        SecondaryButton,
        FormatId,
        EditIcon,
        AccessGate,
        AdmissionSelector,
        Modal,
        BackIcon,
        ReportIcon,
        BreadCrumb,
        TrashIcon,
        RestoreIcon,
        UserSelector,
        CircleXIcon,
        CirclePlusIcon

    },
    props: {
        medicalOrder: Object,
        errors: [Array, Object],
        admissions: Array,
        details: Array,
        regimes: Array,
        filters: Object,
        doctor: Object,
        admission_id: Number,

    },
    data() {
        return {
            recordBeingDeleted: ref(null),
            detailBeingDeleted: ref(null),
            selectedDetail: ref(null),
            modalform: useForm({
                selectedDetail: this.selectedDetail,

            }),
            medicalOrderDetailBeingCreated: ref(null),
            isVisibleDetail: ref(false),
            originalSuspendedState: ref(null),
            isVisibleEditSign: ref(null),
            signatureError: false,
            showEditAdmission: ref(null),
            showEditDoctor: ref(null),
            isVisibleAdm: false,
            formAdmission: {
                admission_id: this.medicalOrder.admission_id,
                active: this.medicalOrder.active
            },
            formRecord: useForm({
                admission_id: this.medicalOrder.admission_id,
                doctor_id: this.medicalOrder.doctor_id,
                impression_diagnosis: this.medicalOrder.impression_diagnosis,
                active: this.medicalOrder.active
            }),
            formDetail: useForm({
                medical_order_id: this.medicalOrder.id,
                order: null,
                regime: null,
                active: null,
            }),
            formSignature: useForm({
                doctor_sign: this.medicalOrder.doctor_sign,
                signature: true,
            }),
            showDeleted: this.filters.show_deleted,
        }
    },
    methods: {
        toggleShowDeleted() {
            this.showDeleted = !this.showDeleted;
            this.$inertia.get(route('medicalOrders.show', this.medicalOrder.id),
                {
                    showDeleted: this.showDeleted,
                    admission_id: this.admission_id !== 0 ? this.admission_id : null
                }, {
                preserveState: true,
                preserveScroll: true
            });
        },
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY HH:mm');
        },
        submitUpdateRecord() {
            this.showEditAdmission = null;
            this.showEditDoctor = null
            this.formRecord.put(route('medicalOrders.update', this.medicalOrder.id))
            this.isVisibleEditDiagnosis = false
        },
        toggleEditAdmission() {
            this.isVisibleAdm = !this.isVisibleAdm;
        },
        submitAdmission() {
            this.showEditDoctor = null;
            this.formRecord.put(route('medicalOrders.update', this.medicalOrder.id), {
                preserveScroll: true
            })
            this.isVisibleAdm = false;

        },
        async downloadRecordReport() {
            window.open(route('reports.medicalOrder', {
                id: this.medicalOrder.id
            }), '_blank');
        },
        submit() {
            this.medicalOrderDetailBeingCreated = null;
            this.formDetail.post(route('medicalOrderDetails.store'),

                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.formDetail.reset();

                    }

                });
        },
        submitCreateRecord() {

            if (this.medicalOrder.admission.medication_record) {

                this.$inertia.get(route('medicationRecordDetails.create', {
                    medicationRecordId: this.medicalOrder.admission.medication_record.id
                }))
            } else {

                this.$inertia.get(route('medicationRecords.create', {
                    admission: this.medicalOrder.admission.id
                }))
            }

        },

        submitUpdateDetail() {
            this.$inertia.put(route('medicalOrderDetails.update', this.selectedDetail.id), this.selectedDetail, {
                preserveScroll: true,
                preserveState: true
            }),
                this.isVisibleAdm = false
            this.isVisibleDetail = false
        },
        submitSignature() {
            if (!this.formSignature.doctor_sign) {
                this.signatureError = true;
                return;
            }
            this.signatureError = false;
            this.formSignature.put(route('medicalOrders.update', this.medicalOrder.id), {
                preserveScroll: true,
                preserveState: true
            });
            this.isVisibleEditSign = false
        },
        openEditModal(detail) {
            this.selectedDetail = {
                ...detail
            };
            this.originalSuspendedState = detail.suspended_at;
            this.isVisibleDetail = true;
        },
        deleteRecord() {
            this.recordBeingDeleted = null;
            this.$inertia.delete(route('medicalOrders.destroy', this.medicalOrder.id));
        },
        deleteDetail() {
            this.detailBeingDeleted = false
            this.isVisibleAdm = false
            this.isVisibleDetail = false
            this.$inertia.delete(route('medicalOrderDetails.destroy', this.selectedDetail.id), {
                preserveScroll: true,
                preserveState: true
            });
        },
        restoreDetail() {
            this.selectedDetail.active = true
            this.submitUpdateDetail()
        },
        restoreRecord() {
            this.formRecord.active = true
            this.submitAdmission()
        }
    }
}
</script>

<style scoped>
.alert {
    color: white;
    background: red;
    padding: 10px;
    margin: 10px 0;
    border-radius: 10;
}
</style>