<template>
    <div class="signature-container" ref="container">
        <div class="relative w-full h-full">
            <div class="absolute inset-0 bg-white dark:bg-gray-800 rounded"></div>
            <canvas ref="signatureCanvas"
                class="signature-pad border rounded dark:border-gray-600 transition-colors duration-200 relative w-full h-full">
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
import { ref, onMounted, onUnmounted, watch } from 'vue'
import SignaturePad from 'signature_pad'

const props = defineProps({
    modelValue: String,
    inputName: {
        type: String,
        default: 'signature'
    },
    aspectRatio: {
        type: Number,
        default: 2 // Relación de aspecto predeterminada (width/height = 2, equivalente a 300/150)
    },
    maxWidth: {
        type: Number,
        default: 800 // Ancho máximo en píxeles
    }
})

const emit = defineEmits(['update:modelValue'])

const container = ref(null)
const signatureCanvas = ref(null)
const signatureData = ref('')
let signaturePad = null
let resizeObserver = null

// Función para redimensionar el canvas
const resizeCanvas = () => {
    if (!signatureCanvas.value || !container.value) return

    // Obtener el ancho del contenedor
    const containerWidth = container.value.clientWidth

    // Limitar al ancho máximo si se especifica
    const width = Math.min(containerWidth, props.maxWidth)

    // Calcular la altura basada en la relación de aspecto
    const height = width / props.aspectRatio

    const canvas = signatureCanvas.value
    const ratio = Math.max(window.devicePixelRatio || 1, 1)

    // Guardar la firma actual si existe
    let data = null
    if (signaturePad && !signaturePad.isEmpty()) {
        data = signaturePad.toDataURL()
    }

    // Actualizar dimensiones del canvas
    canvas.width = width * ratio
    canvas.height = height * ratio

    canvas.style.width = `${width}px`
    canvas.style.height = `${height}px`

    const context = canvas.getContext('2d')
    context.scale(ratio, ratio)

    // Reinicializar SignaturePad
    if (signaturePad) {
        signaturePad.clear()
        signaturePad.off()
    }

    signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(0,0,0,0)',
        penColor: 'rgb(0, 0, 0)',
        minWidth: 0.5,
        maxWidth: 2.5
    })

    // Restaurar la firma si existía
    if (data) {
        signaturePad.fromDataURL(data)
    }

    signaturePad.addEventListener('endStroke', () => {
        const newData = signaturePad.toDataURL('image/png')
        if (newData !== signatureData.value) {
            signatureData.value = newData
            emit('update:modelValue', newData)
        }
    })
}

onMounted(() => {
    // Configurar el observer para detectar cambios en el tamaño del contenedor
    resizeObserver = new ResizeObserver(() => {
        resizeCanvas()
    })

    if (container.value) {
        resizeObserver.observe(container.value)
    }

    // Realizar el dimensionamiento inicial
    resizeCanvas()

    // Si hay un valor inicial, cargarlo
    if (props.modelValue) {
        signaturePad.fromDataURL(props.modelValue)
        signatureData.value = props.modelValue
    }

    // También escuchar eventos de redimensionamiento de ventana
    window.addEventListener('resize', resizeCanvas)
})

onUnmounted(() => {
    // Limpieza al desmontar
    if (resizeObserver) {
        resizeObserver.disconnect()
    }

    window.removeEventListener('resize', resizeCanvas)

    if (signaturePad) {
        signaturePad.off()
    }
})

// Observar cambios en el valor del modelo
watch(() => props.modelValue, (newValue) => {
    if (newValue !== signatureData.value && signaturePad) {
        if (!newValue) {
            signaturePad.clear()
            signatureData.value = ''
        } else {
            signaturePad.fromDataURL(newValue)
            signatureData.value = newValue
        }
    }
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
    width: 100%;
    display: block;
}

.signature-pad {
    touch-action: none;
    background-color: transparent !important;
}
</style>
