<template>
	<div id="searchwp-settings-engines">
		<div :class="['searchwp-settings', saving ? 'searchwp-settings-disabled' : '' ]">
			<div class="searchwp-engines">
				<div class="searchwp-settings-view-header">
					<h1>{{ 'SearchWP Engines' | i18n }}</h1>
					<ul class="searchwp-actions searchwp-settings-engines-actions">
						<li>
							<button class="button" @click="newEngine">{{ 'Add New' | i18n }}</button>
						</li>
					</ul>
				</div>

				<ul>
					<li v-for="(settings, name, index) in engines" :key="index">
						<Engine :name="name"></Engine>
					</li>
				</ul>
			</div>

			<div class="searchwp-settings-info">
				<ul class="searchwp-actions searchwp-settings-actions">
					<li>
						<button :disabled="!updated" class="button button-primary" @click="save">
							{{ 'Save Engines' | i18n }}
						</button>
					</li>
					<li>
						<button :disabled="updated" class="button" @click="docContentResetAsk ? docContentResetModal() : rebuildIndex()">
							{{ 'Rebuild Index' | i18n }}
						</button>
					</li>
				</ul>

				<Notice v-if="!hasInitialSave"
					:type="'warning'"
					:message="'_needs_initial_save' | i18n ">
				</Notice>
				<Notice
					v-else-if="indexOutdated"
					:type="'warning'"
					:message="'_index_outdated' | i18n"
					:tooltip="'_index_outdated_tooltip' | i18n"
				></Notice>

				<Environment></Environment>

				<div class="searchwp-index-info">
					<IndexProgress :progress="indexProgressValue"></IndexProgress>
					<IndexStats
						:lastActivity="$store.state.index.lastActivity"
						:indexed="$store.state.index.indexed"
						:omitted="$store.state.index.omitted"
						:total="$store.state.index.total"></IndexStats>
					<p class="description">{{ '_index_optimization_note' | i18n }}</p>
				</div>
			</div>

			<Modal :name="'omitted'"
				:label="'Omitted Entries' | i18n()"
				:actionIsPrimary="false"
				:actionLabel="'Close' | i18n">
				<div v-if="$store.state.index.omitted && $store.state.index.omitted.length" class="searchwp-omitted-notes">
					<p>{{ 'SearchWP was unable to index the following content due to an indexing failure. Reviewing server logs may expose the reason for failure.' | i18n }}</p>
					<table>
						<thead>
							<tr>
								<th>{{ 'Source' | i18n }}</th>
								<th>{{ 'ID' | i18n }}</th>
								<th>{{ 'Actions' | i18n }}</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="entry in $store.state.index.omitted" :key="entry.source + entry.id">
								<td>{{ entry.source }}</td>
								<td>{{ entry.id }}</td>
								<td>
									<button class="button" @click="reintroduce(entry)">{{ 'Reintroduce' | i18n }}</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<p v-else class="description">{{ 'There are no omitted entries at this time.' | i18n }}</p>
			</Modal>

			<Modal :name="'welcome'"
				:label="'Welcome!' | i18n()"
				:actionIsPrimary="false"
				:actionLabel="'Close' | i18n">
				<div class="searchwp-migration-notes">
					<div v-if="welcome && !activeMigration">
						<p>{{ '_welcome_intro', | i18n }}</p>
						<Notice :type="'warning'">
							<component :is="i18nWelcomeWarning"></component>
						</Notice>
						<component :is="i18nWelcomeBlurb"></component>
					</div>
					<div v-else-if="activeMigration">
						<component :is="i18nWelcomeMigratedIntro"></component>

						<Notice v-if="!migratingComplete" :type="'warning'">
							<component :is="i18nWelcomeMigratedWarning"></component>
							<p><button
								:disabled="migrating"
								@click="migrateStatistics"
							class="button button-primary">{{ 'Migrate Statistics' | i18n }} </button></p>
							<div class="searchwp-migrating-statistics" v-if="migrating">
								<p>{{ 'Performing migration, please wait...' | i18n }}</p>
								<div>
									<Spinner
										size="small"
										:line-fg-color="spinnerFgColor"
										:line-bg-color="spinnerBgColor"
									></Spinner>
								</div>
							</div>
						</Notice>
						<Notice v-else :type="'success'">
							<component :is="i18nWelcomeMigratedSuccess"></component>
						</Notice>
					</div>
				</div>
			</Modal>

			<Modal :name="'docContentResetNotice'"
				:label="'Reset Document Content?' | i18n()"
				:actionIsPrimary="false"
				:actionLabel="'Cancel' | i18n"
				@hide="docContentResetNoticeShowing = false">
				<div class="searchwp-doc-ccontent-reset">
					<Notice :type="'warning'">
					<p>{{ 'In order to optimize index rebuilds, SearchWP does not purge extracted Document Content, PDF Metadata, or image EXIF data by default.' | i18n }}</p>
					</Notice>
					<p>{{ 'If you are customizing Document Content, PDF Metadata, or image EXIF in any way, you may want to change that behavior and have SearchWP re-parse that content when rebuilding the index.' | i18n }}</p>
					<p>{{ 'Would you prefer that SearchWP remove all stored Document Content, PDF Metadata, and image EXIF data so it can be re-parsed during Index rebuilds?' | i18n }}</p>
					<div style="padding-top: 0.5em; padding-bottom: 1em;">
						<Checkbox
							:id="'searchwp-doc-content-reset-modal'"
							:checked="false"
							@change="function(value) { resetDocContentOnRebuild = value; }">
							<template v-slot:default>
								{{ 'Remove stored Document Content, PDF Metadata, and image EXIF when rebuilding index' | i18n }}
							</template>
							<template v-slot:description>
								<p class="description">{{ '(This setting can be changed at any time on the Advanced tab of the SearchWP settings screen)' | i18n }}</p>
							</template>
						</Checkbox>
					</div>
					<p><button
						@click="handleDocContentResetModal"
					class="button button-primary">{{ 'Continue Index Rebuild' | i18n }} </button></p>
				</div>
			</Modal>
		</div>
		<transition name="fade">
			<div v-if="saving" class="searchwp-settings-disabled-message">
				<div>
					<span>{{ saving }}</span>
					<div>
						<Spinner
							size="small"
							:line-fg-color="spinnerFgColor"
							:line-bg-color="spinnerBgColor"
						></Spinner>
					</div>
				</div>
			</div>
		</transition>
	</div>
