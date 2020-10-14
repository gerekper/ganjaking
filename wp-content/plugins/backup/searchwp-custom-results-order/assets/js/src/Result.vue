<template>

	<div class="searchwp-cro__result">
		<div class="searchwp-cro__result-meta">
			<h2>
				<span class="searchwp-cro__result-meta-title">{{ title }}</span>
				<span class="searchwp-cro__result-meta-id"><span>(Post ID: {{ id }})</span></span>
			</h2>
		</div>
		<ul class="searchwp-cro__result-actions">
			<li>
				<button
					:class="[
						'button',
						'searchwp-cro__result-action-unpromote',
						promoted ? '' : 'searchwp-cro__result-action--unavailable'
					]"
					@click="$emit('release', id)"
					title="Return this result to natural rank">
					<span>
						<span class="dashicons dashicons-download"></span>
						<span>Remove Promotion</span>
					</span>
				</button>
			</li>
			<li>
				<button
					:class="[
						'button',
						'searchwp-cro__result-action-promote',
						rank > 1 ? '' : 'searchwp-cro__result-action--unavailable'
					]"
					@click="$emit('promote', id)"
					title="Make this the first result">
					<span>
						<span class="dashicons dashicons-star-filled"></span>
						<span>Promote to Top</span>
					</span>
				</button>
			</li>
		</ul>
	</div>

</template>

<script>
export default {
	name: 'Result',
	props: {
		promoted: {
			type: Boolean,
			default: true
		},
		title: {
			type: String,
			default: '',
			required: true
		},
		id: {
			type: Number,
			default: 0,
			required: true
		},
		rank: {
			type: Number,
			default: 1,
			required: true
		}
	}
}
</script>

<style lang="scss">
	.searchwp-cro__result {
		display: flex;
		align-items: center;
		min-width: 0;
		width: 100%;
		padding: 0 0.25em;
		border-radius: 2px;

		&:hover {
			background: #efefef;
		}

		&:last-of-type {
			margin-bottom: 0;
		}
	}

	.inside .searchwp-cro__result-meta {
		flex: 1;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;

		h2 {
			margin: 0;
			padding: 0;
			font-size: 1.2em;
			display: flex;
			align-items: center;
			min-width: 0;

			span {
				display: block;
			}
		}
	}

	.searchwp-cro__result-meta-title {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.searchwp-cro__result-meta-id {
		white-space: nowrap;

		span {
			font-size: 13px;
			font-weight: normal;
			font-style: italic;
			padding-left: 0.6em;
			white-space: nowrap;
		}
	}

	.searchwp-cro__result-actions {
		white-space: nowrap;
		display: flex;
		align-items: center;
		margin: 0 0 0 auto;
		padding: 0.5em 0 0 2em;
		list-style: none;

		> li {
			display: inline-block;
			margin-left: 1em;
			font-size: 0.8em;
		}

		button {

			span {
				display: flex;
				align-items: center;

				.dashicons {
					margin-right: 0.2em;
				}
			}
		}
	}

	.searchwp-cro__result-action--unavailable {
		visibility: hidden;
	}
</style>
