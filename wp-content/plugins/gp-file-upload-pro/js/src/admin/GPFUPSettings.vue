<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue from 'vue';
import InputUnit from "./InputUnit.vue";
import Tooltip from "./Tooltip.vue";

interface Data {
	gpfupEnable: boolean
	gpfupEnableCrop: boolean
	gpfupEnableSorting: boolean
	gpfupCropRequired: boolean
	gpfupAspectRatioAntecedent: number | undefined
	gpfupAspectRatioConsequent: number | undefined
	gpfupMaxWidth: number | undefined
	gpfupMaxHeight: number | undefined
	gpfupMinWidth: number | undefined
	gpfupMinHeight: number | undefined
	gpfupExactWidth: number | undefined
	gpfupExactHeight: number | undefined
}

const data: Data = {
	gpfupEnable: false,
	gpfupEnableCrop: false,
	gpfupEnableSorting: false,
	gpfupCropRequired: false,
	gpfupAspectRatioAntecedent: undefined,
	gpfupAspectRatioConsequent: undefined,
	gpfupMaxWidth: undefined,
	gpfupMaxHeight: undefined,
	gpfupMinWidth: undefined,
	gpfupMinHeight: undefined,
	gpfupExactWidth: undefined,
	gpfupExactHeight: undefined,
};

/** Used for resetting data */
const defaultData = {...data};

export default Vue.extend({
	name: 'GPFUPSettings',
	props: [
		'field',
		'strings',
	],
	components: {
		InputUnit,
		Tooltip,
	},
	data() {
		return data;
	},
	/**
	 * Listen for  changes in observables/data and send them to Gravity Forms.
	 */
	created: function () {
		for (const fieldSetting of Object.keys(data)) {
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
				/**
				 * This is needed due to the watch on gpfupEnable not firing at the right time if
				 * upgrading a single-file
				 */
				this.toggleMultiFileNoticeAndDisable();

				if (prevField && field.id === prevField.id) {
					return;
				}

				this.syncFieldToData();
			},
			deep: true,
		},
		gpfupEnable: function(enabled) {
			// @ts-ignore
			if (!this.field?.['multipleFiles'] && enabled) {
				if (window.has_entry(this.field.id)) {
					// @ts-ignore
					const confirmed = confirm(this.strings?.['upgrade_multiple_files']);
					if (!confirmed) {
						// @ts-ignore
						this.$nextTick(() => {
							// @ts-ignore
							this.gpfupEnable = false;
						});

						return;
					}

					window.SetFieldProperty('gpfupUpgradeToMultipleFiles', true);
					window.SetFieldProperty('maxFiles', '1');
				}

				// Convert from single file to multiple uploads.
				window.SetFieldProperty('multipleFiles', true);

				jQuery("#field_multiple_files").prop("checked", true);
				window.ToggleMultiFile();
			}

			this.toggleMultiFileNoticeAndDisable();
		}
	},
	computed: {
		usingExact: function (): boolean {
			return !!(this.gpfupExactWidth || this.gpfupExactHeight);
		},
		usingMin: function (): boolean {
			return !!(this.gpfupMinWidth || this.gpfupMinHeight);
		},
		usingMax: function (): boolean {
			return !!(this.gpfupMaxWidth || this.gpfupMaxHeight);
		},
		usingRatio: function (): boolean {
			return !!(this.gpfupAspectRatioAntecedent || this.gpfupAspectRatioConsequent);
		},
		lockExact: function () {
			return (this.usingMax || this.usingMin || this.usingRatio);
		},
		lockMin: function () {
			return this.usingExact;
		},
		lockMax: function () {
			return this.usingExact;
		},
		lockRatio: function () {
			return this.usingExact;
		},
	},
	methods: {
		toggleMultiFileNoticeAndDisable() {
			jQuery('#gpfup-multi-file-requirement-notice').remove();

			const $multipleFiles = jQuery('#field_multiple_files');

			if (this.gpfupEnable) {
				const $notice = jQuery(`<div id="gpfup-multi-file-requirement-notice" class="gform-accessibility-warning field_setting gform-alert gform-alert--accessibility gform-alert--inline">
					<span class="gform-icon gform-icon--password gform-alert__icon"></span>
					<div class="gform-alert__message-wrap">
						<p class="gform-alert__message" style="margin: 0;">${this.strings?.['multi_file_requirement']}</p>
					</div>
				</div>`);

				$multipleFiles.prop('disabled', true);
				$notice.insertBefore('#gform_multiple_files_options');
			} else {
				$multipleFiles.prop('disabled', window.has_entry(this.field.id));
			}
		},
		syncFieldToData() {
			var vm = this;

			Object.assign(vm.$data, defaultData);

			for (const fieldSetting of Object.keys(data)) {
				if (!window.field) {
					continue;
				}

				if (fieldSetting in window.field) {
					vm.$data[fieldSetting] = window.field[fieldSetting];
				}
			}
		},
	}
});
</script>

