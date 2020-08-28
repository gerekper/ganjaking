import { Line } from 'vue-chartjs';

export default {
	name: 'LineChart',
	props: {
		labels: Array,
		datasets: Array
	},
	extends: Line,
	mounted () {
		this.renderChart({
			labels: this.labels,
			datasets: this.datasets
		}, {
			responsive: true,
			maintainAspectRatio: false,
			legend: {
				display: false
			},
			scales: {
				xAxes: [{
					ticks: {
						maxTicksLimit: 9,
						maxRotation: 0
					}
				}]
			},
			layout: {
				padding: {
					left: 15, // This doesn't render pixel perfect, we want to match the heading above which is padded 20px.
					right: 20,
					top: 10,
					bottom: 10
				}
			}
		});
	}
}