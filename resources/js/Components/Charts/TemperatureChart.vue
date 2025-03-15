<template>
    <div>
        <apexchart ref="chart" type="line" :options="chartOptions" :series="chartSeries"></apexchart>
    </div>
</template>

<script>
import { defineComponent, ref, computed, watchEffect } from 'vue';
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
        eliminationData: {
            type: Array,
            required: true,
        },
        height: {
            type: Number,
            default: 250,
        }
    },
    setup(props) {
        const chart = ref(null);
        const isDarkMode = ref(localStorage.getItem('darkMode') === 'true');

        const getChartImage = async () => {
            if (chart.value?.chart) {
                return await chart.value.chart.dataURI();
            }
            return null;
        };

        const chartOptions = computed(() => ({
            chart: {
                height: props.height,
                type: 'line',
                background: 'transparent',
                foreColor: isDarkMode.value ? '#e5e7eb' : '#1f2937',
                toolbar: { show: false }
            },
            title: {
                text: 'Temperaturas Registradas',
                align: 'center',
                style: {
                    fontSize: '18px',
                    fontWeight: 'bold',
                    color: isDarkMode.value ? '#f3f4f6' : '#1f2937',
                },
            },
            stroke: { curve: 'smooth', width: 3 },
            colors: ['#3b82f6'],
            grid: {
                borderColor: isDarkMode.value ? '#374151' : 'rgba(209, 213, 219, 0.3)',
                strokeDashArray: 5,
            },
            xaxis: {
                categories: [],
                labels: {
                    style: {
                        colors: isDarkMode.value ? '#e5e7eb' : '#1f2937',
                        fontSize: '11px',
                    },
                    rotate: -45,
                },
            },
            yaxis: {
                title: {
                    text: 'Temperatura (°C)',
                    style: {
                        color: isDarkMode.value ? '#e5e7eb' : '#1f2937',
                        fontSize: '12px',
                    },
                },
                labels: {
                    style: {
                        colors: isDarkMode.value ? '#e5e7eb' : '#1f2937',
                        fontSize: '11px',
                    },
                },
            },
            annotations: {
                yaxis: [{
                    y: 37,
                    borderColor: '#ff0000',
                    strokeDashArray: 0,
                    borderWidth: 2,
                    label: {
                        borderColor: '#ff0000',
                        style: { background: isDarkMode.value ? '#374151' : '#ffffff' }
                    }
                }],
                xaxis: []
            },
            tooltip: {
                theme: isDarkMode.value ? 'dark' : 'light',
                y: {
                    formatter: (value, { dataPointIndex }) => {
                        const timestamp = props.temperatureData[dataPointIndex].updated_at;
                        const elimination = props.eliminationData.find(item => item.updated_at === timestamp);
                        const evacuations = elimination ? elimination.evacuations : 'N/A';
                        const urinations = elimination ? elimination.urinations : 'N/A';
                        const nurse = props.temperatureData[dataPointIndex].nurse;
                        const nurseName = `${nurse.name} ${nurse.last_name}`;
                        return `Temperatura: ${value} °C<br>Evacuaciones: ${evacuations}<br>Micciones: ${urinations}<br>Enfermero: ${nurseName}`;
                    }
                }
            },
            markers: { size: 4, colors: ['#3b82f6'], strokeColors: isDarkMode.value ? '#fff' : '#e5e7eb', strokeWidth: 2 }
        }));

        const chartSeries = ref([{ name: 'Temperatura', data: [] }]);

        const processTemperatureData = () => {
            if (!props.temperatureData.length) return;

            const dateObjects = props.temperatureData.map(item => new Date(item.updated_at));
            chartOptions.value.xaxis.categories = dateObjects.map(date =>
                date.toLocaleString('es-ES', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })
            );

            chartSeries.value[0].data = props.temperatureData.map(item => item.temperature);

            const xAxisAnnotations = [];
            let lastProcessedDay = '';
            let dayCounter = 1;

            props.temperatureData.forEach((item, index) => {
                const date = new Date(item.updated_at);
                const currentDay = date.getDate();
                const currentMonth = date.getMonth();
                const currentYear = date.getFullYear();
                const dayKey = `${currentYear}-${currentMonth}-${currentDay}`;

                if (dayKey !== lastProcessedDay) {
                    xAxisAnnotations.push({
                        x: chartOptions.value.xaxis.categories[index],
                        strokeDashArray: 0,
                        borderColor: isDarkMode.value ? 'rgba(255, 255, 255, 0.15)' : 'rgba(0, 0, 0, 0.15)',
                        label: {
                            borderColor: 'transparent',
                            style: { color: isDarkMode.value ? '#e5e7eb' : '#374151', background: isDarkMode.value ? '#374151' : '#e5e7eb' },
                            text: `Día ${dayCounter}`,
                            position: 'top',
                            orientation: 'horizontal',
                            offsetY: -15
                        }
                    });
                    lastProcessedDay = dayKey;
                    dayCounter++;
                }
            });

            chartOptions.value.annotations.xaxis = xAxisAnnotations;
        };

        watchEffect(() => { processTemperatureData(); });

        return { chart, chartOptions, chartSeries, getChartImage };
    },
});
</script>
