import Vue from 'vue';
import Vuex from 'vuex';
import VueCollapse from 'vue2-collapse';
import VModal from 'vue-js-modal';
import vSelect from 'vue-select';
import VTooltip from 'v-tooltip';
import VueScrollTo from 'vue-scrollto';
import cloneDeep from 'lodash.clonedeep';

import Engines from './Components/Engines.vue';
import { __, normalizeSource, persistViewConfig, removeCollapsedSources } from './helpers.js';

Vue.use(Vuex);
Vue.use(VueCollapse);
Vue.use(VModal, {componentName: 'v-modal'});
Vue.use(VTooltip);
Vue.use(VueScrollTo);

vSelect.props.components.default = () => ({
	Deselect: {
		render: createElement => createElement('span', { class: 'dashicons dashicons-no-alt' } ),
	},
	OpenIndicator: {
		render: createElement => createElement('span', { class: 'dashicons dashicons-arrow-down-alt2' } ),
	},
});

Vue.component('v-select', vSelect);

Vue.filter('i18n', function (source, placeholders = []) {
	return __( source, placeholders );
});

const store = new Vuex.Store({
	state: {
		engines: cloneDeep(_SEARCHWP.engines),
		index: {
			lastActivity: _SEARCHWP.index.last_activity,
			indexed: _SEARCHWP.index.indexed,
			omitted: _SEARCHWP.index.omitted,
			total: _SEARCHWP.index.total,
			outdated: false
		},
		view: cloneDeep(_SEARCHWP.view),
		cache: {
			// Stores Attribute Options that have been retrieved via AJAX during this session.
			attributeOptions: {}
		}
	},
	getters: {
		indexProgress: state => {
			if (!state.index.indexed && !state.index.total) {
				return 0;
			} else {
				return Math.round((state.index.indexed / state.index.total) * 100);
			}
		},
		engineCollapsedSources: state => (engine) => {
			const engineIndex = Object.keys(state.engines).indexOf(engine) + _SEARCHWP.separator;

			return state.view.collapsed.filter((source) => engineIndex === source.substring(0, engineIndex.length));
		},
		cachedAttributeOptions: state => (attribute) => {
			return state.cache.attributeOptions.hasOwnProperty(attribute) ? state.cache.attributeOptions[attribute] : [];
		},
		adminEngine: state => {
			let adminEngine = false;

			for (const engine in state.engines) {
				if (state.engines[engine].settings.adminengine) {
					adminEngine = state.engines[engine];
					break;
				}
			}

			return adminEngine;
		}
	},
	mutations: {
		removeOmittedEntry(state, entry) {
			for(let i in state.index.omitted) {
				if (
					entry.id == state.index.omitted[i].id
					&& entry.source == state.index.omitted[i].source
				) {
					state.index.omitted.splice(i, 1);
				}
			}
		},
		toggleEngineSource(state, payload) {
			const engineSourceIndex = state.view.collapsed.indexOf(payload);

			if (-1 !== engineSourceIndex) {
				state.view.collapsed.splice(engineSourceIndex, 1);
			} else {
				state.view.collapsed.push(payload);
			}

			persistViewConfig(state);
		},
		collapseAllEngineSources(state, engine) {
			const engineIndex   = Object.keys(state.engines).indexOf(engine);
			const engineSources = Object.keys(state.engines[engine].sources)
									.map(source => engineIndex + _SEARCHWP.separator + source);

			// Build a unique array.
			const collapsed = state.view.collapsed.concat(engineSources)
								.filter(function (value, index, self) {
									return self.indexOf(value) === index;
								});

			Vue.set(state.view, 'collapsed', collapsed);

			persistViewConfig(state);
		},
		expandAllEngineSources(state, engine) {
			const engineIndex = Object.keys(state.engines).indexOf(engine) + _SEARCHWP.separator;
			const collapsed   = state.view.collapsed.filter(source => engineIndex !== source.substring(0, engineIndex.length));

			Vue.set(state.view, 'collapsed', collapsed);

			persistViewConfig(state);
		},
		cacheDynamicAttributeOptions(state, payload) {
			if (!state.cache.attributeOptions.hasOwnProperty(payload.attribute)) {
				state.cache.attributeOptions[payload.attribute] = [];
			}

			const cached = cloneDeep(state.cache.attributeOptions[payload.attribute]).map(x => JSON.stringify(x));

			payload.options.forEach(function(option) {
				if (-1 === cached.indexOf(JSON.stringify(option))) {
					state.cache.attributeOptions[payload.attribute].push(option);
				}
			});
		},
		engineSourceRemoved(state, payload) {
			let engineIndex = Object.keys(state.engines).indexOf(payload.engine);
			let collapsed = state.view.collapsed.indexOf(engineIndex + _SEARCHWP.separator + payload.source);

			if (-1 !== collapsed) {
				Vue.delete(state.view.collapsed, collapsed);
			}
		},
		addNewEngine(state) {
			// Base all new engines on the Default engine model.
			let newEngineModel = cloneDeep(state.engines.default);

			// Set label and name.
			newEngineModel.label = 'Supplemental';
			newEngineModel.name = 'supplemental';
			while (state.engines.hasOwnProperty(newEngineModel.name)) {
				newEngineModel.name += '_copy';
			}

			// New engines have Sources that have Attributes with Defaults.
			const sources = cloneDeep(_SEARCHWP.sources);
			let newEngineSources = {};
			for (const newEngineSource in sources) {
				let newSourceModel = normalizeSource(sources, newEngineSource);

				for (const attribute in newSourceModel.attributes) {
					if (newSourceModel.attributes[attribute].settings) {
						newEngineSources[newEngineSource] = newSourceModel;
						break;
					}
				}
			}

			newEngineModel.sources = newEngineSources;

			Vue.set(state.engines, newEngineModel.name, newEngineModel);

			// Remove any collapsed state that may be out of date.
			if(removeCollapsedSources(state, newEngineModel.name)) {
				persistViewConfig(state);
			}
		},
		updateIndexStats(state, payload) {
			state.index = payload;
		},
		deleteEngine(state, engine) {
			if(removeCollapsedSources(state, engine)) {
				persistViewConfig(state);
			}
			Vue.delete(state.engines, engine);
		},
		replaceupdateEngineSettings(state, payload) {
			Vue.set(state, 'engines', payload);
		},
		updateEngineSettings(state, payload) {
			let oldEngines = state.engines;
			let newEngines = {};

			for (let engine in oldEngines) {
				if (engine !== payload.originalEngineName) {
					newEngines[engine] = oldEngines[engine];
					continue;
				}

				newEngines[payload.newEngineName] = {
					label: payload.newEngineLabel,
					name: payload.newEngineName,
					sources: payload.sources,
					settings: {
						stemming: payload.stemming,
						adminengine: payload.adminengine
					}
				};
			}

			Vue.set(state, 'engines', newEngines);
		},
		updateEngineSourceOptions(state, payload) {
			Vue.set(state.engines[payload.engine].sources[payload.source].options, payload.index, payload.settings);
		},
		updateSourceRuleValues(state, payload) {
			if (!state.ruleValues[payload.source]) {
				state.ruleValues[payload.source] = {};
			}

			if (!state.ruleValues[payload.source][payload.rule]) {
				state.ruleValues[payload.source][payload.rule] = {};
			}

			state.ruleValues[payload.source][payload.rule][payload.option] = payload.values;
		},
		updateEngineSourceAttributes(state, payload) {
			state.engines[payload.engine].sources[payload.source].attributes = payload.attributes;
		},
		updateEngineSourceAttributeSettings(state, payload) {
			// We may need to alter a property of the settings if this Attribute has options.
			if (payload.option) {
				state.engines[payload.engine].sources[payload.source].attributes[payload.attribute].settings[payload.option] = payload.value;
			} else {
				state.engines[payload.engine].sources[payload.source].attributes[payload.attribute].settings = payload.value;
			}
		},
		updateEngineSourceRuleGroupType(state, payload) {
			state.engines[payload.engine]
				.sources[payload.source]
				.ruleGroups[payload.index]
				.type = payload.type;
		},
		updateEngineSourceRule(state, payload) {
			// Update the rule attribute value first.
			state.engines[payload.engine]
				.sources[payload.source]
				.ruleGroups[payload.ruleGroupIndex]
				.rules[payload.ruleIndex][payload.attribute] = payload.value;

			// If the Rule model changed, we need to change everything else.
			if ('rule'===payload.attribute) {
				let newRuleModel = JSON.parse(JSON.stringify(state.engines[payload.engine].sources[payload.source].rules[payload.value]));

				state.engines[payload.engine]
					.sources[payload.source]
					.ruleGroups[payload.ruleGroupIndex]
					.rules[payload.ruleIndex].condition = newRuleModel.conditions ? newRuleModel.conditions[0] : null;

				state.engines[payload.engine]
					.sources[payload.source]
					.ruleGroups[payload.ruleGroupIndex]
					.rules[payload.ruleIndex].option = newRuleModel.options ? newRuleModel.options[0].value : null;

				state.engines[payload.engine]
					.sources[payload.source]
					.ruleGroups[payload.ruleGroupIndex]
					.rules[payload.ruleIndex].value = newRuleModel.values ? [] : '';
			}

			// If a new Rule option was chosen, the existing values no longer apply.
			if ('option'===payload.attribute) {
				let ruleModel = JSON.parse(JSON.stringify(state.engines[payload.engine].sources[payload.source].rules[payload.rule]));

				state.engines[payload.engine]
					.sources[payload.source]
					.ruleGroups[payload.ruleGroupIndex]
					.rules[payload.ruleIndex].value = ruleModel.values ? [] : '';
			}
		},
		removeEngineSourceRuleGroup(state, payload) {
			state.engines[payload.engine]
				.sources[payload.source]
				.ruleGroups.splice(payload.index, 1);
		},
		removeEngineSourceRuleGroupRule(state, payload) {
			state.engines[payload.engine]
				.sources[payload.source]
				.ruleGroups[payload.ruleGroupIndex]
				.rules.splice(payload.ruleIndex, 1);
		},
		addEngineSourceRuleGroupRule(state, payload) {
			const ruleModels = JSON.parse(JSON.stringify(state.engines[payload.engine].sources[payload.source].rules));
			const newRuleModel = JSON.parse(JSON.stringify(state.engines[payload.engine].sources[payload.source].rules[Object.keys(ruleModels)[0]]));

			state.engines[payload.engine].sources[payload.source].ruleGroups[payload.ruleGroupIndex].rules.push({
				condition: newRuleModel.conditions ? newRuleModel.conditions[0] : null,
				option: newRuleModel.options ? newRuleModel.options[0].value : null,
				rule: newRuleModel.name,
				value: newRuleModel.values ? [] : ''
			});
		},
		addEngineSourceRuleGroup(state, payload) {
			const ruleModels = JSON.parse(JSON.stringify(state.engines[payload.engine].sources[payload.source].rules));
			const newRuleModel = JSON.parse(JSON.stringify(state.engines[payload.engine].sources[payload.source].rules[Object.keys(ruleModels)[0]]));

			state.engines[payload.engine]
				.sources[payload.source]
				.ruleGroups.push({
					type: 'IN',
					rules: [{
						condition: Array.isArray(newRuleModel.conditions) ? newRuleModel.conditions[0] : null,
						option: Array.isArray(newRuleModel.options) ? newRuleModel.options[0].value : null,
						rule: newRuleModel.name,
						value: Array.isArray(newRuleModel.values) ? [] : ''
					}]
				});
		}
	}
});

new Vue({
	el: '#searchwp-engines',
	store,
	render: h => h(Engines)
});