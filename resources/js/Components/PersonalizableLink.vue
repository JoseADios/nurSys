<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        default: 'get',
    },
    as: {
        type: String,
        default: 'a',
    },
    preserveScroll: {
        type: Boolean,
        default: false,
    },
    preserveState: {
        type: Boolean,
        default: false,
    },
    replace: {
        type: Boolean,
        default: false,
    },
    only: {
        type: Array,
        default: () => [],
    },
    data: {
        type: Object,
        default: () => ({}),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: 'default', // 'small', 'default', 'large'
    },
    variant: {
        type: String,
        default: 'solid', // 'solid', 'outline', 'subtle', 'text'
    },
    color: {
        type: String,
        default: 'primary', // cualquier color de Tailwind
    },
    // Clases personalizadas
    customClass: {
        type: String,
        default: '',
    }
});
</script>

<template>
    <Link :href="href" :method="method" :as="as" :preserve-scroll="preserveScroll" :preserve-state="preserveState"
        :replace="replace" :only="only" :data="data" :class="[
            // Clases base compartidas
            'inline-flex items-center justify-center transition-all duration-150 ease-in-out',
            'font-semibold text-xs uppercase tracking-widest',
            'focus:outline-none focus:ring-2 focus:ring-offset-2',
            'disabled:opacity-25 cursor-pointer',
            disabled && 'opacity-25 cursor-not-allowed',

            // Clases de tamaño
            size === 'small' ? 'px-3 py-1.5 text-xs rounded' :
                size === 'large' ? 'px-6 py-3 text-sm rounded-md' :
                    'px-4 py-2 text-xs rounded-md',

            // Clases de variante y color
            variant === 'solid' ? [
                `bg-${color}-500`,
                'text-white',
                'border',
                'border-transparent',
                `hover:bg-${color}-600`,
                `active:bg-${color}-700`,
                `focus:ring-${color}-500`
            ] : null,
            variant === 'outline' ? [
                'bg-transparent',
                `text-${color}-500`,
                'border',
                `border-${color}-500`,
                `hover:bg-${color}-50`,
                `active:bg-${color}-100`,
                `focus:ring-${color}-500`
            ] : null,
            variant === 'subtle' ? [
                `bg-${color}-50`,
                `text-${color}-700`,
                'border',
                'border-transparent',
                `hover:bg-${color}-100`,
                `active:bg-${color}-200`,
                `focus:ring-${color}-500`
            ] : null,
            variant === 'text' ? [
                'bg-transparent',
                `text-${color}-500`,
                'border',
                'border-transparent',
                `hover:bg-${color}-50`,
                `active:bg-${color}-100`,
                `focus:ring-${color}-500`
            ] : null,

            // Clases personalizadas
            customClass
        ]" @click.prevent="disabled ? null : undefined">
    <!-- Contenedor para asegurar alineación vertical correcta -->
    <span class="flex items-center">
        <slot />
    </span>
    </Link>
</template>
