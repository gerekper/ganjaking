<template>
	<div class="searchwp-engine-source-config-option">
		<Checkbox
			:id="engine + '-' + source + '-setting-' + option"
			:value="option"
			:checked="model.enabled"
			@change="toggleSetting"
		><span v-if="onlyEntryId">{{ model.label }} {{ 'to entry ID' | i18n }} </span><span v-else>{{ model.label }}</span></Checkbox>

		<div :class="['searchwp-engine-source-config-option-options', optionsInapplicable ? 'searchwp-no-show' : '' ]">
			<v-select
				v-if="model.options && 1 !== model.options.length"
				:value="model.option"
				:options="model.options"
				:reduce="value => value.value"
				:searchable="false"
				:clearable="false"
				@input="value => change(value)"
			>
			</v-select>
			<input class="searchwp-input-mimic" v-if="model.option==='id'" :value="model.value" @input="onInput" />
		</div>
	</div>
</template>

<script>
import debounce from 'debounce';
import Checkbox from './../Inputs/Checkbox.vue';
import { EngineUtils } from './../../Mixins/EngineUtils.js';

export default {
	name: 'SourceOption',
	props: {
		option: {
			type: String,
			required: true
		},
		source: {
			type: String,
			required: true
		},
		engine: {
			type: String,
			required: true
		}
	},
	components: {
		Checkbox
	},
	mixins: [EngineUtils],
	computed: {
		optionsInapplicable: function() {
			return ! this.model.enabled || ( this.model.options && ! this.model.options.length );
		},
		onlyEntryId: function() {
			return this.model.option === 'id' && this.model.options && 1 === this.model.options.length
		},
		enabled: function() {
			return this.model.enabled;
		},
		model: function() {
			return this.getEngineSourceProperty(this.engine, this.source, 'options')
						.filter(option => this.option === option.name)[0];
		}
	},
	methods: {
		toggleSetting: function(setting) {
			if (!setting) {
				this.model.value = '';
			}

			this.$emit('change', {
				label: this.model.label,
				name: this.model.name,
				option: this.model.option,
				options: this.model.options,
				value: this.model.value,
				enabled: setting
			});
		},
		change: function(value) {
			this.$emit('change', {
				label: this.model.label,
				name: this.model.name,
				option: value,
				options: this.model.options,
				value: this.model.value,
				enabled: this.model.enabled
			});
		},
		onInput: function(event) {
			this.input(event, this);
		},
		input: debounce((event, vm) => {
			vm.$emit('change', {
				label: vm.model.label,
				name: vm.model.name,
				option: vm.model.option,
				options: vm.model.options,
				value: event.target.value,
				enabled: vm.model.enabled
			});
		}, 300)
	}
}
</script>

<style lang="scss">
	.searchwp-engine-source-config-option {
		display: flex;
		align-items: center;

		.v-select {
			display: block;
			width: 100%;
			max-width: 15em;

			+ input {
				display: block;
				margin-left: 0.8em;
			}
		}
	}

	.searchwp-engine-source-config-option-options {
		padding-left: 0.8em;
		flex: 1;
		display: flex;
		align-items: center;

		.searchwp-input-mimic {
			flex: 1;
		}
	}
</style>
