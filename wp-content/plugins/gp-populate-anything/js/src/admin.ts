/* Polyfills */
import 'core-js/es/array/includes';
import 'core-js/es/object/assign';
import 'core-js/es/object/values';
import 'core-js/es/object/entries';

import Vue from 'vue';
import Root from './components/Root.vue';

const $ = window.jQuery;

class GPPopulateAnythingAdmin {
	public vm: any;

	constructor() {
		for ( const i in window.fieldSettings ) {
			window.fieldSettings[ i ] += ', #gppa';
			window.fieldSettings[ i ] += ', #gppa-choices';
			window.fieldSettings[ i ] += ', #gppa-values';
		}

		$( document ).on(
			'gform_load_field_settings',
			this.onLoadFieldSettings
		);

		$(
			'.custom_inputs_setting, .custom_inputs_sub_setting, .sub_labels_setting'
		).on( 'click keypress', '.input_active_icon', () => {
			this.vm.$set( this.vm.$data.field, 'inputs', {
				...window.field.inputs,
			} );
		} );

		this.addFilters();
		this.initVueVM();
	}

	onLoadFieldSettings = ( event: JQuery.Event, field: GravityFormsField ) => {
		this.vm.$data.field = { ...field };
		this.vm.$refs.root.refresh();
	};

	addFilters() {
		window.gform.addFilter(
			'gppa_is_supported_field',
			(
				isSupportedField: boolean,
				field: GravityFormsField,
				populate: 'choices' | 'values',
				component: any
			) => {
				if ( ! field ) {
					return false;
				}

				/* Exclude specific field types */
				if ( [ 'consent', 'tos' ].indexOf( field.type ) !== -1 ) {
					return false;
				}

				switch ( populate ) {
					case 'choices':
						if ( field.type === 'list' ) {
							return false;
						}

						if ( component.hasChoices() ) {
							/* Exclude chained selects */
							if (
								field.choices[ 0 ] &&
								'choices' in field.choices[ 0 ]
							) {
								return false;
							}

							return true;
						}

						if (
							[ 'workflow_user', 'workflow_multi_user' ].indexOf(
								field.type
							) !== -1
						) {
							return true;
						}

						break;

					case 'values':
						if ( component.hasChoices() ) {
							/* Exclude chained selects */
							if (
								field.choices[ 0 ] &&
								'choices' in field.choices[ 0 ]
							) {
								return false;
							}

							return true;
						}

						/* Single input */
						if (
							component.currentFieldSettings.indexOf(
								'.default_value_setting'
							) !== -1
						) {
							return true;
						}

						/* Textarea */
						if (
							component.currentFieldSettings.indexOf(
								'.default_value_textarea_setting'
							) !== -1
						) {
							return true;
						}

						/* Input with multiple fields */
						if (
							component.currentFieldSettings.indexOf(
								'.default_input_values_setting'
							) !== -1
						) {
							return true;
						}

						if ( field.inputType === 'singleproduct' ) {
							return true;
						}

						if ( field.type === 'list' ) {
							return true;
						}

						break;
				}

				return false;
			}
		);
	}

	initVueVM() {
		this.vm = new Vue( {
			el: '#gppa',
			render( h ) {
				/* h is used here to avoid needing the Vue runtime compiler */
				return h( 'Root', {
					props: {
						field: this.field,
					},
					ref: 'root',
				} );
			},
			data: {
				field: null,
			},
			components: {
				Root,
			},
		} );
	}
}

( window as any ).GPPA = new GPPopulateAnythingAdmin();
