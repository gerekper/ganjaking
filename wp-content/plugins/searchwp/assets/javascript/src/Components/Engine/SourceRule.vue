<template>
	<div class="searchwp-engine-source-rule-wrapper">
		<div :class="[ 'searchwp-engine-source-rule', disabled ? 'searchwp-engine-source-rule-disabled' : '' ]">
			<span class="searchwp-engine-source-rule-model">
				<v-select
					v-if="Object.keys(models).length"
					:value="rule"
					:options="models"
					:reduce="value => value.name"
					:searchable="false"
					:clearable="false"
					@input="value => change('rule', value)"
				>
				</v-select>
			</span>

			<span class="searchwp-engine-source-rule-option">
				<v-select
					v-if="model.options.length"
					:value="option"
					:options="model.options"
					:reduce="value => value.value"
					:searchable="false"
					:clearable="false"
					@input="value => change('option', value)"
				>
				</v-select>
				<span v-else>{{ 'is' | i18n }}</span>
			</span>

			<span class="searchwp-engine-source-rule-condition">
				<v-select
					:options="model.conditions"
					:value="condition"
					:searchable="false"
					:clearable="false"
					@input="value => change('condition', value)">
				</v-select>
			</span>

			<span class="searchwp-engine-source-rule-value">
				<v-select
					v-if="model.get_values"
					:value="value"
					:filterable="false"
					@search="onSearch"
					:options="values"
					:searchable="true"
					@input="value => change('value', value)"
					:multiple="true"
				>
					<template slot="no-options">{{ 'Type to search...' | i18n }}</template>
					<template v-slot:spinner="spinner">
						<div v-show="spinner.loading">
							<Spinner
								size="small"
								:line-fg-color="spinnerFgColor"
								:line-bg-color="spinnerBgColor"
							></Spinner>
						</div>
					</template>
				</v-select>
				<v-select
					v-else-if="model.values.length"
					:value="value"
					:options="model.values"
					:searchable="false"
					@input="value => change('value', value)"
					:multiple="true"
				>
				</v-select>
				<!-- need to debounce this -->
				<input v-else class="searchwp-input-mimic" :value="value" @input="event => change('value', event.target.value)">
			</span>
			<span v-if="model.tooltip && model.tooltip.length" class="searchwp-engine-source-rule-tooltip">
				<Tooltip :content="model.tooltip"></Tooltip>
			</span>
		</div>
		<div v-if="model.notes && model.notes.length" class="searchwp-engine-source-rule-notes">
			<p
				class="description"
				v-for="(note, noteIndex) in model.notes"
				:key="noteIndex"
				>{{ note }}</p>
		</div>
	</div>
</template>

<script>
import debounce from 'debounce';
import Tooltip from './../Tooltip.vue';
import Spinner from 'vue-simple-spinner';

export default {
	name: 'EngineSourceRule',
	props: {
		condition: {
			required: true
		},
		option: {
			required: true
		},
		rule: {
			type: String,
			required: true
		},
		value: {
			type: Array|String,
			required: true
		},
		ruleIndex: {
			type: String|Number,
			required: true
		},
		ruleGroupIndex: {
			type: String|Number,
			required: true
		},
		engine: {
			type: String,
			required: true
		},
		source: {
			type: String,
			required: true
		}
	},
	components: {
		Spinner,
		Tooltip
	},
	methods: {
		onSearch: function(search, loading) {
			if (search) {
				loading(true);
				this.search(loading, search, this);
			}
		},
		search: debounce((loading, search, vm) => {
			let results = new Promise(function(resolve, reject) {
				jQuery.post(ajaxurl, {
					_ajax_nonce: _SEARCHWP.nonce,
					action: vm.model.get_values,
					rule: vm.model.name,
					option: vm.option,
					search: search
				}, function(response) {
					if (response.success) {
						resolve(response.data);
					} else {
						reject(response);
					}
				});
			});

			results.then(function(values) {
				vm.values = values;
				loading(false);
			});
		}, 300),
		change: function(attribute, value) {
			this.$store.commit('updateEngineSourceRule', {
				engine: this.engine,
				source: this.source,
				ruleGroupIndex: this.ruleGroupIndex,
				ruleIndex: this.ruleIndex,
				rule: this.rule,
				attribute: attribute,
				value: value
			});
		}
	},
	computed: {
		spinnerFgColor: function() {
			return _SEARCHWP.misc.colors.link.hover;
		},
		spinnerBgColor: function() {
			return _SEARCHWP.misc.colors.border;
		},
		models: function() {
			// We need to build an array for the v-select.
			let models = [];

			for (const model in _SEARCHWP.sources[this.source].rules) {
				models.push(_SEARCHWP.sources[this.source].rules[model]);
			}

			return models;
		},
		model: function() {
			return _SEARCHWP.sources[this.source].rules[this.rule];
		}
	},
	data() {
		return {
			disabled: false,
			values: []
		}
	}
}
</script>

<style lang="scss">
	.searchwp-engine-source-rule {
		flex: 1;
		display: flex;
		align-items: center;
		position: relative;

		&.searchwp-engine-source-rule-disabled:after {
			display: block;
			content: '';
			position: absolute;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			background-color: #f8f8f8;
			cursor: wait;
			opacity: 0.5;
		}

		> * {
			display: flex;
			margin-left: 0.8em;

			&:first-of-type {
				margin-left: 0;
			}
		}

		.v-select {
			width: 100%;
		}
	}

	.searchwp-engine-source-rule-value {
		flex: 1;
		flex-wrap: wrap;

		// Text input should match v-select.
		> .searchwp-input-mimic {
			width: 100%;
		}
	}

	.searchwp-engine-source-rule-tooltip {
		margin-left: 0;
	}

	.searchwp-engine-source-rule-wrapper {
		width: 100%;
	}

	.searchwp-engine-source-rule-notes {
		padding-left: 3px;
		padding-top: 0.3em;
	}
</style>
