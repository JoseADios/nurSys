<template>
    <div class="signature-container">
        <div class="relative">
            <div class="absolute inset-0 bg-white dark:bg-gray-800 rounded"></div>
            <canvas ref="signatureCanvas"
                class="signature-pad border rounded dark:border-gray-600 transition-colors duration-200 relative"
                :width="width" :height="height">
            </canvas>
        </div>

        <input type="hidden" :name="inputName" :value="signatureData" />

        <div class="mt-2 space-x-2">
            <button type="button" @click="clear"
                class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 rounded transition-colors duration-200">
                Limpiar firma
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SignaturePad from 'signature_pad'

const props = defineProps({
    modelValue: String,
    inputName: {
        type: String,
        default: 'signature'
    },
    width: {
        type: Number,
        default: 300
    },
    height: {
        type: Number,
        default: 150
    }
})

const emit = defineEmits(['update:modelValue'])

const signatureCanvas = ref(null)
const signatureData = ref('')
let signaturePad = null

onMounted(() => {
    const canvas = signatureCanvas.value
    const ratio = Math.max(window.devicePixelRatio || 1, 1)

    canvas.width = props.width * ratio
    canvas.height = props.height * ratio

    canvas.style.width = props.width + 'px'
    canvas.style.height = props.height + 'px'

    const context = canvas.getContext('2d')
    context.scale(ratio, ratio)

    signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(0,0,0,0)',
        penColor: 'rgb(0, 0, 0)',
        minWidth: 0.5,
        maxWidth: 2.5
    })

    signaturePad.addEventListener('endStroke', () => {
        const data = signaturePad.toDataURL('image/png')
        if (data !== signatureData.value) {
            signatureData.value = data
            emit('update:modelValue', data)
        }
    })
})

const clear = () => {
    if (signaturePad) {
        signaturePad.clear()
        signatureData.value = ''
        emit('update:modelValue', '')
    }
}
</script>

<style scoped>
.signature-container {
    display: inline-block;
}

.signature-pad {
    touch-action: none;
    background-color: transparent !important;
}
</style>
