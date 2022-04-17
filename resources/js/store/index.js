import { getField, updateField } from 'vuex-map-fields';
import { stateMerge } from 'vue-object-merge'

export default {
    state: {
        schedule: {},
    },

    getters: {
        getField,
        getSchedule(state) {
            return state.schedule
        },
    },
    mutations: {
        updateField,
        schedule(state, payload) {
            return state.schedule = Object.assign({}, state.schedule, payload.schedule)
        },
        MERGE(state, value) {
            stateMerge(state, value.changes, null, value.ignoreNull)
        },
    }
}
