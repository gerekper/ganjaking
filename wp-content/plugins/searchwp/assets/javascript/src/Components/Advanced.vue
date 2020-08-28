<template>
	<div :class="['searchwp-settings', 'searchwp-settings-advanced' ]">
		<div class="searchwp-settings-view-header">
			<h1>{{ 'SearchWP Advanced' | i18n }}</h1>
		</div>
		<div :class="['searchwp-settings-types' ]">
			<MetaBox
				class="searchwp-advanced-settings"
				:active="true"
				:label="'Advanced Settings' | i18n">
				<template v-slot:heading>
					<span>{{ 'Actions & Settings' | i18n }}</span>
				</template>
				<template v-slot:content>
					<div class="inside">
						<ul class="searchwp-advanced-settings-items">
							<li class="searchwp-advanced-settings-item-action">
								<button :disabled="wakingIndexer" class="button" @click="wakeIndexer">
									{{ 'Wake Up Indexer' | i18n }}
								</button>
								<p class="description">{{ '_wake_indexer_note' | i18n }}</p>
							</li>
							<li v-for="(setting, name) in settings" :key="name"  class="searchwp-advanced-settings-item-setting">
								<Checkbox
									:id="'searchwp-settings-advanced-' + name"
									:checked="!!setting.value"
									@change="function(value) { toggleSetting(name) }">
									<template v-slot:default>
										<Tooltip v-if="setting.tooltip" :content="setting.tooltip">{{ setting.label }}</Tooltip>
										<span v-else>{{ setting.label }}</span>
									</template>
									<template v-slot:description v-if="setting.description && setting.description.length">
										<p class="description">{{ setting.description }}</p>
									</template>
								</Checkbox>
							</li>
						</ul>
					</div>
				</template>
			</MetaBox>

			<MetaBox
				class="searchwp-advanced-engine-config-transfer"
				:active="true"
				:label="'Engine Configuration Transfer' | i18n">
				<template v-slot:heading>
					<span>{{ 'Engine Configuration Transfer' | i18n }}</span>
				</template>
				<template v-slot:content>
					<vue-tabs :activeTextColor="activeTabColor" :disabledTextColor="inactiveTabColor">
						<v-tab :title="'Import' | i18n">
							<div class="inside">
								<textarea v-model="enginesImport" :placeholder="'_import_note' | i18n"></textarea>
								<ul class="searchwp-actions">
									<li>
										<button class="button" @click="importEngines">{{ 'Import Engine(s)' | i18n }}</button>
									</li>
								</ul>
							</div>
						</v-tab>
						<v-tab :title="'Export' | i18n">
							<div class="inside">
								<textarea
									v-model="enginesExport"
									:placeholder="'There are no engines to export!' | i18n"
									@focus="$event.target.select()"
									@click="$event.target.select()">
								</textarea>
							</div>
						</v-tab>
					</vue-tabs>
				</template>
			</MetaBox>
		</div>
	</div>
</template>

<script>
import MetaBox from './MetaBox.vue';
import Tooltip from './Tooltip.vue';
import { __ } from './../helpers.js';
import Checkbox from './Inputs/Checkbox.vue';

