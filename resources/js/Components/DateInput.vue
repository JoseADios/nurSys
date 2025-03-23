<template>
    <div class="m-0 p-0 bg-transparent border-0">
        <input :type="'date'" id="birthdate" :value="modelValue" @input="updateValue($event.target.value)"
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full p-2.5 [&::-webkit-calendar-picker-indicator]:dark:invert"
            required>
    </div>

</template>

<script setup>
import { watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (value) => {
    emit('update:modelValue', value);
};

// Watch for changes in the prop
watch(() => props.modelValue, (newValue) => {
    updateValue(newValue);
});

</script>
