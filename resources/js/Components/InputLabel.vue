<script setup>
defineProps({
    value: String,
    required: {
      type: Boolean,
      default: false,
    },
});
</script>

<template>
    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="required" class="ml-1 text-red-500">*</span>
    </label>
</template>
