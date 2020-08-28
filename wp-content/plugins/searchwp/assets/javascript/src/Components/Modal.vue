<template>
	<v-modal
		:name="name"
		:minWidth="400"
		:maxWidth="maxWidth"
		:width="'80%'"
		:adaptive="true"
		:clickToClose="false"
		height="auto"
		:scrollable="true"
		@closed="$emit('closed')">
		<div class="searchwp-modal">
			<div class="searchwp-modal-heading">
				<h3 class="searchwp-modal-heading-label">{{ label }}</h3>
				<ul v-if="showAction" class="searchwp-actions">
					<li>
						<button type="button"
							:class="['button', actionIsPrimary ? 'button-primary' : '']"
							@click="hide">{{ actionLabel }}</button>
					</li>
				</ul>
			</div>
			<div class="searchwp-modal-content" :style="{ borderColor: borderColor }">
				<div class="searchwp-modal-content-container">
					<slot></slot>
				</div>
			</div>
		</div>
	</v-modal>
</template>

<script>
import { __ } from './../helpers.js';

export default {
	name: 'Modal',
	props: {
		name: {
			type: String,
			required: true
		},
		label: {
			type: String,
			required: true
		},
		showAction: {
			type: Boolean,
			required: false,
			default: true
		},
		actionLabel: {
			type: String,
			required: false,
			default: __('Done')
		},
		actionIsPrimary: {
			type: Boolean,
			required: false,
			default: true
		},
		maxWidth: {
			type: Number,
			required: false,
			default: 640
		}
	},
	methods: {
		hide: function() {
			this.$modal.hide(this.name);
			this.$emit('hide');
		}
	},
	computed: {
		borderColor: function() {
			return _SEARCHWP.misc.colors.border;
		}
	}
}
</script>

<style lang="scss">
	.searchwp-settings .searchwp-modal {

		.searchwp-actions > * {
			margin-bottom: 0;
		}
	}

	.searchwp-modal-heading {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 1em;

		h3.searchwp-modal-heading-label {
			margin: 0;
			padding: 0;
			font-size: 1.25em;
			font-weight: 500;
		}

		ul.searchwp-actions {
			margin: 0;

			> * {
				margin-bottom: 0;
			}
		}

		+ .searchwp-modal-content {
			border-style: solid;
			border-width: 1px 0 0;
		}
	}

	.searchwp-modal-content {
		padding: 1em;
		max-height: 75vh;
		overflow: auto;
	}

	.searchwp-modal-content-container {

		> *:last-child {
			margin-bottom: 1em;
		}
	}
</style>
