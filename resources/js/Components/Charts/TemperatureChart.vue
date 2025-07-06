<template>
    <div>
        <apexchart ref="chart" type="line" :options="chartOptions" :series="chartSeries"></apexchart>
    </div>
</template>

<script>
import { defineComponent, ref, computed, watchEffect, nextTick } from 'vue';
import ApexCharts from 'vue3-apexcharts';
import moment from 'moment/moment';
import 'moment/locale/es';

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
        const isDarkMode = ref(null);

        if (localStorage.getItem('darkMode') === 'true') {
            isDarkMode.value = true;
        } else if (localStorage.getItem('darkMode') === 'false') {
            isDarkMode.value = false;
        }

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
                toolbar: {
                    show: false
                },
                redrawOnWindowResize: true,
                redrawOnParentResize: true,
                animations: {
                    enabled: true
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
                borderColor: isDarkMode.value ? '#374151' : 'rgba(209, 213, 219, 0.3)',
                strokeDashArray: 5,
                padding: {
                    top: 20,
                    right: 10,
                    bottom: 0,
                    left: 10
                },
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    style: {
                        colors: isDarkMode.value ? '#e5e7eb' : '#1f2937',
                        fontSize: '11px',
                    },
                    datetimeUTC: false,
                },
                axisBorder: {
                    color: isDarkMode.value ? '#4b5563' : '#e5e7eb',
                },
                axisTicks: {
                    color: isDarkMode.value ? '#4b5563' : '#e5e7eb',
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
                yaxis: [
                    {
                        y: 37,
                        borderColor: '#ff0000',
                        strokeDashArray: 0,
                        borderWidth: 2,
                        label: {
                            borderColor: '#ff0000',
                            style: {
                                background: isDarkMode.value ? '#374151' : '#ffffff',
                                border: '1px solid #e5e7eb'
                            }
                        }
                    }
                ],
                xaxis: []
            },
            tooltip: {
                theme: isDarkMode.value ? 'dark' : 'light',
                // CAMBIO 1: Configuración más permisiva para el tooltip
                intersect: true, // Cambiado a false para mejor detección
                shared: false,
                followCursor: false, // Hace que el tooltip siga el cursor
                // CAMBIO 2: Configuración específica para markers
                marker: {
                    show: true,
                },
                x: {
                    formatter: function (val) {
                        return moment(val).format('DD/MM/YYYY HH:mm');
                    }
                },
                y: {
                    formatter: (value, { series, seriesIndex, dataPointIndex, w }) => {
                        // CAMBIO 3: Lógica mejorada para manejar índices
                        let originalDataIndex = dataPointIndex;

                        // Si hay más de un punto y agregamos el punto null inicial
                        if (props.temperatureData.length > 1) {
                            originalDataIndex = dataPointIndex - 1;
                        }

                        // Verificar que el índice sea válido
                        if (originalDataIndex >= 0 && originalDataIndex < props.temperatureData.length) {
                            const item = props.temperatureData[originalDataIndex];
                            const evacuation = item.evacuations;
                            const urination = item.urinations;
                            const nurse = item.nurse;
                            const nurseName = `${nurse.name} ${nurse.last_name}`;
                            return `Temperatura: ${value} °C<br>Evacuaciones: ${evacuation}<br>Micciones: ${urination}<br>Enfermero: ${nurseName}`;
                        }
                        return `Temperatura: ${value} °C`;
                    }
                }
            },
            markers: {
                size: 8, // Aumenté el tamaño
                colors: ['#696CFF'],
                strokeColors: isDarkMode.value ? '#fff' : '#e5e7eb',
                strokeWidth: 2,
                fillOpacity: 1,
                strokeOpacity: 0.9,
                shape: "circle",
                radius: 2,
                discrete: [],
                showNullDataPoints: false,
                // CAMBIO 5: Configuración adicional para hover
                // hover: {
                //     size: props.temperatureData.length === 1 ? 12 : 8, // Más grande en hover
                //     sizeOffset: 3
                // }
            },
            // CAMBIO 6: Configuración adicional para un solo punto
            plotOptions: {
                line: {
                    isSlopeChart: false,
                }
            },
            responsive: [
                {
                    breakpoint: 600,
                    options: {
                        chart: {
                            height: 200,
                        },
                        xaxis: {
                            labels: {
                                show: false,
                            }
                        },
                        markers: {
                            size: 4,
                        },
                    },
                },
            ],
        }));

        const chartSeries = ref([
            {
                name: '',
                data: [],
            },
        ]);

        const processTemperatureData = () => {
            if (!props.temperatureData.length) return;

            const firstDate = moment(props.temperatureData[0].updated_at);
            const seriesData = props.temperatureData.map(item => {
                return [new Date(item.updated_at).getTime(), item.temperature];
            });

            // CAMBIO 7: Solo agregar el punto null si hay más de un punto de datos
            if (props.temperatureData.length > 1) {
                seriesData.unshift([firstDate.startOf('day').valueOf(), null]);
            }

            chartSeries.value[0].data = seriesData;

            const xAxisAnnotations = [];

            // Solo mostrar etiquetas de días si hay más de un punto
            if (props.temperatureData.length > 1) {
                let lastProcessedDayStr = '';

                props.temperatureData.forEach((item, index) => {
                    const date = moment(item.updated_at);
                    const dayStr = date.format('YYYY-MM-DD');

                    if (dayStr !== lastProcessedDayStr) {
                        const dayNumber = date.startOf('day').diff(firstDate.startOf('day'), 'days') - 1;

                        xAxisAnnotations.push({
                            x: moment(item.updated_at).startOf('day').valueOf(),
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
            }

            chartOptions.value.annotations.xaxis = xAxisAnnotations;

            // Forzar actualización del gráfico después de procesar los datos
            nextTick(() => {
                if (chart.value?.chart) {
                    chart.value.chart.updateSeries(chartSeries.value, true);
                }
            });
        };

        watchEffect(() => {
            processTemperatureData();
        });

        return {
            chart,
            chartOptions,
            chartSeries,
            getChartImage,
        };
    },
});
</script>
