<template>
    <div class="bed-status-chart-container">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200/60 dark:border-gray-700/60">
            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">
                Distribución de Camas por Estado
            </h3>
            <div id="bedStatusPieChart" class="w-full h-80"></div>
        </div>
    </div>
</template>

<script>
import ApexCharts from 'apexcharts';
import { ref } from 'vue';

export default {
    name: 'BedStatusPieChart',
    props: {
        statusData: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            chart: null,
            statusColors: {
                'Disponible': '#71DD37',      // Verde
                'Ocupada': '#696CFF',         // Morado
                'En limpieza': '#FFAB00',     // Amarillo
                'Fuera de servicio': '#FC4C51',   // Rojo
            }
        };
    },
    mounted() {
        this.initChart();
    },
    methods: {
        getStatusColors() {
            return this.statusData.map(item => {
                return this.statusColors[item.status] || '#8592A3';
            });
        },
        initChart() {
            const options = {
                series: this.statusData.map(item => item.count),
                chart: {
                    type: 'pie',
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
                    width: 0,
                    colors: this.isDarkMode() ? '#1F2937' : '#FFFFFF',
                },
                labels: this.statusData.map(item => item.status),
                colors: this.getStatusColors(),
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                        dataLabels: {
                            offset: 5,
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        return opts.w.config.series[opts.seriesIndex];
                    },
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold',
                        colors: ['#fff']
                    },
                    background: {
                        enabled: false
                    },
                    dropShadow: {
                        enabled: false,
                    }
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
                            return val + ' camas';
                        },
                        title: {
                            formatter: function (seriesName) {
                                return seriesName + ':';
                            }
                        }
                    }
                }
            };

            this.chart = new ApexCharts(document.querySelector('#bedStatusPieChart'), options);
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
                this.chart.updateSeries(this.statusData.map(item => item.count));
                this.chart.updateOptions({
                    labels: this.statusData.map(item => item.status),
                    colors: this.getStatusColors(),
                    foreColor: this.isDarkMode() ? '#e2e8f0' : '#4b5563',
                    tooltip: {
                        theme: this.isDarkMode() ? 'dark' : 'light'
                    }
                });
            }
        }
    },
    watch: {
        statusData: {
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
