<template>
    <div class="m-0 p-0 bg-transparent border-0">
        <input type="text" id="cedula-input" aria-describedby="helper-text-explanation" :value="formattedValue"
            @input="handleInput" @keydown="handleKeyDown"
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full p-2.5"
            placeholder="402-250219-1" required maxlength="12" />
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
            this.$emit('update:modelValue', this.formatCedula(inputValue));
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
    },
    mounted() {
        this.$emit('update:modelValue', this.formattedValue);
    }
};
</script>
