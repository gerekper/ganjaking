<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue from 'vue';
import truncateStringMiddle from '../helpers/truncateStringMiddle';
import Filter from './Filter.vue';
import ResultsPreview from './ResultsPreview.vue';
import SelectWithCustom from './SelectWithCustom.vue';
import Templates from "./Templates.vue";
import Ordering from "./Ordering.vue";

const $ = window.jQuery;

export default Vue.extend({
	data: function () {
		return this.defaultData();
	},
	components: {
		'gppa-filter': Filter,
		'gppa-templates': Templates,
		'gppa-ordering': Ordering,
		'gppa-results-preview': ResultsPreview,
		'gppa-select-with-custom': SelectWithCustom,
	},
	created: function () {
		for ( const [property, fieldSetting] of Object.entries(this.fieldSettingMap) ) {
			this.$watch(property, (val, oldVal) => {
				try {
					if (typeof val !== 'undefined') {
						window.SetFieldProperty(fieldSetting, JSON.parse(JSON.stringify(val)));
					}
				} catch (e) {
					console.warn(e);
				}
			}, {deep: true});
		}
	},
	props: [
		'field',
		'populate',
	],
	watch: {
		field: {
			handler: function (field, prevField) {
				/* Disable GPPA if 'enableCalculation' is enabled on the current field */
				if ('enableCalculation' in field && field.enableCalculation) {
					this.enabled = false;
				}

				if (prevField && field.id === prevField.id) {
					return;
				}

				this.initialLoad();
			},
			deep: true,
		},
		objectType: function () {
			if (!this.objectTypeInstance) {
				return;
			}

			if ('primary-property' in this.objectTypeInstance && !this.usingFieldObjectType) {
				this.getPropertyValues('primary-property');
			} else {
				this.getProperties();
			}
		},
		primaryProperty: function () {
			if (!this.objectTypeInstance) {
				return;
			}

			if (this.usingFieldObjectType && !('primary-property' in this.objectTypeInstance)) {
				return;
			}

			this.getProperties();
		},
		enabled: function (val) {

			this.toggleStaticChoices();
			this.toggleEnabledClass();

		},
	},
	computed: {
		fieldSettingMap: function () {
			var prefix = 'gppa-' + this.populate + '-';

			return {
				'enabled': prefix + 'enabled',
				'objectType': prefix + 'object-type',
				'primaryProperty': prefix + 'primary-property',
				'orderingProperty': prefix + 'ordering-property',
				'orderingMethod': prefix + 'ordering-method',
				'filterGroups': prefix + 'filter-groups',
				'templates': prefix + 'templates',
				'uniqueResults': prefix + 'unique-results',
			};
		},
		isRestrictedObjectTypeActive: function () {
			return this.objectTypeInstance && this.objectTypeInstance.restricted;
		},
		fieldSettingConversion: function () {
			var prefix = 'gppa-' + this.populate + '-';

			return {
				'templates': function () {
					if (window.field && prefix + 'templates' in window.field) {
						var value = window.field[prefix + 'templates'];

						if (typeof value !== 'undefined' && Array.isArray(value)) {
							return {};
						}

						return value;
					}

					return undefined;
				}
			};
		},
		objectTypeInstance: function () {
			if (!this.objectType) {
				return null;
			}

			if (this.usingFieldObjectType) {
				var targetFieldSettings = this.fieldObjectTypeTargetFieldSettings;

				if (!targetFieldSettings || !targetFieldSettings['gppa-choices-object-type'] || !window.GPPA_ADMIN.objectTypes[targetFieldSettings['gppa-choices-object-type']]) {
					this.objectType = '';

					return;
				}

				var fieldObjectType = window.GPPA_ADMIN.objectTypes[targetFieldSettings['gppa-choices-object-type']];

				return Object.assign({}, fieldObjectType);
			}

			return Object.assign({}, window.GPPA_ADMIN.objectTypes[this.objectType]);
		},
		primaryPropertyComputed: function () {
			if (this.usingFieldObjectType) {
				return this.fieldObjectTypeTargetFieldSettings['gppa-choices-primary-property'];
			}

			return this.primaryProperty;
		},
		usingFieldObjectType: function () {
			return this.objectType.indexOf('field_value_object') === 0;
		},
		fieldObjectTypeTargetFieldSettings: function () {
			if (!this.usingFieldObjectType) {
				return null;
			}

			var targetFieldID = this.objectType.split(':')[1];

			return window.form.fields.filter(function (field) {
				return field.id == targetFieldID;
			})[0];
		},
		fieldValueObjects: function () {
			var vm = this;

			return window.form.fields.filter(function (field) {
				if (!('choices' in field)) {
					return false;
				}

				if (field.id === vm.field.id) {
					return false;
				}

				return 'gppa-choices-enabled' in field && field['gppa-choices-enabled'] && field['gppa-choices-object-type'];
			});
		},
		groupedProperties: function () {
			var groupedProperties = Object.assign({}, this.properties);
			delete groupedProperties.ungrouped;

			return groupedProperties;
		},
		ungroupedProperties: function () {
			return this.properties.ungrouped;
		},
		flattenedProperties: function () {
			var propertiesFlat = {};
			var vm = this;

			Object.keys(this.properties).forEach(function (group) {
				var groupProperties = vm.properties[group];

				groupProperties.forEach(function (property) {
					propertiesFlat[property.value] = property;
				});
			});

			return propertiesFlat;
		},
		propertiesLoaded: function () {
			return this.flattenedProperties && Object.keys(this.flattenedProperties).length;
		},
		currentFieldSettings: function () {

			var currentFieldSettings = window.fieldSettings[ this.field.type ];
			if( this.field.type !== this.field.inputType ) {
				currentFieldSettings += ',' + window.fieldSettings[ this.field.inputType ];
			}

			return $.map(currentFieldSettings.split(','), function (value) {
				return value.trim();
			});
		},
		isSupportedField: function () {
			/**
			 * Specify what fields can be populated by Populate Anything. This filter runs in the Form Editor and determines
			 * which fields the Populate Anything field settings will show for.
			 *
			 * @param {boolean}             isSupportedField   Whether or not the current field is supported for population. Defaults to `false`.
			 * @param {GravityFormsField}   field              The current field selected in the Form Editor.
			 * @param {'choices'|'values'}  populate           What is being populated. It will be either `choices` or `values`.
			 * @param {Vue}                 component          The Vue component for Populate Anything in the Form Editor.
			 */
			return window.gform.applyFilters('gppa_is_supported_field', false, this.field, this.populate, this);
		},
		templateRows: function () {
			var templateRows = [];

			if (!this.field) {
				return templateRows;
			}

			switch (this.populate) {
				case 'choices':
					templateRows.push({id: 'value', label: this.i18nStrings.value, required: true});
					templateRows.push({id: 'label', label: this.i18nStrings.label, required: true});

					if ('basePrice' in this.field || this.field.type === 'option') {
						templateRows.push({id: 'price', label: this.i18nStrings.price, required: true});
					}

					break;

				case 'values':
					if (
						this.field.inputs
						&& !window.GPPA_ADMIN.interpretedMultiInputFieldTypes.includes(this.field.type)
						&& !window.GPPA_ADMIN.multiSelectableChoiceFieldTypes.includes(this.field.type)
					) {
						for ( const input of this.field.inputs ) {
							if (input.isHidden) {
								continue;
							}

							templateRows.push({id: input.id, label: input.label});
						}
					} else {
						templateRows.push({id: 'value', label: this.i18nStrings.value});
					}
					break;
			}

			/**
			 * Modify the templates that will be shown under Choice Templates and/or Value Templates.
			 *
			 * @since 1.0-beta-4.116
			 *
			 * @param {Object[]} The available template rows. Each template should have an "id" string, "label" string, and "required" boolean.
			 * @param {Object}   The current field shown in the Form Editor.
			 * @param {String}   What's being populated. Either "choices" or "values"
			 */
			return window.gform.applyFilters( 'gppa_template_rows', templateRows, this.field, this.populate );
		},
	},
	methods: {
		truncateStringMiddle: truncateStringMiddle,
		getObjectTypes: function () {
			return window.GPPA_ADMIN.objectTypes;
		},
		isSuperAdmin: function () {
			return window.GPPA_ADMIN.isSuperAdmin;
		},
		defaultData: function () {
			return {
				enabled: false,
				uniqueResults: true,
				objectType: '',
				primaryProperty: '',
				properties: [],
				propertyValues: {},
				orderingProperty: '',
				orderingMethod: 'asc',
				filterGroups: [],
				i18nStrings: window.GPPA_ADMIN.strings,
				multiSelectableChoiceFieldTypes: window.GPPA_ADMIN.multiSelectableChoiceFieldTypes,
				templates: {},
			};
		},
		initialLoad: function () {
			var vm = this;

			Object.assign(vm.$data, this.defaultData());

			for ( const [property, fieldSetting] of Object.entries(this.fieldSettingMap) ) {
				if (typeof vm.fieldSettingConversion[property] === 'function') {
					var value = vm.fieldSettingConversion[property]();

					if (typeof value !== 'undefined') {
						vm.$data[property] = value;
					}

					continue;
				}

				if ( !window.field ) {
					continue;
				}

				if (fieldSetting in window.field) {
					vm.$data[property] = window.field[fieldSetting];
				}
			}

			if (!this.enabled || !this.isSupportedField) {
				this.showStaticSettings();

				return;
			}

			this.hideStaticSettings();

			if (!this.objectTypeInstance) {
				return;
			}

			if ('primary-property' in this.objectTypeInstance && !this.usingFieldObjectType) {
				this.getPropertyValues('primary-property');

				if (!this.primaryProperty) {
					return;
				}
			}

			this.getProperties();

			for ( const filters of this.filterGroups ) {
				for ( const filter of filters ) {
					vm.getPropertyValues(filter.property);
				}
			}
		},
		showStaticSettings: function () {

			if (!this.isSupportedField) {
				return;
			}

			switch (this.populate) {
				case 'choices':
					if ( $.inArray( window.field.type, [ 'post_category' ] ) === -1 ) {
						if (window.field.choices && window.field.choices.length) {
							$('.choices_setting, .choices-ui__trigger-section').show();
						}
					}
					break;
				case 'values':
					if( $.inArray( window.GetInputType( window.GetSelectedField() ), [ 'number', 'calculation' ] ) !== -1 && window.GetSelectedField()['type'] !== 'quantity' ) {
						$('.calculation_setting').show();
					}
					break;
			}
		},
		hideStaticSettings: function () {

			if (!this.isSupportedField) {
				return;
			}

			switch (this.populate) {
				case 'choices':
					if (window.field.choices) {
						$('.choices_setting, .choices-ui__trigger-section').hide();
						window?.gform?.instances?.choicesUi?.flyout?.closeFlyout();
					}
					break;
				case 'values':
					if (window.fieldSettings[window.field.type] && window.fieldSettings[window.field.type].indexOf('calculation_setting') !== -1) {
						window.ToggleCalculationOptions(false, window.GetSelectedField());
						$('.calculation_setting').hide();
					}
					break;
			}
		},
		toggleStaticChoices: function () {
			if (this.enabled === true) {
				this.hideStaticSettings();
			} else if (this.enabled === false) {
				this.showStaticSettings();
			}
		},
		toggleEnabledClass: function () {
			const $field = $('.gfield').filter('#field_' + this.field.id);

			if (this.enabled === true) {
				$field.addClass('gppa-' + this.populate + '-enabled');
			} else if (this.enabled === false) {
				$field.removeClass('gppa-' + this.populate + '-enabled');
			}
		},
		changeObjectType: function () {
			this.propertyValues = this.defaultData().propertyValues;
			this.primaryProperty = this.defaultData().primaryProperty;
			this.filterGroups = this.defaultData().filterGroups;
			this.properties = this.defaultData().properties;
			this.orderingProperty = this.defaultData().orderingProperty;
			this.orderingMethod = this.defaultData().orderingMethod;

			if (typeof this.objectTypeInstance.templates === 'object' && Object.keys(this.objectTypeInstance.templates).length) {
				this.templates = Object.assign({}, this.objectTypeInstance.templates);
			} else {
				this.templates = this.defaultData().templates;
			}
		},
		changePrimaryProperty: function () {
			this.filterGroups = this.defaultData().filterGroups;
		},
		filterFactory: function () {
			var date = new Date();

			return {
				property: '',
				operator: 'is',
				value: '',
				uuid: date.getTime(),
			};
		},
		addFilterGroup: function () {
			this.filterGroups.push([this.filterFactory()])
		},
		addFilter: function (filterIndex, filterGroupIndex) {
			if (!isNaN(filterIndex)) {
				this.filterGroups[filterGroupIndex].splice(filterIndex + 1, 0, this.filterFactory());

				return;
			}

			this.filterGroups[filterGroupIndex].push(this.filterFactory());
		},
		removeFilter: function (index, filterGroupIndex) {
			this.filterGroups[filterGroupIndex].splice(index, 1);

			if (this.filterGroups[filterGroupIndex].length === 0) {
				this.filterGroups.splice(filterGroupIndex, 1);
			}
		},
		resetPropertyValues: function (keepPrimaryPropertyValues) {
			var primaryPropertyValues = Object.assign({}, this.propertyValues['primary-property'] || {});
			this.propertyValues = this.defaultData().propertyValues;

			if (keepPrimaryPropertyValues && Object.keys(primaryPropertyValues).length) {
				this.propertyValues['primary-property'] = primaryPropertyValues;
			}
		},
		getProperties: function () {
			this.resetPropertyValues(true);

			var ajaxArgs = {
				'action': 'gppa_get_object_type_properties',
				'object-type': this.objectTypeInstance.id,
				'populate': this.populate,
			};

			if ('primary-property' in this.objectTypeInstance && this.primaryPropertyComputed) {
				ajaxArgs['primary-property-value'] = this.primaryPropertyComputed;
			}

			var vm = this;

			$.post(window.ajaxurl, ajaxArgs, null, 'json').done(function (data) {
				vm.properties = Object.assign({}, data);
			});
		},
		getPropertyValues: function (property) {
			var vm = this;

			if (property instanceof Event) {
				property = property.target.value;
			}

			if (property in vm.propertyValues) {
				return vm.propertyValues[property];
			}

			var ajaxArgs = {
				'action': 'gppa_get_property_values',
				'object-type': this.objectTypeInstance.id,
				'property': property
			};

			if ('primary-property' in this.objectTypeInstance && this.primaryPropertyComputed && property !== 'primary-property') {
				ajaxArgs['primary-property-value'] = this.primaryPropertyComputed;
			}

			$.post(window.ajaxurl, ajaxArgs, null, 'json').done(function (data) {
				if (data === 'gppa_over_max_values_in_editor') {
					/**
					 * If gppa_max_property_values_in_editor filter is met, do not output any properties to be selected.
					 *
					 * Instead, a custom value or special value should by used by the user.
					 *
					 * This is done for usability purposes but also to help browsers from locking up if there are a huge number of
					 * results.
					 */
					vm.$set(vm.propertyValues, property, [
						{
							value: '',
							label: vm.i18nStrings.tooManyPropertyValues,
							disabled: true,
						}
					]);

					return;
				}

				/* See https://vuejs.org/v2/guide/list.html#Object-Change-Detection-Caveats */
				vm.$set(vm.propertyValues, property, $.map(data, function (option, index) {
					var value = option;
					var label = option;

					if (Array.isArray(option)) {
						value = option[0];
						label = option[1];
					}

					return {
						value: value,
						label: label
					}
				}));
			});
		},
		hasChoices: function() {
			return 'choices' in this.field && this.field.choices !== '' && this.field.choices !== null;
		},

	}
});
</script>

