<template>
	<button @click="onclick" :title="i18n.remove" class="searchwp-delete dashicons dashicons-dismiss"></button>
</template>

<script>
import Vue from 'vue';

export default {
	methods: {
		onclick: function() {
			this.$emit('onclick');
		}
	},
	data () {
		return {
			i18n: {
				remove: _SEARCHWP_METRICS_VARS.i18n.remove
			}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-delete {
		display: inline-block;
		width: 1em;
		height: 1em;
		color: #434343;
		margin: 0;
		padding: 0;
		border: 0;
		background: transparent;
		cursor: pointer;
	}
</style>