export default {
	name: 'Advanced',
	components: {
		MetaBox,
		Tooltip,
		Checkbox
	},
	methods: {
		importEngines: function() {
			if (confirm(__('Existing engines with the same name will be overwritten. Continue?'))) {
				let vm = this;

				vm.importing = true;

				setTimeout(function() {
					jQuery.post(ajaxurl, {
						_ajax_nonce: _SEARCHWP.nonce,
						action: _SEARCHWP.prefix + 'import_engines',
						configs: JSON.stringify(JSON.parse(vm.enginesImport))
					}, function(response) {
						vm.importing = false;

						if (response.success) {
							alert(__('Engines import complete.'));
						} else {
							console.log(response);
							alert(__('Saving engine settings FAILED! View console for more information.'));
						}
					});
				}, 500);
			}
		},
		wakeIndexer: function() {
			if (confirm(__('Are you sure? The existing background process will be destroyed and then restarted.'))) {
				let vm = this;
				vm.wakingIndexer = true;
				jQuery.post(ajaxurl, {
					_ajax_nonce: _SEARCHWP.nonce,
					action: _SEARCHWP.prefix + 'wake_indexer'
				}, function(response) {
					vm.wakingIndexer = false;
					if (!response.success) {
						console.log(response);
						alert(__('Waking indexer FAILED. View console for more information.'));
					}
				});
			}
		},
		toggleSetting: function(setting) {
			this.settings[setting].value = ! this.settings[setting].value;

			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + 'update_setting',
				setting: setting,
				value: JSON.stringify(this.settings[setting].value)
			}, function(response) {
				if (!response.success) {
					alert(__('Settings update FAILED'));
				}
			});
		}
	},
	data() {
		return {
			importing: false,
			wakingIndexer: false,
			enginesImport: '',
			enginesExport: JSON.stringify(_SEARCHWP.engines),
			activeTabColor: _SEARCHWP.misc.colors.hover,
			inactiveTabColor: _SEARCHWP.misc.colors.base,
			settings: {
				debug: {
					label: __('Debugging enabled'),
					value: !!_SEARCHWP.settings.debug,
					tooltip: '',
					description: __('Log information during indexing and searching for review')
				},
				partial_matches: {
					label: __('Partial matches (fuzzy when necessary)'),
					value: !!_SEARCHWP.settings.partial_matches,
					tooltip: '',
					description: __('Find partial matches when search terms yield no results')
				},
				do_suggestions: {
					label: __('Automatic "Did you mean?" corrections'),
					value: !!_SEARCHWP.settings.do_suggestions,
					tooltip: '',
					description: __('Use the closest match for searches that yield no results and output a notice')
				},
				quoted_search_support: {
					label: __('Support "quoted/phrase searches"'),
					value: !!_SEARCHWP.settings.quoted_search_support,
					tooltip: '',
					description: __('When search terms are wrapped in double quotes, results will be limited to those with exact matches')
				},
				highlighting: {
					label: __('Highlight terms in results'),
					value: !!_SEARCHWP.settings.highlighting,
					tooltip: '',
					description: __('Automatically highlight search terms when possible')
				},
				parse_shortcodes: {
					label: __('Parse Shortcodes when indexing'),
					value: !!_SEARCHWP.settings.parse_shortcodes,
					tooltip: '',
					description: __('Index expanded Shortcode output (at the time of indexing)')
				},
				tokenize_pattern_matches: {
					label: __('Tokenize regex pattern matches'),
					value: !!_SEARCHWP.settings.tokenize_pattern_matches,
					tooltip: '',
					description: __('When enabled, additional tokens will be generated from regex pattern matches')
				},
				remove_min_word_length: {
					label: __('Remove minimum word length'),
					value: !!_SEARCHWP.settings.remove_min_word_length,
					tooltip: '',
					description: __('Index everything regardless of token length')
				},
				indexer_paused: {
					label: __('Indexer Paused'),
					value: !!_SEARCHWP.settings.indexer_paused,
					tooltip: __('Queued updates will be processed immediately when the indexer is unpaused'),
					description: __('Continue to queue (but do not apply) delta index updates')
				},
				reduced_indexer_aggressiveness: {
					label: __('Reduced indexer aggressiveness'),
					value: !!_SEARCHWP.settings.reduced_indexer_aggressiveness,
					tooltip: '',
					description: __('Process less data per index pass (less resource intensive, but slower)')
				},
				document_content_reset: {
					label: __('Delete parsed document content when rebuilding Index'),
					value: !!_SEARCHWP.settings.document_content_reset,
					tooltip: __('Leaving this parsed content in place speeds up index rebuilds'),
					description: __('Remove extracted Document Content, PDF Metadata, and image EXIF data and re-parse when rebuilding Index')
				},
				nuke_on_delete: {
					label: __('Remove all data on uninstall'),
					value: !!_SEARCHWP.settings.nuke_on_delete,
					tooltip: '',
					description: __('Remove all traces of SearchWP when it is deactivated and deleted from the Plugins page')
				}
			}
		}
	}
}
</script>

<style lang="scss">
	@import './../global.scss';

	.searchwp-settings-advanced {
		.searchwp-settings-view-header {
			width: 100%;
		}
	}

	.searchwp-settings-types {
		display: flex;
		justify-content: space-between;
		width: 100%;

		> * {
			width: 49%;
			margin-top: 1em;
		}
	}

	.searchwp-settings-view .searchwp-settings .searchwp-advanced-settings-items {
		margin: 0.7em 0 0;

		.searchwp-advanced-settings-item-action {
			margin-bottom: 1.5em;

			p.description {
				margin-top: 0.5em;
			}
		}

		li + li {
			margin-top: 0.5em;
		}
	}

	.searchwp-settings-view .v-collapse-content-end {
		display: flex !important;
		flex-direction: column;
		flex: 1;
	}

	.searchwp-advanced-engine-config-transfer {
		display: flex;
		flex-direction: column;

		.searchwp-actions {
			margin: 1em 0 0;

			> * {
				margin: 0;
			}
		}

		.tab-content {
			padding-top: 0.5em;
		}

		.vue-tabs,
		.tab-content,
		.tab-container,
		.inside,
		textarea {
			display: flex;
			flex-direction: column;
			flex: 1;
		}

		textarea {
			font-family: monospace;
			border-radius: 2px;
			resize: none;
			min-height: 8em;
		}
	}

	.searchwp-advanced-settings-item-setting {

		label {
			font-weight: 400;
		}

		p.description {
			margin-top: 0;
			margin-bottom: 1em;
			margin-left: 23px;
		}

		&:last-child p.description {
			margin-bottom: 0;
		}
	}

	@media screen and (max-width:1024px) {
		.searchwp-settings-view .searchwp-settings-types {
			display: block;

			> * {
				width: auto;
				margin-bottom: 1.5em;
			}
		}
	}
</style>
