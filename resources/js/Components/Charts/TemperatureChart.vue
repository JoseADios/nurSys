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
        height: {
            type: Number,
            default: 250,
        }
    },
    setup(props) {
        const chart = ref(null);
        const isDarkMode = ref(false);

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
                foreColor: isDarkMode.value ? '#e5e7eb' : '#374151',
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
                    color: isDarkMode.value ? '#f3f4f6' : '#1f2937',
                },
            },
            stroke: {
                curve: 'smooth',
                width: 3,
            },
            colors: ['#3b82f6'],
            grid: {
                borderColor: isDarkMode.value ? '#374151' : '#e5e7eb',
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
                        colors: isDarkMode.value ? '#e5e7eb' : '#374151',
                        fontSize: '11px',
                    },
                    rotate: -45,
                    rotateAlways: false,
                },
                axisBorder: {
                    color: isDarkMode.value ? '#4b5563' : '#d1d5db',
                },
                axisTicks: {
                    color: isDarkMode.value ? '#4b5563' : '#d1d5db',
                },
            },
            yaxis: {
                title: {
                    text: 'Temperatura (°C)',
                    style: {
                        color: isDarkMode.value ? '#e5e7eb' : '#374151',
                        fontSize: '12px',
                    },
                },
                labels: {
                    style: {
                        colors: isDarkMode.value ? '#e5e7eb' : '#374151',
                        fontSize: '11px',
                    },
                },
            },
            annotations: {
                yaxis: [
                    {
                        y: 37,
                        borderColor: '#ff0000',
                        strokeDashArray: 0, // Asegurarse de que la línea no sea discontinua
                        borderWidth: 2, // Usar borderWidth para aumentar el grosor de la línea roja
                        label: {
                            borderColor: '#ff0000',
                            style: {
                                color: '#fff',
                                background: '#ff0000',
                            },
                        }
                    }
                ],
                xaxis: []
            },
            tooltip: {
                theme: isDarkMode.value ? 'dark' : 'light',
                y: {
                    formatter: (value, { dataPointIndex }) => {
                        const evacuation = props.temperatureData[dataPointIndex].evacuations;
                        const urination = props.temperatureData[dataPointIndex].urinations;
                        const nurse = props.temperatureData[dataPointIndex].nurse;
                        const nurseName = `${nurse.name} ${nurse.last_name}`;
                        return `Temperatura: ${value} °C<br>Evacuaciones: ${evacuation}<br>Micciones: ${urination}<br>Enfermero: ${nurseName}`;
                    }
                }
            },
            markers: {
                size: 4,
                colors: ['#3b82f6'],
                strokeColors: isDarkMode.value ? '#fff' : '#000',
                strokeWidth: 2,
            },
        }));

        const chartSeries = ref([
            {
                name: '',
                data: [],
            },
        ]);

        const processTemperatureData = () => {
            if (!props.temperatureData.length) return;

            const dateObjects = props.temperatureData.map(item => new Date(item.updated_at));

            chartOptions.value.xaxis.categories = dateObjects.map(date =>
                date.toLocaleString('es-ES', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })
            );

            chartSeries.value[0].data = props.temperatureData.map(item => item.temperature);

            const xAxisAnnotations = [];
            const firstDate = new Date(props.temperatureData[0].updated_at);
            let lastProcessedDayStr = '';

            props.temperatureData.forEach((item, index) => {
                const date = new Date(item.updated_at);
                const dayStr = date.toISOString().split('T')[0];

                if (dayStr !== lastProcessedDayStr) {
                    const diffTime = Math.abs(date - firstDate);
                    const dayNumber = Math.floor(diffTime / (1000 * 60 * 60 * 24));

                    xAxisAnnotations.push({
                        x: chartOptions.value.xaxis.categories[index],
                        strokeDashArray: 0,
                        borderColor: isDarkMode.value ? 'rgba(255, 255, 255, 0.15)' : 'rgba(0, 0, 0, 0.15)',
                        label: {
                            borderColor: 'transparent',
                            style: {
                                color: isDarkMode.value ? '#e5e7eb' : '#374151',
                                background: isDarkMode.value ? '#374151' : '#e5e7eb',
                                padding: { left: 10, right: 10, top: 2, bottom: 2 }
                            },
                            text: index === 0 ? 'I N G' : `Día ${dayNumber + 1}`,
                            position: 'top',
                            orientation: 'horizontal',
                            offsetY: -15
                        }
                    });

                    lastProcessedDayStr = dayStr;
                }
            });

            chartOptions.value.annotations.xaxis = xAxisAnnotations;
        };
        watchEffect(() => {
            processTemperatureData();
        });

        // Detectar el modo oscuro/claro
        const detectDarkMode = () => {
            isDarkMode.value = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        };

        detectDarkMode();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', detectDarkMode);

        return {
            chart,
            chartOptions,
            chartSeries,
            getChartImage,
        };
    },
});
</script>
