<template>
    <div>
        <div class="form-group col-md-12">
            <label class="col-md-6 control-label" for="schedule_name">{{'Schedule Name'}}</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="name" id="schedule_name" v-model="schedule.name"/>
            </div>
            <label class="col-md-2 control-label" for="schedule_name">{{}}</label>
        </div>
        <div class="form-group col-md-12">
            <label class="col-md-6 control-label" for="schedule_watering_weekdays">{{'Watering Weekdays'}}</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="watering_weekdays" id="schedule_watering_weekdays" v-model="schedule.watering_weekdays"/>
            </div>
            <label class="col-md-2 control-label" for="schedule_watering_weekdays">{{}}</label>
        </div>
        <div class="form-group col-md-12">
            <label class="col-md-6 control-label" for="schedule_watering_weekdays_frequency">{{'Watering Frequency'}}</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="watering_weekdays_frequency" id="schedule_watering_weekdays_frequency" v-model="schedule.watering_weekdays_frequency"/>
            </div>
            <label class="col-md-2 control-label" for="schedule_watering_weekdays_frequency">{{}}</label>
        </div>
        <div class="form-group col-md-12">
            <label class="col-md-6 control-label" for="schedule_watering_weekdays_time">{{'Watering Weekdays Time'}}</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="watering_weekdays_time" id="schedule_watering_weekdays_time" v-model="schedule.watering_weekdays_time"/>
            </div>
            <label class="col-md-2 control-label" for="schedule_watering_weekdays_time">{{}}</label>
        </div>
        <div class="form-group col-md-12">
            <label class="col-md-6 control-label" for="schedule_watering_weekdays_duration">{{'Watering Weekdays Duration'}}</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="watering_weekdays_duration" id="schedule_watering_weekdays_duration" v-model="schedule.watering_weekdays_duration"/>
            </div>
            <label class="col-md-2 control-label" for="schedule_watering_weekdays_duration">{{}}</label>
        </div>
        <div>
            <button type="submit" @click="createSchedule()">Create</button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "schedule",
        props: [
            'schedule_in',
        ],
        created() {
            this.$store.commit('MERGE', {changes: {schedule: this.schedule_in}, type: 'schedule'});
        },
        mounted() {
        },
        methods: {
            createSchedule() {

            }
        }
    }
</script>
