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
			objectTypes: { [objectTypeId: string]: GPPAObjectType }
			nonce: string
		}
		gform: any
		gf_raw_input_change: any
		GPPA_AJAXURL: string
		GPPA_NONCE: string
		GPPA_I18N: { [s: string]: string }
		gf_global: any
		GPPA_GF_BASEURL: string
		gformInitChosenFields: any
		GetSelectedField: any
		ToggleCalculationOptions: any
		GetInputType: any
		SetFieldProperty: any
	}
}
