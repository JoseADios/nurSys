<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
           <!-- Navigation -->
           <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <Link :href="route('medicationRecords.index')"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Volver</span>
                    </Link>
                    <button v-if="medicationRecord.active" @click="recordBeingDeleted = true"
                        class="flex items-center space-x-2 text-red-600 hover:text-red-800 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H3a1 1 0 100 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm2 4a1 1 0 011 1v7a1 1 0 11-2 0V7a1 1 0 011-1zm4 0a1 1 0 011 1v7a1 1 0 11-2 0V7a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Eliminar</span>
                    </button>
                    <button v-else @click="restoreRecord(medicationRecord)"
                        class="flex items-center space-x-2 text-green-600 hover:text-green-800 transition-colors">
                        <span class="font-medium">Restaurar</span>
                    </button>
                </div>
            <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-600 p-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-white">Ficha Medicamentos</h2>
                        <Link :href="route('medicationRecords.index')"
                            class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-4 rounded-full transition duration-300 ease-in-out">
                            Volver
                        </Link>
                    </div>
                </div>
   <!-- Mostrar errores -->
   <div class="mb-4 flex flex-col items-center">
            <div class="mb-4 text-red-500" v-for="error in errors" :key="error">
                {{ error }}
            </div>
        </div>
                <!-- Información Principal -->
                <div class="p-8 space-y-8">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Paciente -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Paciente</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ medicationRecord.admission.patient.first_name }}
                                {{ medicationRecord.admission.patient.first_surname }}
                                {{ medicationRecord.admission.patient.second_surname }}
                            </p>
                        </div>

                        <!-- Doctor -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Doctor</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                <!-- Verifica que la relación drug esté definida -->
                                    {{ medicationRecord.doctor.name }} {{ medicationRecord.doctor.last_name }}
                            </p>
                        </div>

                        <!-- Diagnostico -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Diagnostico</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ medicationRecord.diagnosis }}
                            </p>
                        </div>

                        <!-- bed -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Ubicación</h3>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                               Cama {{ medicationRecord.admission.bed.number }} Habitacion {{ medicationRecord.admission.bed.room }}
                            </p>
                        </div>

                        <!-- Dieta -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Dieta</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ medicationRecord.diet}}
                                </p>
                            </div>
                       <!-- Fecha Admission -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-md">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Fecha Ficha de Medicamentos</h3>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ medicationRecord.created_at}}
                                </p>
                            </div>
                            <div v-if="$page.props.errors.message" class="alert alert-danger">
      {{ $page.props.errors.message }}
    </div>
                        </div>
                    </div>
                    <!-- <div v-if="errors.length > 0" class="bg-red-50 border-l-4 border-red-500 p-4 mx-8 my-4">
                    <div class="text-red-700" v-for="error in errors" :key="error">
                        {{ error }}
                    </div>
                </div> -->
                <div class="p-8">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Agregar Nuevo Detalle</h3>
                    <div>
                        <button @click="OpenFormCreateRecord" id="add_detail"
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md
                                hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                transition-colors duration-300">
                            Agregar Detalle
                        </button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:mx-10 mt-6 hidden" id="formcreaterecord">
        <!-- Tarjeta para información del Medical Order -->
        <div class="relative overflow-hidden rounded-lg shadow-md bg-white dark:bg-gray-800 mb-5">

                        <div class="max-h-80 overflow-y-auto  shadow-md sm:rounded-lg mt-10 space-y-2 lg:mx-10">
                            <div v-if="order.length === 0" class="text-center text-gray-500 dark:text-gray-300 p-4">
    No hay órdenes disponibles.
  </div>

<div v-for="orders in order" :key="orders.id"  class="border rounded-lg">
    <p class="text-lg ml-2 font-semibold text-gray-900 dark:text-white">Order {{ orders.id }}</p>
    <div
    v-for="orderdetail in orders.medical_order_detail"
  :key="orderdetail.id"
  :value="orderdetail.description"
  @click="selectOrder(orderdetail.id)"
  :class="{
    'bg-blue-500 text-white': selectedOrderId === orderdetail.id && !orderdetail.suspended_at,
    'bg-white dark:bg-gray-800': selectedOrderId !== orderdetail.id && !orderdetail.suspended_at,



}"
  class="border mb-2 rounded-lg p-4 m-2 shadow-md cursor-pointer transition duration-200"
