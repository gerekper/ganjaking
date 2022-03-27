<template>
	<transition name="gpfup__progress-container--fade">
		<div class="gpfup__progress-container" v-if="percentage !== 100 || !metMinimumDisplayTime">
			<div class="gpfup_progress" :style="{ width: `${percentage}%` }"></div>
		</div>
	</transition>
</template>

<script lang="ts">
	export default {
		name: "LoadingBar",
		props: ['file'],
		data: function() {
			return {
				/**
				* metMinimumDisplayTime is used for showing the progress bar for a minimum amount of time. This provides
				* a more consistent experience between files and helps the visitor know that the file was uploaded
				* successfully.
				*/
				metMinimumDisplayTime: false,
				metMinimumDisplayTimeTimeout: undefined,
			}
		},
		watch: {
			percentage: function (newPercent, oldPercent) {
				if (newPercent === 100) {
					this.metMinimumDisplayTimeTimeout = setTimeout(() => {
						this.metMinimumDisplayTime = true;
					}, 750);
				}
			}
		},
		beforeDestroy() {
			if (this.metMinimumDisplayTimeTimeout) {
				clearTimeout(this.metMinimumDisplayTimeTimeout)
			}
		},
		computed: {
			percentage: function() {
				return this.file.percent;
			}
		}
	}
</script>

<style>
	.gpfup__progress-container {
		/*margin-top: 10px;*/
		/*border-radius: 2px;*/
		overflow: hidden;
		height: 6px;
		width: 100%;
		background: rgba( 0, 0, 0, 0.05 );
		position: absolute;
		left: 0;
		bottom: 0;
	}

	/**
	* Classes for <transition>
	* https://vuejs.org/v2/guide/transitions.html#Transition-Classes
	*/
	.gpfup__progress-container--fade-leave-active {
		transition: opacity .25s, height .25s, margin .25s;
	}

	.gpfup__progress-container--fade-leave-to {
		opacity: 0;
		/*height: 0;*/
		/*margin-top: 0;*/
	}

	.gpfup_progress {
		transition: width .5s linear;
		height: 100%;
		background: #3498db;
	}
</style>
