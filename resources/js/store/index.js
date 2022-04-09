import { getField, updateField } from 'vuex-map-fields';
import {stateMerge} from 'vue-object-merge'
import Vuex from 'vuex';

export default {
    state: {
        stateData: {},
    },

    getters: {
        getField,
        getStateData(state){ //take parameter state
            return state.stateData
        },
    },
    mutations: {
        updateField,
        stateData(state, payload) {
            return state.stateData = Object.assign({}, state.stateData, payload.stateData)
        },
        MERGE(state, value) {
			stateMerge(state, value.changes, null, value.ignoreNull)
		},
    }
}
