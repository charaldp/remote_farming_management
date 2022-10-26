<template>
    <div class="card">
        <div class="card-header" @click="openChart"><b>{{watering_entry_created_at}}</b></div>
        <div class="card-body" v-show="opened">
            <line-chart
                ref="readings_chart"
                :chart-data="line_chart_data.pressure_data"
                :options="options_watering_entry"
                :width="700"
                :height="500">
            </line-chart>
        </div>
    </div>
</template>

<script>
import LineChart from './LineChart.vue';
import baseMixin from './baseMixin.js';

export default {
    mixins: [baseMixin],
    components: {
        LineChart
    },
    props: [
        'sensor_device_id',
        'watering_entry_created_at',
        'watering_entry_id',
    ],
    data() {
        return {
            sensor_readings_data: {},
            line_chart_data: {pressure_data: {}},
            options_watering_entry: {
                title: {
                    display: true,
                    text: 'Pressure graph per time'
                },
                elements: {
                    point: {
                        radius: 1,
                    }
                },
                responsive: true,
            },
            opened: false,
        }
    },
    mounted() {
        if (this.control_device.watering_entry_id == this.watering_entry_id ) {
            this.openChart();
        }
        Echo.channel('control_device.'+this.control_device.id+'.sensor_reading')
            // .here(user => {
            //     this.users = user;
            // })
            // .joining(user => {
            //     this.users.push(user);
            // })
            // .leaving(user => {
            //     this.users = this.users.filter(u => u.id != user.id);
            // })
            .listen('SensorReadingAdded',(event) => {
                this.sensor_readings_data = event.sensor_readings_data;
                // this.$store.commit('state_data', {state_data: {sensor_readings_data: event.sensor_readings_data}});
                this.updateChart();
            })
            // .listenForWhisper('typing', user => {
            //     this.activeUser = user;
            //     if(this.typingTimer) {
            //         clearTimeout(this.typingTimer);
            //     }
            //     this.typingTimer = setTimeout(() => {
            //         this.activeUser = false;
            //     }, 3000);
            // })
        this
    },
    methods: {
        openChart() {
            if (this.opened) {
                this.opened = false;
                return;
            }
            this.opened = true;
            axios
            .get(`/control_device/${this.control_device.id}/watering_entry/${this.watering_entry_id}/sensor_device/${this.sensor_device_id}/sensor_readings`)
            .then(
                function (response) {
                    this.sensor_readings_data = response.data.sensor_readings_data;
                    // this.$store.commit('state_data', {state_data: {sensor_readings_data: response.data.sensor_readings_data}});
                    this.updateChart();
                }.bind(this)
            )
            .catch((err) => console.log(err));
        },
        updateChart() {
            this.line_chart_data = {
				pressure_data: {
	                labels: JSON.parse(JSON.stringify(this.sensor_readings_data.labels)),
	                datasets: [{
	                    data: JSON.parse(JSON.stringify(this.sensor_readings_data.sensor_readings)),
	                    label: "Pressure Reading (Bar)",
	                    borderColor: "#3e95cd",
	                    fill: false,
	                    lineTension: 0.2,
	                }]
	            }
            }
        }

    }
}
</script>

<style>

</style>
