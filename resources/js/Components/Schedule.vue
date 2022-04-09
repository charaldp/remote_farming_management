<template>
    <div>
        <div class="form-group col-md-12">
            <label class="col-md-6 control-label" for="schedule_name">{{'Schedule Name'}}</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="name" id="schedule_name" v-model="stateData.schedule.name"/>
            </div>
            <label class="col-md-2 control-label" for="schedule_name">{{''}}</label>
        </div>
        <div class="form-group col-md-12">
            <table>
                <tr>
                    <td><b>WeekDay</b></td>
                    <td><b>Selected</b></td>
                    <td><b>Watering Frequency</b></td>
                    <td><b>Watering Time</b></td>
                    <td><b>Watering Duration</b></td>
                </tr>
                <tr v-for="(weekday, index) in this.weekmap" :key="index">
                    <td><b>{{index}}</b></td>
                    <td><input type="checkbox" v-model="stateData.schedule.watering_weekdays[index]" @click="clickDay(index, $event)"/></td>
                    <td><input type="text" :id="'watering_weekdays_frequency_'+index" :disabled="!stateData.schedule.watering_weekdays[index]" class="form-control" :name="'watering_weekdays_frequency['+index+']'"  v-model="stateData.schedule.watering_weekdays_frequency[index]"/></td>
                    <td><input type="text" :id="'watering_weekdays_time_'+index" :disabled="!stateData.schedule.watering_weekdays[index]" class="form-control" :name="'watering_weekdays_time['+index+']'" v-model="stateData.schedule.watering_weekdays_time[index]"/></td>
                    <td><input type="text" :id="'watering_weekdays_duration_'+index" :disabled="!stateData.schedule.watering_weekdays[index]" class="form-control" :name="'watering_weekdays_duration['+index+']'" v-model="stateData.schedule.watering_weekdays_duration[index]"/></td>
                </tr>
            </table>
        </div>
        <div>
            <button type="submit" @click="createSchedule()">Create</button>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    import {mapState} from 'vuex';
    import {mapFields} from 'vuex-map-fields';

    export default {
        name: "schedule",
        props: [
            'schedule_in',
            'weekmap',
        ],
        computed: {
            ...mapState(['stateData']),
        },
        created() {
            this.$store.commit('MERGE', {changes: {stateData: {schedule: this.schedule_in}}, type: 'schedule'});
        },
        mounted() {
        },
        methods: {
            createSchedule() {
                axios.post('/schedule/store', this.stateData.schedule
                ).then(
                    function (response) {
                        this.$store.commit('MERGE', {changes: {stateData :{schedule: response}}, type: 'schedule'});
                    }.bind(this)
                ).catch( err => console.log(err));
            },
            clickDay(index, event) {
                var watering_weekdays = {};
                watering_weekdays[index] = event.target.checked?true:undefined;
                this.$store.commit('MERGE', {changes: {stateData: {schedule: {watering_weekdays: watering_weekdays}}}, type: 'schedule'});
                console.log(this.stateData.schedule);
            }
        }
    }
</script>
