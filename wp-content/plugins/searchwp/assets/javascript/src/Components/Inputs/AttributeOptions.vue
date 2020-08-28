<template>
	<v-select
		v-model="value"
		:value="value"
		:filterable="true"
		@search="function(search, loading) {
			onSearch(attribute.name, search, loading);
		}"
		:options="options"
		:searchable="true"
		:taggable="attribute.allow_custom"
		@input="function() { if (attribute.get_options) { options = []; } $emit('change', value);}"
		:createOption="option => ({ value: option, label: option, icon: '' })"
		@option:created="optionCreated"
		:multiple="true"
		:class="[ applicableSpecial.length ? 'searchwp-attribute-options-has-special' : 'searchwp-attribute-options-no-special' ]"
	>
		<template slot="no-options">
			<component :is="i18nAttributeOptionsSearchNote"></component>
			<div v-if="applicableSpecial.length" class="searchwp-attribute-options-special">
				<p class="description">{{ 'Or choose from the following shortcuts' | i18n }}</p>
				<div class="vs__dropdown-option vs__dropdown-option-searchwp-special" v-for="(option, index) in applicableSpecial" :key="index">
					<span
						class="searchwp-attribute-options-special-option"
						@click="function() {value.push(option); $emit('change', value)}">
						<span v-if="option.icon.length" class="searchwp-icon-label">
							<Icon :icon="option.icon"></Icon>
							<span class="searchwp-button-label">{{ option.label }}</span>
						</span>
						<span v-else>{{ option.label }}</span>
					</span>
				</div>
			</div>
		</template>
		<template v-slot:option="option">
			<span v-if="option.icon" class="searchwp-icon-label">
				<Icon :icon="option.icon"></Icon>
				<span>{{ option.label }}</span>
			</span>
			<span v-else>{{ option.label }}</span>
		</template>
		<template v-slot:selected-option="option">
			<span v-if="option.icon" class="searchwp-icon-label">
				<Icon :icon="option.icon"></Icon>
				<span>{{ option.label }}</span>
			</span>
			<span v-else>{{ option.label }}</span>
		</template>
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
</template>

<script>
import debounce from 'debounce';
import Icon from './../Icon.vue';
import { __ } from './../../helpers.js';
import cloneDeep from 'lodash.clonedeep';
import Spinner from 'vue-simple-spinner';
import { SourceAttribute } from './../../Mixins/SourceAttribute.js';

export default {
	name: 'AttributeOptions',
	props: {
		attribute: {
			type: Object,
			required: true
		},
		source: {
			type: String,
			required: true
		}
	},
	components: {
		Spinner,
		Icon
	},
	methods: {
		optionCreated: function(newOption) {
			let vm = this;
			vm.$store.commit('cacheDynamicAttributeOptions', {
				options: [ newOption ],
				attribute: vm.source + _SEARCHWP.separator + vm.attribute.name
			});
		},
		onSearch: function(attributeName, search, loading) {
			if (search) {
				loading(true);
				this.search(attributeName, this.source, loading, search, this);
			} else {
				this.options = [];
			}
		},
		search: debounce((attributeName, sourceName, loading, search, vm) => {
			let results = new Promise(function(resolve, reject) {
				jQuery.post(ajaxurl, {
					_ajax_nonce: _SEARCHWP.nonce,
					action: vm.attribute.get_options,
					source: sourceName,
					attribute: attributeName,
					search: search
				}, function(response) {
					if (response.success) {
						resolve(response.data);
					} else {
						reject(response);
					}
				});
			});

			results.then(function(options) {
				vm.$store.commit('cacheDynamicAttributeOptions', {
					options: options,
					attribute: vm.source + _SEARCHWP.separator + vm.attribute.name
				});
				vm.options = options;
				loading(false);
			});
		}, 300)
	},
	computed: {
		i18nAttributeOptionsSearchNote: function() {
			let self = this;
			return {
				computed: {
					attributeLabel: function() {
						return self.attribute.label;
					}
				},
				template: '<p class="searchwp-attribute-options-search-note">' + __('_attributes_options_search_note') + '</p>'
			};
		},
		applicableSpecial: function() {
			return this.special.filter(option => -1 === this.value.map(value => value.value).indexOf(option.value));
		},
		spinnerFgColor: function() {
			return _SEARCHWP.misc.colors.link.hover;
		},
		spinnerBgColor: function() {
			return _SEARCHWP.misc.colors.border;
		}
	},
	created() {
		if (this.attribute.options.length) {
			this.options = cloneDeep(this.attribute.options);
		}

		// Set our value to be the format we're working with.
		let value = [];
		const settings = cloneDeep(this.attribute.settings);

		for (const option in settings) {
			const optionsOption = this.special
				.concat(this.attribute.options)
				.concat(this.$store.getters.cachedAttributeOptions(this.source + _SEARCHWP.separator + this.attribute.name))
				.filter(optionsOption => optionsOption.value === option);

			if (optionsOption.length >= 1) {
				value.push({
					value: optionsOption[0].value,
					label: optionsOption[0].label,
					icon: optionsOption[0].icon
				});
			}
		}

		this.value = value;
	},
	data() {
		return {
			value: [],
			options: [],
			special: cloneDeep(this.attribute.special),
		}
	}
}
</script>

<style lang="scss">
	.searchwp-attribute-options-search-note {
		text-align: left;
		margin: 0;
		padding-left: 0.5em;
	}

	.searchwp-attribute-options-special {
		text-align: left;

		p.description {
			padding-left: 0.5em;
			margin-top: 1em;
		}

		.searchwp-attribute-options-special-option {
			display: block;

			.dashicons {
				margin-left: -2px; // Visual offset.
				margin-right: 4px;
			}
		}
	}
</style>