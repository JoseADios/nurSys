<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <!-- Encabezado con información del registro -->
        <div v-if="activityItem" class="mb-6">
            <h3 class="flex items-center text-xl font-semibold text-blue-600 dark:text-blue-400 mb-4">
                <span class="mr-2 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
                Cambios realizados al registro
            </h3>

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
                <div v-if="description" class="flex items-center">
                    <span class="font-semibold mr-2">Acción:</span>
                    <span class="text-gray-800 dark:text-gray-300">{{ description }}</span>
                </div>
            </div>
        </div>

        <!-- Tabla de cambios -->
        <div v-if="hasChanges" class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th
                            class="bg-gray-100 dark:bg-gray-700 text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                            Campo</th>
                        <th
                            class="bg-gray-100 dark:bg-gray-700 text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                            Valor anterior</th>
                        <th
                            class="bg-gray-100 dark:bg-gray-700 text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                            Nuevo valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(change, field) in formattedChanges" :key="field" class="bg-blue-50 dark:bg-blue-900/20">
                        <td
                            class="py-3 px-4 font-medium text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700">
                            {{ formatFieldName(field) }}</td>
                        <td
                            class="py-3 px-4 text-gray-400 dark:text-gray-500 line-through bg-red-50 dark:bg-red-900/10 border-b border-gray-200 dark:border-gray-700">
                            <span v-if="change.old !== null && change.old !== undefined">{{ formatValue(change.old,
                                field) }}</span>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">No especificado</span>
                        </td>
                        <td
                            class="py-3 px-4 font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/10 border-b border-gray-200 dark:border-gray-700">
                            <span v-if="change.new !== null && change.new !== undefined">{{ formatValue(change.new,
                                field) }}</span>
                            <span v-else class="text-gray-400 dark:text-gray-500 italic">No especificado</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mensaje para cuando no hay cambios -->
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400 italic">
            <p>No se encontraron cambios en este registro.</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ActivityLogDiff',
    props: {
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
            // Puedes añadir cualquier estado necesario aquí
        }
    },
    computed: {
        // Verifica si hay cambios para mostrar
        hasChanges() {
            return this.activityItem.properties &&
                this.activityItem.properties.attributes &&
                this.activityItem.properties.old &&
                Object.keys(this.formattedChanges).length > 0;
        },

        // Procesa los cambios para mostrar solo los campos que cambiaron
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
                'created': 'Creación del registro',
                'updated': 'Actualización del registro',
                'deleted': 'Eliminación del registro',
                'restored': 'Restauración del registro'
            };

            return eventMap[this.activityItem.description] || this.activityItem.description;
        }
    },
    methods: {
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
                return causer.name;
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
