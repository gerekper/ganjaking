<template>
	<div class="searchwp-engine-source-attributes-manager">
		<component :is="i18nAttributeChoicesNote"></component>
		<div class="searchwp-engine-source-attributes-types">
			<div class="searchwp-engine-source-attributes-manager-no-options">
				<ul>
					<li v-for="(attribute, index) in attributesWithoutOptions" :key="index">
						<Checkbox
							:id="engine + '-' + source + '-' + attribute.name"
							:value="attribute.name"
							:checked="!!attribute.settings"
							@change="function(value) { toggleAttribute(attribute) }"
						>
							<Tooltip v-if="attribute.tooltip && attribute.tooltip.length"
								:content="attribute.tooltip">
								{{ attribute.label }}
							</Tooltip>
							<span v-else>{{ attribute.label }}</span>
						</Checkbox>
						<ul v-if="attribute.notes && attribute.notes.length"
							class="searchwp-engine-source-attributes-notes">
							<li v-for="(note, index) in attribute.notes" :key="index">
								<p class="description">{{ note }}</p>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="searchwp-engine-source-attributes-manager-has-options">
				<ul>
					<li v-for="(attribute, index) in attributesWithOptions" :key="index">
						<h5>
							<Tooltip v-if="attribute.tooltip && attribute.tooltip.length"
								:content="attribute.tooltip">
								{{ attribute.label }}
							</Tooltip>
							<span v-else>{{ attribute.label }}</span>
						</h5>
						<AttributeOptions
							:attribute="attribute"
							:source="source"
							@change="function(options) {
								updateAttributeOptions(attribute, options);
							}"
						></AttributeOptions>
						<ul v-if="attribute.notes && attribute.notes.length"
							class="searchwp-engine-source-attributes-notes">
							<li v-for="(note, index) in attribute.notes" :key="index">
								<p class="description">{{ note }}</p>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
		<p>
			<button class="button button-primary" style="display: block; margin-left: auto;"
				@click="$emit('save', state)">
				<component :is="i18nSaveAttributes"></component>
			</button>
		</p>
	</div>
</template>

<script>
import Tooltip from './../Tooltip.vue';
import { __ } from './../../helpers.js';
import cloneDeep from 'lodash.clonedeep';
import Checkbox from './../Inputs/Checkbox.vue';
import { EngineUtils } from './../../Mixins/EngineUtils.js';
import AttributeOptions from './../Inputs/AttributeOptions.vue';

export default {
	name: 'EngineSourceAttributesManager',
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
		Checkbox,
		Tooltip,
		AttributeOptions
	},
	mixins: [EngineUtils],
	computed: {
		i18nSaveAttributes: function() {
			let self = this;
			return {
				computed: {
					sourceSingular: function() {
						return self.sourceSingular;
					}
				},
				template: '<span>' + __('Done') + '</span>'
			};
		},
		i18nAttributeChoicesNote: function() {
			let self = this;
			return {
				computed: {
					engineLabel: function() {
						return self.engineLabel;
					},
					sourceSingular: function() {
						return self.sourceSingular;
					}
				},
				template: '<p class="description">' + __('_attributes_choices_note') + '</p>'
			};
		},
		engineLabel: function() {
			return this.getEngineProperty(this.engine, 'label');
		},
		sourceSingular: function() {
			return this.getEngineSourceProperty(this.engine, this.source, 'labels').singular;
		},
		attributes: function() {
			return this.getEngineSourceProperty(this.engine, this.source, 'attributes');
		},
		attributesWithoutOptions: function() {
			let attributes = {};

			for (const attribute in this.attributes) {
				if (!Array.isArray(this.attributes[attribute].options)) {
					attributes[attribute] = cloneDeep(this.attributes[attribute]);
				}
			}

			return attributes;
		},
		attributesWithOptions: function() {
			let attributes = {};

			for (const attribute in this.attributes) {
				if (Array.isArray(this.attributes[attribute].options)) {
					attributes[attribute] = cloneDeep(this.attributes[attribute]);
				}
			}

			return attributes;
		}
	},
	methods: {
		getAttributeDefault: function(attribute) {
			const weights = Object.keys(_SEARCHWP.weights);
			const defaultWeight = this.state[attribute.name].default;

			return defaultWeight ? defaultWeight : weights[0];
		},
		updateAttributeOptions: function(attribute, options) {
			let self = this;

			const existingSettings = cloneDeep(self.state[attribute.name].settings);
			let updatedAttributeSettings = options.length ? {} : null; // null is how it comes back from the server.

			options.forEach(function(option) {
				// Did this Option already exist?
				if (existingSettings && existingSettings.hasOwnProperty(option.value)) {
					updatedAttributeSettings[option.value] = existingSettings[option.value];
				} else {
					updatedAttributeSettings[option.value] = self.getAttributeDefault(attribute);
				}
			});

			self.state[attribute.name].settings = updatedAttributeSettings;
		},
		toggleAttribute: function(attribute) {
			if (this.state[attribute.name].settings) {
				this.state[attribute.name].settings = false;
			} else {
				this.state[attribute.name].settings = this.getAttributeDefault(attribute);
			}
		}
	},
	created() {
		this.state = cloneDeep(this.attributes);
	},
	data() {
		return {
			state: {}
		}
	}
}
</script>

<style lang="scss">
	.searchwp-engine-source-attributes-types {
		display: flex;
		justify-content: space-between;
		margin: 1em 0 2em;
	}

	.searchwp-settings-view .searchwp-settings .searchwp-engine-source-attributes-manager {

		li {
			margin-bottom: 0.7em;
		}

		.searchwp-engine-source-attributes-notes {
			list-style: none;
			margin: 0.5em 0 1em;
			padding: 0;

			li {
				margin: 0;

				p.description {
					margin-bottom: 0;
				}
			}
		}
	}

	.searchwp-engine-source-attributes-manager-no-options {
		margin-top: 0.7em;
	}

	.searchwp-engine-source-attributes-manager-has-options {
		flex: 1;
		padding-left: 5%;
		max-width: 75%;
		min-height: 170px;

		h5 {
			font-weight: 500;
			font-size: 1.05em;
			margin: 0 0 0.4em;
		}

		li {
			margin-bottom: 1em;
		}
	}
</style>
