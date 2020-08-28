<template>
	<div class="searchwp-metrics-chart searchwp-metrics-chart--bar">
		<bar-chart
			:chart-data="datacollection"
			:options="options"
		></bar-chart>
	</div>
</template>

<script>
	import BarChart from '../BarChart.js'

	export default {
	components: {
		BarChart
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
	.searchwp-metrics-chart--bar {
		width: 100%;
		position: relative;
	}
</style>
