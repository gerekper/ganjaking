/* Polyfills */
import 'core-js/es/array/includes'
import 'core-js/es/object/assign'
import 'core-js/es/object/values'
import 'core-js/es/object/entries'

import Vue from 'vue';
import GPFUPSettings from './GPFUPSettings.vue';

const $ = window.jQuery;

class GPFileUploadProAdmin {

	public vm!: Vue;

	constructor() {
		const { fieldSettings } = window;

		for (let fieldType in fieldSettings) {
			if (fieldSettings.hasOwnProperty(fieldType) && $.inArray(fieldType, ['fileupload']) !== -1) {
				fieldSettings[fieldType] += ', .gpfup-field-setting, #gpfup';
			}
		}

		// @ts-ignore
		$(document).on('gform_load_field_settings', this.onLoadFieldSettings);

		this.initVueVM();
	}

	onLoadFieldSettings = (event:JQueryEventObject, field:GravityFormsField)  => {
		this.vm.$data.field = {...field};
	};

	initVueVM() {
		this.vm = new Vue({
			el: '#gpfup',
			render (h) {
				/* h is used here to avoid needing the Vue runtime compiler */
				return h('GPFUPSettings', {
					props: {
						field: this.field,
						strings: window.GPFUP_CONSTANTS.STRINGS,
					},
					ref: 'root',
				});
			},
			data: {
				field: null,
			},
			components: {
				GPFUPSettings,
			},
		});
	}

}

(window as any).GPFUPInstance = new GPFileUploadProAdmin();
