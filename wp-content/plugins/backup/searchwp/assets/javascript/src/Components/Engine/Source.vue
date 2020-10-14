<template>
	<MetaBox
		:collapsible="true"
		:active="isExpanded"
		v-on:afterToggle="afterToggle"
		class="searchwp-engine-source">
		<template v-slot:heading>
			<span>{{ plural }}</span>
			<span>
				<span v-if="attributesCount">{{ attributesCount }}</span>
				<span v-if="rulesCount">{{ rulesCount }}</span>
			</span>
		</template>
		<template v-slot:content>
			<div class="inside searchwp-engine-source-config">
				<div class="searchwp-engine-source-config-overview" :style="{ borderColor: borderColor }">
					<div class="searchwp-engine-source-config-attributes">
						<h3>{{ 'Applicable Attribute Relevance' | i18n }}</h3>
						<EngineSourceAttributes
							:engine="engine"
							:source="name"
						></EngineSourceAttributes>
					</div>
					<div class="searchwp-engine-source-config-options-rules">
						<div v-if="options && options.length" class="searchwp-engine-source-config-options">
							<h3>{{ 'Options' | i18n }}</h3>
							<ul v-if="options">
								<li v-for="(option, index) in options" :key="index">
									<SourceOption
										:engine="engine"
										:source="name"
										:option="option.name"
										@change="function(settings) { updateSourceOption(index, settings)}"
									></SourceOption>
								</li>
							</ul>
						</div>
						<div class="searchwp-engine-source-config-rules">
							<h3>{{ 'Rules' | i18n }}</h3>
							<p v-if="ruleGroups && !ruleGroups.length" class="description">{{ '_no_rules_for_note' | i18n([plural]) }}</p>
							<EngineSourceRulesOverview v-else :source="name" :engine="engine" ></EngineSourceRulesOverview>
							<ul class="searchwp-actions searchwp-engine-source-config-rules-actions">
								<li>
									<button type="button" class="button" @click="$modal.show(id + '-rule-editor')">
										{{ 'Edit Rules' | i18n }}
									</button>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<Modal :name="id + '-rule-editor'"
					:maxWidth="980"
					:label="'_edit_rules_for_source_engine' | i18n([plural, engineLabel])"
					:showAction="Object.keys(ruleModels).length===0">
					<div class="searchwp-engine-source-config-rules-editor">
						<EngineSourceRules
							:engine="engine"
							:source="name"
							@close="$modal.hide(id + '-rule-editor')"
						></EngineSourceRules>
					</div>
				</Modal>

			</div>
		</template>
	</MetaBox>
</template>

<script>
import MetaBox from './../MetaBox.vue';
import Modal from './../Modal.vue';
import { __ } from './../../helpers.js';
import cloneDeep from 'lodash.clonedeep';
import { persistViewConfig } from './../../helpers.js';
import SourceOption from './../Inputs/SourceOption.vue';
import EngineSourceRules from './../Engine/SourceRules.vue';
import { EngineUtils } from './../../Mixins/EngineUtils.js';
import EngineSourceAttributes from './../Engine/SourceAttributes.vue';
import EngineSourceRulesOverview from './../Engine/SourceRulesOverview.vue';

