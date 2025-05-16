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
        default: 'primary', // 'primary', 'secondary', 'success', 'info', 'warning'
    },
});
</script>

<template>
    <Link :href="disabled ? '#' : href" :method="method" :as="as" :preserve-scroll="preserveScroll"
        :preserve-state="preserveState" :replace="replace" :only="only" :data="data" :class="[
            // Clases base compartidas
            'inline-flex items-center justify-center transition-all duration-150 ease-in-out',
            'font-semibold text-xs uppercase tracking-widest',
            'focus:outline-none focus:ring-2 focus:ring-offset-2',
            disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '',

            // Tamaños
            size === 'small' ? 'px-3 py-1.5 text-xs rounded' :
                size === 'large' ? 'px-6 py-3 text-sm rounded-md' :
                    'px-4 py-2 text-xs rounded-md',

            // Color y variante
            // Primary
            color === 'primary' && variant === 'solid' && 'bg-primary-500 text-white border border-transparent hover:bg-primary-600 active:bg-primary-700 focus:ring-primary-500',
            color === 'primary' && variant === 'outline' && 'bg-transparent text-primary-500 border border-primary-500 hover:bg-primary-50 active:bg-primary-100 focus:ring-primary-500',
            color === 'primary' && variant === 'subtle' && 'bg-primary-50 text-primary-700 border border-transparent hover:bg-primary-100 active:bg-primary-200 focus:ring-primary-500',
            color === 'primary' && variant === 'text' && 'bg-transparent text-primary-500 border border-transparent hover:bg-primary-50 active:bg-primary-100 focus:ring-primary-500',

            // Secondary
            color === 'secondary' && variant === 'solid' && 'bg-secondary-500 text-white border border-transparent hover:bg-secondary-600 active:bg-secondary-700 focus:ring-secondary-500',
            color === 'secondary' && variant === 'outline' && 'bg-transparent text-secondary-500 border border-secondary-500 hover:bg-secondary-50 active:bg-secondary-100 focus:ring-secondary-500',
            color === 'secondary' && variant === 'subtle' && 'bg-secondary-50 text-secondary-700 border border-transparent hover:bg-secondary-100 active:bg-secondary-200 focus:ring-secondary-500',
            color === 'secondary' && variant === 'text' && 'bg-transparent text-secondary-500 border border-transparent hover:bg-secondary-50 active:bg-secondary-100 focus:ring-secondary-500',

            // Success
            color === 'success' && variant === 'solid' && 'bg-green-500 text-white border border-transparent hover:bg-green-600 active:bg-green-700 focus:ring-green-500',
            color === 'success' && variant === 'outline' && 'bg-transparent text-green-500 border border-green-500 hover:bg-green-50 active:bg-green-100 focus:ring-green-500',
            color === 'success' && variant === 'subtle' && 'bg-green-50 text-green-700 border border-transparent hover:bg-green-100 active:bg-green-200 focus:ring-green-500',
            color === 'success' && variant === 'text' && 'bg-transparent text-green-500 border border-transparent hover:bg-green-50 active:bg-green-100 focus:ring-green-500',

            // Info
            color === 'info' && variant === 'solid' && 'bg-blue-500 text-white border border-transparent hover:bg-blue-600 active:bg-blue-700 focus:ring-blue-500',
            color === 'info' && variant === 'outline' && 'bg-transparent text-blue-500 border border-blue-500 hover:bg-blue-50 active:bg-blue-100 focus:ring-blue-500',
            color === 'info' && variant === 'subtle' && 'bg-blue-50 text-blue-700 border border-transparent hover:bg-blue-100 active:bg-blue-200 focus:ring-blue-500',
            color === 'info' && variant === 'text' && 'bg-transparent text-blue-500 border border-transparent hover:bg-blue-50 active:bg-blue-100 focus:ring-blue-500',

            // Warning
            color === 'warning' && variant === 'solid' && 'bg-yellow-500 text-white border border-transparent hover:bg-yellow-600 active:bg-yellow-700 focus:ring-yellow-500',
            color === 'warning' && variant === 'outline' && 'bg-transparent text-yellow-500 border border-yellow-500 hover:bg-yellow-50 active:bg-yellow-100 focus:ring-yellow-500',
            color === 'warning' && variant === 'subtle' && 'bg-yellow-50 text-yellow-700 border border-transparent hover:bg-yellow-100 active:bg-yellow-200 focus:ring-yellow-500',
            color === 'warning' && variant === 'text' && 'bg-transparent text-yellow-500 border border-transparent hover:bg-yellow-50 active:bg-yellow-100 focus:ring-yellow-500',
        ]" @click.prevent="disabled ? null : undefined">
    <!-- Contenedor para asegurar alineación vertical correcta -->
    <span class="flex items-center">
        <slot />
    </span>
    </Link>
</template>