>


  <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
   Detalle de Orden # - {{ orderdetail.id }}
  </h3>
  <p class="text-lg font-semibold text-gray-900 dark:text-white">
    {{ orderdetail.order }}  <br />
    <!-- Mostrar régimen solo si la orden está seleccionada -->
    <span v-if="selectedOrderId === orderdetail.id" class="text-lg font-semibold text-gray-900 dark:text-white">
      Régimen: {{ orderdetail.regime }}
      {{orderdetail.medicalOrder}}
    </span>
  </p>
</div>

</div>

  </div>



                <!-- Espacio del detalle de Medical Orders -->

        </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg   lg:mx-10">
            <form @submit.prevent="submit" class="max-w-sm mx-auto">



           <!-- Contenedor para la Medicamento y el selector -->
<div class="flex justify-between items-center mt-6">
    <input type="hidden" v-model="form.selectedOrderId" />

    <!-- Selector de Medicamento -->
    <div class="w-2/3 mb-2">
        <label for="drug-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Medicamento
        </label>
        <select id="drug-select" required v-model="form.drug"
            class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option v-for="drugs in drug" :key="drugs.id" :value="drugs.name">
                {{ drugs.name }} - {{ drugs.description }}
            </option>
        </select>
    </div>

    <!-- Botón Crear Medicamento -->
    <button  @click="openCreateModal"
        class="ml-2 mt-3 flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">

        <span class="font-medium">Crear Medicamento</span>
    </button>
</div>


                  <!-- Contenedor para la Via y el selector -->

                    <!-- Selector -->
                    <div class="flex-1">
                        <label for="route-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Via
                        </label>
                        <select id="route-select" required v-model="form.route"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option v-for="routes in routeOptions" :key="routes.id" :value="routes.description">
                            {{ routes.name }} - {{ routes.description }}
                        </option>
                    </select>

                    </div>


                 <!-- Contenedor para la Dosis y el selector -->
                 <div class="flex items-center space-x-4 mt-6">
                    <!-- Dosis -->
                    <div class="flex-1">
                        <label for="dose" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Dosis
                        </label>
                        <input  id="dose" required type="text" v-model="form.dose"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Escribe la Dosis asignada..." />
                    </div>
                    <!-- Selector -->
                    <div class="flex-1">
                        <label for="dose-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
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
                <label for="fc" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Frecuencia
                </label>
                <input  id="fc" rows="4" required v-model="form.fc"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Escribe los estudios pendientes..."></input>

                <!-- Firma del Doctor -->
                <label for="interval_in_hours" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Intervalo en Horas
                </label>
                <input required id="interval_in_hours" type="text" v-model="form.interval_in_hours"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Intervalo en Horas..." />

                     <!-- Hora de Inicio -->
                <label for="start_time" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
                    Hora de Inicio
                </label>
                <input required id="start_time" type="time" v-model="form.start_time"
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
        <div id="errorMessage" v-if="errorMessage" class="bg-red-100 text-red-700 text-center p-2 w-full rounded-md mb-2">
    {{ errorMessage }}
</div>
                </div>

                <div class="p-8 space-y-4  bg-gray-50 dark:bg-gray-700">
                    <div class="flex items-center justify-between">
    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
        Detalles del Registro
    </h3>

    <AccessGate :permission="['medicationRecords.delete']">
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
    </AccessGate>
