<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue, {PropType} from 'vue';
import Tooltip from "./Tooltip.vue";

export default Vue.extend({
	name: 'ResourcePropertyMap',
	props: {
		value: {
			required: true,
			type: Object as PropType<{ [propertyId: string]: number }>,
		},
		field: {
			type: Object as PropType<GravityFormsField>,
			required: true,
		},
		resource: {
			type: Object as PropType<Resource>,
		}
	},
	components: {Tooltip},
	data() : {} {
		return {
			rawFields: window.form.fields, // Make the fields prop observable by attaching it to data
		};
	},
	watch: {},
	computed: {
		hasProperties() {
			return !!this.resource?.properties?.length;
		},
		fields() : { label: string, id: number }[] {
			return this.rawFields.map((field: GravityFormsField) => ({
				label: field.adminLabel ? field.adminLabel : field.label,
				id: field.id,
				type: field.type,
			}))
				.filter((field: GravityFormsField) => field.id !== this.field.id)
				.filter((field: GravityFormsField) => !['product', 'total', 'section', 'html', 'page', 'quantity'].includes(field.type))
		}
	},
	methods: {}
});
</script>

<template>
	<li class="gpi-field-setting field_setting gpi-resource-property-map" v-if="hasProperties">
		<label class="section_label">
			{{ $i18n.resource_scopes }}
			<Tooltip :content="$i18n.tooltip_resource_scopes_map"/>
		</label>

		<div class="gp-group" v-for="property of resource.properties">
			<label>{{ property.name }}</label>
			<select
				@input="$emit('input', { ...value, [property.id]: $event.target.value })"
				:value="value[property.id]">
				<option disabled value="">&ndash; {{ $i18n.select_a_field }} &ndash;</option>
				<option v-for="field of fields" :value="field.id">{{ field.label }}</option>
			</select>
		</div>
	</li>
</template>

<style>
.gpi-resource-property-map {
	margin-bottom: 1.25rem !important;
}

.gp-group > select {
	flex: 1;
}

.gpi-resource-property-map .gp-group label {
	line-height: 2.25rem;
	margin: 0;
}
</style>
