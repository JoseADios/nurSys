<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
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

// Clases de color computadas según la variante y shade
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
                `hover:bg-${getColorClass(props.color, 'outline', 'hover', '50')}`,
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

// Clases base comunes
const baseClasses = computed(() => [
    'inline-flex items-center justify-center transition-all duration-150 ease-in-out',
    'font-semibold text-xs uppercase tracking-widest',
    'focus:outline-none focus:ring-2 focus:ring-offset-2',
    'disabled:opacity-25 cursor-pointer',
    props.disabled && 'opacity-25 cursor-not-allowed',
    sizeClasses.value,
    ...variantClasses.value,
    props.customClass
]);
</script>

<template>
    <Link :href="href" :method="method" :as="as" :preserve-scroll="preserveScroll" :preserve-state="preserveState"
        :replace="replace" :only="only" :data="data" :class="baseClasses" @click.prevent="disabled ? null : undefined">
    <!-- Contenedor para asegurar alineación vertical correcta -->
    <span class="flex items-center">
        <slot />
    </span>
    </Link>
</template>
