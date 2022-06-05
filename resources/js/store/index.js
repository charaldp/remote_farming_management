import { getField, updateField } from 'vuex-map-fields';
import { stateMerge } from 'vue-object-merge'

export default {
    state: {
        schedule: {},
        control_device: {},
        sensor_device: {},
        state_data: {},
    },

    getters: {
        getField,
        getStateData(state) {
            return state.state_data
        },
        getSchedule(state) {
            return state.schedule
        },
        getControlDevice(state) {
            return state.control_device
        },
        getSensorDevice(state) {
            return state.sensor_device
        },
    },
    mutations: {
        updateField,
        state_data(state, payload) {
            return state.state_data = Object.assign({}, state.state_data, payload.state_data)
        },
        schedule(state, payload) {
            return state.schedule = Object.assign({}, state.schedule, payload.schedule)
        },
        control_device(state, payload) {
            return state.control_device = Object.assign({}, state.control_device, payload.control_device)
        },
        sensor_device(state, payload) {
            return state.sensor_device = Object.assign({}, state.sensor_device, payload.sensor_device)
        },
        MERGE(state, value) {
            stateMerge(state, value.changes, null, value.ignoreNull)
        },
    }
}
