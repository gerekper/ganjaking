<template>
	<span class="searchwp-tooltip">
		<span>
			<slot></slot>
		</span>
		<span
			class="dashicons dashicons-editor-help"
			v-tooltip="content"
		></span>
	</span>
</template>

<script>
export default {
	props: {
		content: String
	}
}
</script>

<style lang="scss">
	.tooltip {
		display: block !important;
		z-index: 10000;

		.tooltip-inner {
			background: rgba(0,0,0,0.8);
			color: #fff;
			border-radius: 2px;
			padding: 5px 10px;
			text-align: center;
		}

		.tooltip-arrow {
			width: 0;
			height: 0;
			border-style: solid;
			position: absolute;
			margin: 5px;
			border-color: rgba(0,0,0,0.8);
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

		&.popover {
			$color: #414141;

			.popover-inner {
				background: $color;
				color: #fff;
				padding: 0.4em 0 0.8em;
				border-radius: 3px;
				min-width: 150px;

				> div {

					> *:first-child {
						margin-top: 0;
						padding-top: 0;
					}

					> *:last-child {
						margin-bottom: 0;
						padding-bottom: 0;
					}
				}

				a {
					color: #fff;
				}

				ul {
					margin: 0;
					padding: 0.6em 0;
					list-style: none;
				}

				li {
					padding: 0.3em 0.7em;
					line-height: 1.5;
					margin: 0;
					cursor: pointer;

					&:hover {
						background: #159FD2;
					}
				}
			}

			.popover-arrow {
				border-color: $color;
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

	.searchwp-tooltip {
		display: flex;
		align-items: center;

		.dashicons {
			margin-left: 0.2em;
			width: 18px;
			height: 18px;
			font-size: 18px;
		}
	}
</style>
