<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow pt-4 md:p-6">
        <!-- Nombre del modelo -->
        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ modelLabel }} ({{ activityItem.subject_id
                }})</h2>
        </div>

        <!-- Encabezado con información del registro -->
        <div v-if="activityItem" class="mb-6">
            <!-- Tipo de acción destacada con iconos y colores -->
            <div class="flex items-center justify-between mb-4">
                <div :class="getActionBadgeClass()" class="flex items-center px-3 py-1 rounded-md mr-2">
                    <component :is="getActionIcon()" class="w-5 h-5 mr-2" />
                    <span class="font-semibold text-sm">{{ description }}</span>
                </div>
                <PrimaryLink v-if="activityItem.log_name !== 'default'" class="!w-fit"
                    :href="route(activityItem.log_name.split(',')[0], activityItem.log_name.split(',')[1])">
                    Ver
                </PrimaryLink>
            </div>

            <!-- Información del usuario que realizó el cambio y cuándo -->
            <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center">
                    <span class="font-semibold mr-2">Realizado por:</span>
                    <span class="text-gray-800 dark:text-gray-300">{{ getUserName(activityItem.causer) }}</span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold mr-2">Fecha:</span>
                    <span class="text-gray-800 dark:text-gray-300">{{ formatDate(activityItem.created_at) }}</span>
                </div>
            </div>
        </div>

        <!-- Tabla de cambios (solo para actualizaciones) -->
        <div v-if="isUpdateAction && hasChanges" class="overflow-x-auto">
            <table class="w-full border-collapse rounded-lg overflow-hidden">
                <thead class="rounded-t-md bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Campo</th>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Valor anterior</th>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Nuevo valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(change, field) in formattedChanges" :key="field" class="bg-blue-50 dark:bg-blue-900/20">
                        <td
                            class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200 border-b border-white dark:border-gray-700">
                            {{ formatFieldName(field) }}</td>
                        <td
                            class="py-3 px-4 text-gray-500 dark:text-gray-400 bg-red-50 dark:bg-red-900/10 border-b border-white dark:border-gray-700">
                            <span v-if="change.old !== null && change.old !== undefined">{{ formatValue(change.old,
                                field) }}</span>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">No especificado</span>
                        </td>
                        <td
                            class="py-3 px-4 font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/10 border-b border-white dark:border-gray-700">
                            <span v-if="change.new !== null && change.new !== undefined">{{ formatValue(change.new,
                                field) }}</span>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">No especificado</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datos creados (para creaciones) -->
        <div v-else-if="isCreateAction" class="overflow-x-auto">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 dark:bg-green-900/20 rounded-full w-2 h-2 mr-2"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Datos del registro creado:</span>
            </div>
            <table class="w-full border-collapse rounded-lg overflow-hidden">
                <thead class="rounded-t-md bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Campo</th>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(value, field) in getCreatedAttributes" :key="field"
                        class="bg-green-50 dark:bg-green-900/10">
                        <td
                            class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200 border-b border-white dark:border-gray-700">
                            {{ formatFieldName(field) }}</td>
                        <td
                            class="py-3 px-4 font-medium text-green-600 dark:text-green-400 border-b border-white dark:border-gray-700">
                            <span v-if="value !== null && value !== undefined">{{ formatValue(value, field) }}</span>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">No especificado</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datos eliminados (para eliminaciones) -->
        <div v-else-if="isDeleteAction" class="overflow-x-auto">
            <div class="flex items-center mb-4">
                <div class="bg-red-100 dark:bg-red-900/20 rounded-full w-2 h-2 mr-2"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Datos del registro eliminado:</span>
            </div>
            <table class="w-full border-collapse rounded-lg overflow-hidden">
                <thead class="rounded-t-md bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Campo</th>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Valor eliminado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(value, field) in getDeletedAttributes" :key="field"
                        class="bg-red-50 dark:bg-red-900/10">
                        <td
                            class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200 border-b border-white dark:border-gray-700">
                            {{ formatFieldName(field) }}</td>
                        <td class="py-3 px-4 text-red-500 dark:text-red-400 border-b border-white dark:border-gray-700">
                            <span v-if="value !== null && value !== undefined">{{ formatValue(value, field) }}</span>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">No especificado</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datos restaurados (para restauraciones) -->
        <div v-else-if="isRestoreAction" class="overflow-x-auto">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 dark:bg-blue-900/20 rounded-full w-2 h-2 mr-2"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Datos del registro restaurado:</span>
            </div>
            <table class="w-full border-collapse rounded-lg overflow-hidden">
                <thead class="rounded-t-md bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Campo</th>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Valor restaurado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(value, field) in getRestoreAttributes" :key="field"
                        class="bg-blue-50 dark:bg-blue-900/10">
                        <td
                            class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200 border-b border-white dark:border-gray-700">
                            {{ formatFieldName(field) }}</td>
                        <td
                            class="py-3 px-4 text-blue-500 dark:text-blue-400 border-b border-white dark:border-gray-700">
                            <span v-if="value !== null && value !== undefined">{{ formatValue(value, field) }}</span>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">No especificado</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datos desactivados -->
        <div v-else-if="isDeactivateAction" class="overflow-x-auto">
            <div class="flex items-center mb-4">
                <div class="bg-red-100 dark:bg-red-900/20 rounded-full w-2 h-2 mr-2"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Se ha desactivado el registro:</span>
            </div>
            <table class="w-full border-collapse rounded-lg overflow-hidden">
                <thead class="rounded-t-md bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Campo</th>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(change, field) in formattedChanges" :key="field" class="bg-red-50 dark:bg-red-900/10">
                        <td
                            class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200 border-b border-white dark:border-gray-700">
                            {{ formatFieldName(field) }}</td>
                        <td class="py-3 px-4 text-red-500 dark:text-red-400 border-b border-white dark:border-gray-700">
                            <span v-if="change.new !== null && change.new !== undefined">{{ formatValue(change.new,
                                field) }}</span>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">No especificado</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datos activados -->
        <div v-else-if="isActivateAction" class="overflow-x-auto">
            <div class="flex items-center mb-4">
                <div class="bg-teal-100 dark:bg-teal-900/20 rounded-full w-2 h-2 mr-2"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Se ha activado el registro:</span>
            </div>
            <table class="w-full border-collapse rounded-lg overflow-hidden">
                <thead class="rounded-t-md bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Campo</th>
                        <th
                            class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-white dark:border-gray-700">
                            Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(change, field) in formattedChanges" :key="field" class="bg-teal-50 dark:bg-teal-900/10">
                        <td
                            class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200 border-b border-white dark:border-gray-700">
                            {{ formatFieldName(field) }}</td>
                        <td
                            class="py-3 px-4 text-teal-500 dark:text-teal-400 border-b border-white dark:border-gray-700">
                            <span v-if="change.new !== null && change.new !== undefined">{{ formatValue(change.new,
                                field) }}</span>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">No especificado</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mensaje para cuando no hay cambios en actualización -->
        <div v-else-if="isUpdateAction && !hasChanges"
            class="flex items-center justify-center py-8 text-gray-500 dark:text-gray-400">
            <p>No se detectaron cambios en los datos.</p>
        </div>

        <!-- Mensaje para otros casos sin datos -->
        <div v-else class="flex items-center justify-center py-8 text-gray-500 dark:text-gray-400">
            <p>No hay información adicional disponible para esta acción.</p>
        </div>
    </div>
