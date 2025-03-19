<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import VueApexCharts from "vue3-apexcharts";
import moment from 'moment/moment';
import 'moment/locale/es';

const props = defineProps({
    admissions: {
        type: Array,
        required: true
    },
    discharges: {
        type: Array,
        required: true
    }
});

// Detectar el modo oscuro
const isDarkMode = ref(false);

// Función para actualizar el estado del modo oscuro
const updateDarkModeState = () => {
    if (localStorage.getItem('darkMode') === 'true') {
        isDarkMode.value = true;
    } else if (localStorage.getItem('darkMode') === 'false') {
        isDarkMode.value = false;
    } else {
        // Si no hay valor en localStorage, usar la preferencia del sistema
        isDarkMode.value = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
};

// Opciones del gráfico computadas basadas en el modo oscuro
const chartOptions = computed(() => {
    return {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            background:'transparent',
            foreColor: isDarkMode.value ? '#a0aec0' : '#64748b',
            stacked: false
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '50%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            type: 'category',
            axisBorder: {
                color: isDarkMode.value ? '#2d3748' : '#e2e8f0'
            },
            axisTicks: {
                color: isDarkMode.value ? '#2d3748' : '#e2e8f0'
            },
            labels: {
                style: {
                    colors: isDarkMode.value ? '#a0aec0' : '#64748b'
                },
                rotate: 0
            }
        },
        yaxis: {
            title: {
                text: 'Cantidad',
                style: {
                    color: isDarkMode.value ? '#a0aec0' : '#64748b'
                }
            },
            labels: {
                style: {
                    colors: isDarkMode.value ? '#a0aec0' : '#64748b'
                }
            }
        },
        grid: {
            borderColor: isDarkMode.value ? '#2d3748' : '#e2e8f0',
            strokeDashArray: 4
        },
        colors: isDarkMode.value ? ['#7B68EE', '#00CED1'] : ['#696CFF', '#03C3EC'],
        tooltip: {
            theme: isDarkMode.value ? 'dark' : 'light',
            y: {
                formatter: function (val) {
                    return val + " pacientes";
                }
            },
            shared: true,
            intersect: false,
            style: {
                fontSize: '12px',
                fontFamily: 'inherit'
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            fontSize: '14px',
            labels: {
                colors: isDarkMode.value ? '#a0aec0' : undefined
            },
            markers: {
                radius: 12
            }
        }
    };
});

const series = ref([
    {
        name: 'Ingresos',
        data: []
    },
    {
        name: 'Altas',
        data: []
    }
]);

// Función para formatear la fecha con Moment.js
function formatDate(dateString) {
    return moment(dateString).format('DD/MM');
}

// Inicializar el estado del modo oscuro al montar el componente
onMounted(() => {
    updateDarkModeState();

    // Escuchar cambios en el localStorage para actualizar en tiempo real
    window.addEventListener('storage', (event) => {
        if (event.key === 'darkMode') {
            updateDarkModeState();
        }
    });

    // También podemos escuchar cambios en la preferencia del sistema
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateDarkModeState);
    }
});

// Vigilar cambios en ambas props
watch([() => props.admissions, () => props.discharges], ([newAdmissions, newDischarges]) => {
    if (!newAdmissions.length && !newDischarges.length) return;

    // Procesar los datos usando Moment.js para un manejo consistente de fechas
    const processedAdmissions = newAdmissions.map(item => ({
        x: formatDate(item.date),
        y: item.total,
        rawDate: moment(item.date) // Guardar el objeto moment para ordenar después
    }));

    const processedDischarges = newDischarges.map(item => ({
        x: formatDate(item.date),
        y: item.total,
        rawDate: moment(item.date)
    }));

    // Recopilar todas las fechas únicas con sus objetos moment
    const dateMap = new Map();

    [...processedAdmissions, ...processedDischarges].forEach(item => {
        if (!dateMap.has(item.x)) {
            dateMap.set(item.x, item.rawDate);
        }
    });

    // Convertir el mapa a array y ordenar por fecha
    const sortedDates = Array.from(dateMap.entries())
        .sort((a, b) => a[1].valueOf() - b[1].valueOf())
        .map(entry => entry[0]);

    // Crear datos ordenados y completos para ambas series
    const admissionsData = [];
    const dischargesData = [];

    sortedDates.forEach(dateStr => {
        const admissionItem = processedAdmissions.find(item => item.x === dateStr);
        const dischargeItem = processedDischarges.find(item => item.x === dateStr);

        admissionsData.push({
            x: dateStr,
            y: admissionItem ? admissionItem.y : 0
        });

        dischargesData.push({
            x: dateStr,
            y: dischargeItem ? dischargeItem.y : 0
        });
    });

    // Actualizar series y categorías
    series.value = [
        {
            name: 'Ingresos',
            data: admissionsData
        },
        {
            name: 'Altas',
            data: dischargesData
        }
    ];
}, { immediate: true, deep: true });
</script>

<template>
    <div class="w-full">
        <VueApexCharts :options="chartOptions" :series="series" height="300" />
    </div>
</template>
