<!-- PersonalizableButton.vue -->
<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
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
        default: 'primary'
    }
});
</script>

<template>
    <button :type="type" :disabled="disabled || loading" :class="[
        // Clases base compartidas
        'inline-flex items-center justify-center transition-all duration-150 ease-in-out',
        'font-semibold text-xs uppercase tracking-widest',
        'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2',
        'disabled:opacity-50 disabled:cursor-not-allowed',

        // Tamaños
        size === 'small' ? 'px-3 py-1.5 text-xs rounded' :
            size === 'large' ? 'px-6 py-3 text-sm rounded-md' :
                'px-4 py-2 text-xs rounded-md',

        // Variantes
        variant === 'solid' && 'bg-primary-500 text-white border border-transparent hover:bg-primary-600 active:bg-primary-700',
        variant === 'outline' && 'bg-transparent text-primary-500 border border-primary-500 hover:bg-primary-50 active:bg-primary-100',
        variant === 'subtle' && 'bg-primary-50 text-primary-700 border border-transparent hover:bg-primary-100 active:bg-primary-200',
        variant === 'text' && 'bg-transparent text-primary-500 border border-transparent hover:bg-primary-50 active:bg-primary-100',

        // Espacio para el icono de carga
        loading && 'relative'
    ]">
        <!-- Icono de carga (spinner) -->
        <svg v-if="loading" class="w-4 h-4 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <slot />
    </button>
</template>
