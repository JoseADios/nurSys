<template>
    <div class="m-0 p-0 bg-transparent border-0">
        <!-- Input de teléfono -->
        <input id="phone-input" type="text" v-model="formattedPhone" @input="formatPhone"
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full p-2.5"
            placeholder="(809)-453-2356" maxlength="14" required />
    </div>
</template>

<script>
export default {
    props: {
        modelValue: String, // Recibe el valor sin formato (ej: "8094532356")
    },
    computed: {
        formattedPhone: {
            get() {
                return this.formatDisplay(this.modelValue);
            },
            set(value) {
                const formattedValue = this.formatDisplay(this.cleanNumber(value));
                this.$emit("update:modelValue", formattedValue);
            },
        },
    },
    methods: {
        formatPhone(event) {
            this.formattedPhone = event.target.value;
        },
        formatDisplay(value) {
            if (!value) return "";
            const digits = this.cleanNumber(value);
            if (digits.length <= 3) return `(${digits}`;
            if (digits.length <= 6) return `(${digits.slice(0, 3)})-${digits.slice(3)}`;
            return `(${digits.slice(0, 3)})-${digits.slice(3, 6)}-${digits.slice(6, 10)}`;
        },
        cleanNumber(value) {
            return value.replace(/\D/g, "").slice(0, 10); // Solo números y límite de 10 dígitos
        },
    },
    mounted() {
        this.$emit("update:modelValue", this.formattedPhone);
    },
};
</script>
