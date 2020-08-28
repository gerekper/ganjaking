<template>
	<div class="searchwp-attribute">
		<dl>
			<dt>
				<span :title="label">
					<slot></slot>
				</span>
			</dt>
			<dd>
				<span>{{ 'Min' | i18n }}</span>
				<VueSlider
					v-model="currentValue"
					:value="value"
					:lazy="true"
					:adsorb="true"
					:marks="marks"
					:contained="true"
					:hide-label="true"
					:data="weights"
					:useKeyboard="true"
					:height="2"
					:railStyle="railStyle"
					:dotSize="10"
					:dotOptions="dotOptions"
					:processStyle="processStyle"
					:stepStyle="stepStyle"
					:stepActiveStyle="stepActiveStyle"
					:tooltip="'none'"
					:position="'right'"
					:tooltip-formatter="tooltipFormatter"
					@change="value => $emit('change', value)"
				></VueSlider>
				<span>{{ 'Max' | i18n }}</span>
			</dd>
		</dl>
	</div>
</template>

<script>
import Color from 'color';
import VueSlider from 'vue-slider-component';

export default {
	name: 'Attribute',
	components: {
		VueSlider
	},
	props: {
		value: {
			type: Number,
			required: true
		},
		label: {
			type: String,
			required: false
		}
	},
	created() {
		this.currentValue = this.value;
	},
	data () {
		return {
			currentValue: 0,
			weights: Object.keys(_SEARCHWP.weights).map(weight => parseInt(weight, 10)),
			tooltipFormatter: v => _SEARCHWP.weights[v],
			marks: function(value) {
				return _SEARCHWP.weights[value];
			},
			railStyle: {
				backgroundColor: _SEARCHWP.misc.colors.border,
				opacity: 0.7
			},
			dotOptions: {
				style: {
					backgroundColor: _SEARCHWP.misc.colors.current,
					borderColor: _SEARCHWP.misc.colors.current
				},
				focusStyle: {
					backgroundColor: _SEARCHWP.misc.colors.current,
					borderColor: _SEARCHWP.misc.colors.current,
					boxShadow: '0 0 0 3px ' + Color(_SEARCHWP.misc.colors.hover).hsl().lighten(0.6).fade(0.5).desaturate(0.6).string()
				},
			},
			processStyle: {
				backgroundColor: _SEARCHWP.misc.colors.current
			},
			stepStyle: {
				width: '4px',
				height: '4px',
				marginTop: '-1px', // The default size of the steps is 2px, but we made it 4px so we need this offset.
				marginLeft: '-1px',
				boxShadow: '0 0 0 2px ' + Color(_SEARCHWP.misc.colors.border).hsl().darken(0.2).string()
			},
			stepActiveStyle: {
				boxShadow: '0 0 0 2px ' + _SEARCHWP.misc.colors.current
			}
		}
	}
}
</script>

<style lang="scss">

	.searchwp-attribute {
		margin-bottom: 0.5em;
		border-radius: 1px;

		dl, dt, dd {
			margin: 0;
			padding: 0;
		}

		dl {
			display: flex;
			// align-items: center;
			justify-content: space-between;
		}

		dt {
			width: 40%;
			display: flex;
			align-items: center;

			> span {
				display: block;
				overflow: hidden;
				white-space: nowrap;
				text-overflow: ellipsis;

				// This chops off text, but it doesn't look good (no surprise!)
				// word-break: break-all;
				// overflow-wrap: break-word;

				+ span {
					opacity: 0;
				}
			}
		}

		dd {
			width: 55%;
			display: flex;
			align-items: center;

			* {
				box-sizing: content-box;
			}

			span {
				display: block;
				font-size: 11px;
				opacity: 0.6;
			}

			.vue-slider {
				flex: 1;
				margin-right: 0.5em;
				margin-left: 0.5em;
			}
		}

		.vue-slider-dot-tooltip {
			transition: all 100ms;
		}

		.vue-slider-dot-tooltip-inner {
			transform: scale(1);
			font-size: 13px;
		}
	}
</style>
