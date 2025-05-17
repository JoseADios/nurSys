<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
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

            <div
                class="max-w-5xl mx-auto bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/60 rounded-2xl overflow-hidden">

                <div class="p-4 bg-gray-100 dark:bg-gray-900 flex justify-between items-center">
                    <Link :href="route('medicationRecords.show', details.medication_record_id)"
                        class="flex items-center space-x-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <BackIcon class="size-5" />Volver
                    </Link>
                    <div class="flex items-center">
                        <button v-if="details.active" @click="downloadRecordReport"
                            class=" mr-4 inline-flex   px-4 py-2 bg-emerald-500 text-white text-sm rounded-lg hover:to-emerald-600 transition-all duration-200">
                            <ReportIcon class="size-5 mr-1" /> Crear Reporte
                        </button>

                    </div>
                </div>


                <div v-for="notification, index in notifications" :key="notification.id"
                    class=' dark:bg-gray-800 border border-gray-300 dark:border-gray-700/60  p-8   flex justify-between items-center transition-colors'>
                    <div class="flex-grow">
                        <div class="flex items-center space-x-20">
                            <div class="font-semibold text-gray-900 dark:text-white mr-20">
                                <div class="mb-2">Notificación - #{{ index + 1 }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fecha: {{
                                    formatDate(notification.created_at) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1"> Fecha programada:
                                    {{ formatDateFromNow(
                                        notification.scheduled_time) }}</div>

                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Medicamento: {{ details.drug
                                    }}</div>
                                <div v-if="notification.administered_time"
                                    class="text-sm text-gray-600 dark:text-gray-300 mt-1"> Medicamento Administrado:
                                    {{ formatDate(
                                        notification.administered_time) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Via: {{ details.route }}
                                </div>


                            </div>

                            <div v-if="notification.applied"
                                class="text-sm  flex flex-col items-center text-gray-500 dark:text-gray-400 p-4">
                                <div class="mb-2 font-medium">Firma de Enfermera</div>
                                <img :src="`/storage/${notification.nurse_sign}`" width="250" alt="Firma">
                            </div>

                        </div>





                        <div v-if="notification.applied == 1">
                            <div id="applied" class="text-sm text-green-500 dark:text-green-400 mt-1">
                                APLICADO
                            </div>
                            <div v-if="lastApplied(notification)" class="flex justify-end mt-2">
                                <AccessGate v-if="canUpdateNotification">
                                <button
                                    class="font-semibold text-red-500 dark:text-red-400 border border-red-300 px-4 py-1 rounded hover:bg-red-100 dark:hover:bg-gray-700 transition"
                                    @click="revert(notification)">

                                    <BackIcon class="h-8 w-8 "/>
                                </button>
                                </AccessGate>
                            </div>

                        </div>

                        <div v-else>
                            <div id="no-applied" class="text-sm text-red-500 dark:text-red-400 mt-1">
                                NO APLICADO
                            </div>
                            <div v-if="Firstnoapplied(notification)" class="flex justify-end mt-1">
                                <button
                                    class="font-semibold text-green-500 dark:text-green-400 border border-green-300 px-4 py-1 rounded hover:bg-green-100 dark:hover:bg-gray-700 transition"
                                    @click="openModal(notification)">
                                     <CheckCircleIcon class="h-8 w-8 " />
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
                <div v-if="notificationBeingUpdated.applied != true">¿Estás seguro de que deseas aplicar este
                    medicamento?
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="notificationSignatureUpdate = null">
                    Cancelar
                </SecondaryButton>

                <div v-if="notificationBeingUpdated.applied != true">
                    <PrimaryButton  :class="{ 'opacity-25': formSignature.processing }" :disabled="formSignature.processing" class="ms-3" @click="markAsAdministered(notificationBeingUpdated)">
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
    Link, useForm
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
import AccessGate from '@/Components/Access/AccessGate.vue';
import {
    ref
} from 'vue';
import moment from "moment/moment";
import 'moment/locale/es';
import CheckCircleIcon from '@/Components/Icons/CheckCircleIcon.vue';

export default {
    props: {
        details: Object,
        notifications: Object,
        canUpdateNotification: Boolean,
    },
    components: {
        AppLayout,
        AccessGate,
        Link,
        FormatId,
        ConfirmationModal,
        DangerButton,
        SecondaryButton,
        PrimaryButton,
        SignaturePad,
        BackIcon,
        ReportIcon,
        CheckCircleIcon,

        BreadCrumb
    },
    data() {
        return {

            notificationSignatureUpdate: ref(null),
            notificationBeingUpdated: ref(null),
            signatureError: false,

            formSignature:  useForm({
                nurse_sign: this.notifications.nurse_sign,

            }),
        }
    },
    methods: {
        formatDateFromNow(date) {

            return moment(date).fromNow();
        },
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY HH:mm');
        },
        markAsAdministered() {

            if (!this.formSignature.nurse_sign) {
                this.signatureError = true;
                return false
            }
            this.signatureError = false;
            // console.log('update', this.formSignature.nurse_sign);
            this.formSignature.put(route('medicationNotification.update', this.notificationBeingUpdated.id), {
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
                    revert: true, preserveScroll: true
                })
            }
        },
        async downloadRecordReport() {
            window.open(route('reports.medicationNotification', {
                id: this.notification.id, preserveScroll: true
            }), '_blank');
        },

    }

}
</script>
