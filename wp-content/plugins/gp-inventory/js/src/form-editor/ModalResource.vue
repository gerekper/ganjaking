<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue, {PropType} from 'vue';
import Modal from "./Modal.vue";
import Tooltip from "./Tooltip.vue";
import Repeater from "./Repeater.vue";
import shortid from 'shortid';

const $ = window.jQuery;

export default Vue.extend({
	name: 'ModalResource',
	props: {
		show: {
			type: Boolean,
			required: true,
		},
		field: {
			type: Object as PropType<GravityFormsField>,
		},
		mode: {
			required: true,
			validator: function (value) {
				return ['add', 'edit', undefined].indexOf(value as string) !== -1
			}
		},
		editingResource: {
			type: [Object, undefined] as PropType<Resource | undefined>,
		},
	},
	components: {
		Repeater,
		Tooltip,
		Modal
	},
	data() : {
		submitting: boolean,
		id?: number,
		name: string,
		limit: number,
		properties: ResourceProperty[],
		claimedInventory?: number,
		loadingClaimedInventory: boolean,
		alwaysShowInventoryLimit: boolean,
	} {
		return {
			submitting: false,
			id: undefined,
			name: '',
			limit: 0,
			properties: [],
			claimedInventory: undefined,
			loadingClaimedInventory: false,
			alwaysShowInventoryLimit: window.GPI_ADMIN.alwaysShowInventoryLimitInEditor,
		};
	},
	watch: {
		// Watch for show to auto update data with the resource that is being edited
		show: function(newShow, oldShow) {
			if (newShow !== true) {
				return;
			}

			this.claimedInventory = undefined;
			this.loadingClaimedInventory = false;

			if (this.editingResource) {
				// Dereference to prevent resource from being updated in UI prior to actually saving
				const resource = JSON.parse(JSON.stringify(this.editingResource));

				this.id = resource.id;
				this.name = resource.name;
				this.limit = resource.inventory_limit;
				this.properties = resource.properties;

				this.fetchClaimedInventory();
			}
		}
	},
	computed: {
		nonce: () : string => window.GPI_ADMIN.nonce,
		title() : string {
			if (this.mode === 'edit') {
				return this.$i18n.edit_resource.replace('%s', this.name);
			}

			return this.$i18n.add_resource;
		},
		submitText() : string {
			if (this.mode === 'edit') {
				return !this.submitting ? this.$i18n.edit : this.$i18n.editing;
			}

			return !this.submitting ? this.$i18n.add : this.$i18n.adding;
		},
		submitDisabled() : boolean {
			return this.submitting || !this.name;
		},
		currentInventory: {
			get: function () {
				return (this.limit ?? 0) - (this.claimedInventory ?? 0);
			},
			set: function (desiredInventory: string) {
				this.limit = parseInt(desiredInventory) + (this.claimedInventory ?? 0);
			}
		} as any,
	},
	methods: {
		resetData() : void {
			Object.assign(this.$data, (this as any).$options.data());
		},
		close() : void {
			this.$emit('close');
			this.resetData();
		},
		// Method to create empty property when added by the repeater
		propertyFactory() : { name: string, id: string } {
			return {
				name: '',
				id: shortid.generate(), // Provide ID on properties so the name can easily be changed down the road
			}
		},
		beforeRemoveProperty(index: number, value: ResourceProperty[]) : boolean | undefined {
			if (this.editingResource) {
				const existingResourceIds = this.editingResource.properties.map((property) => property.id);

				if (existingResourceIds.indexOf(value[index].id) !== -1) {
					return confirm(this.$i18n.delete_resource_scope_confirm);
				}
			}
		},
		deleteResource() : void {
			if (!confirm(this.$i18n.delete_resource_confirm.replace('%s', this.name))) {
				return;
			}

			$.post(window.ajaxurl, {
				action: 'gpi_delete_resource',
				security: this.nonce,
				resource_id: this.id,
			}, null, 'json').done(() => {
				this.$emit('delete', this.id as Resource['id']);

				this.close();
			}).fail(() => {
				alert(this.$i18n.error_deleting_resource);
			});
		},
		submit() : void {
			this.submitting = true;

			$.post(window.ajaxurl, {
				action: this.mode === 'add' ? 'gpi_add_resource' : 'gpi_edit_resource',
				security: this.nonce,
				resource_id: this.id,
				resource_name: this.name,
				inventory_limit: this.limit,
				choice_based: !!this.field?.choices?.length,
				properties: this.properties,
			}, null, 'json').done((data: { resource_id: number }) => {
				this.$emit('submit', {
					id: data.resource_id,
					name: this.name,
					inventory_limit: this.limit,
					choice_based: !!this.field?.choices?.length,
					properties: this.properties,
				} as Resource);

				this.close();
			}).fail(() => {
				const { error_adding_resource, error_editing_resource } = this.$i18n;

				alert(this.mode === 'add' ? error_adding_resource : error_editing_resource);
				this.submitting = false;
			});
		},
		fetchClaimedInventory(): void {
			if (this.properties?.length || this.alwaysShowInventoryLimit) {
				this.claimedInventory = undefined;
				this.loadingClaimedInventory = false;
				return;
			}

			this.loadingClaimedInventory = true;

			$.post(window.ajaxurl, {
				action: 'gpi_get_resource_claimed_inventory',
				security: this.nonce,
				resource_id: this.id,
			}, null, 'json').done((claimedInventory) => {
				this.claimedInventory = claimedInventory;
			}).always(() => {
				this.loadingClaimedInventory = false;
			})
		},
	}
});
</script>

