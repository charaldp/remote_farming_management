<template>
    <div>
        <a :class="button_class" @click="changeIsOnState" v-html="button_message"></a>
        <watering-entry></watering-entry>
    </div>
</template>

<script>
import baseMixin from './baseMixin.js';
import { mapFields } from "vuex-map-fields";

export default {
    name: "control-device",
    mixins: [baseMixin],
    props: [
        'control_device_in'
    ],
    computed: {
        button_class() {
            return (this.is_on?'btn btn-danger':'btn btn-success');
        },
        button_message() {
            return (this.is_on? 'Turn Off': 'Turn On');
        },
        ...mapFields(['control_device.is_on'])
    },
    created() {
        this.$store.commit("control_device", {
            control_device: this.control_device_in,
            type: "control_device",
        });
    },
    methods: {
        changeIsOnState() {
            this.editCreateControlDevice(!this.control_device.is_on);
        },
        editCreateControlDevice(patch_is_on = null) {
            if (this.control_device.at_creation) {
                axios
                .post("/control_device/store", this.control_device)
                .then(
                function (response) {
                    this.$store.commit("control_device", {
                    control_device: {...response.data, at_creation: false},
                    type: "control_device",
                    });
                }.bind(this)
                )
                .catch((err) => console.log(err));
            } else {
                var sent_data = JSON.parse(JSON.stringify(this.control_device));
                if (patch_is_on !== null) {
                    sent_data.is_on = patch_is_on;
                }
                axios
                .patch(`/control_device/${this.control_device.id}/update`, sent_data)
                .then(
                function (response) {
                    this.$store.commit("control_device", {
                    control_device: response.data,
                    type: "control_device",
                    });
                }.bind(this)
                )
                .catch((err) => console.log(err));
            }
        },

    }
}
</script>
