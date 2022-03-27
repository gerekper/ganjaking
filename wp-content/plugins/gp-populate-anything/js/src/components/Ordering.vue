<template>
	<div class="gppa-ordering-container">
		<label class="section_label gppa-ordering-label" style="margin-top: 15px">
			{{ i18nStrings.ordering }}
		</label>

		<div class="gppa-ordering">
			<select class="gppa-ordering-property" :value="orderingProperty" @input="$emit('input-ordering-property', $event.target.value)">
				<option v-if="!propertiesLoaded" selected="selected" value=""
						disabled hidden>{{ i18nStrings.loadingEllipsis }}
				</option>
				<option v-else selected="selected" value="" disabled hidden>&ndash; Select a
					Property &ndash;
				</option>

				<option v-for="option in orderingPropertiesUngrouped"
						v-bind:value="option.value">
					{{ truncateStringMiddle( option.label ) }}
				</option>

				<optgroup v-for="(options, groupID) in orderingPropertiesGrouped"
						  v-bind:label="groupID in objectTypeInstance.groups && objectTypeInstance.groups[groupID].label">
					<option v-for="option in options" v-bind:value="option.value">
						{{ truncateStringMiddle( option.label ) }}
					</option>
				</optgroup>
			</select>

			<select class="gppa-ordering-method" :value="orderingMethod" @input="$emit('input-ordering-method', $event.target.value)">
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
	</div>
</template>

<script lang="ts">
import Vue from 'vue';
import truncateStringMiddle from '../helpers/truncateStringMiddle';

const $ = window.jQuery;

export default Vue.extend({
	data: function () {
		return {
			i18nStrings: window.GPPA_ADMIN.strings,
		};
	},
	props: [
		'objectTypeInstance',
		'propertiesLoaded',
		'groupedProperties',
		'ungroupedProperties',
		'orderingProperty',
		'orderingMethod',
	],
	watch: {

	},
	computed: {
		orderingPropertiesGrouped: function () {
			const groupedProperties: { [groupId: string]: any[] } = {...this.groupedProperties};

			for ( const [groupId, properties] of Object.entries(groupedProperties) ) {
				groupedProperties[groupId] = properties.filter(property => property?.['orderby']);

				if (groupedProperties[groupId].length === 0) {
					delete groupedProperties[groupId];
				}
			}

			return groupedProperties;
		},
		orderingPropertiesUngrouped: function () {
			return this.ungroupedProperties?.filter((property: any) => property?.['orderby']);
		},
	},
	methods: {
		truncateStringMiddle,
	}
});
</script>
