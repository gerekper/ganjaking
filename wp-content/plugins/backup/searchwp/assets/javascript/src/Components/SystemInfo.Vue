<template>
	<div class="searchwp-system-info">
		<p>
			<button class="button"
				v-clipboard:copy="systemInfo"
				v-clipboard:success="onCopy"
				v-clipboard:error="onError">{{ 'Copy to clipboard' | i18n }}</button>
			<transition name="fade">
				<span v-if="copied">{{ 'Copied!' | i18n }}</span>
			</transition>
		</p>
		<textarea v-model="systemInfo"></textarea>
	</div>
</template>

<script>
import { __ } from './../helpers.js';

export default {
	name: 'SystemInfo',
	methods: {
		onCopy: function() {
			let vm = this;

			vm.copied = true;

			setTimeout(function() {
				vm.copied = false;
			}, 2000);
		},
		onError: function() {
			alert(__('_copy_clipboard_error'));
		}
	},
	data() {
		return {
			copied: false,
			systemInfo: _SEARCHWP.system_info
		}
	}
}
</script>

<style lang="scss">
	.searchwp-system-info {

		> p {
			display: flex;
			align-items: center;

			span {
				display: block;
				padding-left: 0.8em;
				line-height: 1;
				font-weight: 500;
			}
		}

		textarea {
			font-family: monospace;
			display: block;
			width: 100%;
			height: 50em;
		}
	}
</style>