export default {
	name: 'EngineSource',
	props: {
		engine: {
			type: String,
			required: true
		},
		name: {
			type: String,
			required: true
		}
	},
	components: {
		MetaBox,
		Modal,
		SourceOption,
		EngineSourceAttributes,
		EngineSourceRulesOverview,
		EngineSourceRules
	},
	mixins: [EngineUtils],
	computed: {
		plural: function() {
			return this.getEngineSourceProperty(this.engine, this.name, 'labels').plural;
		},
		attributes: function() {
			return this.getEngineSourceProperty(this.engine, this.name, 'attributes');
		},
		ruleGroups: function() {
			return this.getEngineSourceProperty(this.engine, this.name, 'ruleGroups');
		},
		ruleModels: function(engine, source) {
			return this.getEngineSourceProperty(this.engine, this.name, 'rules');
		},
		options: function() {
			return this.getEngineSourceProperty(this.engine, this.name, 'options');
		},
		engineLabel: function() {
			return this.getEngineProperty(this.engine, 'label');
		},
		id: function() {
			// We're going to use the engine index in case the engine name gets updated.
			const engineIndex = Object.keys(this.$store.state.engines).indexOf(this.engine);

			return engineIndex + _SEARCHWP.separator + this.name;
		},
		isExpanded: function() {
			let expanded = true;

			// SettingsView.php config stores whether this is collapsed.
			if ( -1 !== this.$store.state.view.collapsed.indexOf(this.id) ) {
				expanded = false;
			}

			return expanded;
		},
		attributesCount: function() {
			let count = 0;

			for (const attribute in this.attributes) {
				const settings = this.attributes[attribute].settings;

				if ( !settings ) {
					continue;
				}

				if ( 'object' === typeof settings ) {
					count += Object.keys(settings).length;
				} else if ( 'array' === typeof settings ) {
					count += settings.length;
				} else {
					count += 1;
				}
			}

			if (!count) {
				return 0;
			} else {
				if (count > 1) {
					return __('_attributes', [count]);
				} else {
					return __('_attribute', [count]);
				}
			}
		},
		rulesCount: function() {
			let count = 0;

			for (const group in this.ruleGroups) {
				count += this.ruleGroups[group].rules.length;
			}

			if (!count) {
				return 0;
			} else {
				if (count > 1) {
					return __('_rules', [count]);
				} else {
					return __('_rule', [count]);
				}
			}
		}
	},
	methods: {
		updateSourceOption: function(index, settings) {
			this.$store.commit('updateEngineSourceOptions', {
				engine: this.engine,
				source: this.name,
				index: index,
				settings: settings
			});
		},
		afterToggle: function(expanded) {
			this.$store.commit('toggleEngineSource', this.id);
		},
	},
	data() {
		return {
			borderColor: _SEARCHWP.misc.colors.border
		}
	}
}
</script>

<style lang="scss">
	// Core overrides.
	#poststuff .searchwp-engine-source h2.searchwp-meta-box-heading {
		font-weight: 500;
		padding-right: 0; // The toggle button has padding.
		padding-top: 4px;
		padding-bottom: 4px;
	}

	.searchwp-engine-source {
		background-color: #f8f8f8; // Related to disabled Rule background color.
		margin: 12px 0 0;

		.searchwp-meta-box-heading__label {

			> span:last-of-type {
				flex: auto;
				text-align: right;
				font-size: 0.8em;
				font-style: italic;
				font-weight: normal;
				margin-top: 1px;

				> span {
					&:after {
						content: ',';
					}

					&:last-of-type:after {
						content: '';
					}
				}
			}
		}
	}

	.searchwp-engine-source-config {
		h3 {
			// margin-bottom: 0;
			font-size: 14px;
			font-weight: 500;
		}

		h4 {
			// margin: 0;
			font-size: 13px;
			font-weight: 500;
		}
	}

	.searchwp-settings .searchwp-engine-source-config-overview {
		display: flex;
		justify-content: space-between;
		position: relative;

		&:after {
			display: block;
			content: '';
			position: absolute;
			border-color: inherit;
			border-width: 0 0 0 1px;
			border-style: solid;
			top: 1em;
			bottom: 0;
			left: 42.6%;
			opacity: 0.85;
		}

		.searchwp-actions {
			margin-top: 1.5em;

			> * {
				margin-bottom: 0;
			}
		}

		.searchwp-modal-heading .searchwp-actions {
			margin-top: 0;
		}
	}

	.searchwp-engine-source-config-attributes {
		width: 41%;

		.searchwp-engine-source-attribute-weight {
			margin-bottom: 1em;

			dd > span {
				opacity: 0;
				transition: opacity 75ms ease-in-out;
			}

			&:hover {
				background-color: rgba(40, 40, 40, 0.05);
				box-shadow: 0 0 0 5px rgba(40, 40, 40, 0.05);

				dd > span {
					opacity: 0.6;
				}
			}
		}
	}

	.searchwp-settings-view .searchwp-settings li.searchwp-engine-source-attribute-weight {
		margin-bottom: 1em;
	}

	.searchwp-engine-source-config-options-rules {
		width: 54%;
	}

	.searchwp-engine-source-config-rules-editor-actions {
		margin-top: 1em;
	}

	.searchwp-button-remove-source {
		font-size: 0.9em;
		font-weight: normal;
	}
</style>