<template>
	<li id="gpfup" v-show="field && field.type === 'fileupload'" class="gp-field-setting field_setting">
		<div class="gpfup-field-setting field_setting">
			<input type="checkbox" value="1" id="gpfup-enable" v-model="gpfupEnable"
			       :disabled="!field"/>

			<label for="gpfup-enable" class="inline">
				{{ strings.enable_file_upload_pro }}
				<Tooltip :content="strings.tooltip_enable" />
			</label>
		</div>

		<div v-if="field && field.multipleFiles && gpfupEnable" class="gp-child-settings gpfup-child-settings">
			<div class="gp-row">
				<input type="checkbox" value="1" id="gpfup-enable-sorting" v-model="gpfupEnableSorting"/>
				<label for="gpfup-enable-sorting" class="inline">
					{{ strings.enable_sorting }}
					<Tooltip :content="strings.tooltip_enable_sorting" />
				</label>
			</div>

			<div class="gp-row">
				<input type="checkbox" value="1" id="gpfup-enable-crop" v-model="gpfupEnableCrop"/>
				<label for="gpfup-enable-crop" class="inline">
					{{ strings.enable_cropping }}
					<Tooltip :content="strings.tooltip_enable_cropping" />
				</label>
			</div>

			<div v-if="gpfupEnableCrop" class="gp-child-settings gpfup-cropper-child-settings">

				<div class="gp-setting">

					<label class="section_label">
						{{ strings.exact_dimensions }}
						<Tooltip :content="strings.tooltip_exact_dimensions" />
					</label>

					<div class="gp-group">
						<div class="gpfup-exact-width gpfup-dimension">
							<label for="gpfup-exact-width">
								{{ strings.width }}
							</label>

							<InputUnit id="gpfup-exact-width" :disabled="lockExact" v-model="gpfupExactWidth" unit="px"/>
						</div>

						<div class="gpfup-exact-height gpfup-dimension">
							<label for="gpfup-exact-height">
								{{ strings.height }}
							</label>

							<InputUnit id="gpfup-exact-height" :disabled="lockExact" v-model="gpfupExactHeight" unit="px"/>
						</div>
					</div>

				</div>

				<div class="gp-setting">

					<label class="section_label">
						{{ strings.max_dimensions }}
						<Tooltip :content="strings.tooltip_max_dimensions" />
					</label>

					<div class="gp-group">
						<div class="gpfup-max-width gpfup-dimension">
							<label for="gpfup-max-width">
								{{ strings.width }}
							</label>

							<InputUnit id="gpfup-max-width" :disabled="lockMax" v-model="gpfupMaxWidth" unit="px"/>
						</div>

						<div class="gpfup-max-height gpfup-dimension">
							<label for="gpfup-max-height">
								{{ strings.height }}
							</label>

							<InputUnit id="gpfup-max-height" :disabled="lockMax" v-model="gpfupMaxHeight" unit="px"/>
						</div>
					</div>

				</div>

				<div class="gp-setting">

					<label class="section_label">
						{{ strings.min_dimensions }}
						<Tooltip :content="strings.tooltip_min_dimensions" />
					</label>

					<div class="gp-group">
						<div class="gpfup-min-width gpfup-dimension">
							<label for="gpfup-min-width">
								{{ strings.width }}
							</label>
							<InputUnit id="gpfup-min-width" :disabled="lockMin" v-model="gpfupMinWidth" unit="px"/>
						</div>

						<div class="gpfup-min-height gpfup-dimension">
							<label for="gpfup-min-height">
								{{ strings.height }}
							</label>
							<InputUnit id="gpfup-min-height" :disabled="lockMin" v-model="gpfupMinHeight" unit="px"/>
						</div>
					</div>

				</div>

				<div class="gp-setting">
					<label class="section_label" for="gpfup-ratio-antecedent">
						{{ strings.aspect_ratio }}
						<Tooltip :content="strings.tooltip_aspect_ratio" />
					</label>

					<div class="gp-group">
						<div class="gpfup-ratio">
							<input type="text"
								   :disabled="lockRatio"
								   id="gpfup-ratio-antecedent"
								   v-model="gpfupAspectRatioAntecedent"/>
							<span class="gpfup-ratio-divider">:</span>
							<input type="text"
								   :disabled="lockRatio"
								   id="gpfup-ratio-consequent"
								   v-model="gpfupAspectRatioConsequent"
								   :aria-labeled="strings.aspect_ratio" />
						</div>
						<div class="gpfup-ratio-placeholder-sibling">
							<!-- Used to ensure flex alignment with the dimension settings above. Don't hate! -->
						</div>
					</div>

				</div>

				<div v-if="gpfupEnableCrop" class="gpfup-require-crop">
					<input type="checkbox" value="1" id="gpfup-crop-required" v-model="gpfupCropRequired"/>
					<label for="gpfup-crop-required" class="inline">
						{{ strings.require_crop }}
						<Tooltip :content="strings.tooltip_require_crop" />
					</label>
				</div>
			</div>
		</div>
	</li>
</template>

<style>
#gpfup label.section_label span.dashicons {
	font-size: inherit;
	line-height: inherit;
	width: auto;
	height: auto;
}

.gpfup-ratio {
	display: flex;
}

.gpfup-ratio-divider {
	line-height: 2.25rem; /* Matches GF 2.5's max/min-height on inputs. */
	margin: 0 0.2rem;
}

#gpfup-ratio-antecedent {
	text-align: right;
}

.gpfup-dimension,
.gpfup-ratio-placeholder-sibling {
	margin-left: 0.9375rem;
}

.gpfup-dimension:first-of-type {
	margin-left: 0;
}

.gpfup-unit-px::after {
	content: "px";
	position: absolute;
	right: 0;
	font-size: 12px;
	display: inline-block;
}

.gpfup-require-crop {
	margin-top: 15px;
}

/* GF has some nasty <br> tags in the Multiple Files setting. */
body:not(.gf-legacy-ui) #gform_multiple_files_options br {
	display: none;
}

/* Tweak GF's icon placement for our notice so it is vertically centered. */
#gpfup-multi-file-requirement-notice .gform-alert__icon {
    top: 0;
    margin-top: auto;
    margin-bottom: auto;
}

</style>
