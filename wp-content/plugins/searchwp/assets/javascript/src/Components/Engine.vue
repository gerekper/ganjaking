<template>
	<MetaBox
		class="searchwp-engine"
		:active="true"
		:label="'SearchWP Engine: ' + label"
		:id="'searchwp-engine-' + name"
	>
		<template v-slot:heading>
			<span>{{ label }} <code>{{ name }}</code></span>
			<button
				v-show="hasSources"
				class="button searchwp-engine-settings-toggle"
				@click.stop="$modal.show(name + '-settings-editor')">
				{{ 'Sources & Settings' | i18n }}
			</button>
		</template>
		<template v-slot:content>
			<div class="inside">
				<div class="searchwp-engine-notes">
					<p v-if="'default' === name && name === currentAdminEngine.name">
						<component :is="i18nDefaultAdminEngineNote"></component>
					</p>
					<p v-else-if="'default' === name">
						<span class="dashicons dashicons-info"></span>
						{{ '_default_engine_note' | i18n }}
					</p>
					<p v-else-if="name === currentAdminEngine.name">
						<span class="dashicons dashicons-info"></span>
						{{ '_admin_engine_note' | i18n }}
					</p>
					<p v-else>{{ '_engine_note' | i18n }}</p>
					<button
						v-show="hasSources"
						class="button searchwp-button-subtle"
						@click.stop="allSourcesCollapsed ? expandeAllSources() : collapseAllSources()">
						<span style="font-weight: normal;" v-if="allSourcesCollapsed">{{ 'Expand Sources' | i18n }}</span>
						<span style="font-weight: normal;" v-else>{{ 'Collapse Sources' | i18n }}</span>
					</button>
				</div>
				<ul v-if="hasSources" class="searchwp-sources">
					<li v-for="(source, index) in engineSources" :key="index">
						<EngineSource
							:name="source.name"
							:engine="name"
						></EngineSource>
					</li>
				</ul>
				<div v-else class="searchwp-sources-missing">
					<Notice
						:type="'warning'"
						:message="'_no_sources_warning' | i18n"
					>
						<ul class="searchwp-actions">
							<li>
								<button class="button searchwp-engine-settings-toggle" @click.stop="$modal.show(name + '-settings-editor')">
									<span>
										<span class="searchwp-button-icon dashicons dashicons-admin-settings"></span>
										<span class="searchwp-button-label">{{ 'Settings' | i18n }}</span>
									</span>
								</button>
							</li>
						</ul>
					</Notice>
				</div>
			</div>

			<Modal :name="name + '-settings-editor'"
				:label="'_edit_settings_engine' | i18n([label])"
				:actionIsPrimary="false"
				:actionLabel="'Cancel' | i18n">
				<EngineSettings
					:engine="name"
					@save="updateSettings"
					@delete="deleteEngine"
				></EngineSettings>
			</Modal>
		</template>
	</MetaBox>
</template>

<script>
import Modal from './Modal.vue';
import Notice from './Notice.vue';
import MetaBox from './MetaBox.vue';
import { __ } from './../helpers.js';
import cloneDeep from 'lodash.clonedeep';
import EngineSource from './Engine/Source.vue';
import EngineSettings from './Engine/Settings.vue';
import { EngineUtils } from './../Mixins/EngineUtils.js';

export default {
	name: 'Engine',
	props: {
		name: {
			type: String,
			required: true,
			default: 'default'
		}
	},
	components: {
		MetaBox,
		Modal,
		Notice,
		EngineSource,
		EngineSettings
	},
	mixins: [EngineUtils],
	computed: {
		i18nDefaultAdminEngineNote: function() {
			return {
				template: '<span><span class="dashicons dashicons-info"></span> ' + __('_default_admin_engine_note') + '</span>'
			};
		},
		currentAdminEngine: function() {
			return this.$store.getters.adminEngine;
		},
		allSourcesCollapsed: function() {
			return this.hasSources === this.$store.getters.engineCollapsedSources(this.name).length;
		},
		hasSources: function() {
			return Object.keys(this.engineSources).length;
		},
		label: function() {
			return this.getEngineProperty(this.name, 'label');
		},
		engineSources: function() {
			return this.getEngineProperty(this.name, 'sources');
		}
	},
	methods: {
		collapseAllSources: function() {
			this.$store.commit('collapseAllEngineSources', this.name);
		},
		expandeAllSources: function() {
			this.$store.commit('expandAllEngineSources', this.name);
		},
		deleteEngine: function(engine) {
			this.$modal.hide(this.name + '-settings-editor');
			this.$store.commit('deleteEngine', engine);
		},
		updateSettings: function(settings) {
			this.$modal.hide(this.name + '-settings-editor');
			this.$store.commit('updateEngineSettings', settings);
		}
	}
}
</script>

<style lang="scss">
	.searchwp-engine {
		margin-top: 12px;
	}

	.searchwp-sources > li {
		margin-bottom: 10px;
	}

	.searchwp-sources-missing {
		padding-top: 1em;

		.searchwp-actions {
			padding-bottom: 1em;
		}
	}

	.searchwp-engine-notes {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding-top: 0.3em;

		> p {
			margin: 0;
		}

		> button {
			margin-left: 1.5em;
		}
	}
</style>
