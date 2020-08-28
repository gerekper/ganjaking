<template>
	<div class="searchwp-metrics-chart searchwp-metrics-chart--doughnut">
		<doughnut-chart
			:chart-data="datacollection"
			:options="options"
		></doughnut-chart>
	</div>
</template>

<script>
	import DoughnutChart from '../DoughnutChart.js'

	export default {
	components: {
		DoughnutChart
	},
	props: {
		datacollection: Object,
		options: {
			type: Object,
			default: {}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-metrics-chart--doughnut {
		width: 150px;
		height: 150px;
		position: relative;
	}
</style>
