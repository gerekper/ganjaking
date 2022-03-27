<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue from 'vue';
import Tooltip from "./Tooltip.vue";
import Modal from "./Modal.vue";
import Repeater from "./Repeater.vue";
import ModalResource from "./ModalResource.vue";

export default Vue.extend({
	name: 'ResourceSelect',
	props: [
		'field',
		'value',
		'resources',
	],
	components: {ModalResource, Repeater, Modal, Tooltip},
	data() : {
		showAddModal: boolean
		editingResource?: Resource
		properties: ResourceProperty[]
	} {
		return {
			showAddModal: false,
			editingResource: undefined,
			properties: [],
		};
	},
	watch: {},
	computed: {
		modalMode() {
			if (this.showAddModal) {
				return 'add';
			} else if (this.editingResource) {
				return 'edit';
			}

			return undefined;
		},
		choiceBasedResources() {
			return Object.values(this.resources as Window['GPI_ADMIN']['resources'])
				.filter((resource) => resource.choice_based);
		},
		singleResources() {
			return Object.values(this.resources as Window['GPI_ADMIN']['resources'])
				.filter((resource) => !resource.choice_based);
		}
	},
	mounted: function() {
		this.clearValueIfResourceNotPresent();
	},
	updated: function() {
		this.clearValueIfResourceNotPresent();
	},
	methods: {
		/**
		 * Reset the value of the Resource select to an empty string if the value does not exist in the dropdown.
		 */
		clearValueIfResourceNotPresent() {
			const valuePresentInResources = !!Object.values(this.resources as Window['GPI_ADMIN']['resources'])
				.filter((resource) => resource.id == this.value)
				.length;

			if (!valuePresentInResources) {
				this.$emit('input', '');
			}
		},
		add() {
			this.showAddModal = true;
			this.editingResource = undefined;
		},
		edit() {
			this.showAddModal = false;
			this.editingResource = this.resources[this.value];
		},
		closeModal() {
			this.showAddModal = false;
			this.editingResource = undefined;
		},
		isCompatibleWithCurrentField(resource: Resource) : boolean {
			if (resource.choice_based && !this.field?.choices?.length) {
				return false;
			}

			if (!resource.choice_based && this.field?.choices?.length) {
				return false;
			}

			return true;
		},
		async updateResource(resource: Resource) {
			this.$emit('resource-updated', resource);

			// Wait for UI to update before updating the select value. Without waiting, the new resource won't
			// be properly selected.
			await this.$nextTick();
			this.$emit('input', resource.id);
		},
		deleteResource(resourceId: Resource['id']) {
			this.$emit('resource-deleted', resourceId);
			this.$emit('input', '');
		}
	}
});
</script>

<template>
	<li class="gpi-field-setting field_setting gpi-resource-select">
		<label for="gpi_resource" class="section_label">
			{{ $i18n.resource }}
			<Tooltip :content="$i18n.tooltip_resource"/>
		</label>

		<ModalResource
			@submit="updateResource"
			@close="closeModal"
			@delete="deleteResource"
			:field="field"
			:mode="modalMode"
			:show="!!modalMode"
			:editing-resource="editingResource" />

		<div class="gpi-resource-select-container">
			<select :value="value"
			        @input="$emit('input', $event.target.value)"
			        id="gpi_resource">
				<option value="" disabled selected>&ndash; {{ $i18n.select_a_resource }} &ndash;</option>

				<optgroup label="Single">
					<option
						v-for="resource of singleResources"
						:value="resource.id"
						:disabled="!isCompatibleWithCurrentField(resource)">{{ resource.name }}</option>
				</optgroup>

				<optgroup label="Choice-based">
					<option
						v-for="resource of choiceBasedResources"
						:value="resource.id"
						:disabled="!isCompatibleWithCurrentField(resource)">{{ resource.name }}</option>
				</optgroup>
			</select>
			<button class="button-link gpi-resource-edit" @click="edit" v-if="value">{{ $i18n.edit }}</button>
			<button class="button-link gpi-resource-add" @click="add">{{ $i18n.add }}</button>
		</div>
	</li>
</template>

<style>
.gpi-resource-select-container {
	display: flex;
	margin-top: 0 !important;
}

.gpi-resource-select-container button.button-link {
	margin-left: 1rem;
}
</style>