</div>


                    <div  v-for="detail in details" :key="detail.id" :class="[
         'rounded-lg p-4 shadow-md flex justify-between items-center transition-colors',
        !detail.suspended_at
             ?'bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-900'
             : 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 hover:bg-red-100 dark:hover:bg-red-900/30',

     ]"
     > <div class="flex-grow">
                            <div class="font-semibold text-gray-900 dark:text-white">
                               Medicamento: {{ detail.drug }}

                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                               Dosis: {{ detail.dose }}
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
                               Fecha: {{ detail.created_at }}
                            </div>
                            <div v-for="notifications in detail.medication_notification " :key=notifications.id class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                               <div v-if="notifications.applied === 1">
                                 Medicamento  Administrado: {{ notifications.administered_time }}
                                </div>
                                <div v-if="Firstnoapplied(notifications)" >
                                    Siguiente: {{ notifications.scheduled_time }}
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <div v-if="detail.active ">
                                 <!-- Editar -->
                            <Link  v-if="!hasApplied(detail)":href="route('medicationRecordDetails.edit', detail.id )"
                                class="flex items-center space-x-2 text-yellow-600 hover:text-yellow-800 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium">Editar</span>
                            </Link>
                              <!-- NOTIF -->
                              <Link :href="route('medicationNotification.show', detail.id )"
                                class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-medium">Notificaciones</span>
                            </Link>
                           </div>
                           <Link  @click="ToggleActivate(detail)"
                            :class="[detail.active ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700']"
                                class="flex items-center space-x-2 text-white-600 hover:text-white-800 transition-colors">
                            <svg xmlns="http:1//www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ detail.active ? 'Eliminar' : 'Restaurar' }}</span>
                            </Link>


                            <div v-if="medicationRecord.active"> <!-- Disable -->
                            <Link  @click="ToggleSuspend(detail)"
                            :class="[!detail.suspended_at ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700']"
                                class="flex items-center space-x-2 text-white-600 hover:text-white-800 transition-colors">
                            <svg xmlns="http:1//www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ !detail.suspended_at ? 'Suspender' : 'Habilitar' }}</span>
                            </Link>
                           </div>




                        </div>
                    </div>

                    <div v-if="details.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No hay detalles de registro disponibles
                    </div>

                </div>

                <!-- mostrar imagen firma -->
                <div v-show="!isVisibleEditSign" class="my-4 flex items-center flex-col justify-center">
                    <div>
                        <h2 class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Firma
                        </h2>
                        <img v-if="medicationRecord.doctor_sign" :src="`/storage/${medicationRecord.doctor_sign}`" alt="Firma">
                        <div v-else>
                            <div class="text-gray-500 dark:text-gray-400 my-16">
                                No hay firma disponible
                            </div>
                        </div>
                    </div>
                    <button @click="isVisibleEditSign = true"
                        class="mt-4 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                        Editar</button>
                </div>
                <!-- Campo de firma -->
                <div v-show="isVisibleEditSign" class="my-4">
                    <form @submit.prevent="submitSignature" class=" flex items-center flex-col justify-center">
                        <label for="doctor_sign"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Firma
                        </label>

                        <SignaturePad v-model="formSignature.doctor_sign" input-name="doctor_sign" />
                        <div v-if="signatureError" class="text-red-500 text-sm mt-2">La firma es obligatoria.</div>

                        <div class="my-4">
                            <button type="button"
                                class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                @click="isVisibleEditSign = false">Cancelar</button>
                            <button
                                class="mr-6 focus:outline-none text-white bg-blue-800 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900"
                                type="submit">Guardar firma</button>
                        </div>
                    </form>
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
            <form  >
                <div class="grid gap-4">
                    <!-- Campo Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre
                        </label>
                        <input type="text" id="name" v-model="modalform.name" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                            placeholder="Nombre del Medicamento" />
                    </div>
                    <!-- Campo Descripción -->
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
import { Link } from '@inertiajs/vue3';
import { ref } from "vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import AccessGate from '@/Components/Access/AccessGate.vue';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue';

