<template>
	<div class="gppa-filter">
		<select disabled
				v-if="!Object.keys(properties).length || (Object.keys(properties).length === 1 && 'primary-property' in properties)">
			<option value="" disabled selected="selected">{{ i18nStrings.loadingEllipsis }}</option>
		</select>
		<select v-else class="gppa-filter-property" v-model="filter.property" @change="resetFilter">
			<option v-for="option in ungroupedProperties" v-bind:value="option.value">
				{{ truncateStringMiddle(option.label) }}
			</option>

			<optgroup v-for="(options, groupID) in groupedProperties"
					  v-bind:label="groupID in objectTypeInstance.groups && objectTypeInstance.groups[groupID].label">
				<option v-for="option in options" v-bind:value="option.value">
					{{ truncateStringMiddle(option.label) }}
				</option>
			</optgroup>
		</select>

		<select class="gppa-filter-operator" v-model="filter.operator"
				:disabled="!Object.keys(properties).length || (Object.keys(properties).length === 1 && 'primary-property' in properties) || !(filter.property in propertyValues)">
			<option v-for="operator in operators" v-bind:value="operator">{{ i18nStrings.operators[operator]
				}}
			</option>
		</select>

		<select disabled v-if="!(filter.property in propertyValues)">
			<option value="" disabled selected="selected">{{ i18nStrings.loadingEllipsis }}</option>
		</select>
		<gppa-select-with-custom v-else additional-class="gppa-filter-value" v-model="filter.value"
								 :operator="filter.operator"
								 :object-type-instance="objectTypeInstance"
								 :flattened-properties="flattenedProperties">
			<option v-if="!filter.value" value="" disabled selected="selected" hidden>&ndash; Value &ndash;</option>

			<optgroup :label="i18nStrings.specialValues">
				<option value="gf_custom">{{ i18nStrings.addCustomValue }}</option>

				<option v-for="(option, optionIndex) in specialValues"
						v-bind:value="option.value"
						:selected="option.value == filter.value">
					{{ option.label }}
				</option>
			</optgroup>

			<optgroup :label="i18nStrings.formFieldValues" v-if="formFieldValues && formFieldValues.length">
				<option v-for="(option, optionIndex) in formFieldValues"
						v-bind:value="option.value"
						:selected="option.value == filter.value">
					{{ truncateStringMiddle(option.label) }}
				</option>
			</optgroup>

			<option v-for="(option, optionIndex) in propertyValues[filter.property]"
					v-bind:value="option.value"
					v-bind:disabled="option.disabled"
					:selected="option.value == filter.value">
				{{ truncateStringMiddle(option.label) }}
			</option>
		</gppa-select-with-custom>

		<div class="repeater-buttons">
			<a class="add-item" @click="$emit('add-filter')">
				<i class="gficon-add"></i>
			</a>

			<a class="remove-item" @click="$emit('remove-filter')">
				<i class="gficon-subtract"></i>
			</a>
		</div>

		<div
			v-if="filters.length > 1 && index !== filters.length - 1"
			class="gppa-filter-and">
			AND
		</div>
	</div>
</template>

<script lang="ts">
	import Vue from 'vue';
	import truncateStringMiddle from '../helpers/truncateStringMiddle';
	import SelectWithCustom from './SelectWithCustom.vue';

	export default Vue.extend({
		props: [
			'filter',
			'filters',
			'index',
			'field',
			'properties',
			'flattenedProperties',
			'propertyValues',
			'ungroupedProperties',
			'groupedProperties',
			'objectTypeInstance',
			'getPropertyValues',
		],
		components: {
			'gppa-select-with-custom': SelectWithCustom,
		},
		created: function () {
			if (this.filter.property) {
				return this.getPropertyValues(this.filter.property);
			}

			this.filter.property = Object.values(this.flattenedProperties)[0].value;
		},
		data: function () {
			return {
				i18nStrings: window.GPPA_ADMIN.strings,
				defaultOperator: 'is',
			}
		},
		watch: {
			'filter.property': function (val, oldVal) {
				this.getPropertyValues(val);
			}
		},
		methods: {
			truncateStringMiddle: truncateStringMiddle,
			/**
			 * resetFilter's contents were originally extracted from the 'filter.property' watcher to prevent the
			 * filter value from needlessly resetting to having no value when the field itself changes.
			 */
			resetFilter: function() {
				this.filter.value = '';
				this.filter.operator = this.defaultOperator;
			}
		},
		computed: {
			specialValues: function () {

				return [
					{
						label: 'Current User ID',
						value: 'special_value:current_user:ID',
					},
					{
						label: 'Current Post ID',
						value: 'special_value:current_post:ID',
					}
				]

			},
			formFieldValues: function () {

				var formFieldValues = [];

				const excludedFormFieldValueInputTypes = ['chainedselect'];

				for (var i = 0; i < form.fields.length; i++) {

					const field = form.fields[i];
					const inputType = GetInputType(field);

					if (excludedFormFieldValueInputTypes.includes(inputType)) {
						continue;
					}

					if (IsConditionalLogicField(field) || ['date'].includes(inputType)) {

						if (field.inputs && !['checkbox', 'email'].includes(inputType)) {
							for (var j = 0; j < field.inputs.length; j++) {
								var input = field.inputs[j];
								if (!input.isHidden) {
									formFieldValues.push({
										label: GetLabel(field, input.id),
										value: 'gf_field:' + input.id,
									});
								}
							}
						} else {
							formFieldValues.push({
								label: GetLabel(field),
								value: 'gf_field:' + field.id,
							});
						}

					}

				}

				return formFieldValues;

			},
			operators: function () {

				/* Labels for operators are pulled from i18nStrings in the Vue bindings */
				if (this.filter.property in this.flattenedProperties) {
					var property = this.flattenedProperties[this.filter.property];

					if ('operators' in property) {
						return property.operators;
					}

					if ('group' in property) {
						var group = this.objectTypeInstance.groups[property.group];

						if ('operators' in group) {
							return group.operators;
						}
					}
				}

				return window.GPPA_ADMIN.defaultOperators;

			}
		},
	});
</script>
