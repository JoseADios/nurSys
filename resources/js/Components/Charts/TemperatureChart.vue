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
            let currentDay = '';
            let dayCounter = 0;

            this.temperatureData.forEach((item, index) => {
                const date = new Date(item.created_at);
                const dayStr = `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}`;

                if (dayStr !== currentDay) {
                    if (index > 0) {
                        // Colocar la anotación en el índice medio entre los días
                        const midIndex = index - Math.floor((index - (this.temperatureData.findIndex(d => d.created_at === item.created_at) + 1)) / 2);

                        xAxisAnnotations.push({
                            x : this.chartOptions.xaxis.categories[midIndex],
                            strokeDashArray : 0,
                            borderColor : 'rgba(255, 255, 255, 0.15)',
                            label : {
                                borderColor : 'transparent',
                                style : {
                                    color : '#e5e7eb',
                                    background : '#374151',
                                    padding : { left : 10, right : 10, top : 2, bottom : 2 }
                                },
                                text : `Día ${dayCounter}`,
                                position : 'top',
                                text : dayCounter === 0 ? 'I N G' : `Día ${dayCounter}`,
                                position : 'top',
                                orientation : 'horizontal'
                            }
                        });

                        // Incrementar el contador de días
                        dayCounter++;

                        // Ajustar la posición Y para centrar la etiqueta
                        xAxisAnnotations[xAxisAnnotations.length - 1].label.offsetY = -15; // Ajusta este valor según sea necesario
                    }

                    currentDay = dayStr;
                }
            });

            this.chartOptions.annotations.xaxis = xAxisAnnotations;
        },
    },
});
</script>
