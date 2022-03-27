<template>
	<div class="gppa-templates">
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
					<select v-if="!propertiesLoaded"
							disabled>
						<option value="" disabled selected>{{ i18nStrings.loadingEllipsis }}
						</option>
					</select>
					<gppa-select-with-custom v-else :value="value[templateRow.id]"
											 @change="updateTemplate(templateRow.id, $event)"
											 :inject-custom-value-option="true"
											 :loading="!propertiesLoaded"
											 :object-type-instance="objectTypeInstance"
											 :flattened-properties="flattenedProperties">
						<option
							v-if="!value[templateRow.label] || !value[templateRow.label].value"
							value="">&ndash; Property &ndash;
						</option>

						<option v-for="option in templatePropertiesUngrouped" v-bind:value="option.value">
							{{ truncateStringMiddle(option.label) }}
						</option>

						<optgroup v-for="(options, groupID) in templatePropertiesGrouped"
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

<script lang="ts">
import Vue from 'vue';
import SelectWithCustom from "./SelectWithCustom.vue";
import truncateStringMiddle from '../helpers/truncateStringMiddle';

const $ = window.jQuery;

export default Vue.extend({
	data: function () {
		return {
			i18nStrings: window.GPPA_ADMIN.strings,
		};
	},
	components: {
		'gppa-select-with-custom': SelectWithCustom,
	},
	props: [
		'flattenedProperties',
		'objectTypeInstance',
		'populate',
		'groupedProperties',
		'ungroupedProperties',
		'propertiesLoaded',
		'templateRows',
		'value',
	],
	watch: {},
	computed: {
		templatePropertiesGrouped: function () {
			const groupedProperties: { [groupId: string]: any[] } = {...this.groupedProperties};

			for ( const [groupId, properties] of Object.entries(groupedProperties) ) {
				groupedProperties[groupId] = properties.filter(property => property?.['supports_templates'] !== false);

				if (groupedProperties[groupId].length === 0) {
					delete groupedProperties[groupId];
				}
			}

			return groupedProperties;
		},
		templatePropertiesUngrouped: function () {
			return this.ungroupedProperties?.filter((property: any) => property?.['supports_templates'] !== false);
		},
	},
	methods: {
		truncateStringMiddle,
		updateTemplate: function(templateRowId: string, value: string) {
			Vue.set(this.value, templateRowId, value);
			this.$emit('input', this.value);
		}
	}
});
</script>
