<template>
	<v-collapse-wrapper
		ref="collapseWrapper"
		v-on:afterToggle="toggleExpanded"
		:active="active || expanded"
		:class="[
			'searchwp-meta-box',
			'postbox',
			expanded ? '' : 'closed'
		]">

		<h2
			:class="['searchwp-meta-box-heading', 'hndle', collapsible ? '' : 'searchwp-meta-box-heading-locked']"
			v-collapse-toggle>
			<button
				v-if="collapsible"
				@click.stop="triggerToggle"
				type="button"
				class="handlediv"
				:aria-expanded="expanded"
			>
				<span class="screen-reader-text">Toggle panel: {{ label }}</span>
				<span class="toggle-indicator" :aria-hidden="expanded"></span>
			</button>
			<span class="searchwp-meta-box-heading__label">
				<slot name="heading"></slot>
			</span>
		</h2>

		<div v-collapse-content>
			<slot name="content"></slot>
		</div>
	</v-collapse-wrapper>
</template>

<script>
export default {
	name: 'MetaBox',
	props: {
		label: String,
		active: {
			type: Boolean,
			default: false
		},
		collapsible: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		expanded: function() {
			return this.collapsible ? this.active : true;
		}
	},
	methods: {
		toggleExpanded() {
			if (this.collapsible) {
				this.$emit('afterToggle', !this.expanded);
			} else {
				this.$refs.collapseWrapper.open();
			}
		},
		triggerToggle() {
			this.$refs.collapseWrapper.toggle();
		}
	}
}
</script>

<style lang="scss">
	.searchwp-meta-box {
		margin: 0;

		&.closed .hndle {
			border-bottom: 0;
		}

		&.postbox .handlediv {
			height: auto;
			float: none;
		}
	}

	.wp-core-ui #poststuff .searchwp-meta-box-heading {
		display: flex;
		flex-direction: row-reverse;

		.searchwp-meta-box-heading__label {
			display: flex;
			align-items: center;
			flex: 1;
		}

		&.hndle.searchwp-meta-box-heading-locked {
			cursor: default;

			.searchwp-meta-box-heading__label {
				cursor: default;
			}
		}
	}
</style>
