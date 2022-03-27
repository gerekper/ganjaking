/**
 * Augment Window typings and add in properties provided by Gravity Forms, WordPress, etc.
 */
interface Window {
    jQuery: JQueryStatic
	fieldSettings: { [fieldType: string]: string }
	SetFieldProperty: (name: string, value: any) => void
	ajaxurl: string
	GPI_ADMIN: {
		nonce: string
		strings: { [key: string]: string }
		resources: { [id: number]: Resource }
		supportedInputTypes: string[]
		supportedFieldTypes: { [fieldType: string]: true | string[] }
		choiceInputTypes: string[]
		alwaysShowInventoryLimitInEditor: boolean
	}
	field: GravityFormsField
	form: GravityFormsForm
	has_entry(fieldId: string | number): boolean
	gform: {
    	addFilter: Function
		addAction: Function
		applyFilters: Function
		doAction: Function
	}
	GPIProperties: any // typed as any as modules can't be imported in ambient declarations
}
