/* Polyfills */
// import 'core-js/es/array/includes'
// import 'core-js/es/object/assign'
// import 'core-js/es/object/values'
// import 'core-js/es/object/entries'

import Vue from 'vue';
import GPISettings from './GPISettings.vue';
import GPIChoiceInventory from './GPIChoiceInventory';

Vue.prototype.$i18n = window.GPI_ADMIN.strings;

// @ts-ignore
const {jQuery: $} = window;

class GPInventoryFormEditor {

	public vm!: Vue;

	public field: GravityFormsField | undefined;

	public settingsSelectors: string[] = ['.gpi-field-setting', '#gp-inventory'];

	/**
	 * Base input types that can accept inventory.
	 */
	public supportedInputTypes: Window['GPI_ADMIN']['supportedInputTypes'] = window.GPI_ADMIN.supportedInputTypes;

	/**
	 * Field type overrides beyond the supported input types. Entire field types (no matter the input) type can be
	 * allowed by providing a value of "true".
	 *
	 * Input types can be excluded from a field type by providing an array of the ALLOWED input types for the given
	 * field type.
	 */
	public supportedFieldTypes: Window['GPI_ADMIN']['supportedFieldTypes'] = window.GPI_ADMIN.supportedFieldTypes;

	/**
	 * Field types that will use the choice inventory type.
	 */
	public choiceInputTypes: Window['GPI_ADMIN']['choiceInputTypes'] = window.GPI_ADMIN.choiceInputTypes;

	public choiceInventory: GPIChoiceInventory;

	constructor() {
		this.choiceInventory = new GPIChoiceInventory();

		$(document).on('gform_load_field_settings', this.onLoadFieldSettings);
		window.gform.addFilter('gform_editor_field_settings', this.allowFieldTypes);

		$('.panel-block').on('click', '.gpi-go-to-choices', this.goToChoicesSetting);

		this.initVueVM();
	}

	isSupportedInputType = (field: GravityFormsField) : boolean => {
		if (!field) {
			return false;
		}

		const inputType = field?.inputType ? field?.inputType : field.type;

		return this.supportedInputTypes.indexOf(inputType) !== -1;
	}

	isSupportedFieldType = (field: GravityFormsField) : boolean => {
		if (!field) {
			return false;
		}

		const supportedInputType = this.isSupportedInputType(field);

		if (field.type in this.supportedFieldTypes) {
			if (this.supportedFieldTypes[field.type] === true) {
				return true;
			} else if (Array.isArray(this.supportedFieldTypes[field.type])) {
				return (this.supportedFieldTypes[field.type] as string[]).indexOf(field.inputType) !== -1;
			}
		}

		return supportedInputType;
	}

	isSupportedChoiceFieldType = (field: GravityFormsField) : boolean => {
		if (!this.isSupportedFieldType(field)) {
			return false;
		}

		const inputType = field.inputType ? field.inputType : field.type;

		return this.choiceInputTypes.indexOf(inputType) !== -1;
	}

	goToChoicesSetting = (event?: JQuery.Event) => {
		($( '.field_settings' ) as any).accordion( 'option', { active: 0 } );
		$('.choices_setting')[0].scrollIntoView();

		$('.field-choice-inventory-limit')?.[0].focus();

		if (event) {
			event.preventDefault();
		}
	}

	allowFieldTypes = (settingsArray: string[], field: GravityFormsField) => {
		if (this.isSupportedFieldType(field)) {
			settingsArray = settingsArray.concat(this.settingsSelectors);
		} else {
			settingsArray = settingsArray.filter((setting) => this.settingsSelectors.indexOf(setting) === -1);
		}

		return settingsArray;
	};

	onLoadFieldSettings = (event: JQuery.Event, field: GravityFormsField) => {
		// gform_editor_field_settings is not available in GF 2.4 so we'll hide and show the GPI container ourselves.
		if (jQuery('body').hasClass('gf-legacy-ui')) {
			if (this.isSupportedFieldType(field)) {
				jQuery(this.settingsSelectors.join(', ')).show();
			} else {
				jQuery(this.settingsSelectors.join(', ')).hide();
			}
		}

		this.vm.$data.field = {...field};
	};

	initVueVM() {
		const GPIInstance = this;

		this.vm = new Vue({
			el: '#gp-inventory',
			render(h) {
				/* h is used here to avoid needing the Vue runtime compiler */
				return h('GPISettings', {
					props: {
						field: this.field,
						GPIInstance,
					},
					ref: 'root',
				});
			},
			data: {
				field: null,
			},
			components: {
				GPISettings,
			},
		});
	}

}

(window as any).GPIInstance = new GPInventoryFormEditor();
