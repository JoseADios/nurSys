<template>
    <div class="space-y-2">
        <label for="birthdate" class="block mb-2 mt-6 text-sm font-medium text-gray-900 dark:text-white">
            Fecha de Nacimiento
        </label>
        <input type="date" id="birthdate" :value="modelValue" @input="updateValue($event.target.value)"
            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-purple-500 dark:focus:border-purple-500"
            required>
        <div v-if="birthdateError" class="text-red-500 text-sm mt-2">
            {{ birthdateError }}
        </div>
    </div>
</template>

<script>
import { ref, watch } from 'vue'

export default {
    props: {
        modelValue: {
            type: String,
            default: null
        }
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        const birthdateError = ref(null)

        const validateBirthdate = (birthdate) => {
            if (!birthdate) {
                birthdateError.value = null
                return
            }

            const today = new Date()
            const birthdateObj = new Date(birthdate)

            let age = today.getFullYear() - birthdateObj.getFullYear()
            const monthDifference = today.getMonth() - birthdateObj.getMonth()

            if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthdateObj.getDate())) {
                age--
            }

            if (age < 18) {
                birthdateError.value = 'El usuario debe tener al menos 18 años.'
            } else {
                birthdateError.value = null
            }
        }

        const updateValue = (value) => {
            emit('update:modelValue', value)
            validateBirthdate(value)
        }

        // Watch for changes in the prop
        watch(() => props.modelValue, validateBirthdate)

        return {
            birthdateError,
            updateValue
        }
    }
}
</script>
