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
									@click="addFilterGroup">
								<i class="gficon-add"></i> Add Filter Group
							</button>
						</div>

						<li style="margin-top: 15px">
							<input type="checkbox" :id="'gppa-' + populate + '-unique-results'" v-model="uniqueResults" />

							<label class="inline" :for="'gppa-' + populate + '-unique-results'">
								<span>{{ i18nStrings.unique }}</span>
							</label>
						</li>

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

						<label class="section_label gppa-ordering-label" style="margin-top: 15px">
							{{ i18nStrings.ordering }}
						</label>

						<div class="gppa-ordering">
							<select class="gppa-ordering-property" v-model="orderingProperty">
								<option v-if="!Object.keys(properties).length" selected="selected" value=""
										disabled hidden>{{ i18nStrings.loadingEllipsis }}
								</option>
								<option v-else selected="selected" value="" disabled hidden>&ndash; Select a
									Property &ndash;
								</option>

								<option v-for="option in orderingProperties" v-bind:value="option.value">
									{{ truncateStringMiddle(option.label) }}
								</option>
							</select>

							<select class="gppa-ordering-method" v-model="orderingMethod">
								<option
									value="asc">{{ i18nStrings.ascending }}
								</option>
								<option
									value="desc">{{ i18nStrings.descending }}
								</option>
								<option
									value="rand">{{ i18nStrings.random }}
								</option>
							</select>
						</div>
					</template>

					<label class="section_label" style="margin-top: 15px" v-if="populate === 'choices'">
						{{ i18nStrings.choiceTemplate }}
					</label>

					<label class="section_label" style="margin-top: 15px" v-else>
						{{ i18nStrings.valueTemplates }}
					</label>

					<table class="field_custom_inputs_ui gppa-templates">
						<tr class="field_custom_input_row gppa-template-row" v-for="templateRow of templateRows"
							:key="templateRow.label">
							<td>
								<label class="inline">{{ templateRow.label }}</label>
							</td>

							<td>
								<select v-if="!flattenedProperties || !Object.keys(flattenedProperties).length"
										disabled>
									<option value="" disabled selected>{{ i18nStrings.loadingEllipsis }}
									</option>
								</select>
								<gppa-select-with-custom v-else v-model="templates[templateRow.id]"
														 :inject-custom-value-option="true"
														 :loading="!Object.keys(properties).length"
														 :object-type-instance="objectTypeInstance"
														 :flattened-properties="flattenedProperties">
									<option
											v-if="!templates[templateRow.label] || !templates[templateRow.label].value"
											value="">&ndash; Property &ndash;
									</option>

									<option v-for="option in ungroupedProperties" v-bind:value="option.value">
										{{ truncateStringMiddle(option.label) }}
									</option>

									<optgroup v-for="(options, groupID) in groupedProperties"
											  v-bind:label="groupID in objectTypeInstance.groups && objectTypeInstance.groups[groupID].label">
										<option v-for="option in options" v-bind:value="option.value">
											{{ truncateStringMiddle(option.label) }}
										</option>
									</optgroup>
								</gppa-select-with-custom>
							</td>
						</tr>
					</table>
				</div>
			</template>
		</div>
	</li>
</template>

<script lang="ts">
	import Vue from 'vue';
	import truncateStringMiddle from '../helpers/truncateStringMiddle';
	import Filter from './Filter.vue';
	import ResultsPreview from './ResultsPreview.vue';
	import SelectWithCustom from './SelectWithCustom.vue';

	const $ = window.jQuery;

	export default Vue.extend({
		data: function () {
			return this.defaultData();
		},
		components: {
			'gppa-filter': Filter,
			'gppa-results-preview': ResultsPreview,
			'gppa-select-with-custom': SelectWithCustom,
		},
		created: function () {
			for ( const [property, fieldSetting] of Object.entries(this.fieldSettingMap) ) {
				this.$watch(property, (val, oldVal) => {
					try {
						if (typeof val !== 'undefined') {
							SetFieldProperty(fieldSetting, JSON.parse(JSON.stringify(val)));
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
			orderingProperties: function () {
				return Object.values(this.flattenedProperties).filter(function (prop) {
					return 'orderby' in prop && prop.orderby;
				});
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
				if (!this.field) {
					return false;
				}

				/* Exclude specific field types */
				if (['consent', 'tos', 'list'].indexOf(this.field.type) !== -1) {
					return false;
				}

				switch (this.populate) {
					case 'choices':
						if ( this.hasChoices() ) {
							/* Exclude chained selects */
							if (this.field.choices[0] && 'choices' in this.field.choices[0]) {
								return false;
							}

							return true;
						}

						if (['workflow_user'].indexOf(this.field.type) !== -1) {
							return true;
						}

						break;

					case 'values':
						if ( this.hasChoices() ) {
							/* Exclude chained selects */
							if (this.field.choices[0] && 'choices' in this.field.choices[0]) {
								return false;
							}

							return true;
						}

						/* Single input */
						if (this.currentFieldSettings.indexOf('.default_value_setting') !== -1) {
							return true;
						}

						/* Textarea */
						if (this.currentFieldSettings.indexOf('.default_value_textarea_setting') !== -1) {
							return true;
						}

						/* Input with multiple fields */
						if (this.currentFieldSettings.indexOf('.default_input_values_setting') !== -1) {
							return true;
						}

						if ( this.field.inputType === 'singleproduct' ) {
							return true;
						}

						break;
				}

				return false;
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
							window.field.choices && window.field.choices.length && $('.choices_setting').show();
						}
						break;
					case 'values':
                        if( $.inArray( GetInputType( GetSelectedField() ), [ 'number', 'calculation' ] ) !== -1 ) {
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
						window.field.choices && $('.choices_setting').hide();
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

				$.post(ajaxurl, ajaxArgs, null, 'json').done(function (data) {
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

				$.post(ajaxurl, ajaxArgs, null, 'json').done(function (data) {
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
			}
		}
	});
</script>
