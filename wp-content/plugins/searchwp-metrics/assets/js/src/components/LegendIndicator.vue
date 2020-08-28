<template>
	<span class="searchwp-metrics-legend-item">
		<span
			:title="text"
			class="searchwp-metrics-legend-indicator"
			v-bind:style="{ backgroundColor: color }"
		></span>
		<span
			class="searchwp-metrics-legend-label"
			:title="text"
			@click="$emit('onclick')">
			<span>{{ text }}</span>
		</span>
	</span>
</template>

<script>
import Vue from 'vue';

export default {
	props: {
		index: Number,
		text: String
	},
	mounted () {
		this.color = Vue.SearchwpMetricsGetColor(this.index);
	},
	data () {
		return {
			color: '#ffffff'
		}
	}
}
</script>

<style lang="scss">
	.searchwp-metrics-legend-item {
		display: flex;
		align-items: center;
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
	}

	.searchwp-metrics-legend-indicator {
		display: block;
		width: 8px;
		height: 8px;
		border-radius: 50%;
	}

	.searchwp-metrics-legend-label {
		display: block;
		flex: 1;
		min-width: 0;

		span {
			display: block;
			padding-left: 0.5em;
			width: 100%;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	}
</style>
