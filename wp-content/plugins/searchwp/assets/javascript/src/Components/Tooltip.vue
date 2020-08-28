<template>
	<span class="searchwp-tooltip">
		<span>
			<span>
				<slot></slot>
			</span>
			<span
				:class="icon"
				v-tooltip="{content: content, placement: placement}"
			></span>
		</span>
	</span>
</template>

<script>
export default {
	name: 'Tooltip',
	props: {
		content: String,
		icon: {
			type: String,
			default: 'dashicons dashicons-info',
			required: false
		},
		placement: {
			type: String,
			default: 'auto',
			required: false
		}
	}
}
</script>

<style lang="scss">
	.searchwp-tooltip {
		display: flex;
		align-items: center;

		> span {
			display: flex;
			// align-items: center; // This was center, but we want the icon to be pinned to the top (flex-start).
		}

		.dashicons {
			margin-left: 0.2em;
		}
	}

	.tooltip {
		display: inline-block !important;
		z-index: 999999999;

		.tooltip-inner {
			background: #424242;
			color: white;
			border-radius: 2px;
			padding: 5px 10px;
			text-align: center;
			max-width: 200px;
		}

		.tooltip-arrow {
			width: 0;
			height: 0;
			border-style: solid;
			position: absolute;
			margin: 5px;
			border-color: #424242;
			z-index: 1;
		}

		&[x-placement^="top"] {
			margin-bottom: 5px;

			.tooltip-arrow {
				border-width: 5px 5px 0 5px;
				border-left-color: transparent !important;
				border-right-color: transparent !important;
				border-bottom-color: transparent !important;
				bottom: -5px;
				left: calc(50% - 5px);
				margin-top: 0;
				margin-bottom: 0;
			}
		}

		&[x-placement^="bottom"] {
			margin-top: 5px;

			.tooltip-arrow {
				border-width: 0 5px 5px 5px;
				border-left-color: transparent !important;
				border-right-color: transparent !important;
				border-top-color: transparent !important;
				top: -5px;
				left: calc(50% - 5px);
				margin-top: 0;
				margin-bottom: 0;
			}
		}

		&[x-placement^="right"] {
			margin-left: 5px;

			.tooltip-arrow {
				border-width: 5px 5px 5px 0;
				border-left-color: transparent !important;
				border-top-color: transparent !important;
				border-bottom-color: transparent !important;
				left: -5px;
				top: calc(50% - 5px);
				margin-left: 0;
				margin-right: 0;
			}
		}

		&[x-placement^="left"] {
			margin-right: 5px;

			.tooltip-arrow {
				border-width: 5px 0 5px 5px;
				border-top-color: transparent !important;
				border-right-color: transparent !important;
				border-bottom-color: transparent !important;
				right: -5px;
				top: calc(50% - 5px);
				margin-left: 0;
				margin-right: 0;
			}
		}

		&[aria-hidden='true'] {
			visibility: hidden;
			opacity: 0;
			transition: opacity .15s, visibility .15s;
		}

		&[aria-hidden='false'] {
			visibility: visible;
			opacity: 1;
			transition: opacity .15s;
		}
	}
</style>