<template>
	<Modal
		@close="close"
		@submit="submit"
		:title="title"
		:subtitle="$i18n.resource_modal_subtitle"
		:show="show"
		:submit-text="submitText"
		:submit-disabled="submitDisabled">
		<template v-slot:panel-right-buttons v-if="mode === 'edit'">
			<input type="button" class="button-link button-link-delete" :value="$i18n.delete" @click="deleteResource" />
		</template>

		<div class="panel-block-tabs__body--settings">
			<ul>
				<li class="gpi-field-setting field_setting">
					<label for="gpi_resource_name" class="section_label">
						{{ $i18n.resource_name }}
						<Tooltip :content="$i18n.tooltip_resource_name" />
					</label>

					<input type="text" id="gpi_resource_name" v-model="name" />
				</li>

				<li class="gpi-field-setting field_setting">
					<label for="gpi_resource_inventory_limit" class="section_label" v-if="properties.length">
						{{ $i18n.inventory_per_combination }}
						<Tooltip :content="$i18n.tooltip_inventory" />
					</label>
					<label for="gpi_resource_inventory_limit" class="section_label" v-else>
						{{ $i18n.inventory }}
						<Tooltip :content="$i18n.tooltip_inventory" />
					</label>

					<input type="number" id="gpi_resource_inventory_limit" v-model="limit" v-if="!field.choices && !this.claimedInventory" :disabled="loadingClaimedInventory" />
					<input type="number" id="gpi_resource_inventory_current" v-model="currentInventory" v-else-if="!field.choices && this.claimedInventory" />
					<p v-else>{{ $i18n.resource_mapped_to_choice_field }}</p>
				</li>

				<li class="gpi-field-setting field_setting">
					<label class="section_label">
						{{ $i18n.resource_scopes }}
						<Tooltip :content="$i18n.tooltip_resource_scopes" />
					</label>

					<Repeater
						v-slot="{ item, itemIndex }"
						:item-factory="propertyFactory"
						:label-add-item="$i18n.add_scope"
						:label-remove-item="$i18n.remove_scope"
						:before-remove-item="beforeRemoveProperty"
						v-model="properties">
						<input type="hidden" v-model="item.id" />
						<input type="text" v-model="item.name" />
					</Repeater>
				</li>
			</ul>
		</div>
	</Modal>
</template>
