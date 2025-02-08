<template>
    <div class="form-group">
        <label for="exequatur-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Número de Exequátur:
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" />
                </svg>
            </div>
            <input type="number" id="exequatur-input" aria-describedby="exequatur-helper-text" :value="modelValue"
                @input="handleInput"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Ingrese número de exequátur" />
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
    methods: {
        handleInput(event) {
            // Emite el evento de actualización del modelo con el valor exacto
            this.$emit('update:modelValue', event.target.value);
        }
    }
};
</script>
