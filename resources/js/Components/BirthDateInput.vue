<template>
    <div class="space-y-2">
        <label for="birthdate" class="block text-sm font-medium text-white">
            Fecha de Nacimiento
        </label>
        <input type="date" id="birthdate" :value="modelValue" @input="updateValue($event.target.value)"
            class="block w-full rounded-lg border-gray-600 bg-gray-700 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
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
