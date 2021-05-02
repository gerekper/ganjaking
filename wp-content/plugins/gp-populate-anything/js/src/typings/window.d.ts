import GPPALiveMergeTags from "../classes/GPPALiveMergeTags";
import GPPopulateAnything from "../classes/GPPopulateAnything";

declare global {
	interface GPPAObjectType {
		id: string
		label: string
		properties: any
		groups: any
		templates: any
		restricted: boolean
	}

	interface Window {
		GFMergeTag: any;
		gppaLiveMergeTags: { [formId: string]: GPPALiveMergeTags };
		gppaForms: { [formId: string]: GPPopulateAnything };
		jQuery: JQueryStatic
		field: GravityFormsField
		fieldSettings: { [fieldType: string]: string };
		ajaxurl: string
		form: any
		gfMergeTagsObj: any
		GPPA_ADMIN: {
			isSuperAdmin: boolean
			interpretedMultiInputFieldTypes: string[]
			multiSelectableChoiceFieldTypes: string[]
			strings: {
				[key: string]: string
			}
			defaultOperators: string[]
			objectTypes: { [objectTypeId: string]: GPPAObjectType }
			nonce: string
		}
		gform: any
		gf_raw_input_change: any
		GPPA: {
			AJAXURL: string
			GF_BASEURL: string
			NONCE: string
			I18N: { [s: string]: string }
		}
		gf_global: any
		gformInitChosenFields: any
		GetSelectedField: any
		ToggleCalculationOptions: any
		GetInputType: any
		SetFieldProperty: any
		gformInitDatepicker: any
		gformCalculateTotalPrice: (formId: string | number) => void
		GPLimitDates: {
			initDisabledDatepicker: ( $input: JQuery ) => void
		}
	}
}