</template>

<script>
import { mapState } from 'vuex';
import Modal from './Modal.vue';
import Engine from './Engine.vue';
import Notice from './Notice.vue';
import Tooltip from './Tooltip.vue';
import isEqual from 'lodash.isequal';
import { __ } from './../helpers.js';
import cloneDeep from 'lodash.clonedeep';
import Spinner from 'vue-simple-spinner';
import IndexStats from './Index/Stats.vue';
import Environment from './Environment.vue';
import Checkbox from './Inputs/Checkbox.vue';
import IndexProgress from './Index/Progress.vue';

export default {
	name: 'Engines',
	components: {
		Engine,
		IndexProgress,
		IndexStats,
		Notice,
		Environment,
		Modal,
		Tooltip,
		Spinner,
		Checkbox
	},
	computed: {
		spinnerFgColor: function() {
			return _SEARCHWP.misc.colors.link.hover;
		},
		spinnerBgColor: function() {
			return _SEARCHWP.misc.colors.border;
		},
		i18nWelcomeWarning: function() {
			return {
				template: '<p> ' + __('_welcome_warning') + '</p>'
			};
		},
		i18nWelcomeBlurb: function() {
			return {
				template: '<div> ' + __('_welcome_blurb') + '</div>'
			};
		},
		i18nWelcomeMigratedIntro: function() {
			return {
				template: '<p> ' + __('_welcome_migration_intro') + '</p>'
			};
		},
		i18nWelcomeMigratedSuccess: function() {
			return {
				template: '<p> ' + __('_welcome_migration_success') + '</p>'
			};
		},
		i18nWelcomeMigratedWarning: function() {
			return {
				template: '<p> ' + __('_welcome_migration_warning') + '</p>'
			};
		},
		activeMigration: function() {
			// Migration is only active if we've migrated and we're showing the Welcome screen.
			return this.migrated && location.search.indexOf('welcome');
		},
		engines: function() {
			return this.$store.state.engines;
		},
		updateInterval: function() {
			return this.indexProgressValue < 100 ? 3000 : 20000;
		},
		indexOutdated: function() {
			return this.$store.state.index.outdated;
		},
		indexProgressValue: function() {
			return this.$store.getters.indexProgress;
		},
		updated: function() {
			// Check to see whether the engine models have changed at all.
			return !isEqual(this.current, this.original);
		},
		...mapState({
			current: state => state.engines
		})
	},
	methods: {
		reintroduce: function(entry) {
			let vm = this;

			// Remove the entry from the list.
			this.$store.commit('removeOmittedEntry', entry);

			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + 'reintroduce_entry',
				source: entry.source,
				id:     entry.id
			}, function(response) {
				clearInterval(vm.pollingIndex);
				vm.$store.commit('updateIndexStats', response.data);
				vm.updateIndexStats();
			});
		},
		migrateStatistics: function() {
			let vm = this;

			vm.migrating = true;
			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + 'migrate_stats'
			}, function(response) {
				vm.migrating = false;
				vm.migratingComplete = true;
			});
		},
		newEngine: function() {
			let self = this;
			this.$store.commit('addNewEngine');

			setTimeout(function() {
				self.$scrollTo('.searchwp-engines > ul > li:last-of-type', 500, { offset: -40 });
			}, 200);
		},
		docContentResetModal: function() {
			// We only want to show this notice if Document Content or PDF Metadata has been added to an Engine.
			let applicable = false;
			let sourceName = 'post' + _SEARCHWP.separator + 'attachment';

			for (let engine in this.engines) {
				if (!this.engines[engine].sources.hasOwnProperty(sourceName)) {
					continue;
				}

				if (
					this.engines[engine].sources[sourceName].attributes.document_content.settings
					|| this.engines[engine].sources[sourceName].attributes.pdf_metadata.settings
					|| this.engines[engine].sources[sourceName].attributes.image_exif.settings
				) {
					applicable = true;
					break;
				}

				if (applicable) {
					break;
				}
			}

			if (applicable) {
				this.docContentResetNoticeShowing = true;
				this.$modal.show('docContentResetNotice');
			} else {
				this.rebuildIndex();
			}
		},
		handleDocContentResetModal: function() {
			this.$modal.hide('docContentResetNotice');
			this.docContentResetAsk = false;

			// Permanently dismiss this dialog.
			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + 'update_setting',
				setting: 'document_content_reset_dismissed',
				value: JSON.stringify(true)
			});

			// Persist the setting.
			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + 'update_setting',
				setting: 'document_content_reset',
				value: JSON.stringify(this.resetDocContentOnRebuild)
			});

			this.rebuildIndex();
		},
		rebuildIndex: function() {
			let vm = this;

			if (confirm(__('_rebuild_index_confirmation'))) {
				// Force an update to the Index Stats.
				vm.saving = __('Resetting index...');
				clearInterval(vm.pollingIndex);
				vm.$store.commit('updateIndexStats', {
					lastActivity: '--',
					indexed: 0,
					total: 0,
					outdated: false
				});
				jQuery.post(ajaxurl, {
					_ajax_nonce: _SEARCHWP.nonce,
					action: _SEARCHWP.prefix + 'rebuild_index'
				}, function(response) {
					vm.saving = false;
					vm.updateIndexStats();
				});
			}
		},
		save: function() {
			let vm = this;

			vm.saving = __('Saving Engines...');

			setTimeout(function() {
				jQuery.post(ajaxurl, {
					_ajax_nonce: _SEARCHWP.nonce,
					action: _SEARCHWP.prefix + 'engines_configs',
					configs: JSON.stringify(vm.$store.state.engines)
				}, function(response) {
					vm.saving = false;

					if (response.success) {
						vm.hasInitialSave = true;

						// We're going to accept the returned configs from the server and reset the entire state using them.
						const engines = response.data.engines;
						vm.original = cloneDeep(engines);
						vm.$store.commit('replaceupdateEngineSettings', cloneDeep(engines));

						// Force the index progress to update right now. Includes whether the index is outdated.
						clearInterval(vm.pollingIndex);
						vm.$store.commit('updateIndexStats', response.data.index);
						vm.updateIndexStats();
					} else {
						alert(__('Saving engine settings FAILED!'));
					}
				});
			}, 500);
		},
		updateIndexStats: function() {
			let vm = this;

			vm.pollingIndex = setInterval(function(){
				if (vm.hasInitialSave && !vm.waiting) {
					vm.waiting = true;
					jQuery.post(ajaxurl, {
						_ajax_nonce: _SEARCHWP.nonce,
						action: _SEARCHWP.prefix + 'index_stats'
					}, function(response) {
						vm.waiting = false;
						if (response.success) {
							vm.$store.commit('updateIndexStats', response.data);

							// If the index has completed building, we can reduce our update interval.
							if (vm.indexProgressValue > 99) {
								clearInterval(vm.pollingIndex);
								vm.updateIndexStats();
							}
						}
					});
				}
			}, vm.updateInterval);
		},
		checkForUnsavedEngines: function(event) {
			if (this.updated) {
				event.preventDefault();
				// Chrome requires returnValue to be set.
				event.returnValue = '';
			}
		}
	},
	created() {
		this.updateIndexStats();

		if (this.hasInitialSave) {
			this.original = cloneDeep(_SEARCHWP.engines);
		}
	},
	mounted() {
		if (this.welcome || this.migrated) {
			this.$modal.show('welcome');
		}
	},
	beforeMount() {
		window.addEventListener('beforeunload', this.checkForUnsavedEngines);
	},
	beforeDestroy() {
		window.removeEventListener('beforeunload', this.checkForUnsavedEngines);
		clearInterval(this.pollingIndex);
	},
	data() {
		return {
			waiting: false,
			original: {},
			saving: false,
			migrating: false,
			migratingComplete: false,
			pollingIndex: null,
			hasInitialSave: _SEARCHWP.misc.hasInitialSave,
			welcome: _SEARCHWP.welcome && _SEARCHWP.welcome === '1' ? true : false,
			migrated: _SEARCHWP.migrated && _SEARCHWP.migrated === '1' ? true : false,
			docContentResetAsk: _SEARCHWP.misc.docContentResetAsk,
			docContentResetNoticeShowing: false,
			resetDocContentOnRebuild: false,
		}
	}
}
</script>

