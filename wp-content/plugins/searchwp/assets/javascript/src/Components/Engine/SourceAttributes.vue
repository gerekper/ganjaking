<template>
	<div class="searchwp-engine-source-attributes">
		<div v-if="!hasAttributes">
			<p>{{ '_no_attributes_note' | i18n }}</p>
		</div>
		<ul v-else class="searchwp-attributes">
			<li v-for="(attribute, attributeName) in applicableAttributes" :key="attributeName">
				<EngineSourceAttribute
					:engine="engine"
					:source="source"
					:name="attributeName"
					@change="settings => updateAttributeSettings(attributeName, settings)"
				></EngineSourceAttribute>
			</li>
		</ul>

		<ul class="searchwp-actions">
			<li>
				<button class="button searchwp-engine-source-attributes-toggle"
					@click.stop="$modal.show(engine + '-' + source + '-attributes')">
					{{ 'Add/Remove Attributes' | i18n }}
				</button>
			</li>
		</ul>

		<Modal
			:name="engine + '-' + source + '-attributes'"
			:class="'searchwp-engine-source-attributes-manager-modal'"
			:label="'_manage_engine_source_attributes' | i18n([singular, engineLabel])"
			:actionIsPrimary="false"
			:actionLabel="'Cancel' | i18n">
			<EngineSourceAttributesManager
				:source="source"
				:engine="engine"
				@save="updateAttributes"
			></EngineSourceAttributesManager>
		</Modal>
	</div>
</template>

<script>
import Modal from './../Modal.vue';
import cloneDeep from 'lodash.clonedeep';
import EngineSourceAttribute from './SourceAttribute.vue';
import { EngineUtils } from './../../Mixins/EngineUtils.js';
import { SourceAttribute } from './../../Mixins/SourceAttribute.js';
import EngineSourceAttributesManager from './SourceAttributesManager.vue';

export default {
	name: 'EngineSourceAttributes',
	props: {
		source: {
			type: String,
			required: true
		},
		engine: {
			type: String,
			required: true
		}
	},
	components: {
		Modal,
		EngineSourceAttribute,
		EngineSourceAttributesManager
	},
	mixins: [EngineUtils],
	computed: {
		engineLabel: function() {
			return this.getEngineProperty(this.engine, 'label');
		},
		singular: function() {
			return this.getEngineSourceProperty(this.engine, this.source, 'labels').singular;
		},
		attributes: function() {
			return this.getEngineSourceProperty(this.engine, this.source, 'attributes');
		},
		applicableAttributes: function() {
			let attributes = {};

			for (const attribute in this.attributes) {
				if (this.isApplicable(this.attributes[attribute])) {
					attributes[attribute] = cloneDeep(this.attributes[attribute]);
				}
			}

			return attributes;
		},
		hasAttributes: function() {
			for (const attribute in this.attributes) {
				if (this.attributes[attribute].settings) {
					return true;
				}
			}

			return false;
		}
	},
	mixins: [EngineUtils, SourceAttribute],
	methods: {
		updateAttributes: function(attributes) {
			// The Attributes Manager keeps existing settings in tact so we can just update the state with this data.
			this.$store.commit('updateEngineSourceAttributes', {
				engine: this.engine,
				source: this.source,
				attributes: attributes
			});
			this.$modal.hide(this.engine + '-' + this.source + '-attributes');
		},
		updateAttributeSettings: function(attributeName, settings) {
			this.$store.commit('updateEngineSourceAttributeSettings', {
				engine: this.engine,
				source: this.source,
				attribute: attributeName,
				option: settings.option,
				value: settings.value
			});
		}
	}
}
</script>

<style lang="scss">
	.searchwp-engine-source-attributes {

		.searchwp-attributes {
			margin: 1.5em 0;
		}
	}

	.searchwp-engine-source-attributes-types {
		min-height: 165px; // Baseline to account for v-selects.
	}

	.searchwp-settings-view .v-select .vs__dropdown-menu {
		max-height: 150px; // Allow for extra space taken up by shortcuts.
	}

	.searchwp-engine-source-attributes-manager-modal .searchwp-modal-content {
		overflow: initial;
	}
</style>
