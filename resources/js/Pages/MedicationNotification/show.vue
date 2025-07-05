<template>
    <AppLayout title="Notificacion de Medicamentos">
        <!-- BreadCrumb -->
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
                        text: 'Fichas de Medicamentos',
                        route: route('medicationRecords.index', { admission_id }),
                    },
                    {
                        formattedId: { id: details.medication_record_id, prefix: 'FICH' },
                        route: route('medicationRecords.show', {
                            medicationRecord: details.medication_record_id,
                            admission_id,
                        }),
                    },
                    { formattedId: { id: details.id, prefix: 'DET' } },
                ]" />
            </h2>
        </template>

        <!-- Contenedor principal -->
        <div class="px-3 sm:px-4 py-6">
            <div
                class="max-w-5xl mx-auto bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700/60 rounded-2xl overflow-hidden">
                <!-- Barra superior -->
                <div class="flex justify-between items-center flex-wrap gap-2 p-4 bg-gray-100 dark:bg-gray-900">
                    <Link :href="route('medicationRecords.show', {
                        medicationRecord: details.medication_record_id,
                        admission_id,
                    })" class="flex items-center gap-2 text-blue-600 hover:text-blue-800 transition-colors">
                    <BackIcon class="size-5" />
                    Volver
                    </Link>

                    <PersonalizableButton v-if="details.active" size="medium" @click="downloadRecordReport"
                        color="emerald" class="flex items-center gap-1">
                        <ReportIcon class="size-5" />
                        <!-- Texto: oculto en móvil, visible ≥ sm -->
                        <span class="hidden sm:inline">Crear Reporte</span>
                    </PersonalizableButton>
                </div>


                <!-- Lista de notificaciones -->
                <div v-for="(notification, index) in notifications" :key="notification.id"
                    class="border-t last:border-b border-gray-200 dark:border-gray-700 p-4 sm:p-6 flex flex-col sm:flex-row gap-4 sm:gap-6">
                    <!-- Info principal -->
                    <div class="flex-1 space-y-1">
                        <p class="font-semibold text-gray-900 dark:text-white">
                            Notificación #{{ index + 1 }}
                        </p>

                        <p v-if="notification.nurse" class="text-[13px] sm:text-sm text-gray-600 dark:text-gray-300">
                            Enfermero/a: {{ notification.nurse.name }} {{ notification.nurse.last_name }}
                        </p>

                        <p class="text-[13px] sm:text-sm text-gray-600 dark:text-gray-300">
                            Medicamento: {{ details.drug }}
                        </p>

                        <p v-if="!notification.applied" class="text-[13px] sm:text-sm text-gray-600 dark:text-gray-300">
                            Programada: {{ formatDateFromNow(notification.scheduled_time) }}
                        </p>
                        <p v-else class="text-[13px] sm:text-sm text-gray-600 dark:text-gray-300">
                            Administrada: {{ formatDateFromNow(notification.administered_time) }}
                        </p>

                        <p class="text-[13px] sm:text-sm text-gray-600 dark:text-gray-300">
                            Vía: {{ details.route }}
                        </p>

                        <p v-if="details.nebulized" class="text-[13px] sm:text-sm text-gray-600 dark:text-gray-300">
                            Nebulizado:
                            <Checkbox :checked="true" disabled />
                        </p>

                        <!-- Estado aplicado/no aplicado -->
                        <div :class="[
                            'inline-flex items-center gap-1 rounded-full py-0.5 pl-2 pr-3 text-[11px] sm:text-sm font-medium',
                            notification.applied
                                ? 'bg-green-50 text-green-600 dark:bg-green-500/15'
                                : 'bg-red-50 text-red-600 dark:bg-red-500/15',
                        ]">
                            <span>{{ notification.applied ? 'APLICADO' : 'NO APLICADO' }}</span>
                        </div>
                    </div>

                    <!-- Firma -->
                    <div v-if="notification.applied"
                        class="flex flex-col items-center text-gray-500 dark:text-gray-400 gap-2">
                        <span class="text-[13px] sm:text-sm font-medium">Firma</span>
                        <img :src="`/storage/${notification.nurse_sign}`" alt="Firma" width="220"
                            class="border rounded-md" />
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-col justify-end gap-2 sm:ml-auto">
                        <AccessGate v-if="notification.applied && canUpdateNotification">
                            <PersonalizableButton color="red" size="medium" @click="revert(notification)"
                                class="w-full sm:w-auto">
                                <BackIcon class="size-5 mr-1" /> Revertir
                            </PersonalizableButton>
                        </AccessGate>

                        <AccessGate v-else-if="!notification.applied" :permission="['medicationNotification.update']">
                            <PersonalizableButton color="green" size="medium" @click="openModal(notification)"
                                class="w-full sm:w-auto">
                                <CheckCircleIcon class="size-5 mr-1" /> Aplicar
                            </PersonalizableButton>
                        </AccessGate>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal firma -->
        <ConfirmationModal :show="notificationSignatureUpdate != null" @close="notificationSignatureUpdate = null">
            <template #title>
                {{ notificationBeingUpdated?.applied ? 'Revertir' : 'Administrar' }}
                <SignaturePad v-model="formSignature.nurse_sign" input-name="nurse_sign" />
                <p v-if="signatureError" class="text-red-500 text-sm mt-2">
                    La firma es obligatoria.
                </p>
            </template>

            <template #content>
                <p v-if="!notificationBeingUpdated?.applied" class="text-sm">
                    ¿Estás seguro de que deseas aplicar este medicamento?
                </p>
            </template>

            <template #footer>
                <SecondaryButton @click="notificationSignatureUpdate = null">Cancelar</SecondaryButton>

                <PrimaryButton v-if="!notificationBeingUpdated?.applied" class="ms-3"
                    :is-loading="formSignature.processing" :disabled="formSignature.processing"
                    @click="markAsAdministered(notificationBeingUpdated)">
                    Administrar
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>


