<script>
import { Line, mixins } from 'vue-chartjs'
// Use Reactive prop method
export default {
  name: 'line-chart',
  extends: Line,
  props: ['options'],
  mixins: [mixins.reactiveProp],
  mounted () {
    this.renderChart(this.chartData, this.options)
  }
}
</script>
