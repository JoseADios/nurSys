<template>
    <div class="form-group">
        <label for="cedula-input" class="block text-sm font-medium text-gray-900 dark:text-white">
            Número de cédula:
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                    <path d="M9 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                    <path d="M15 8l2 0" />
                    <path d="M15 12l2 0" />
                    <path d="M7 16l10 0" />
                </svg>
            </div>
            <input type="text" id="cedula-input" aria-describedby="helper-text-explanation" :value="formattedValue"
                @input="handleInput" @keydown="handleKeyDown"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="402-250219-1" required maxlength="12" />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: {
            type: String,
            default: ''
        }
    },
    computed: {
        formattedValue() {
            return this.formatCedula(this.modelValue);
        }
    },
    methods: {
        formatCedula(value) {
            // Elimina cualquier carácter que no sea número
            const cleanedValue = value.replace(/[^0-9]/g, '');

            // Formatea la cédula
            if (cleanedValue.length <= 3) {
                return cleanedValue;
            } else if (cleanedValue.length <= 6) {
                return `${cleanedValue.slice(0, 3)}-${cleanedValue.slice(3)}`;
            } else if (cleanedValue.length <= 9) {
                return `${cleanedValue.slice(0, 3)}-${cleanedValue.slice(3, 9)}-${cleanedValue.slice(9)}`;
            }

            return `${cleanedValue.slice(0, 3)}-${cleanedValue.slice(3, 9)}-${cleanedValue.slice(9, 10)}`;
        },
        handleInput(event) {
            // Elimina todos los guiones del valor de entrada
            const inputValue = event.target.value.replace(/[^0-9]/g, '');

            // Emite el evento de actualización del modelo con solo números
            this.$emit('update:modelValue', inputValue);
        },
        handleKeyDown(event) {
            // Permite borrar incluso con guiones presentes
            if (event.key === 'Backspace') {
                event.preventDefault();
                const currentValue = this.modelValue.replace(/[^0-9]/g, '');
                const newValue = currentValue.slice(0, -1);
                this.$emit('update:modelValue', newValue);
            }
        }
    }
};
</script>

<style scoped>
/* Estilos específicos para el componente si es necesario */
</style>
