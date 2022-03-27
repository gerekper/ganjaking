<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue from 'vue';
import Tooltip from "./Tooltip.vue";
import ResourceSelect from "./ResourceSelect.vue";
import ResourcePropertyMap from "./ResourcePropertyMap.vue";

const $ = window.jQuery;

interface Data {
	gpiInventory: '' | 'simple' | 'advanced'
	gpiInventoryLimit: undefined | number
	gpiHideForm: boolean
	gpiHideChoice: boolean
	gpiShowAvailableInventory: boolean
	gpiMessageInventoryInsufficient: string
	gpiMessageInventoryExhausted: string
	gpiMessageInventoryAvailable: string
	gpiResource: undefined | number
	gpiResourcePropertyMap: { [propertyId: string]: number }
	claimedInventory: undefined | number
	loadingClaimedInventory: boolean
	alwaysShowInventoryLimit: boolean
	resources: { [id: number]: Resource }
}

export default Vue.extend({
	name: 'GPISettings',
	props: [
		'field',
		'GPIInstance',
	],
	components: {
		ResourceSelect,
		ResourcePropertyMap,
		Tooltip,
	},
	data() : Data {
		return {
			gpiInventory: '',
			gpiInventoryLimit: undefined,
			gpiHideForm: false,
			gpiHideChoice: false,
			gpiShowAvailableInventory: false,
			gpiMessageInventoryInsufficient: '',
			gpiMessageInventoryExhausted: '',
			gpiMessageInventoryAvailable: '',
			gpiResource: undefined,
			gpiResourcePropertyMap: {},
			claimedInventory: undefined,
			loadingClaimedInventory: false,
			alwaysShowInventoryLimit: window.GPI_ADMIN.alwaysShowInventoryLimitInEditor,
			resources: window.GPI_ADMIN.resources ?? {},
		};
	},
	/**
	 * Listen for changes in observables/data and send them to Gravity Forms.
	 */
	created: function () {
		for (const fieldSetting of Object.keys(this.$options.data())) {
			// Only sync data keys starting with gpi
			if (fieldSetting.indexOf('gpi') !== 0) {
				continue;
			}

			this.$watch(fieldSetting, (val, oldVal) => {
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
	watch: {
		field: {
			handler: function (field, prevField) {
				if (prevField && field.id === prevField.id) {
					return;
				}

				this.syncFieldToData();
				this.fetchClaimedInventorySimple();
			},
			deep: true,
		},
		gpiInventory: async function() {
			await this.$nextTick(); // Wait for gpiInventory to be synced to window.field
			this.GPIInstance.choiceInventory.toggleEnableInventoryLimits();
		},
		gpiResource: function() {
			this.filterPropertyMap();
		},
	},
	computed: {
		selectedResource() : Resource {
			return this.resources?.[this.gpiResource!];
		},
		isSupportedField() : boolean {
			return this.GPIInstance.isSupportedFieldType(this.field);
		},
		isSupportedChoiceField() : boolean {
			return this.GPIInstance.isSupportedChoiceFieldType(this.field);
		},
		currentInventory: {
			get: function () {
				return (this.gpiInventoryLimit ?? 0) - (this.claimedInventory ?? 0);
			},
			set: function (desiredInventory: string) {
				this.gpiInventoryLimit = parseInt(desiredInventory) + (this.claimedInventory ?? 0);
			}
		} as any,
	},
	methods: {
		syncFieldToData() : void {
			var vm = this;

			for (const fieldSetting of Object.keys(this.$options.data())) {
				if (!window.field) {
					continue;
				}

				// Only sync data keys starting with gpi
				if (fieldSetting.indexOf('gpi') !== 0) {
					continue;
				}

				if (fieldSetting in window.field) {
					vm.$data[fieldSetting] = window.field[fieldSetting];
				} else {
					vm.$data[fieldSetting] = (this as any).$options.data()[fieldSetting];
				}
			}
		},
		fetchClaimedInventorySimple() : void {
			if (this.alwaysShowInventoryLimit) {
				return;
			}

			this.claimedInventory = undefined;

			/**
			 * If there are entries for the product/quantity, then we show a "Current Inventory" setting instead of the
			 * "Inventory Limit" setting. This makes it more intuitive to make inventory adjustments.
			 */
			if (
				!window.has_entry(this.field.id)
				|| (window as any).GPIInstance.choiceInventory.isSupportedFieldType(this.field)
				|| this.field.gpiInventory !== 'simple'
			) {
				this.loadingClaimedInventory = false;
				return;
			}

			this.loadingClaimedInventory = true;

			$.post(window.ajaxurl, {
				fieldId: this.field.id,
				formId: this.field.formId,
				security: window.GPI_ADMIN.nonce,
				action: 'gpi_get_simple_current_inventory_claimed',
			}, null, 'json').done( (claimedInventory) => {
				this.claimedInventory = claimedInventory;
				this.loadingClaimedInventory = false;
			});
		},
		onResourceUpdate(resource: Resource) : void {
			this.$set(this.resources, resource.id, {
				id: resource.id,
				name: resource.name,
				inventory_limit: resource.inventory_limit,
				choice_based: resource.choice_based,
				properties: resource.properties,
			});

			this.filterPropertyMap();
		},
		onResourceDelete(resourceId: Resource['id']) : void {
			this.$delete(this.resources, resourceId);
		},
		// If resource is updated, filter the property map and remove any properties that were removed as well.
		filterPropertyMap() : void {
			const updatedPropertyMap = {};

			for ( const property of this.selectedResource?.properties ?? [] ) {
				if (typeof this.gpiResourcePropertyMap[property.id] !== 'undefined') {
					updatedPropertyMap[property.id] = this.gpiResourcePropertyMap[property.id];
				}
			}

			this.gpiResourcePropertyMap = updatedPropertyMap;
		}
	}
});
</script>

<template>
	<li id="gp-inventory" class="gpi-field-setting field_setting" v-show="isSupportedField">
		<div class="gpi-field-setting">
			<label class="section_label">
				{{ $i18n.inventory_type }}
				<Tooltip :content="$i18n.tooltip_inventory_type" />
			</label>

			<input type="radio" name="gpi_inventory" id="gpi_inventory_unlimited" value="" v-model="gpiInventory" />
			<label for="gpi_inventory_unlimited" class="inline">{{ $i18n.inventory_type_untracked }}</label>

			<input type="radio" name="gpi_inventory" id="gpi_inventory_simple" value="simple" v-model="gpiInventory" />
			<label for="gpi_inventory_simple" class="inline">{{ $i18n.inventory_type_simple }}</label>

			<input type="radio" name="gpi_inventory" id="gpi_inventory_advanced" value="advanced" v-model="gpiInventory" />
			<label for="gpi_inventory_advanced" class="inline">{{ $i18n.inventory_type_advanced }}</label>
		</div>

		<ul class="gp-child-settings" v-if="gpiInventory !== ''">
			<li class="gpi-field-setting field_setting" v-if="gpiInventory === 'simple'">
				<label for="gpi_inventory_limit" class="section_label">
					{{ $i18n.inventory }}
					<Tooltip :content="$i18n.tooltip_inventory" />
				</label>

				<input type="number" id="gpi_inventory_limit" v-model="gpiInventoryLimit" v-if="!isSupportedChoiceField && !claimedInventory" :disabled="loadingClaimedInventory" />
				<input type="number" id="gpi_current_inventory" v-model="currentInventory" v-else-if="!isSupportedChoiceField && claimedInventory" />
				<p v-else v-html="$i18n.set_inventory_on_choices"></p>
			</li>

			<ResourceSelect
				:resources="resources"
				:field="field"
				v-model="gpiResource"
				v-if="gpiInventory === 'advanced'"
				@resource-updated="onResourceUpdate"
				@resource-deleted="onResourceDelete" />

			<ResourcePropertyMap
				:field="field"
				:resource="selectedResource"
				v-model="gpiResourcePropertyMap"
				v-if="gpiInventory === 'advanced'" />

			<li class="gpi-field-setting field_setting">
				<label for="gpi_inventory_insufficient_message" class="section_label">
					{{ $i18n.inventory_insufficient_message }}
					<Tooltip :content="$i18n.tooltip_insufficient_message" />
				</label>

				<input
					type="text"
					id="gpi_inventory_insufficient_message"
					v-model="gpiMessageInventoryInsufficient"
					:placeholder="$i18n.inventory_insufficient_message_default"/>
			</li>

			<li class="gpi-field-setting field_setting">
				<label for="gpi_inventory_exhausted_message" class="section_label">
					{{ $i18n.inventory_exhausted_message }}
					<Tooltip :content="$i18n.tooltip_exhausted_message" />
				</label>

				<input
					type="text"
					id="gpi_inventory_exhausted_message"
					v-model="gpiMessageInventoryExhausted"
					:placeholder="$i18n.inventory_exhausted_message_default"/>
			</li>

			<li class="gpi-field-setting field_setting">
				<input type="checkbox" id="gpi_show_available_inventory" v-model="gpiShowAvailableInventory" />
				<label for="gpi_show_available_inventory" class="inline">{{ $i18n.show_available_inventory }} <Tooltip :content="$i18n.tooltip_show_available_inventory" /></label>
			</li>

			<div v-if="gpiShowAvailableInventory" class="gp-child-settings" style="margin-bottom: 0.7rem">
				<div class="gp-row">
					<label for="gpi_available_inventory_message" class="section_label">
						{{ $i18n.available_inventory_message }}
						<Tooltip :content="$i18n.tooltip_available_message" :button-class="'gp-tooltip-right'" />
					</label>

					<input
						type="text"
						id="gpi_available_inventory_message"
						v-model="gpiMessageInventoryAvailable"
						:placeholder="isSupportedChoiceField ? $i18n.inventory_available_on_choice_message_default : $i18n.inventory_available_message_default "/>
				</div>
			</div>

			<li class="gpi-field-setting field_setting">
				<input type="checkbox" id="gpi_hide_form" v-model="gpiHideForm" />
				<label for="gpi_hide_form" class="inline">{{ $i18n.hide_form_inventory_exhausted }} <Tooltip :content="$i18n.tooltip_hide_form_inventory_exhausted" :button-class="'gp-tooltip-right'" /></label>
			</li>

			<li class="gpi-field-setting field_setting" v-if="isSupportedChoiceField">
				<input type="checkbox" id="gpi_hide_choice" v-model="gpiHideChoice" />
				<label for="gpi_hide_choice" class="inline">{{ $i18n.hide_choice_inventory_exhausted }} <Tooltip :content="$i18n.tooltip_hide_choice_inventory_exhausted" /></label>
			</li>
		</ul>
	</li>
</template>

<style>
#gp-inventory label.section_label span.dashicons {
	font-size: inherit;
	line-height: inherit;
	width: auto;
	height: auto;
}

.gpi-field-setting input[type="number"] {
	width: 11ch;
	height: 2.25rem;
}


/* Choice Inventory Limit */

.gp-inventory-choices { }

.gp-inventory-choices .field-choice-current-inventory,
.gp-inventory-choices .field-choice-inventory-limit {
	display: none;
}

.gp-inventory-choices.inventory-limits-enabled .field-choice-current-inventory,
.gp-inventory-choices.inventory-limits-enabled .field-choice-inventory-limit {
	display: block;
	text-align: center;
	/* Would love to have some padding but we need all the space we can get in GF 2.5. */
	padding: 0;
}

.inventory-limits-enabled #gfield_settings_choices_container .gfield_choice_header_label {
	width: calc(100% - 8.5rem);
}

/* Gotta get aggressive to override GF styles for our limit input. */
#gfield_settings_choices_container input.field-choice-current-inventory,
#gfield_settings_choices_container input.field-choice-inventory-limit,
#gfield_settings_choices_container.choice_with_value input.field-choice-current-inventory,
#gfield_settings_choices_container.choice_with_price input.field-choice-current-inventory,
#gfield_settings_choices_container.choice_with_value input.field-choice-inventory-limit,
#gfield_settings_choices_container.choice_with_price input.field-choice-inventory-limit,
#gfield_settings_choices_container.choice_with_value_and_price input.field-choice-current-inventory,
#gfield_settings_choices_container.choice_with_value_and_price input.field-choice-inventory-limit {
	width: 40px !important;
	flex-basis: auto !important;
}

.gp-inventory-choices.inventory-limits-enabled .choice_with_price > label {
	width: calc(50% - 4.4375rem) !important;
}

.gp-inventory-choices.inventory-limits-enabled .choice_with_price > label.gfield_choice_header_inv {
	width: 2.5rem !important;
}

.gp-inventory-choices.inventory-limits-enabled .choice_with_value .field-choice-current-inventory,
.gp-inventory-choices.inventory-limits-enabled .choice_with_value .field-choice-inventory-limit,
.gp-inventory-choices.inventory-limits-enabled .choice_with_price .field-choice-current-inventory,
.gp-inventory-choices.inventory-limits-enabled .choice_with_price .field-choice-inventory-limit,
.gp-inventory-choices.inventory-limits-enabled .choice_with_value_and_price .field-choice-current-inventory,
.gp-inventory-choices.inventory-limits-enabled .choice_with_value_and_price .field-choice-inventory-limit {
	margin-left: 0.3125rem;
}

.gp-inventory-choices .gfield_choice_header_inv {
	display: none;
}

.gp-inventory-choices.inventory-limits-enabled .gfield_choice_header_inv {
	display: inline-block;
}


/**
 * Legacy Styles
 */

.gf-legacy-ui .gp-inventory-choices label.gfield_choice_header_inv {
	display: none !important;
	float: none;
	margin-right: 0;
}

.gf-legacy-ui .gp-inventory-choices input.field-choice-inventory-limit,
.gf-legacy-ui .field-choice-inventory-limit {
	display: none;
}

.gf-legacy-ui .gp-inventory-choices.inventory-limits-enabled label.gfield_choice_header_inv {
	display: inline !important;
}

.gf-legacy-ui .gp-inventory-choices.inventory-limits-enabled label.gfield_choice_header_limit {
	display: none !important;
}

.gf-legacy-ui .gp-inventory-choices.inventory-limits-enabled input.field-choice-limit {
	display: none;
}

.gf-legacy-ui .gp-inventory-choices.inventory-limits-enabled input.field-choice-inventory-limit {
	display: inline;
	width: 40px;
	margin-left: 0;
}

/* Headers */

.gf-legacy-ui .gp-inventory-choices .gfield_choice_header_label {
	display: inline !important;
}

.gf-legacy-ui .gp-inventory-choices .gfield_choice_header_inv {
	display: inline !important;
	padding-left: 228px;
}

.gf-legacy-ui .gp-inventory-choices .choice_with_value .gfield_choice_header_inv {
	padding-left: 65px;
}

.gf-legacy-ui .gp-inventory-choices .choice_with_price .gfield_choice_header_inv {
	padding-left: 67px;
}

.gf-legacy-ui .gp-inventory-choices .choice_with_value_and_price .gfield_choice_header_price {
	padding-left: 74px;
}

.gf-legacy-ui .gp-inventory-choices .choice_with_value_and_price .gfield_choice_header_inv {
	padding-left: 1px;
}

/* Label Input */

.gf-legacy-ui .gp-inventory-choices.inventory-limits-enabled #field_choices li input.field-choice-text {
	width: 270px;
}

/* Value Input */

.gf-legacy-ui .gp-inventory-choices.inventory-limits-enabled .choice_with_value li input.field-choice-value {
	width: 113px !important;
}

/* Price Input */

.gf-legacy-ui .gp-inventory-choices.inventory-limits-enabled .choice_with_price li input.field-choice-price {
	width: 113px !important;
}

.gf-legacy-ui .gp-inventory-choices.inventory-limits-enabled .choice_with_value_and_price li input.field-choice-price {
	width: 60px !important;
}

</style>
