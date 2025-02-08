<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
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
                        <button @click="OpenFormCreateRecord"
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
                            <div
  v-for="orders in order"
  :key="orders.id"
  :value="orders.description"
  @click="selectOrder(orders.id)"
  :class="{
    'bg-blue-500 text-white': selectedOrderId === orders.id && !orders.suspended_at,
    'bg-white dark:bg-gray-800': selectedOrderId !== orders.id && !orders.suspended_at,



}"
  class="border mb-2 rounded-lg p-4 m-2 shadow-md cursor-pointer transition duration-200"
>

  <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">
    {{ orders.id }} - Orden
  </h3>
  <p class="text-lg font-semibold text-gray-900 dark:text-white">
    {{ orders.order }} <br />
    <!-- Mostrar régimen solo si la orden está seleccionada -->
    <span v-if="selectedOrderId === orders.id" class="text-lg font-semibold text-gray-900 dark:text-white">
      Régimen: {{ orders.regime }}
    </span>
  </p>
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
            <option v-for="drugs in drug" :key="drugs.id" :value="drugs.description">
                {{ drugs.name }} - {{ drugs.description }}
            </option>
        </select>
    </div>

    <!-- Botón Crear Medicamento -->
    <button @click="openCreateModal"
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
                </div>

                <div class="p-8 space-y-4  bg-gray-50 dark:bg-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Detalles del Registro</h3>

                    <div v-for="detail in details" :key="detail.id" :class="[
         'rounded-lg p-4 shadow-md flex justify-between items-center transition-colors',
         detail.active
             ?'bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-900'
             : 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 hover:bg-red-100 dark:hover:bg-red-900/30'
     ]"> <div class="flex-grow">
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
                            <div v-if="detail.active">
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

                            <!-- Disable -->
                            <Link  @click="ToggleActivate(detail)"
                            :class="[detail.active ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700']"
                                class="flex items-center space-x-2 text-white-600 hover:text-white-800 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ detail.active ? 'Suspender' : 'Habilitar' }}</span>
                            </Link>

                        </div>
                    </div>

                    <div v-if="details.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                        No hay detalles de registro disponibles
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';


export default{
    props: {
        medicationRecord: Object,
        details: Array,
        order: Object,
        drug: Array,
        routeOptions: Array,
        dose: Array
    },
    components: {
        AppLayout,
        Link,
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

            },
            selectedOrderId: null
        }
    },
    methods: {
        submit() {
            if (!this.form.selectedOrderId) {
            alert("Debe seleccionar una orden antes de guardar.")
            return;
        }
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
                    }

                });

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
    form_div.classList.remove("hidden");
},
closeform(){
    let form_div = document.getElementById('formcreaterecord');
    form_div.classList.add("hidden");
},


ToggleActivate(detail) {
        if (!detail.active) {

            this.$inertia.put(route('medicationRecordDetails.update', detail.id), {
                active: !detail.active,
                    onSuccess: () => {

                    },
                    onError: (errors) => {
                        console.error('Error al habilitar:', errors);
                    },
                }
            );
        } else {

            this.$inertia.delete(
                route('medicationRecordDetails.destroy', detail.id),
                {
                    onSuccess: () => {

                    },
                    onError: (errors) => {
                        console.error('Error al deshabilitar:', errors);
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
}
</style>
