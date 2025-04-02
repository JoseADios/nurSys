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

// Estado para el filtro de tiempo seleccionado
const selectedTimeFilter = ref('week');
// Emitir eventos para el filtro de tiempo
const emit = defineEmits(['time-filter-change']);

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
    // Personalizar el título del eje X según el filtro seleccionado
    let xaxisTitle = 'Día';
    if (selectedTimeFilter.value === 'month') {
        xaxisTitle = 'Día del mes';
    } else if (selectedTimeFilter.value === 'year') {
        xaxisTitle = 'Mes';
    } else if (selectedTimeFilter.value === 'all') {
        xaxisTitle = 'Año';
    }

    return {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            background: 'transparent',
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
            title: {
                text: xaxisTitle,
                style: {
                    color: isDarkMode.value ? '#a0aec0' : '#64748b'
                }
            },
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
                rotate: selectedTimeFilter.value === 'year' ? 0 : 0
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

// Función para formatear la fecha con Moment.js según el filtro de tiempo
function formatDate(dateString) {
    if (selectedTimeFilter.value === 'week') {
        return moment(dateString).format('DD/MM');
    } else if (selectedTimeFilter.value === 'month') {
        return moment(dateString).format('DD');
    } else if (selectedTimeFilter.value === 'year') {
        return moment(dateString).format('MMM');
    } else if (selectedTimeFilter.value === 'all') {
        return moment(dateString).format('YYYY');
    }
    return moment(dateString).format('DD/MM');
}

// Manejar el cambio de filtro de tiempo
function handleTimeFilterChange(filter) {
    selectedTimeFilter.value = filter;
    emit('time-filter-change', filter);

    // Reiniciar los datos de las series cuando cambia el filtro
    // Esto soluciona el problema principal
    resetAndUpdateSeries();
}

// Función para reiniciar y actualizar las series
function resetAndUpdateSeries() {
    // Limpiar las series antes de actualizar con nuevos datos
    series.value = [
        {
            name: 'Ingresos',
            data: []
        },
        {
            name: 'Altas',
            data: []
        }
    ];

    // Procesar los datos de admissions y discharges
    if (props.admissions.length || props.discharges.length) {
        processChartData(props.admissions, props.discharges);
    }
}

// Función para procesar los datos del gráfico
function processChartData(admissions, discharges) {
    // Procesar los datos usando Moment.js para un manejo consistente de fechas
    const processedAdmissions = admissions.map(item => ({
        x: formatDate(item.date),
        y: item.total,
        rawDate: moment(item.date) // Guardar el objeto moment para ordenar después
    }));

    const processedDischarges = discharges.map(item => ({
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

    // Actualizar series
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

    // Procesar datos iniciales si están disponibles
    if (props.admissions.length || props.discharges.length) {
        processChartData(props.admissions, props.discharges);
    }
});

// Vigilar cambios en ambas props y en el filtro de tiempo seleccionado
watch([() => props.admissions, () => props.discharges, () => selectedTimeFilter.value],
    ([newAdmissions, newDischarges]) => {
        if (!newAdmissions.length && !newDischarges.length) {
            // Reiniciar las series si no hay datos
            series.value = [
                {
                    name: 'Ingresos',
                    data: []
                },
                {
                    name: 'Altas',
                    data: []
                }
            ];
            return;
        }

        // Procesar los datos con los nuevos filtros
        processChartData(newAdmissions, newDischarges);
    },
    { deep: true }
);
</script>

<template>
    <div class="w-full">
        <div class="flex justify-between items-center mb-4 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ingresos y Altas</h3>
            <div class="grid grid-cols-2 gap-2 sm:flex sm:space-x-2">
                <button @click="handleTimeFilterChange('week')" :class="[
                    'px-3 py-1 text-sm rounded-md transition',
                    selectedTimeFilter === 'week'
                        ? 'bg-indigo-400 text-white'
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                ]">
                    Semana
                </button>
                <button @click="handleTimeFilterChange('month')" :class="[
                    'px-3 py-1 text-sm rounded-md transition',
                    selectedTimeFilter === 'month'
                        ? 'bg-indigo-400 text-white'
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                ]">
                    Mes
                </button>
                <button @click="handleTimeFilterChange('year')" :class="[
                    'px-3 py-1 text-sm rounded-md transition',
                    selectedTimeFilter === 'year'
                        ? 'bg-indigo-400 text-white'
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                ]">
                    Año
                </button>
                <button @click="handleTimeFilterChange('all')" :class="[
                    'px-3 py-1 text-sm rounded-md transition',
                    selectedTimeFilter === 'all'
                        ? 'bg-indigo-400 text-white'
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                ]">
                    Histórico
                </button>
            </div>
        </div>
        <VueApexCharts :options="chartOptions" :series="series" height="300" />
    </div>
</template>
