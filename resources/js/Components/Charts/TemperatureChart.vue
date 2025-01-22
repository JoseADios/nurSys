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
            default: 250,
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
                        show: false
                    }
                },
                title: {
                    text: 'Temperaturas Registradas',
                    align: 'center',
                    style: {
                        fontSize: '18px',
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
                        top: 20,
                        right: 10,
                        bottom: 0,
                        left: 10
                    },
                },
                xaxis: {
                    categories: [],
                    labels: {
                        style: {
                            colors: '#e5e7eb',
                            fontSize: '11px',
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
                            fontSize: '12px',
                        },
                    },
                    labels: {
                        style: {
                            colors: '#e5e7eb',
                            fontSize: '11px',
                        },
                    },
                },
                annotations: {
                    position: 'back',
                    yaxis: [],
                    xaxis: []
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: (value, { dataPointIndex }) => {
                            const evacuation = this.temperatureData[dataPointIndex].evacuations;
                            const urination = this.temperatureData[dataPointIndex].urinations;
                            const nurse = this.temperatureData[dataPointIndex].nurse;
                            const nurseName = `${nurse.name} ${nurse.last_name}`;
                            return `Temperatura: ${value} °C<br>Evacuaciones: ${evacuation}<br>Micciones: ${urination}<br>Enfermero: ${nurseName}`;
                        }
                    }
                },
                markers: {
                    size: 4,
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
                new Date(item.created_at).toLocaleString('es-ES', { day:'2-digit', month:'2-digit', hour:'2-digit', minute:'2-digit' })
            );
            this.chartSeries[0].data = this.temperatureData.map(item => item.temperature);

            const xAxisAnnotations = [];
            let dayBoundaries = [];
            let currentDay = '';

            // Primero, identificamos todas las fronteras de los días
            this.temperatureData.forEach((item, index) => {
                const date = new Date(item.created_at);
                const dayStr = `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}`;

                if (dayStr !== currentDay) {
                    if (currentDay !== '') {
                        dayBoundaries.push(index);
                    }
                    currentDay = dayStr;
                }
            });
            dayBoundaries.push(this.temperatureData.length);

            // Ahora creamos las anotaciones entre las fronteras
            let previousBoundary = 0;
            dayBoundaries.forEach((boundary, index) => {
                const midPoint = previousBoundary + Math.floor((boundary - previousBoundary) / 2);

                if (index < dayBoundaries.length) {
                    xAxisAnnotations.push({
                        x: this.chartOptions.xaxis.categories[midPoint],
                        strokeDashArray: 0,
                        borderColor: 'transparent', // Quitamos la línea vertical
                        label: {
                            borderColor: 'transparent',
                            style: {
                                color: '#e5e7eb',
                                background: '#374151',
                                padding: { left: 10, right: 10, top: 2, bottom: 2 }
                            },
                            text: index === 0 ? 'I N G' : `Día ${index}`,
                            position: 'top',
                            orientation: 'horizontal',
                            offsetY: -15
                        }
                    });
                }
                previousBoundary = boundary;
            });

            // Añadimos las líneas verticales divisorias por separado
            dayBoundaries.forEach((boundary, index) => {
                if (boundary < this.temperatureData.length) {
                    xAxisAnnotations.push({
                        x: this.chartOptions.xaxis.categories[boundary],
                        strokeDashArray: 0,
                        borderColor: 'rgba(255, 255, 255, 0.15)',
                        label: {
                            borderColor: 'transparent',
                            style: {
                                background: 'transparent'
                            },
                            text: '',
                        }
                    });
                }
            });

            this.chartOptions.annotations.xaxis = xAxisAnnotations;
        },
    },
});
</script>
