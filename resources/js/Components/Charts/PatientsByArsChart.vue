<template>
    <div class="insurance-chart-container">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200/60 dark:border-gray-700/60">
            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">
                Distribución de Pacientes por Tipo de Seguro
            </h3>
            <div id="insuranceDonutChart" class="w-full h-80"></div>
        </div>
    </div>
</template>

<script>
import ApexCharts from 'apexcharts';
import { ref } from 'vue';

export default {
    name: 'InsuranceDonutChart',
    props: {
        arsData: {
            type: Array,
            required: true
            // Formato esperado: [
            //   { name: 'Seguro Público', count: 125 },
            //   { name: 'Seguro Privado', count: 84 },
            //   { name: 'Sin Seguro', count: 31 }
            // ]
        }
    },
    data() {
        return {
            chart: null
        };
    },
    mounted() {
        this.initChart();
    },
    methods: {
        initChart() {
            const options = {
                series: this.arsData.map(item => item.count),
                chart: {
                    type: 'donut',
                    height: 320,
                    fontFamily: 'inherit',
                    foreColor: this.isDarkMode() ? '#e2e8f0' : '#4b5563',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                stroke: {
                        width: 4, // Esto elimina los bordes blancos
                        // Opcional: si quieres un color específico para los bordes en modo oscuro
                        colors: this.isDarkMode() ? '#1F2937' : '#FFFFFF',
                    },
                labels: this.arsData.map(item => item.name),
                colors: ['#71DD37', '#03C3EC', '#8592A3', '#696CFF', '#FFAB00', '#FF3E1D'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '80%',
                            background: 'transparent',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '22px',
                                    fontWeight: 600,
                                    offsetY: -10
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    fontWeight: 400,
                                    formatter: function (val) {
                                        return val;
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    formatter: function (seriesName, opts) {
                        return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]];
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 5
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 260
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                tooltip: {
                    enabled: true,
                    fillSeriesColor: false,
                    theme: this.isDarkMode() ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return val + ' pacientes';
                        },
                        title: {
                            formatter: function (seriesName) {
                                return seriesName + ':';
                            }
                        }
                    }
                }
            };

            this.chart = new ApexCharts(document.querySelector('#insuranceDonutChart'), options);
            this.chart.render();
        },
        isDarkMode() {
            // Detectar el modo oscuro
            let isDarkMode = null;

            if (localStorage.getItem('darkMode') === 'true') {
                isDarkMode = true;
            } else if (localStorage.getItem('darkMode') === 'false') {
                isDarkMode = false;
            }
            return isDarkMode;
        },
        updateChart() {
            if (this.chart) {
                this.chart.updateSeries(this.arsData.map(item => item.count));
                this.chart.updateOptions({
                    labels: this.arsData.map(item => item.name),
                    foreColor: this.isDarkMode() ? '#e2e8f0' : '#4b5563',
                    tooltip: {
                        theme: this.isDarkMode() ? 'dark' : 'light'
                    }
                });
            }
        }
    },
    watch: {
        insuranceData: {
            handler() {
                this.updateChart();
            },
            deep: true
        }
    },
    beforeDestroy() {
        if (this.chart) {
            this.chart.destroy();
        }
    }
};
</script>
