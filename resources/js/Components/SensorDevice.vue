<template>
    <div>
        <div class="form-group col-md-12">
            <label class="col-md-6 control-label" for="sensor_device">{{"Sensor Device Name"}}</label>
            <div class="col-md-4">
            <input
                type="text"
                class="form-control"
                name="name"
                id="sensor_device_name"
                v-model="sensor_device.name"
            />
            </div>
        </div>
        <div class="form-group col-md-12">
            <label class="col-md-12 control-label" for="sensor_device">{{"Sensor Reader Type"}}</label>
            <label class="col-md-12 radio" v-for="sensor_reader_type in this.sensor_reader_types" :key="sensor_reader_type+'_key'" :for="sensor_reader_type+'_id'">
                <input type="radio" v-model="sensor_device.type" class="uniform" name="bellow_forming" :value="sensor_reader_type"
                    :id="sensor_reader_type+'_id'"/>
                &nbsp;{{sensor_reader_type}}
            </label>
        </div>
        <div>
            <button type="submit" @click="editCreateSensorDevice()">{{submit_message}}</button>
        </div>
    </div>
</template>

<script>
import baseMixin from './baseMixin.js';

export default {
    name: "sensor-device",
    mixins: [baseMixin],
    props: [
        'sensor_device_in',
        'sensor_reader_types',
    ],
    computed: {
        submit_message() {
            return !this.sensor_device.at_creation ? 'Update': 'Create';
        },
    },
    created() {
        this.$store.commit("sensor_device", {
        sensor_device: this.sensor_device_in,
        type: "sensor_device",
        });
    },
    methods: {
        editCreateSensorDevice() {
            if (this.sensor_device.at_creation) {
                axios
                .post(`/control_device/${this.sensor_device.control_device_id}/sensor_device/store`, this.schedule)
                .then(
                function (response) {
                    this.$store.commit("sensor_device", {
                    sensor_device: {...response.data, at_creation: false},
                    type: "sensor_device",
                    });
                }.bind(this)
                )
                .catch((err) => console.log(err));
            } else {
                axios
                .patch(`/control_device/${this.sensor_device.control_device_id}/sensor_device/${this.sensor_device.id}/update`, this.sensor_device)
                .then(
                function (response) {
                    this.$store.commit("sensor_device", {
                    sensor_device: response.data,
                    type: "sensor_device",
                    });
                }.bind(this)
                )
                .catch((err) => console.log(err));
            }
        },
    }
}
</script>
