<template>
    <div>
        <apexchart type="line" :options="chartOptions" :series="chartSeries"></apexchart>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import ApexCharts from 'vue3-apexcharts';

export default defineComponent({
    components: {
        apexchart: ApexCharts,
    },
    props: {
        temperatureData: {
            type: Array,
            required: true,
        },
        height: {
            type: Number,
            default: 250, // altura por defecto más pequeña
        }
    },
    data() {
        return {
            chartOptions: {
                chart: {
                    height: this.height,
                    type: 'line',
                    background: 'transparent',
                    foreColor: '#e5e7eb',
                    toolbar: {
                        show: false // oculta la barra de herramientas para ahorrar espacio
                    }
                },
                title: {
                    text: 'Temperaturas Registradas',
                    align: 'center',
                    style: {
                        fontSize: '18px', // título más pequeño
                        fontWeight: 'bold',
                        color: '#f3f4f6',
                    },
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                },
                colors: ['#3b82f6'],
                grid: {
                    borderColor: '#374151',
                    strokeDashArray: 5,
                    padding: {
                        top: 0,
                        right: 10,
                        bottom: 0,
                        left: 10
                    },
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    },
                },
                xaxis: {
                    categories: [],
                    labels: {
                        style: {
                            colors: '#e5e7eb',
                            fontSize: '11px', // texto más pequeño
                        },
                        rotate: -45,
                        rotateAlways: false,
                    },
                    axisBorder: {
                        color: '#4b5563',
                    },
                    axisTicks: {
                        color: '#4b5563',
                    },
                },
                yaxis: {
                    title: {
                        text: 'Temperatura (°C)',
                        style: {
                            color: '#e5e7eb',
                            fontSize: '12px', // texto más pequeño
                        },
                    },
                    labels: {
                        style: {
                            colors: '#e5e7eb',
                            fontSize: '11px', // texto más pequeño
                        },
                    },
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: (value, { dataPointIndex }) => {
                            const evacuation = this.temperatureData[dataPointIndex].evacuations;
                            const urination = this.temperatureData[dataPointIndex].urinations;
                            return `Temperatura: ${value} °C<br>Evacuations: ${evacuation}<br>Urinations: ${urination}`;
                        }
                    }
                },
                markers: {
                    size: 4, // marcadores más pequeños
                    colors: ['#3b82f6'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                },
            },
            chartSeries: [
                {
                    name: '',
                    data: [],
                },
            ],
        };
    },
    mounted() {
        this.processTemperatureData();
    },
    methods: {
        processTemperatureData() {
            this.chartOptions.xaxis.categories = this.temperatureData.map(item =>
                new Date(item.created_at).toLocaleString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                })
            );
            this.chartSeries[0].data = this.temperatureData.map(item => item.temperature);
        },
    },
});
</script>
