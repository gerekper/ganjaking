<template>
	<div class="searchwp-engine-source-rules">
		<div v-if="!hasRules">
			<p>{{ '_no_rules_note' | i18n }}</p>
		</div>

		<ul v-else class="searchwp-rules">
			<li v-for="(ruleGroup, ruleGroupIndex) in ruleGroups" :key="ruleGroupIndex">
				<p v-if="ruleGroupIndex>0" class="searchwp-rules-and" :style="{ borderColor: borderColor }">{{ 'AND' | i18n }}</p>
				<EngineSourceRuleGroup
					:engine="engine"
					:models="models"
					:source="source"
					:config="ruleGroup"
					:index="ruleGroupIndex"
				></EngineSourceRuleGroup>
			</li>
		</ul>

		<ul v-if="Object.keys(models).length" class="searchwp-actions searchwp-engine-source-rules-actions" style="padding-top: 1.5em;">
			<li><button type="button" class="button" @click="add">{{ 'Add Rule' | i18n }}</button></li>
			<li style="display: block; margin-left: auto;"><button type="button" class="button button-primary" @click="$emit('close')">{{ 'Done' | i18n }}</button></li>
		</ul>
	</div>
</template>

<script>
import cloneDeep from 'lodash.clonedeep';
import EngineSourceRuleGroup from './SourceRuleGroup.vue';
import { EngineUtils } from './../../Mixins/EngineUtils.js';

export default {
	name: 'EngineSourceRules',
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
		EngineSourceRuleGroup
	},
	mixins: [EngineUtils],
	computed: {
		models: function() {
			return cloneDeep(this.getEngineSourceProperty(this.engine, this.source, 'rules'));
		},
		ruleGroups: function() {
			return cloneDeep(this.getEngineSourceProperty(this.engine, this.source, 'ruleGroups'));
		},
		hasRules: function() {
			return !!this.ruleGroups.length;
		},
		borderColor: function() {
			return _SEARCHWP.misc.colors.border;
		}
	},
	methods: {
		add: function(rule) {
			this.$store.commit('addEngineSourceRuleGroup', {
				engine: this.engine,
				source: this.source
			});
		}
	}
}
</script>

<style lang="scss">
	.searchwp-rules > li {
		margin-bottom: 1em;
	}

	.searchwp-rules-and {
		margin: 1em 0;
		padding: 0;
		border: 0;
		font-weight: 500;
		font-size: 1.1em;
	}

	.searchwp-settings .searchwp-actions.searchwp-engine-source-rules-actions {
		margin-top: 1em;

		> * {
			margin-bottom: 0;
		}
	}
</style>
