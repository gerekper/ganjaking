/* Polyfills */
import 'core-js/es/array/includes'
import 'core-js/es/object/assign'
import 'core-js/es/object/values'
import 'core-js/es/object/entries'

import Vue from 'vue';
import Root from './components/Root.vue';

const $ = window.jQuery;

class GPPopulateAnythingAdmin {

	public vm:any;

	constructor () {

		for (let i in window.fieldSettings) {
			window.fieldSettings[i] += ', #gppa';
			window.fieldSettings[i] += ', #gppa-choices';
			window.fieldSettings[i] += ', #gppa-values';
		}

		$(document).on('gform_load_field_settings', this.onLoadFieldSettings);

		$('.custom_inputs_setting, .custom_inputs_sub_setting, .sub_labels_setting')
			.on('click keypress', '.input_active_icon', () => {
				this.vm.$set(this.vm.$data.field, 'inputs', {...window.field.inputs});
			});

		this.initVueVM();

	}

	onLoadFieldSettings = (event:JQueryEventObject, field:GravityFormsField)  => {
		this.vm.$data.field = {...field};
		this.vm.$refs.root.refresh();
	};

	initVueVM () {

		this.vm = new Vue({
			el: '#gppa',
			render (h) {
				/* h is used here to avoid needing the Vue runtime compiler */
				return h('Root', {
					props: {
						field: this.field
					},
					ref: 'root',
				});
			},
			data: {
				field: null,
			},
			components: {
				Root,
			},
		});

	}


}

(window as any).GPPA = new GPPopulateAnythingAdmin();
