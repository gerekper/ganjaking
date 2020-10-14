<template>
	<div class="searchwp-index-progress">
		<div class="searchwp-index-progress-heading">
			<h3>{{ 'Index Status' | i18n }}</h3>
			<p>{{ progress + '%' }}</p>
		</div>
		<div class="searchwp-index-progress-track" :style="{ boxShadow: 'inset 0 0 0 1px ' + borderColor }">
			<div class="searchwp-index-progress-bar" :style="{ width: progress + '%', backgroundColor: backgroundColor }">
				<span class="screen-reader-text">{{ progress + '%' }}</span>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'IndexProgress',
	props: {
		progress: {
			type: Number,
			default: 0,
			required: true
		}
	},
	data () {
		return {
			borderColor: _SEARCHWP.misc.colors.border,
			backgroundColor: _SEARCHWP.misc.colors.current
		}
	}
}
</script>

<style lang="scss">
	.searchwp-index-progress-heading {
		display: flex;
		justify-content: space-between;
		align-items: center;

		p {
			margin: 0;
		}
	}

	$colorBand: rgba(175, 175, 175, .2);

	.searchwp-index-progress-track {
		width: 100%;
		position: relative;
		height: 8px;
		border-radius: 16px;
		transition:width 1s ease-in-out;
		background-color:#ececec;
		background-size: 30px 30px;
		background-image: linear-gradient(135deg, $colorBand 25%, transparent 25%,
		transparent 50%, $colorBand 50%, $colorBand 75%,
		transparent 75%, transparent);
		animation: searchwp_progress 3s linear infinite;
	}

	@keyframes searchwp_progress {
		0% {
			background-position: 0 0;
		}

		100% {
			background-position: 60px 0;
		}
	}

	.searchwp-index-progress-bar {
		height: 8px;
		border-radius: 16px;
		transition: width 400ms linear;
		position: absolute;
		top: 0;
		left: 0;
	}
</style>
