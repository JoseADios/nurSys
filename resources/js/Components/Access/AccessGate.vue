<!-- components/AccessGate.vue -->
<template>
    <div v-if="hasAccess">
        <slot></slot>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    role: {
        type: [String, Array],
        default: null
    },
    permission: {
        type: [String, Array],
        default: null
    },
    exceptRole: {
        type: [String, Array],
        default: null
    },
    exceptPermission: {
        type: [String, Array],
        default: null
    },
    requireAll: {
        type: Boolean,
        default: false
    }
});

const hasAccess = computed(() => {
    const user = usePage().props.auth.user;
    if (!user) return false;

    const roles = user.roles || [];
    const permissions = user.permissions || [];

    // Verificar roles excluidos primero
    if (props.exceptRole) {
        const excludedRoles = Array.isArray(props.exceptRole)
            ? props.exceptRole
            : [props.exceptRole];

        if (excludedRoles.some(r => roles.includes(r))) {
            return false;
        }
    }

    // Verificar permisos excluidos
    if (props.exceptPermission) {
        const excludedPermissions = Array.isArray(props.exceptPermission)
            ? props.exceptPermission
            : [props.exceptPermission];

        if (excludedPermissions.some(p => permissions.includes(p))) {
            return false;
        }
    }

    // Si no hay roles o permisos requeridos después de verificar exclusiones
    if (!props.role && !props.permission) return true;

    const hasRole = props.role ? (Array.isArray(props.role)
        ? props.role.some(r => roles.includes(r))
        : roles.includes(props.role))
        : null;

    const hasPermission = props.permission ? (Array.isArray(props.permission)
        ? props.permission.some(p => permissions.includes(p))
        : permissions.includes(props.permission))
        : null;

    // Si solo se especifica rol, verificar solo rol
    if (props.role && !props.permission) return hasRole;

    // Si solo se especifica permiso, verificar solo permiso
    if (!props.role && props.permission) return hasPermission;

    // Si se especifican ambos, usar requireAll
    return props.requireAll
        ? (hasRole && hasPermission)
        : (hasRole || hasPermission);
});
</script>
