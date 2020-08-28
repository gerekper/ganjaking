<template>
	<div class="searchwp-metrics__insight">
		<span v-if="icon" :class="'dashicons dashicons-' + icon"></span>
		<div class="searchwp-metrics__engine-suggestions-insight-content">
			<p><component :is="translatedCopy" v-bind="$props"></component>
				<button
					v-if="buttonText" class="searchwp-metrics-nonbutton"
					@click="$emit('onclick')">
					{{ buttonText }}
				</button>
			</p>
		</div>
	</div>
</template>

<script>
import Vue from 'vue';

export default {
	props: {
		query: String,
		clickCount: Number,
		postCount: Number,
		icon: {
			type: String,
			default: 'arrow-right'
		},
		buttonText: {
			type: String,
			default: _SEARCHWP_METRICS_VARS.i18n.details
		}
	},
	computed: {
		translatedCopy() {
			return {
				template: '<span>' + this.i18n.insightAnalysis + '</span>',
				props: this.$options.props
			}
		}
	},
	data () {
		return {
			i18n: {
				insightAnalysis: _SEARCHWP_METRICS_VARS.i18n.insight_analysis
			}
		}
	}
}
</script>

<style lang="scss">

</style>
