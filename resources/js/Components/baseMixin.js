import axios from "axios";
import { mapState } from "vuex";
import { mapFields } from "vuex-map-fields";

export default {
    computed: {
        ...mapState(["state_data", "schedule", "control_device", "sensor_device"]),
    }
}
