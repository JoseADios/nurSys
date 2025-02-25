<template>
    <div class="mx-auto">
        <label for="phone-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Número de Teléfono:
        </label>
        <div class="relative">
            <!-- Icono del teléfono -->
            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 19 18">
                    <path
                        d="M18 13.446a3.02 3.02 0 0 0-.946-1.985l-1.4-1.4a3.054 3.054 0 0 0-4.218 0l-.7.7a.983.983 0 0 1-1.39 0l-2.1-2.1a.983.983 0 0 1 0-1.389l.7-.7a2.98 2.98 0 0 0 0-4.217l-1.4-1.4a2.824 2.824 0 0 0-4.218 0c-3.619 3.619-3 8.229 1.752 12.979C6.785 16.639 9.45 18 11.912 18a7.175 7.175 0 0 0 5.139-2.325A2.9 2.9 0 0 0 18 13.446Z" />
                </svg>
            </div>
            <!-- Input de teléfono -->
            <input id="phone-input" type="text" v-model="formattedPhone" @input="formatPhone"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="(809)-453-2356" maxlength="14" required />
        </div>
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
};
</script>