<template>
	<li :id="'gppa-' + populate" class="gppa field_setting" v-if="isSupportedField">
		<input type="checkbox" :id="'gppa-' + populate + '-enabled'" v-model="enabled" />

		<label class="inline" :for="'gppa-' + populate + '-enabled'">
			<span>{{ populate === 'choices' ? i18nStrings.populateChoices : i18nStrings.populateValues }}</span>
		</label>

		<div class="gp-child-settings" v-if="enabled && isRestrictedObjectTypeActive && !isSuperAdmin()">
			<div class="gppa-warning" v-html="i18nStrings.restrictedObjectTypeNonPrivileged"></div>
		</div>

		<div class="gp-child-settings" v-if="enabled && !(isRestrictedObjectTypeActive && !isSuperAdmin())">
			<div v-if="isRestrictedObjectTypeActive && isSuperAdmin()" class="gppa-warning" v-html="i18nStrings.restrictedObjectTypePrivileged"></div>

			<label class="section_label">
				{{ i18nStrings.type }}
			</label>

			<select class="gppa-object-type" v-model="objectType" @change="changeObjectType">
				<option value="" disabled>
					&ndash; {{ i18nStrings.objectType }} &ndash;
				</option>

				<option v-for="objectType in getObjectTypes()" :value="objectType.id" v-if="!(objectType.restricted && !isSuperAdmin())">
					{{ objectType.label }}
				</option>

				<template v-if="populate === 'values'">
					<option v-for="field in fieldValueObjects" v-bind:value="'field_value_object:' + field.id">
						Field Value Object: {{ field.label }}
					</option>
				</template>
			</select>

			<template v-if="objectType">
				<template v-if="'primary-property' in objectTypeInstance && !usingFieldObjectType">
					<label class="section_label gppa-primary-property-label"
						   style="margin-top: 15px;">{{ objectTypeInstance['primary-property'].label }}</label>

					<select key="loading" class="gppa-primary-property" v-if="!('primary-property' in propertyValues)"
							disabled>
						<option value="" disabled selected>{{ i18nStrings.loadingEllipsis }}</option>
					</select>
					<select v-else class="gppa-primary-property" v-model="primaryProperty"
							@change="changePrimaryProperty">
						<option v-if="!primaryProperty" value="" hidden disabled selected>{{
							i18nStrings.selectAnItem.replace(/%s/g,
							objectTypeInstance['primary-property']['label']) }}
						</option>

						<option v-for="option in propertyValues['primary-property']"
								v-bind:value="option.value">
							{{ truncateStringMiddle(option.label) }}
						</option>
					</select>
				</template>

				<div class="gppa-main-settings"
					 v-if="('primary-property' in objectTypeInstance && primaryPropertyComputed) || !('primary-property' in objectTypeInstance)">
					<template v-if="!usingFieldObjectType">
						<label class="section_label gppa-filters-label" style="margin-top: 15px">
							{{ i18nStrings.filters }}
						</label>

						<div class="gppa-filter-groups">
							<template v-for="(filters, filterGroupIndex) in filterGroups">
								<div class="gppa-filter-group">
									<gppa-filter v-for="(filter, filterIndex) in filters"
												 :field="field"
												 :filter="filter"
												 :filters="filters"
												 :index="filterIndex"
												 :key="filter.uuid"
												 :properties="properties"
												 :flattened-properties="flattenedProperties"
												 :property-values="propertyValues"
												 :ungrouped-properties="ungroupedProperties"
												 :grouped-properties="groupedProperties"
												 :object-type-instance="objectTypeInstance"
												 :get-property-values="getPropertyValues"
												 @add-filter="addFilter(filterIndex, filterGroupIndex)"
												 @remove-filter="removeFilter(filterIndex, filterGroupIndex)"></gppa-filter>

								</div>

								<div
									v-if="filterGroups.length > 1 && filterGroupIndex !== filterGroups.length - 1"
									class="gppa-filter-group-or">
									&mdash; OR &mdash;
								</div>
							</template>

							<button class="gppa-add-filter-group button button-secondary"
									@click="addFilterGroup" :disabled="!propertiesLoaded">
								<i class="gficon-add"></i> Add Filter Group
							</button>
						</div>

						<div style="margin-top: 15px">
							<input type="checkbox" :id="'gppa-' + populate + '-unique-results'" v-model="uniqueResults" />

							<label class="inline" :for="'gppa-' + populate + '-unique-results'">
								<span>{{ i18nStrings.unique }}</span>
							</label>
						</div>

						<gppa-results-preview :field="field"
						  :filter-groups="filterGroups"
						  :templates="templates"
						  :template-rows="templateRows"
						  :enabled="enabled"
						  :object-type-instance="objectTypeInstance"
						  :ordering-property="orderingProperty"
						  :ordering-method="orderingMethod"
						  :populate="populate"
						  :uniqueResults="uniqueResults"></gppa-results-preview>

						<gppa-ordering
							:grouped-properties="groupedProperties"
							:ungrouped-properties="ungroupedProperties"
							:object-type-instance="objectTypeInstance"
							:ordering-property="orderingProperty"
							:ordering-method="orderingMethod"
							:properties-loaded="propertiesLoaded"
							@input-ordering-property="orderingProperty = $event"
							@input-ordering-method="orderingMethod = $event"
						></gppa-ordering>
					</template>

					<gppa-templates
						:properties-loaded="propertiesLoaded"
						:object-type-instance="objectTypeInstance"
						:grouped-properties="groupedProperties"
						:ungrouped-properties="ungroupedProperties"
						:flattened-properties="flattenedProperties"
						:populate="populate"
						:template-rows="templateRows"
						v-model="templates"
					></gppa-templates>
				</div>
			</template>
		</div>
	</li>
</template>
