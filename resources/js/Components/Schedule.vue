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
          <td><b>Watering Time</b></td>
          <td><b>Watering Duration</b></td>
        </tr>
        <tr v-for="(weekday, index) in this.weekmap2" :key="index">
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
              v-model="schedule.watering_weekdays_time[index]"
            />
          </td>
          <td>
            <input
              type="text"
              :id="'watering_weekdays_duration_' + index"
              :disabled="!schedule.watering_weekdays[index]"
              class="form-control"
              :name="'watering_weekdays_duration[' + index + ']'"
              v-model="schedule.watering_weekdays_duration[index]"
            />
          </td>
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
import { mapState } from "vuex";
import { mapFields } from "vuex-map-fields";

export default {
  name: "schedule",
  props: ["schedule_in", "weekmap"],
  data() {
    return {
      weekmap2: {},
    };
  },
  computed: {
    ...mapState(["schedule"]),
  },
  created() {
    this.weekmap2 = this.weekmap;
    // this.schedule = this.schedule_in;
    this.$store.commit("schedule", {
      schedule: this.schedule_in,
      type: "schedule",
    });
  },
  mounted() {
    console.log(this.schedule_in);
  },
  methods: {
    createSchedule() {
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
