<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button :type="type"
        class="inline-flex items-center justify-center px-4 py-2 bg-primary-500 dark:bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-primary-700 dark:hover:bg-primary focus:bg-primary-700 dark:focus:bg-primary active:bg-primary-900 dark:active:bg-primary-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
        :disabled="isLoading">
        <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <slot />
    </button>
</template>
