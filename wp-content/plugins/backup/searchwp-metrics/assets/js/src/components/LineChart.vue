<template>
	<div class="searchwp-metrics-chart searchwp-metrics-chart--line">
		<line-chart
			:chart-data="datacollection"
			:options="options"
		></line-chart>
	</div>
</template>

<script>
	import LineChart from '../LineChart.js'

	export default {
	components: {
		LineChart
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
	.searchwp-metrics-chart {
		width: 100%;
		height: 30px;
		position: relative;
	}
</style>
