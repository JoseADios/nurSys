<script setup>
import { ref, onMounted, watch } from 'vue';
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    admissions: {
        type: Array,
        required: true
    }
});

const chartOptions = ref({
    chart: {
        type: 'bar',
        height: 350,
        toolbar: {
            show: false
        },
        background: 'transparent',
        foreColor: '#64748b' // Color del texto para modo claro
    },
    plotOptions: {
        bar: {
            borderRadius: 4,
            horizontal: false,
            columnWidth: '60%'
        }
    },
    dataLabels: {
        enabled: false
    },
    xaxis: {
        type: 'datetime',
        labels: {
            format: 'dd/MM',
            style: {
                colors: '#64748b'
            }
        },
        axisBorder: {
            color: '#e2e8f0'
        },
        axisTicks: {
            color: '#e2e8f0'
        }
    },
    yaxis: {
        title: {
            text: 'Ingresos',
            style: {
                color: '#64748b'
            }
        },
        labels: {
            style: {
                colors: '#64748b'
            }
        }
    },
    grid: {
        borderColor: '#e2e8f0',
        strokeDashArray: 4
    },
    colors: ['#3B82F6'], // Azul que coincide con el tema
    tooltip: {
        theme: 'light',
        x: {
            format: 'dd/MM/yy'
        },
        style: {
            fontSize: '12px',
            fontFamily: 'inherit'
        }
    }
});

const series = ref([
    {
        name: 'Ingresos',
        data: []
    }
]);

watch(() => props.admissions, (newAdmissions) => {
    series.value = [{
        name: 'Ingresos',
        data: newAdmissions.map(item => ({
            x: new Date(item.date).getTime(),
            y: item.total
        }))
    }];
}, { immediate: true });
</script>

<template>
    <div class="w-full">
        <VueApexCharts
            :options="chartOptions"
            :series="series"
            height="350"
        />
    </div>
</template>