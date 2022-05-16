<template>
    <div>
        <div class="form-group col-md-12">
            <label class="col-md-6 control-label" for="schedule_name">{{
            "Schedule Name"
            }}</label>
            <div class="col-md-4">
            <input
                type="text"
                class="form-control"
                name="name"
                id="schedule_name"
                v-model="schedule.name"
            />
            </div>
        </div>
        <div class="form-group col-md-12">
            <table>
                <tr>
                    <td><b>WeekDay</b></td>
                    <td><b>Selected</b></td>
                    <td><b>Watering Frequency</b></td>
                    <td><b>Hour</b></td>
                    <td><b>Minute</b></td>
                    <td><b>Watering Hours</b></td>
                    <td><b>Watering Minute</b></td>
                </tr>
                <tr v-for="(weekday, index) in this.weekmap" :key="index">
                    <td>
                        <b>{{ index }}</b>
                    </td>
                    <td>
                        <input
                            type="checkbox"
                            :name="'watering_weekdays[' + index + ']'"
                            v-model="schedule.watering_weekdays[index]"
                            @click="clickDay(index, $event)"
                        />
                    </td>
                    <td>
                        <input
                            type="text"
                            :id="'watering_weekdays_frequency_' + index"
                            :disabled="!(schedule.watering_weekdays[index] == true)"
                            class="form-control"
                            :name="'watering_weekdays_frequency[' + index + ']'"
                            v-model="schedule.watering_weekdays_frequency[index]"
                        />
                    </td>
                    <td>
                        <input
                            type="text"
                            :id="'watering_weekdays_time_' + index"
                            :disabled="!schedule.watering_weekdays[index]"
                            class="form-control"
                            :name="'watering_weekdays_time[' + index + ']'"
                            v-model="schedule.watering_weekdays_time_hours[index]"
                            @change="editField(index, 'watering_weekdays_time_hours', $event.target.value)"
                        />
                    </td>
                    <td>
                        <input
                            type="text"
                            :id="'watering_weekdays_time_' + index"
                            :disabled="!schedule.watering_weekdays[index]"
                            class="form-control"
                            :name="'watering_weekdays_time[' + index + ']'"
                            v-model="schedule.watering_weekdays_time_minutes[index]"
                            @change="editField(index, 'watering_weekdays_time_minutes', $event.target.value)"
                        />
                    </td>
                    <td>
                        <input
                            type="text"
                            :id="'watering_weekdays_duration_' + index"
                            :disabled="!schedule.watering_weekdays[index]"
                            class="form-control"
                            :name="'watering_weekdays_duration[' + index + ']'"
                            v-model="schedule.watering_weekdays_duration_hours[index]"
                            @change="editField(index, 'watering_weekdays_duration_hours', $event.target.value)"
                        />
                    </td>
                    <td>
                        <input
                            type="text"
                            :id="'watering_weekdays_duration_' + index"
                            :disabled="!schedule.watering_weekdays[index]"
                            class="form-control"
                            :name="'watering_weekdays_duration[' + index + ']'"
                            v-model="schedule.watering_weekdays_duration_minutes[index]"
                            @change="editField(index, 'watering_weekdays_duration_minutes', $event.target.value)"
                        />
                    </td>
                </tr>
            </table>
        </div>
        <div>
            <button type="submit" @click="editCreateSchedule()">{{submit_message}}</button>
        </div>
    </div>
</template>

<script>
import baseMixin from './baseMixin.js';

export default {
    name: "schedule",
    props: ["schedule_in", "weekmap"],
    mixins: [baseMixin],
    data() {
        return {
        };
    },
    computed: {
        submit_message() {
            return !this.schedule.at_creation ? 'Edit': 'Create';
        },
    },
    created() {
        this.$store.commit("schedule", {
        schedule: this.schedule_in,
        type: "schedule",
        });
    },
    mounted() {
    },
    methods: {
        editField(day_index, field, value) {
            switch (field) {
                case 'watering_weekdays_time_minutes':
                case 'watering_weekdays_duration_minutes':
                    if (value > 60) {
                        var commit_obj = {changes: {schedule: {}}};
                        commit_obj[field] = {};
                        commit_obj[field][day_index] = 0;
                        this.$store.commit('MERGE', commit_obj)
                    }
                    break;
                case 'watering_weekdays_time_hours':
                case 'watering_weekdays_duration_hours':
                    if (value > 24) {
                        var commit_obj = {changes: {schedule: {}}};
                        commit_obj[field] = {};
                        commit_obj[field][day_index] = 0;
                        this.$store.commit('MERGE', commit_obj)
                    }
                    break;
            }
        },
        editCreateSchedule() {
            if (this.schedule.at_creation) {
                axios
                .post("/schedule/store", this.schedule)
                .then(
                function (response) {
                    this.$store.commit("schedule", {
                    schedule: response,
                    type: "schedule",
                    });
                }.bind(this)
                )
                .catch((err) => console.log(err));
            } else {
                axios
                .patch(`/schedule/${this.schedule.id}/update`, this.schedule)
                .then(
                function (response) {
                    this.$store.commit("schedule", {
                    schedule: response,
                    type: "schedule",
                    });
                }.bind(this)
                )
                .catch((err) => console.log(err));
            }
        },
        clickDay(index, event) {
            var schedule = {
                watering_weekdays: {},
                watering_weekdays_frequency: {},
                watering_weekdays_time: {},
                watering_weekdays_duration: {},
            };
            schedule.watering_weekdays[index] = event.target.checked;
            if (schedule.watering_weekdays[index]) {
                schedule.watering_weekdays_frequency[index] = "";
                schedule.watering_weekdays_time[index] = "";
                schedule.watering_weekdays_duration[index] = "";
            }
            this.$store.commit("MERGE", {
                changes: {
                schedule: schedule,
                },
                type: "schedule",
            });
            //   this.schedule.watering_weekdays = watering_weekdays;
            //   console.log(this.schedule);
        },
    },
};
</script>
