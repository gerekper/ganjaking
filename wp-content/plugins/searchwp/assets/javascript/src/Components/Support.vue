<template>
	<div :class="['searchwp-settings', 'searchwp-settings-support' ]">
		<div class="searchwp-settings-view-header">
			<h1>{{ 'SearchWP Support' | i18n }}</h1>
		</div>

		<div class="searchwp-settings-support-help">
			<MetaBox
				v-if="activeLicense"
				class="searchwp-support-ticket"
				:active="true"
				:label="'Support' | i18n"
				:id="'searchwp-support-ticket'"
			>
				<template v-slot:heading>
					<span>{{ 'Get Help' | i18n }}</span>
					<span>
						<Tag>&nbsp;</Tag>
					</span>
				</template>
				<template v-slot:content>
					<div class="searchwp-support-ticket-inside">
						<iframe ref="iframe" :src="ticketUrl + '&license=' + licenseKey"></iframe>
					</div>
				</template>
			</MetaBox>

			<MetaBox
				class="searchwp-support-system-info"
				:active="true"
				:label="'Support' | i18n"
				:id="'searchwp-support-system-info'"
			>
				<template v-slot:heading>
					<span>{{ 'System Information' | i18n }}</span>
					<span>
						<Tag>&nbsp;</Tag>
					</span>
				</template>
				<template v-slot:content>
					<div class="inside">
						<p class="description">{{ '_system_information_note' | i18n }}</p>
						<SystemInfo></SystemInfo>
					</div>
				</template>
			</MetaBox>
		</div>

		<div class="searchwp-settings-support-license">
			<MetaBox
				class="searchwp-support-license-manager"
				:active="true"
				:label="'License' | i18n"
				:id="'searchwp-support-license-manager'"
			>
				<template v-slot:heading>
					<span>{{ 'License' | i18n }}</span>
					<span>
						<Tag v-if="activeLicense" :type="'success'">{{ 'Active' | i18n }}</Tag>
						<Tag v-else :type="'error'">{{ 'Inactive' | i18n }}</Tag>
					</span>
				</template>
				<template v-slot:content>
					<div :class="['inside', updatingLicense ? 'searchwp-settings-support-license-updating' : '' ]">
						<div class="searchwp-settings-support-license-key">
							<input type="text" v-model="licenseKey" :disabled="activeLicense">
							<button v-if="!activeLicense" class="button" @click="activateLicense">{{ 'Activate' | i18n }}</button>
							<button v-else class="button" @click="deactivateLicense">{{ 'Deactivate' | i18n }}</button>
						</div>
						<p v-if="licenseRemaining.length" class="description">{{ licenseRemaining }}</p>

						<div v-if="!activeLicense" class="searchwp-support-license-inactive-info">
							<Notice
								:type="'error'"
								:icon="'dashicons dashicons-warning'"
								:message="'_inactive_license_note' | i18n">
							</Notice>
							<component :is="i18nInactiveLicenseInfo"></component>
						</div>
					</div>
				</template>
			</MetaBox>
		</div>

	</div>
</template>

<script>
import Tag from './Tag.vue';
import Notice from './Notice.vue';
import MetaBox from './MetaBox.vue';
import { __ } from './../helpers.js';
import cloneDeep from 'lodash.clonedeep';
import SystemInfo from './SystemInfo.vue';

