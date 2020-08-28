<template>
	<v-popover offset="5" @show="setStyles" ref="target">
		<slot></slot>
		<template slot="popover">
			<ul :style="{ color: hoverColor }">
				<li v-for="(item, index) in items" :key="index">
					<button v-close-popover @click.stop="item.click()">{{ item.text }}</button>
				</li>
			</ul>
		</template>
	</v-popover>
</template>

<script>
export default {
	name: 'Menu',
	props: {
		items: {
			type: Array,
			required: true,
			validator: function (value) {
				return value.length === value
					.filter(item => item.click && 'function' === typeof item.click && item.text && item.text.length).length;
			}
		}
	},
	methods: {
		setStyles: function() {
			this.$refs.target.$refs.arrow.style.borderColor = this.bgColor;
			this.$refs.target.$refs.inner.style.background = this.bgColor;
			this.$refs.target.$refs.inner.style.color = this.color;
		}
	},
	created() {
		// There's a nonconformant setup for the default color scheme for some reason. This is the actual hover color.
		let hoverColor = _SEARCHWP.misc.colors.current;
		if ( '#0073aa' === hoverColor ) {
			hoverColor = '#00b9eb';
		}

		this.hoverColor = hoverColor;
	},
	data() {
		return {
			bgColor: _SEARCHWP.misc.colors.base,
			color: _SEARCHWP.misc.colors.text,
			hoverColor: ''
		}
	}
}
</script>

<style lang="scss">
	.tooltip {

		&.popover {
			$color: #414141;

			.popover-inner {
				background: $color;
				color: white;
				padding: 0.5em;
				border-radius: 3px;

				ul {
					margin: 0;
					padding: 0;
					list-style: none;
				}

				button {
					display: block;
					cursor: pointer;
					color: white;
					border: 0;
					background: transparent;
					text-align: center;
					width: 100%;
					padding: 0.25em 0.5em;
					line-height: 1;

					&:hover {
						color: inherit;
					}
				}
			}

			.popover-arrow {
				border-color: $color;
			}
		}
	}
</style>
