<template>
    <line-chart ref="monthly_chart" :chart-data="line_chart_data.watering_entry" :options="options_watering_entry" :width="700" :height="500">
    </line-chart>
</template>

<script>
import LineChart from './LineChart.vue';

export default {
    components: {
        LineChart
    },
    data() {
        return {
            line_chart_data: {watering_entry: {}},
            options_watering_entry: {
                title: {
                    display: true,
                    text: 'Pressure graph per time'
                },
                elements: {
                    point: {
                        radius: 0,
                    }
                },
                responsive: true,
            },
        }
    },
    methods: {
        updateChart(chart_data) {
            this.line_chart_data = {
				counter_data: {
	                labels: labels,
	                datasets: [{
	                    data: defaultData,
	                    label: "Pressure Reading (Bar)",
	                    borderColor: "#3e95cd",
	                    fill: false,
	                    lineTension: 0.0,
	                }]
	            }
            }
        }
    }
}
</script>

<style>

</style>
