<template>
	<div class="searchwp-alternate-indexer">
		<Notice v-if="'alternate' === type"
			:type="'warning'"
			:message="'_alternate_indexer_note' | i18n"
			:more="{
				target: 'https://searchwp.com/?p=223030',
				text: httpLoopbackLinkText
			}"
			:tooltip="'_http_loopback_note' | i18n"
		></Notice>
		<Notice v-else-if="'basicauth' === type"
			:type="'error'"
			:message="'_indexer_blocked_note' | i18n"
			:more="{
				target: 'https://searchwp.com/?p=223034',
				text: httpBasicAuthLinkText
			}"
		></Notice>
	</div>
</template>

<script>
import Notice from './Notice.vue';
import { __ } from './../helpers.js';

export default {
	name: 'AlternateIndexer',
	components: {
		Notice
	},
	computed: {
		httpLoopbackLinkText: function() {
			return __('More Info');
		},
		httpBasicAuthLinkText: function() {
			return __('Fix this');
		}
	},
	methods: {
		triggerIndexer: function() {
			let vm = this;

			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + 'trigger_indexer'
			}, function(response) {
				setTimeout(function() {
					vm.triggerIndexer();
				}, 5000);
			});
		}
	},
	created() {
		let vm = this;

		jQuery.post(ajaxurl, {
			_ajax_nonce: _SEARCHWP.nonce,
			action: _SEARCHWP.prefix + 'indexer_method'
		}, function(response) {
			if (!response.success) {
				alert(__('Indexer communication error. See console.'));
			} else {
				vm.type = response.data;

				// If it's the alternate indexer, we need to kick it off.
				if ('alternate' == vm.type) {
					vm.triggerIndexer();
				}
			}
		});
	},
	data() {
		return {
			type: 'default'
		}
	}
}
</script>

<style lang="scss">

</style>
