/**
 * Augment Window typings and add in properties provided by Gravity Forms, WordPress, etc.
 */
interface Window {
	jQuery: JQueryStatic

	// Form Editor
	fieldSettings: { [fieldType: string]: string }

	// Frontend
	gform: {
    	doAction: (action: string, ...args: any[]) => void
    	addAction: (action: string, ...args: any[]) => void
		applyFilters: <S>(filter: string, subject: S, ...args: any[]) => S
	}
	gpaaInit: () => void
	gpaaReady: true | undefined
	GP_Address_Autocomplete: GP_Address_Autocomplete
	GP_ADDRESS_AUTOCOMPLETE_CONSTANTS: {
		allowed_countries: string[]
		countries: { [abbreviation: string]: string }
	}
}
