<template>
	<div class="searchwp-metrics__insight">
		<span v-if="icon" :class="'dashicons dashicons-' + icon"></span>
		<div class="searchwp-metrics__engine-suggestions-insight-content">
			<p>
				<component
					v-if="1 === postCount"
					:is="translatedCopySingular"
				></component>
				<component
					v-else
					:is="translatedCopyPlural"
					v-bind="$props"
				></component>
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
		postCount: Number,
		icon: {
			type: String,
			default: 'sos'
		},
		buttonText: {
			type: String,
			default: _SEARCHWP_METRICS_VARS.i18n.details
		}
	},
	computed: {
		translatedCopySingular() {
			return {
				template: '<span>' + this.i18n.insightUnderdogSingular + '</span>'
			}
		},
		translatedCopyPlural() {
			return {
				template: '<span>' + this.i18n.insightUnderdogPlural + '</span>',
				props: this.$options.props
			}
		}
	},
	data () {
		return {
			i18n: {
				insightUnderdogPlural: _SEARCHWP_METRICS_VARS.i18n.insight_underdog_plural,
				insightUnderdogSingular: _SEARCHWP_METRICS_VARS.i18n.insight_underdog_singular,
			}
		}
	}
}
</script>

<style lang="scss">

</style>
