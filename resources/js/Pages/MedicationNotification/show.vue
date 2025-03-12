<template>
    <AppLayout>
        <template #header>

            <div class="container mx-auto px-4 py-8">

                <div class="ml-12 my-2 inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400">


<Link :href="route('medicationRecords.index')"
    class="inline-flex ml-8  items-center hover:text-blue-600 dark:hover:text-white">
Ficha de Medicamentos
</Link>
<svg class="rtl:rotate-180 w-3 ml-2 h-3 text-gray-400 mx-1" aria-hidden="true"                 fill="none" viewBox="0 0 6 10">
    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="m1 9 4-4-4-4" />
</svg>
<Link :href="route('medicationRecords.show',details.medication_record_id)">
<div class="ml-2 inline-flex items-center dark:hover:text-white">
    <FormatId :id="details.medication_record_id" prefix="FIC"></FormatId>
</div>
</Link>
<svg class="rtl:rotate-180 w-3 ml-2 h-3 text-gray-400 mx-1" aria-hidden="true"                 fill="none" viewBox="0 0 6 10">
    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="m1 9 4-4-4-4" />
</svg>
<div class="ml-2 inline-flex items-center">
    <FormatId :id="details.id" prefix="NOT"></FormatId>
</div>
</div>
            <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-xl overflow-hidden">



            <div class="p-8 space-y-4 bg-gray-50 dark:bg-gray-700">



                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Detalles del Registro</h3>

                <div class="flex-grow">
                    <div class="font-semibold text-gray-900 dark:text-white">
                        Medicamento: {{ details.drug }}
                    </div>
                    <!-- <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                        Dosis: {{ details.dose }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Vía: {{ details.route }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Frecuencia: {{ details.fc }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Intervalo en Horas: {{ details.interval_in_hours }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Fecha: {{ details.created_at }}
                    </div> -->
                </div>
            </div>

            <div   v-for="notification in notifications" :key="notification.id"  class="p-8 space-y-4 bg-gray-50 dark:bg-gray-700">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Notificación:</h3>

                <!-- Iterando sobre las notificaciones -->
                <div class="flex-grow">
                    <div class="font-semibold text-gray-900 dark:text-white">
                        Enfermera: {{ notification.nurse_id }}
                    </div>

                    <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                        Hora Programada : {{ notification.scheduled_time }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Hora Aplicado: {{ notification.administered_time }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Firma de la enfermera: {{ notification.nurse_sign }}
                    </div>

                    <div v-if="notification.applied == 1">
                        <div id="applied" class="text-sm text-green-500 dark:text-green-400 mt-1">
                        APLICADO
                    </div>
                    <div v-if="lastApplied(notification)">
                    <button class="text-white" @click="revert(notification.id)">
                        Revertir
                    </button>
                </div>


                    </div>
                    <div v-else>
                        <div id="no-applied" class="text-sm text-red-500 dark:text-red-400 mt-1">
                       NO APLICADO


                    </div>
                    <div v-if="Firstnoapplied(notification)">
                        <button class="text-white" @click="markAsAdministered(notification.id)">
                        administrar
                    </button>
                    </div>



                    </div>


                </div>
            </div>
        </div>
    </div>
        </template>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import FormatId from '@/Components/FormatId.vue';

export default {
    props: {
        details: Object,
        notifications: Object,
    },
    components: {
        AppLayout,
        Link,
        FormatId
    },

    methods: {
        Firstnoapplied(notification){
            const firstNotApplied = this.notifications.find((n) => n.applied === 0);
      return firstNotApplied && firstNotApplied.id === notification.id;

        },
        lastApplied(notification) {
    const lastApplied = this.notifications.reduceRight((acc, n) => {
        return acc || (n.applied === 1 ? n : null);
    }, null);
    return lastApplied && lastApplied.id === notification.id;
},
    markAsAdministered(id) {

        const notification = this.notifications;
        if (notification) {
           const newAppliedValue = notification.applied === 1 ? 0 : 1;
            notification.applied = newAppliedValue;
            this.$inertia.put(route('medicationNotification.update', id), {
                markAsAdministered: true
            })
        }
    },
        revert(id) {

    const notification = this.notifications;


    if (notification) {

        const newAppliedValue = notification.applied === 0 ? 0 : 1;


        notification.applied = newAppliedValue;


        this.$inertia.put(route('medicationNotification.update', id), {
            revert: true
        })
    }
    }

}


}


</script>
