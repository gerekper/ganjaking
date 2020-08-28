<template>
	<div class="searchwp-engine-source-rules-overview" :style="style">
		<ol>
			<li
				v-for="(ruleGroup, ruleGroupIndex) in ruleGroups"
				:key="ruleGroupIndex"
				:style="style" class="searchwp-engine-source-rules-overview-group"
			>
				<p>
					<span class="searchwp-engine-source-rules-overview-and" v-if="ruleGroupIndex>0"><code>{{ 'AND' | i18n }}</code></span>
					<span v-if="'IN' === ruleGroup.type" class="searchwp-engine-source-rules-overview-type">
						<component :is="i18nOnlyShowIf"></component>
					</span>
					<span v-else class="searchwp-engine-source-rules-overview-type">
						<component :is="i18nExcludeIf"></component>
					</span>
				</p>
				<ul class="searchwp-engine-source-rules-overview-rules">
					<li v-for="(rule, ruleIndex) in ruleGroup.rules" :key="ruleIndex">
						<p v-if="ruleIndex>0" class="searchwp-engine-source-rules-overview-or"><code>{{ 'OR' | i18n }}</code></p>
						<Rule
							:name="rule.rule"
							:option="rule.option"
							:condition="rule.condition"
							:value="rule.value"
							:source="source"
							:rules="rules"
						></Rule>
					</li>
				</ul>
			</li>
		</ol>
	</div>
</template>

<script>
import Rule from './../Rule.vue';
import { __ } from './../../helpers.js';
import { EngineUtils } from './../../Mixins/EngineUtils.js';

export default {
	name: 'EngineSourceRulesOverview',
	props: {
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
		Rule
	},
	mixins: [EngineUtils],
	computed: {
		i18nOnlyShowIf: function() {
			return {
				template: '<p>' + __('_only_show_if') + '</p>'
			};
		},
		i18nExcludeIf: function() {
			return {
				template: '<p>' + __('_exclude_if') + '</p>'
			};
		},
		rules: function() {
			return this.getEngineSourceProperty(this.engine, this.source, 'rules');
		},
		ruleGroups: function() {
			return this.getEngineSourceProperty(this.engine, this.source, 'ruleGroups');
		}
	},
	data () {
		return {
			style: {
				borderColor: _SEARCHWP.misc.colors.border
			}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-engine-source-rules-overview {
		border-width: 1px;
		border-style: solid;
		border-radius: 3px;
		overflow: hidden;
		margin-bottom: 1em;

		ol {
			list-style: none;
			margin: 0;
			padding: 0;

			> li {
				padding: 0.5em;
				border-style: solid;
				border-width: 0 0 1px;
				line-height: 1.8;

				&:last-of-type {
					border-width: 0;
				}

				&:nth-child(odd) {
					background-color: white;
				}
			}

		}

		p {
			margin: 0.5em 0;
			line-height: 1.4;
		}
	}

	.searchwp-engine-source-rules-overview-group {
		p, ol {
			display: inline;
		}
	}

	.searchwp-engine-source-rules-overview-rule {

		&.searchwp-engine-source-rules-overview-rule-disabled {
			cursor: wait;
			opacity: 0.5;
		}
	}

	.searchwp-engine-source-rules-overview-rules {
		display: inline;

		li, p {
			display: inline;
		}
	}

	.searchwp-engine-source-rules-overview-or:before {
		display: inline-block;
		content: ' ';
		white-space: pre;
	}
</style>
