/**
 * Augment Window typings and add in properties provided by Gravity Forms, WordPress, etc.
 */
export {};
import TomSelect from "tom-select";

declare global {
	type formID = number | string;
	type fieldID = number | string;
	type GPAdvancedSelectTomSelectKey = `GPAdvancedSelect_${string}`;


	interface MultiSelectChoice {
		isSelected: boolean;
		price: string;
		text: string;
		value: string;
	}
	interface Field {
		id: number
		formId: number
		type: string
		gpadvsEnable: boolean
		placeholder: string
		choices: MultiSelectChoice[]
		'gppa-choices-enabled': boolean
	}

	interface Window {
		jQuery: JQueryStatic
		gppaForms: any
		GPPA: {
			AJAXURL: string
			GF_BASEURL: string
			NONCE: string
			I18N: { [s: string]: string }
		}
		gform: any
		GPAdvancedSelect: any
		fieldSettings: { [inputType: string]: string }
		GPADVS_FORM_EDITOR: {
			strings: {
				not_compat_with_enhanced_ui: string
			 }
		}
		GPADVS: {
			strings: {
				remove_this_item: string
			}
		}
		form: {
			fields: Field[]
		}
		field: any
		SetFieldProperty: (setting: string, value: any) => void
		SetFieldEnhancedUI: (enabled: boolean) => void
		UpdateFieldChoices: (fieldType: string) => void
		imageChoicesAdmin: any
		[key: GPAdvancedSelectTomSelectKey]: TomSelect
	}
}