</template>

<script>
import CirclePlusIcon from './Icons/CirclePlusIcon.vue';
import EditIcon from './Icons/EditIcon.vue';
import TrashIcon from './Icons/TrashIcon.vue';
import RestoreIcon from './Icons/RestoreIcon.vue';
import ToggleLeftIcon from './Icons/ToggleLeftIcon.vue';
import ToggleRigthIcon from './Icons/ToggleRigthIcon.vue';
import PrimaryLink from './PrimaryLink.vue';

export default {
    name: 'ActivityLogDiff',
    components: {
        CirclePlusIcon,
        EditIcon,
        TrashIcon,
        RestoreIcon,
        ToggleLeftIcon,
        ToggleRigthIcon,
        PrimaryLink
    },
    props: {
        modelLabel: String,
        // El registro de actividad completo
        activityItem: {
            type: Object,
            required: true
        },
        // Diccionario opcional para mapear nombres de campos técnicos a nombres amigables
        fieldMappings: {
            type: Object,
            default: () => ({})
        },
        // Diccionario opcional para mapear valores (IDs a nombres, códigos a descripciones, etc.)
        valueMappings: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            // Incluimos Font Awesome en caso de que no esté ya incluido en el proyecto
            fontAwesomeLoaded: false
        }
    },
    computed: {
        // Detectar el tipo de acción
        isCreateAction() {
            return this.activityItem.description === 'created';
        },
        isUpdateAction() {
            return this.activityItem.description === 'updated';
        },
        isDeleteAction() {
            return this.activityItem.description === 'deleted';
        },
        isRestoreAction() {
            return this.activityItem.description === 'restored';
        },
        isDeactivateAction() {
            return this.activityItem.description === 'deactivated';
        },
        isActivateAction() {
            return this.activityItem.description === 'activated';
        },

        // Verifica si hay cambios para mostrar (solo para actualizaciones)
        hasChanges() {
            return this.activityItem.properties &&
                this.activityItem.properties.attributes &&
                this.activityItem.properties.old &&
                Object.keys(this.formattedChanges).length > 0;
        },

        // Obtener atributos para visualizar en caso de creación
        getCreatedAttributes() {
            if (!this.activityItem.properties || !this.activityItem.properties.attributes) {
                return {};
            }
            return this.activityItem.properties.attributes;
        },

        // Obtener atributos para visualizar en caso de eliminación
        getDeletedAttributes() {
            if (!this.activityItem.properties || !this.activityItem.properties.attributes) {
                return {};
            }
            return this.activityItem.properties.attributes;
        },

        // Obtener atributos para visualizar en caso de restauración
        getRestoreAttributes() {
            if (!this.activityItem.properties || !this.activityItem.properties.attributes) {
                return {};
            }
            return this.activityItem.properties.attributes;
        },

        // Procesa los cambios para mostrar solo los campos que cambiaron (para actualizaciones)
        formattedChanges() {
            if (!this.activityItem.properties || !this.activityItem.properties.attributes || !this.activityItem.properties.old) {
                return {};
            }

            const newValues = this.activityItem.properties.attributes;
            const oldValues = this.activityItem.properties.old;
            const changes = {};

            // Comparar valores y encontrar diferencias
            Object.keys(newValues).forEach(field => {
                // Si el campo no existía antes o el valor cambió
                if (!(field in oldValues) || oldValues[field] !== newValues[field]) {
                    changes[field] = {
                        old: field in oldValues ? oldValues[field] : null,
                        new: newValues[field]
                    };
                }
            });

            // También verificar si algún campo fue eliminado
            Object.keys(oldValues).forEach(field => {
                if (!(field in newValues) && !(field in changes)) {
                    changes[field] = {
                        old: oldValues[field],
                        new: null
                    };
                }
            });

            return changes;
        },

        // Devuelve una descripción legible de la acción
        description() {
            const eventMap = {
                'created': 'Creación',
                'updated': 'Actualización',
                'deleted': 'Eliminación',
                'restored': 'Restauración',
                'deactivated': 'Desactivación',
                'activated': 'Activación'
            };

            return eventMap[this.activityItem.description] || this.activityItem.description;
        }
    },
    methods: {
        // Obtiene la clase CSS para el badge de acción
        getActionBadgeClass() {
            const classMap = {
                'created': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                'updated': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                'deleted': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                'restored': 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                'deactivated': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                'activated': 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400'
            };

            return classMap[this.activityItem.description] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
        },

        // Obtiene el componente de icono para el tipo de acción
        getActionIcon() {
            const iconMap = {
                'created': CirclePlusIcon,
                'updated': EditIcon,
                'deleted': TrashIcon,
                'restored': RestoreIcon,
                'deactivated': ToggleLeftIcon,
                'activated': ToggleRigthIcon
            };

            return iconMap[this.activityItem.description] || null;
        },

        // Formatea los nombres de campos a una versión más legible
        formatFieldName(field) {
            // Primero verifica si hay un mapeo específico para este campo
            if (this.fieldMappings[field]) {
                return this.fieldMappings[field];
            }

            // Si no hay mapeo, capitaliza y reemplaza guiones bajos con espacios
            return field
                .replace(/_/g, ' ')
                .replace(/^\w/, c => c.toUpperCase())
                .replace(/(\w)(\w*)/g, function (g0, g1, g2) {
                    return g1.toUpperCase() + g2;
                });
        },

        // Formatea valores específicos basados en el tipo de campo
        formatValue(value, field) {
            // Si el valor es null o undefined, devolver un texto predeterminado
            if (value === null || value === undefined) {
                return 'No especificado';
            }

            // Si el campo es de tipo password, mostrar puntos en lugar del valor real
            if (field.toLowerCase().includes('password') || field.toLowerCase().includes('remember_token') || field.toLowerCase().includes('two_factor')) {
                return '••••••••';
            }


            // Si el campo es de tipo firma (ruta de imagen), mostrar un texto descriptivo
            if (field.toLowerCase().includes('sign')) {
                return 'Firma registrada o actualizada';
            }

            // Verificar mapeos personalizados primero
            const fieldKey = `${field}.${value}`;

            if (this.valueMappings[fieldKey]) {
                return this.valueMappings[fieldKey];
            }

            // Verificar si es un valor booleano
            if (typeof value === 'boolean') {
                return value ? 'Sí' : 'No';
            }

            // Si es un objeto, lo convertimos a JSON para visualización
            if (typeof value === 'object' && value !== null) {
                return JSON.stringify(value);
            }

            // Detectar y formatear fechas (asumiendo formato ISO)
            if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/.test(value)) {
                return this.formatDate(value);
            }

            // Valor predeterminado
            return value;
        },

        // Formatea una fecha para hacerla más legible
        formatDate(dateString) {
            if (!dateString) return 'No especificado';

            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;

            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };

            return date.toLocaleDateString('es-ES', options);
        },

        // Obtiene el nombre del usuario que realizó el cambio
        getUserName(causer) {
            if (!causer) return 'Sistema';

            // Asumiendo que el usuario tiene nombre y apellido
            if (causer.name) {
                return causer.name + (causer.last_name ? ' ' + causer.last_name : '');
            }

            // Si hay otros campos disponibles
            if (causer.first_name && causer.last_name) {
                return `${causer.first_name} ${causer.last_name}`;
            }

            // Fallback a email o username
            return causer.email || causer.username || `Usuario #${causer.id}`;
        }
    }
}
</script>
