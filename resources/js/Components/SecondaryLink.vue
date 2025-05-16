<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
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
});
</script>

<template>
    <Link
        :href="href"
        :method="method"
        :as="as"
        :preserve-scroll="preserveScroll"
        :preserve-state="preserveState"
        :replace="replace"
        :only="only"
        :data="data"
        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-25 transition ease-in-out duration-150"
    >
        <slot />
    </Link>
</template>
