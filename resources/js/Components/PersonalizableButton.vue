<script setup>
import { computed } from 'vue';

const props = defineProps({
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
    },
    // Nueva prop para la intensidad del color
    shade: {
        type: [String, Number],
        default: null, // Por defecto usará los valores preestablecidos
    },
    // Clases personalizadas
    customClass: {
        type: String,
        default: '',
    }
});

// Función para generar las clases de color con la intensidad personalizada
const getColorClass = (baseColor, variant, colorType, defaultShade) => {
    const intensity = props.shade || defaultShade;
    return `${baseColor}-${intensity}`;
};

// Clases de tamaño
const sizeClasses = computed(() => {
    switch (props.size) {
        case 'small':
            return 'px-3 py-1.5 text-xs rounded';
        case 'large':
            return 'px-6 py-3 text-sm rounded-md';
        default: // default
            return 'px-4 py-2 text-xs rounded-md';
    }
});

// Clases de variante y color
const variantClasses = computed(() => {
    switch (props.variant) {
        case 'solid':
            return [
                `bg-${getColorClass(props.color, 'solid', 'bg', '500')}`,
                'text-white',
                'border',
                'border-transparent',
                `hover:bg-${getColorClass(props.color, 'solid', 'hover', '600')}`,
                `active:bg-${getColorClass(props.color, 'solid', 'active', '700')}`,
                `focus:ring-${getColorClass(props.color, 'solid', 'focus', '500')}`
            ];
        case 'outline':
            return [
                'bg-transparent',
                `text-${getColorClass(props.color, 'outline', 'text', '500')}`,
                'border',
                `border-${getColorClass(props.color, 'outline', 'border', '500')}`,
                `hover:bg-${getColorClass(props.color, 'outline', 'hover', '0')}`,
                `active:bg-${getColorClass(props.color, 'outline', 'active', '100')}`,
                `focus:ring-${getColorClass(props.color, 'outline', 'focus', '500')}`
            ];
        case 'subtle':
            return [
                `bg-${getColorClass(props.color, 'subtle', 'bg', '50')}`,
                `text-${getColorClass(props.color, 'subtle', 'text', '700')}`,
                'border',
                'border-transparent',
                `hover:bg-${getColorClass(props.color, 'subtle', 'hover', '100')}`,
                `active:bg-${getColorClass(props.color, 'subtle', 'active', '200')}`,
                `focus:ring-${getColorClass(props.color, 'subtle', 'focus', '500')}`
            ];
        case 'text':
            return [
                'bg-transparent',
                `text-${getColorClass(props.color, 'text', 'text', '500')}`,
                'border',
                'border-transparent',
                `hover:bg-${getColorClass(props.color, 'text', 'hover', '50')}`,
                `active:bg-${getColorClass(props.color, 'text', 'active', '100')}`,
                `focus:ring-${getColorClass(props.color, 'text', 'focus', '500')}`
            ];
        default:
            return [];
    }
});

// Todas las clases combinadas
const buttonClasses = computed(() => [
    // Clases base compartidas
    'inline-flex items-center justify-center transition-all duration-150 ease-in-out',
    'font-semibold text-xs uppercase tracking-widest',
    `focus:outline-none focus:ring-2 focus:ring-${getColorClass(props.color, '', 'focus', '500')} focus:ring-offset-2`,
    'disabled:opacity-50 disabled:cursor-not-allowed',

    // Tamaños
    sizeClasses.value,

    // Clases de variante y color
    ...variantClasses.value,

    // Clases personalizadas
    props.customClass,

    // Espacio para el icono de carga
    props.loading && 'relative'
]);
</script>

<template>
    <button :type="type" :disabled="disabled || loading" :class="buttonClasses">
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
