<template>
<AppLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-white leading-tight text-center">
            <BreadCrumb :items="[
                    ...(details.medication_record_id ? [{
                        formattedId: { id: details.medication_record_id, prefix: 'ING' },
                        route: route('admissions.show', details.medication_record_id)
                    }] : []),
                    {
                        text: 'Fichas de Medicamentos',
                        route: details.medication_record_id
                            ? route('medicationRecords.show', { id: details.medication_record_id })
                            : route('medicationRecords.show')
                    },

                    {
                        formattedId: { id: details.medication_record_id, prefix: 'FICH' },
                        route: details.medication_record_id
                            ? route('medicationRecords.show', { id: details.medication_record_id })
                            : route('medicationRecords.show')
                    },
                    {
                        formattedId: { id: details.id, prefix: 'DET' }
                    }
                ]" />
        </h2>


    </template>

    <div class="container mx-auto px-4 py-8">



        <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/60 rounded-2xl overflow-hidden">

            <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                <Link :href="route('medicationRecords.show',details.medication_record_id)" class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                <BackIcon class="size-5" />Volver
                </Link>
                <div class="flex items-center">
                    <button v-if="details.active" @click="downloadRecordReport" class=" mr-4 inline-flex   px-4 py-2 bg-emerald-500 text-white text-sm rounded-lg hover:to-emerald-600 transition-all duration-200">
                        <ReportIcon class="size-5 mr-1" /> Crear Reporte
                    </button>

                </div>
            </div>

            <div v-for="notification in notifications" :key="notification.id" class="p-8 space-y-4 bg-gray-50 dark:bg-gray-700">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Notificación:</h3>

                <!-- Iterando sobre las notificaciones -->
                <div class="flex-grow">
                    <div v-if="notification.nurse" class="font-semibold text-gray-900 dark:text-white">
                        Enfermera: {{ notification.nurse.name }} {{ notification.nurse.last_name }}
                    </div>

                    <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                        Hora Programada : {{ notification.scheduled_time }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Hora Aplicado: {{ notification.administered_time }}
                    </div>
                    <div v-if="notification.applied" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Firma de la enfermera: <img :src="`/storage/${notification.nurse_sign}`" width="200" alt="Firma">
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
                            <button class="text-white" @click="openModal(notification)">
                                administrar
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- modal para dar de alta -->
    <ConfirmationModal :show="notificationSignatureUpdate != null" @close="notificationSignatureUpdate = null">
        <template #title>
            <div v-if="notifications.applied != true">Administrar</div>
            <div v-if="notifications.applied == true">Revertir</div>
            <div>

                <SignaturePad v-model="formSignature.nurse_sign" input-name="nurse_sign" />
                <div v-if="signatureError" class="text-red-500 text-sm mt-2">La firma es obligatoria.</div>

            </div>

        </template>

        <template #content>
            <div v-if="notificationBeingUpdated.applied != true">¿Estás seguro de que deseas aplicar este medicamento?
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="notificationSignatureUpdate = null">
                Cancelar
            </SecondaryButton>

            <div v-if="notificationBeingUpdated.applied != true">
                <PrimaryButton class="ms-3" @click="markAsAdministered(notificationBeingUpdated)">
                    Administrar
                </PrimaryButton>
            </div>

        </template>
    </ConfirmationModal>
</AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    Link
} from '@inertiajs/vue3';
import FormatId from '@/Components/FormatId.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SignaturePad from '@/Components/SignaturePad/SignaturePad.vue';
import BackIcon from '@/Components/Icons/BackIcon.vue';
import ReportIcon from '@/Components/Icons/ReportIcon.vue';
import BreadCrumb from '@/Components/BreadCrumb.vue';
import {
    ref
} from 'vue';
export default {
    props: {
        details: Object,
        notifications: Object,
    },
    components: {
        AppLayout,
        Link,
        FormatId,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        PrimaryButton,
        SignaturePad,
        BackIcon,
        ReportIcon,
        BreadCrumb
    },
    data() {
        return {

            notificationSignatureUpdate: ref(null),
            notificationBeingUpdated: ref(null),
            signatureError: false,

            formSignature: {
                nurse_sign: this.notifications.nurse_sign,

            },
        }
    },
    methods: {
        markAsAdministered() {

            if (!this.formSignature.nurse_sign) {
                this.signatureError = true;
                return false
            }
            this.signatureError = false;
            // console.log('update', this.formSignature.nurse_sign);
            this.$inertia.put(route('medicationNotification.update', this.notificationBeingUpdated.id), this.formSignature, {
                preserveScroll: true,
                onSuccess: (response) => {

                    this.formSignature.nurse_sign = null;
                    this.notificationSignatureUpdate = null;
                    const notification = this.notifications;
                    if (notification) {
                        const newAppliedValue = notification.applied === 1 ? 0 : 1;
                        notification.applied = newAppliedValue;
                        this.$inertia.put(route('medicationNotification.update', this.notificationBeingUpdated.id), {
                            markAsAdministered: true
                        })
                    }
                },
                onError: (errors) => {
                    console.error('Error al habilitar:', errors);
                },
            });
        },
        Firstnoapplied(notification) {
            const firstNotApplied = this.notifications.find((n) => n.applied === 0);
            return firstNotApplied && firstNotApplied.id === notification.id;

        },
        openModal(notification) {

            this.notificationSignatureUpdate = true;
            this.notificationBeingUpdated = notification;
        },

        lastApplied(notification) {
            const lastApplied = this.notifications.reduceRight((acc, n) => {
                return acc || (n.applied === 1 ? n : null);
            }, null);
            return lastApplied && lastApplied.id === notification.id;
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
        },
        async downloadRecordReport() {
            window.open(route('reports.medicationNotification', {
                id: this.notification.id
            }), '_blank');
        },

    }

}
</script>
