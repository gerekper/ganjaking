<template>
	<div
		:class="[additionalClass, 'gppa-select-with-custom', { 'gppa-show-custom-input': showCustomInput, 'gppa-no-reset': forceCustomInput, 'gppa-has-merge-tag-selector': hasMergeTagSelector }]">
		<select v-if="loading">
			<option value="" disabled hidden>{{ i18nStrings.loadingEllipsis }}</option>
		</select>
		<select v-else-if="!showCustomInput" v-model="selectValueProxy">
			<slot></slot>

			<option value="gf_custom" v-if="injectCustomValueOption">{{ i18nStrings.addCustomValue }}</option>
		</select>

		<div class="gppa-select-with-custom-input-container">
			<input type="text" v-model="inputValueProxy" :id="'gppa-select-with-custom-input_' + _uid"
				   ref="customInput"
				   class="mt-position-right"/>
			<a href="#"
			   class="custom-reset"
			   @click.prevent="reset"
			   v-if="!forceCustomInput">{{ i18nStrings.reset }}</a>
		</div>
	</div>
</template>

<script lang="ts">
	import Vue from 'vue';

	export default Vue.extend({
		data: function () {
			return {
				i18nStrings: window.GPPA_ADMIN.strings,
				hasMergeTagSelector: false,
			}
		},
		mounted: function () {
			var vm = this;

			this.mergeTagsObj = new window.gfMergeTagsObj( window.form, jQuery(this.$refs.customInput) );
			this.mergeTagsObj.getMergeTags = this.getMergeTags;
			this.mergeTagsObj.getTargetElement = function () {
				return jQuery(vm.$refs.customInput);
			};

			/* GF Merge Tag selector doesn't trigger change by default so we need to shim that in. */
			jQuery(this.$refs.customInput).on('propertychange', function () {
				vm.$refs.customInput.dispatchEvent(new Event('input'));
			});

			this.hasMergeTagSelector = true;
		},
		beforeDestroy: function () {
			this.mergeTagsObj.destroy();
		},
		watch: {
			operator: function () {
				if (this.forceCustomInput && this.value.indexOf('gf_custom') !== 0) {
					this.$emit('change', 'gf_custom:' + this.value);
				}
			}
		},
		methods: {
			getMergeTags: function (fields, elementId, hideAllFields, excludeFieldTypes, isPrepop, option) {

				var vm = this;
				var mergeTags = {
					gppaProperties: {
						label: 'Properties',
						tags: []
					}
				};

				for (const [groupId, group] of Object.entries(this.objectTypeInstance.groups)) {
					mergeTags[groupId] = {
						label: group.label,
						tags: []
					};
				}

				for (const property of Object.values(this.flattenedProperties)) {
					mergeTags[property.group || 'gppaProperties'].tags.push({
						tag: '{' + vm.objectTypeInstance.id + ':' + property.value + '}',
						label: property.label
					});
				}

				return window.gform.applyFilters('gppa_template_merge_tags', mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option, this );

			},
			reset: function () {
				this.$emit('change', '');
			},
		},
		model: {
			prop: 'value',
			event: 'change'
		},
		computed: {
			selectValueProxy: {
				get: function () {
					return this.value;
				},
				set: function (newValue) {
					this.$emit('change', newValue);
				}
			},
			inputValueProxy: {
				get: function () {
					return this.value && this.value.toString().replace(/^gf_custom:?/, '');
				},
				set: function (newValue) {
					this.$emit('change', 'gf_custom:' + newValue);
				}
			},
			showCustomInput: function () {
				if (this.forceCustomInput) {
					return true;
				}

				return this.value && this.value.toString().indexOf('gf_custom') === 0;
			},
			forceCustomInput: function () {
				return ['like'].indexOf(this.operator) !== -1;
			}
		},
		props: {
			value: {
				type: [Number, String],
				default: function () {
					return '';
				}
			},
			additionalClass: String,
			operator: String,
			loading: {
				type: Boolean,
				default: function () {
					return false;
				}
			},
			objectTypeInstance: Object,
			flattenedProperties: Object,
			injectCustomValueOption: Boolean,
		}
	});
</script>
