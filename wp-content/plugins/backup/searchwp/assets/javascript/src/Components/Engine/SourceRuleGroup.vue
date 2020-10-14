<template>
	<div class="searchwp-engine-source-rule-group" :style="{ borderColor: borderColor }">
		<div class="searchwp-engine-source-rule-group-type" :style="{ borderColor: borderColor }">
			<v-select
				:value="config.type"
				:options="conditions"
				:reduce="value => value.value"
				:searchable="false"
				:clearable="false"
				@input="typeChanged">
			</v-select>
			<ul class="searchwp-actions searchwp-engine-source-rule-group-actions">
				<li>
					<button class="button button-link-delete" @click="remove">
						{{ 'Delete' | i18n }}
					</button>
				</li>
			</ul>
		</div>

		<ul class="searchwp-engine-source-rule-group-rules">
			<li
				v-for="(rule, ruleIndex) in config.rules"
				:key="ruleIndex"
			>
				<p v-if="ruleIndex>0" class="searchwp-rules-or">{{ 'OR' | i18n }}</p>
				<div class="searchwp-engine-source-rule-group-rule">
					<SourceRule
						:condition="rule.condition"
						:option="rule.option"
						:rule="rule.rule"
						:value="rule.value"
						:ruleIndex="ruleIndex"
						:ruleGroupIndex="index"
						:source="source"
						:engine="engine"
					></SourceRule>
					<ul v-if="ruleIndex>0 || config.rules.length > 1"
						class="searchwp-actions searchwp-actions--mini searchwp-engine-source-rule-actions">
						<li>
							<button class="button searchwp-engine-source-rule-actions-remove" @click="removeRule(ruleIndex)">
								<span>
									<span class="searchwp-button-icon dashicons dashicons-no-alt"></span>
								</span>
							</button>
						</li>
					</ul>
				</div>
			</li>
		</ul>
		<ul class="searchwp-actions searchwp-engine-source-rule-group-rule-actions">
			<li>
				<button class="button" @click="addRule()">{{ 'OR' | i18n }}</button>
			</li>
		</ul>
	</div>
</template>

<script>
import { __ } from './../../helpers.js';
import SourceRule from './SourceRule.vue';

export default {
	name: 'EngineSourceRuleGroup',
	props: {
		config: {
			type: Object,
			required: false
		},
		engine: {
			type: String,
			required: true
		},
		models: {
			type: Object,
			required: false
		},
		source: {
			type: String,
			required: true
		},
		index: {
			type: Number,
			required: true
		}
	},
	components: {
		SourceRule
	},
	computed: {
		borderColor: function() {
			return _SEARCHWP.misc.colors.border;
		}
	},
	methods: {
		typeChanged: function(type) {
			this.$store.commit('updateEngineSourceRuleGroupType', {
				engine: this.engine,
				source: this.source,
				index: this.index,
				type: type
			});
		},
		removeRule: function(index) {
			this.$store.commit('removeEngineSourceRuleGroupRule', {
				engine: this.engine,
				source: this.source,
				ruleGroupIndex: this.index,
				ruleIndex: index
			});
		},
		remove: function() {
			this.$store.commit('removeEngineSourceRuleGroup', {
				engine: this.engine,
				source: this.source,
				index: this.index
			});
		},
		addRule: function() {
			this.$store.commit('addEngineSourceRuleGroupRule', {
				engine: this.engine,
				source: this.source,
				ruleGroupIndex: this.index
			});
		}
	},
	data () {
		return {
			conditions: [ // There are only two possibilities for Rule group logic.
				{ label: __('Exclude entries if:'), value: 'NOT IN' },
				{ label: __('Only show entries if:'), value: 'IN' },
			]
		}
	}
}
</script>

<style lang="scss">
	.searchwp-engine-source-rules {

		.vs__selected {
			min-width: 4em;
		}
	}

	.searchwp-engine-source-rule-group {
		border-radius: 1px;
		border-style: solid;
		border-width: 1px;
		background-color: #f8f8f8;
	}

	.wp-core-ui .searchwp-settings .searchwp-actions.searchwp-engine-source-rule-group-actions {

		button {
			border-color: transparent;
			background-color: transparent;
			margin-right: 0.5em;

			&.button-link-delete {
				padding-right: 0;
				padding-left: 0;
			}

			&:hover {
				border-color: transparent;
				background-color: transparent;
				text-decoration: underline;
			}
		}
	}

	.searchwp-engine-source-rule-group-type {
		padding: 0.5em 0 0.5em 0.5em;
		display: flex;
		align-items: center;
		justify-content: space-between;
		background: white;
		border-width: 0 0 1px;
		border-style: solid;

		.v-select {
			display: inline-block;
		}
	}

	.searchwp-rules-or {
		font-weight: 500;
		padding: 0 0.5em;
		margin: 0.5em 0;
	}

	ul.searchwp-engine-source-rule-group-rules {
		margin-top: 1em;
		margin-bottom: 1em;
	}

	.searchwp-engine-source-rule-group-rule {
		display: flex;
		align-items: center;
		margin: 0.5em;

		.searchwp-engine-source-rule {
			flex: 1;
		}
	}

	.searchwp-settings .searchwp-engine-source-rule-group-rule .searchwp-actions.searchwp-actions--mini {
		padding-left: 0.5em;
	}

	.searchwp-settings .searchwp-actions.searchwp-engine-source-rule-group-rule-actions {
		margin: 1em .5em;
	}
</style>