<script>
import {
    ref
} from 'vue';
import moment from "moment/moment";
import 'moment/locale/es';
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
import CheckCircleIcon from '@/Components/Icons/CheckCircleIcon.vue';
import PersonalizableButton from '@/Components/PersonalizableButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
export default {
    components: {
        AppLayout,
        BreadCrumb,
        Link,
        BackIcon,
        PersonalizableButton,
        ReportIcon,
        Checkbox,
        AccessGate,
        CheckCircleIcon,
        ConfirmationModal,
        SignaturePad,
        SecondaryButton,
        PrimaryButton,
        FormatId,
        DangerButton,
    },
    props: {
        details: Object,
        notifications: Object,
        canUpdateNotification: Boolean,
        admission_id: Number,
    },
    data() {
        return {
            notificationSignatureUpdate: ref(null),
            notificationBeingUpdated: ref(null),
            formSignature: useForm({
                nurse_sign: this.notifications.nurse_sign,
            }),
            signatureError: false,
        }
    },
    methods: {
        async downloadRecordReport() {
            window.open(route('reports.medicationRecord', {
                id: this.details.medication_record_id, preserveScroll: true
            }), '_blank');
        },
        formatDateFromNow(date) {
            return moment(date).fromNow();
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
        openModal(notification) {
            this.notificationSignatureUpdate = true;
            this.notificationBeingUpdated = notification;
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
        formatDate(date) {
            return moment(date).format('DD MMMM YYYY HH:mm');
        },
        Firstnoapplied(notification) {
            const firstNotApplied = this.notifications.find((n) => n.applied === 0);
            return firstNotApplied && firstNotApplied.id === notification.id;
        },
        lastApplied(notification) {
            const lastApplied = this.notifications.reduceRight((acc, n) => {
                return acc || (n.applied === 1 ? n : null);
            }, null);
            return lastApplied && lastApplied.id === notification.id;
        },
    }
}
</script>

