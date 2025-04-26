<template>
    <div ref="avatar" class="flex items-center justify-center font-bold rounded-full" :class="[sizeClass]" :style="{
        backgroundColor: background,
        color: textColor,
        fontSize: fontSize + 'px'
    }">
        <span class="font-light">{{ initials }}</span>
    </div>
</template>

<script>
export default {
    name: 'Avatar',
    props: {
        name: {
            type: String,
            required: true
        },
        sizeClass: {
            type: String,
            default: 'w-10 h-10' // Clase predeterminada de tamaño
        },
        bgColor: {
            type: String,
            default: null
        },
        color: {
            type: String,
            default: null
        },
        length: {
            type: Number,
            default: 2
        }
    },
    data() {
        return {
            fontSize: 14 // valor inicial, se actualizará dinámicamente
        };
    },
    computed: {
        initials() {
            if (!this.name) return '?';
            const nameParts = this.name.trim().split(/\s+/);
            if (nameParts.length === 1) {
                return this.name.substring(0, this.length).toUpperCase();
            }
            let initials = nameParts.map(part => part.charAt(0)).join('');
            return initials.substring(0, this.length).toUpperCase();
        },
        background() {
            if (this.bgColor) return this.bgColor;
            return '#EBF4FF';
        },
        textColor() {
            if (this.color) return this.color;
            return '#7F9CF5';
        }
    },
    mounted() {
        this.calculateFontSize();
        window.addEventListener('resize', this.calculateFontSize);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.calculateFontSize);
    },
    methods: {
        calculateFontSize() {
            this.$nextTick(() => {
                const avatar = this.$refs.avatar;
                if (avatar) {
                    const size = Math.min(avatar.clientWidth, avatar.clientHeight);
                    // Ajusta el factor según lo que se vea mejor (e.g., 0.5 para mitad del tamaño)
                    if (size > 0) {
                        const calculated = size * 0.45;
                        this.fontSize = Math.max(calculated, 10); // mínimo 10px
                    }
                }
            });
        }
    }
};
</script>