export default {
	name: 'Support',
	components: {
		MetaBox,
		Notice,
		Tag,
		SystemInfo
	},
	computed: {
		activeLicense: function() {
			return 'valid' === this.licenseStatus;
		},
		i18nInactiveLicenseInfo: function() {
			return {
				template: '<div>' + __('_inactive_license_info') + '</div>'
			}
		}
	},
	watch: {
		activeLicense: function(isActive, wasActive) {
			if (isActive) {
				jQuery('.searchwp-settings-nav-tab-support .dashicons').hide();
			} else {
				jQuery('.searchwp-settings-nav-tab-support .dashicons').show();
			}
		}
	},
	methods: {
		activateLicense: function() {
			let vm = this;
			vm.updatingLicense = true;

			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + 'license_activate',
				license_key: vm.licenseKey
			}, function(response) {
				vm.updatingLicense = false;
				if (response.success && 'valid'===response.data.status) {
					vm.licenseNote = '';
					vm.licenseKey = response.data.key;
					vm.licenseStatus = response.data.status;
					vm.licenseExpiration = response.data.expires;
					vm.licenseRemaining = response.data.remaining;
				} else if (response.success) {
					vm.licenseNote = response.data;
				} else {
					vm.licenseNote = __('_license_activation_problem');
				}
			});
		},
		deactivateLicense: function() {
			let vm = this;
			vm.updatingLicense = true;

			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + 'license_deactivate',
				license_key: vm.licenseKey
			}, function(response) {
				vm.updatingLicense = false;
				if (response.success) {
					if ('deactivated'===response.data.status) {
						vm.licenseNote = '';
						vm.licenseStatus = response.data.status;
						vm.licenseExpiration = response.data.expires;
						vm.licenseRemaining = '';
					} else {
						vm.licenseNote = response.data;
					}
				} else {
					vm.licenseNote = __('_license_deactivation_problem');
				}
			});
		},
		updateTicketHeight: function(event) {
			if (event.origin === "https://searchwp.com") {
				this.$refs.iframe.style.height = parseInt(event.data, 10) + 'px';
			}
		}
	},
	beforeMount() {
		window.addEventListener('message', this.updateTicketHeight, false);
	},
	beforeDestroy() {
		window.removeEventListener('message', this.updateTicketHeight);
	},
	data() {
		return {
			updatingLicense: false,
			licenseNote: '',
			ticketUrl: _SEARCHWP.ticket_url,
			licenseKey: _SEARCHWP.license.key ? _SEARCHWP.license.key : '',
			licenseStatus: _SEARCHWP.license.status ? _SEARCHWP.license.status : '',
			licenseExpiration: _SEARCHWP.license.expires ? _SEARCHWP.license.expires : '',
			licenseRemaining: _SEARCHWP.license.remaining ? _SEARCHWP.license.remaining : ''
		}
	}
}
</script>

<style lang="scss">
	@import './../global.scss';

	.searchwp-settings-support {

		.searchwp-settings-view-header {
			width: 100%;
		}

		.searchwp-meta-box-heading__label {

			span + span {
				display: flex;
				align-items: center;
			}

			.searchwp-tag {
				margin-left: auto;
			}
		}
	}

	.searchwp-settings-support-help {
		flex: 1;

		.searchwp-tag {
			visibility: hidden;
		}

		.searchwp-support-ticket + .searchwp-support-system-info {
			margin-top: 1.5em;
		}

		.searchwp-support-ticket-inside {
			padding-left: 0.1em;
		}

		.inside {
			padding-top: 0.5em;
		}

		iframe {
			display: block;
			width: 100%;
		}
	}

	.searchwp-settings-support-items {
		display: flex;
		justify-content: space-between;
		width: 100%;
	}

	.searchwp-settings-support-ticket {
		flex: 1;
	}

	.searchwp-settings-support-license {
		width: 34em;
		padding-left: 1.5em;

		.searchwp-settings-support-license-key {
			display: flex;
			padding-top: 0.5em;

			input {
				display: block;
				width: 100%;
				margin-right: 1em;
				font-family: monospace;
			}
		}

		p.description {
			padding-top: 0.2em;
			padding-left: 4px; // Visual offset.
		}
	}

	.searchwp-support-license-inactive-info {
		padding-top: 1.5em;
	}

	.searchwp-settings-support-license-updating {
		position: relative;

		&:after {
			display: block;
			content: '';
			position: absolute;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			cursor: wait;
			background: rgba(255, 255, 255, 0.5);
		}
	}

	@media screen and (max-width:1280px) {

		.searchwp-settings-support .searchwp-settings-view-header {
			order: 1;
		}

		.searchwp-settings-support-license {
			order: 2;
			width: 100%;
			padding-left: 0;
			margin-bottom: 1.5em;
		}

		.searchwp-settings-support-help {
			flex: auto;
			width: 100%;
			order: 3;
		}
	}
</style>
