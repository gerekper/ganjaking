<template>
	<div class="searchwp-engine-source-attribute">
		<div v-if="model.options && Array.isArray(model.options)">
			<h3>{{ model.label }}</h3>
			<ul>
				<li class="searchwp-engine-source-attribute-weight"
					v-for="(value, option) in model.settings"
					:key="option">
					<Attribute
						:value="parseInt(model.settings[option],10)"
						:label="getLabelForOption(option)"
						@change="value => $emit('change', {value: value, option: option})">
						<span v-if="getIconForOption(option).length" class="searchwp-icon-label">
							<Icon :icon="getIconForOption(option)"></Icon>
							<span>{{ getLabelForOption(option) }}</span>
						</span>
						<span v-else>{{ getLabelForOption(option) }}</span>
					</Attribute>
				</li>
			</ul>
		</div>
		<div v-else class="searchwp-engine-source-attribute-weight">
			<Attribute
				:value="parseInt(model.settings,10)"
				:label="model.label"
				@change="value => $emit('change', {value: value, option: false})">
				{{ model.label }}
			</Attribute>
		</div>
	</div>
</template>

<script>
import Icon from './../Icon.vue';
import cloneDeep from 'lodash.clonedeep';
import Attribute from './../Attribute.vue';
import { EngineUtils } from './../../Mixins/EngineUtils.js';

export default {
	name: 'EngineSourceAttribute',
	props: {
		engine: {
			type: String,
			required: true
		},
		source: {
			type: String,
			required: true
		},
		name: {
			type: String,
			required: true
		}
	},
	components: {
		Attribute,
		Icon
	},
	mixins: [EngineUtils],
	computed: {
		model: function() {
			return cloneDeep(this.getEngineSourceProperty(this.engine, this.source, 'attributes')[this.name]);
		}
	},
	methods: {
		getOption: function(optionValue) {
			return this.model.special
					.concat(this.model.options)
					.concat(this.$store.getters.cachedAttributeOptions(this.source + _SEARCHWP.separator + this.name))
					.filter((option) => option.value === optionValue);
		},
		getLabelForOption: function(optionValue) {
			const option = this.getOption(optionValue);

			return option.length >= 1 ? option[0].label : '';
		},
		getIconForOption: function(optionValue) {
			const option = this.getOption(optionValue);

			return option.length >= 1 ? option[0].icon : false;
		}
	}
}
</script>

<style lang="scss">
	.searchwp-engine-source-attribute {

		h3 {
			margin-top: 1em;
			margin-bottom: 0.7em;
		}
	}
</style>