export default{
    props: {
        medicationRecord: Object,
        details: Array,
        order: Object,
        drug: Array,
        routeOptions: Array,
        dose: Array,
        filters: Object,
    },
    components: {
        AppLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        DialogModal,
        AccessGate,
        SignaturePad
    },
    data(){
        return{
            form: {
                medication_record_id: this.medicationRecord.id,
                drug: '',
                dose: '',
                route: '',
                fc: '',
                interval_in_hours: '',
                start_time: '',
                dose_metric: '',
                selectedOrderId: null,
                showDeleted: this.filters.show_deleted,
            },
            recordBeingDeleted: ref(null),
            selectedOrderId: null,
            errorMessage: "",
            isVisible: false,
            isVisibleEditSign: ref(null),
            signatureError: false,
            modalform:{
                description: '',
                name: '',
            },
            formSignature: {
                nurse_sign: this.medicationRecord.nurse_sign,
                signature: true,
            },
        }
    },
    methods: {
        toggleShowDeleted() {
    this.form.showDeleted = !this.form.showDeleted;
    this.$inertia.get(route('medicationRecords.show', { medicationRecord: this.medicationRecord }), {
        showDeleted: this.form.showDeleted
    }, {
        preserveState: true,
        preserveScroll: true
    });
},
submitSignature() {
            if (!this.formSignature.doctor_sign) {
                this.signatureError = true;
                return;
            }
            this.signatureError = false;
            this.$inertia.put(route('medicationRecords.update', this.medicationRecord.id), this.formSignature, {
                preserveScroll: true
            });
            this.isVisibleEditSign = false
        },


        openCreateModal() {
            this.isVisible = true;
        },
        submitModal() {
            this.$inertia.post(route('Drugs.store'), this.modalform,{
                preserveScroll: true
            });
            this.isVisible = false;
            this.modalform = {
                name: '',
                description: '',
            };



        },
        restoreRecord(record){
            this.$inertia.put(
                route('medicationRecords.update', record.id), {
                    active: true
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
        this.errorMessage = "";
            this.$inertia.post(route('medicationRecordDetails.store'),
                this.form,
                {
                    onSuccess: () => {
                        this.form = {
                            medication_record_id: this.medicationRecord.id,
                            drug: '',
                            dose: '',
                            route: '',
                            fc: '',
                            interval_in_hours: '',
                            selectedOrderId: null,
                        };
                        this.selectedOrderId = null;
                        let form_div = document.getElementById('formcreaterecord');
                         form_div.classList.add("hidden");
                         let add_detail = document.getElementById('add_detail');
                         add_detail.classList.remove("hidden");
                    },
                    preserveState: true,


                } );

            },
            selectOrder(id) {
                this.selectedOrderId = id;
                this.form.selectedOrderId = id;
    },
            hasApplied(detail){
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
    let form_div = document.getElementById('formcreaterecord');
    let add_detail = document.getElementById('add_detail');
    add_detail.classList.add("hidden");
    form_div.classList.remove("hidden");

},
closeform(){
    let form_div = document.getElementById('formcreaterecord');
    form_div.classList.add("hidden");
    let add_detail = document.getElementById('add_detail');
    add_detail.classList.remove("hidden");
    let errorMessage = document.getElementById('errorMessage');
    errorMessage.classList.add('hidden');
},
deleteRecord() {
            this.recordBeingDeleted = null;
            this.$inertia.delete(route('medicationRecords.destroy', this.medicationRecord.id), {
                onSuccess: (response) => {
                        console.log('eliminado correctamente',response);
                        this.recordBeingDeleted = null;
                    },
            });
        },
ToggleActivate(detail){
    if (detail.active) {
    this.$inertia.delete(route('medicationRecordDetails.destroy', detail.id), {
         preserveState: true, preserveScroll: true,
                    onSuccess: (response) => {
                        console.log('eliminado correctamente',response);
                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                }
            );
    }else{
        this.$inertia.put(route('medicationRecordDetails.update', detail.id), {
                active: true, preserveState: true, preserveScroll: true,
                    onSuccess: (response) => {
                        console.log('update',response);
                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                }
            );
    }
},

ToggleSuspend(detail) {
        if (detail.suspended_at) {

            this.$inertia.put(route('medicationRecordDetails.update', detail.id), {
                suspended_at: true, preserveState: true, preserveScroll: true,
                    onSuccess: (response) => {
                        console.log('update',response);
                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                }
            );
        } else {

            this.$inertia.put(route('medicationRecordDetails.update', detail.id), {
                suspended_at: false, preserveState: true, preserveScroll: true,
                    onSuccess: (response) => {
                        console.log('update',response);
                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                }
            );
        }
    },

        }
}

</script>
<style scoped>

.max-h-80 {
  max-height: 600px;
}.alert {
  color: white;
  background: red;
  padding: 10px;
  margin: 10px 0;
  border-radius: 10;
}
</style>
