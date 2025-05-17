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
        class="inline-flex w-full items-center justify-center px-4 py-2 bg-primary-500 dark:bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-primary-700 dark:hover:bg-primary-600 focus:bg-primary-700 dark:focus:bg-primary active:bg-primary-900 dark:active:bg-primary-400 disabled:opacity-50 transition ease-in-out duration-150"
    >
        <slot />
    </Link>
</template>
