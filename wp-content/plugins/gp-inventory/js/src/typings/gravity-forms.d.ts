interface GravityFormsField {
	inputs: any
	choices: any
	label: string
	adminLabel?: string
	gpiInventory?: string
	gpiResource?: number
	gpiResourcePropertyMap?: { [propertyId: string]: string }
	id: number
	formId: number
	type: string
	inputType: string
}

interface GravityFormsForm {
	button: {
		type: string
		text: string
		imageUrl: string
	}
	confirmations: any
	description: string
	descriptionPlacement: string
	fields: GravityFormsField[]
	firstPageCssClass: string
	id: number
	labelPlacement: string
	lastPageButton: string
	markupVersion: number
	nextFieldId: number
	notifications: any
	pagination: any
	postContentTemplate: string
	postContentTemplateEnabled: boolean
	postTitleTemplate: string
	postTitleTemplateEnabled: boolean
	title: string
	useCurrentUserAsAuthor: boolean
	version: string
}
