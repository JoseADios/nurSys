<template>
    <div v-show="visible"
        class="fixed bottom-5 right-5 flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-sm dark:text-gray-400 dark:bg-gray-800 transition-opacity duration-300 ease-in-out"
        :class="toastClasses">
        <!-- Ícono dinámico -->
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg" :class="iconClasses">
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15.147 15.085a7.159 7.159 0 0 1-6.189 3.307A6.713 6.713 0 0 1 3.1 15.444c-2.679-4.513.287-8.737.888-9.548A4.373 4.373 0 0 0 5 1.608c1.287.953 6.445 3.218 5.537 10.5 1.5-1.122 2.706-3.01 2.853-6.14 1.433 1.049 3.993 5.395 1.757 9.117Z" />
            </svg>
            <span class="sr-only">Fire icon</span>
        </div>

        <!-- Mensaje -->
        <div class="ms-3 text-sm font-normal">
            {{ message }}
        </div>

        <!-- Botón de cierre -->
        <button type="button"
            class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
            @click="closeToast" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>
</template>

<script>
export default {
    props: {
        message: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            default: 'info', // Puede ser 'success', 'error', 'warning', 'info'
        },
        duration: {
            type: Number,
            default: 3000,
        },
    },
    data() {
        return {
            visible: false,
        };
    },
    computed: {
        toastClasses() {
            return {
                'bg-green-100': this.type === 'success',
                'bg-red-100': this.type === 'error',
                'bg-yellow-100': this.type === 'warning',
                'bg-blue-100': this.type === 'info',
            };
        },
        iconClasses() {
            return {
                'text-green-500': this.type === 'success',
                'text-red-500': this.type === 'error',
                'text-yellow-500': this.type === 'warning',
                'text-blue-500': this.type === 'info',
            };
        },
    },
    watch: {
        message(newValue) {
            if (newValue) {
                this.show();
            }
        }
    },
    methods: {
        show() {
            this.visible = true;
            if (this.duration > 0) {
                setTimeout(() => {
                    this.closeToast();
                }, this.duration);
            }
        },
        closeToast() {
            this.visible = false;
        },
    },
    mounted() {
        if (this.message) {
            this.show();
        }
    }
};
</script>