<style lang="scss">
	@import './../global.scss';

	.js .searchwp-settings .postbox .hndle {
		cursor: pointer;
	}

	.searchwp-migrating-statistics {
		margin: 0.5em 0;
		display: flex;
		align-items: center;

		> p {
			margin-right: 0.5em;
		}
	}

	.searchwp-engines {
		width: 75%;

		> ul {
			margin: 0;
			padding: 0;
			list-style: none;
		}

		.searchwp-engine {
			margin-bottom: 2em;
		}
	}

	.searchwp-settings-info {
		width: 23%;
		position: sticky;
		top: calc(1em + 36px); // 1em is .searchwp-settings-view padding-top. 36px is Admin Bar height.

		.searchwp-notice {
			margin-top: 1em;
		}
	}

	.searchwp-settings-actions {
		justify-content: space-between;
	}

	.searchwp-index-info {
		margin-top: 1em;

		> p {
			margin-top: 1em;
		}
	}

	.searchwp-settings .searchwp-actions {
		list-style: none;
		margin: 0;
		padding: 0;
		display: flex;
		flex-wrap: wrap;

		> * {
			margin-right: 1em;
			margin-bottom: 1em;

			&:last-child {
				margin-right: 0;
			}
		}
	}

	.searchwp-settings-view .searchwp-settings-view-header .searchwp-actions.searchwp-settings-engines-actions > * {
		margin-left: 0;
	}

	.searchwp-migration-status {
		text-align: center !important;
	 }

	.searchwp-migration-notes > div > p:first-of-type {
		margin-top: 0;
	 }

	.searchwp-migration-notes table {
		margin-top: 2em;
	 }

	.searchwp-migration-notes .searchwp-notice {
		background-color: #f7f7f7;
	 }

	 #searchwp-settings-engines {
		 position: relative;
	 }

	.searchwp-settings-disabled-message {
		position: absolute;
		top: 0;
		left: 0;
		width: 96%;
		height: 60vh;
		font-size: 1.5em;
		text-align: center;
		display: flex;
		align-content: center;
		align-items: center;
		justify-content: center;
		cursor: wait;

		> div {
			display: flex;
			align-items: center;
			justify-items: center;
			padding: 1.25em 2em;
			background: white;
			border-radius: 3px;
			border: 1px solid #e3e3e3;

			> span {
				display: block;
				line-height: 1;
			}

			> div {
				padding-left: 1em;
			}
		}
	}

	@media screen and (max-width:1024px) {
		.searchwp-settings-view .searchwp-settings {
			display: block;

			.searchwp-engines,
			.searchwp-settings-info {
				width: auto;
			}

			.searchwp-settings-info {
				max-width: 400px;
			}
		}
	}

	@media screen and (max-width:782px) {
		.searchwp-engines .searchwp-engine-settings-sources ul > li {
			width: 50%;
		}
	}

	@media screen and (max-width:700px) {
		.searchwp-settings .searchwp-engines {

			.searchwp-engine-source-config-overview {
				display: block;
			}

			.searchwp-engine-source-config-overview::after {
				display: none;
			}

			.searchwp-engine-source-config-attributes,
			.searchwp-engine-source-config-options-rules {
				width: auto;
			}
		}
	}

	@media screen and (max-width:550px) {
		.wp-core-ui #poststuff .searchwp-engines .searchwp-meta-box-heading .searchwp-meta-box-heading__label {
			display: block;
		}

		.searchwp-engines .searchwp-meta-box-heading__label .searchwp-button-subtle {
			display: none;
		}

		.searchwp-engines .searchwp-meta-box-heading__label > button {
			margin-top: 1em;
			margin-left: 0;
		}

		.searchwp-engines .searchwp-engine-settings-sources ul > li {
			width: 100%;
		}

		.searchwp-engine-source-rule-group-rule .searchwp-engine-source-rule {
			display: block;
		}
		.searchwp-engines .searchwp-engine-source-rule > * {
			margin-left: 0;
			margin-top: 0.5em;
		}
	}
</style>
