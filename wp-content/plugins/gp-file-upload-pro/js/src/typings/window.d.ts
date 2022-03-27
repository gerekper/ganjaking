import * as Plupload from 'plupload';

declare global {
	interface Window {
		jQuery: JQueryStatic
		fieldSettings: string[]
		GPFUP: {
			NONCE: string
			AJAXURL: string
			STRINGS: {
				[string: string]: string
			}
		}
		gfMultiFileUploader: {
			uploaders: { [name: string]: Plupload.Uploader }
			setup: Function
			toggleDisabled: Function
		}
		gformDeleteUploadedFile: (formId: string, fieldId: string, deleteButton: HTMLElement) => void
		mOxie: any
		gform_gravityforms: {
			strings: { [string: string]: string }
		}
		gform: any
		has_entry: (fieldID: string | number) => boolean
		SetFieldProperty: (setting: string, value: any) => void
		ToggleMultiFile: (isInit?: boolean) => void
	}

	interface GPFUPFieldInitSettings {
		formId: string
		fieldId: string
		enableCrop: boolean
		enableSorting: boolean
		cropRequired: boolean
		aspectRatio: number
		maxWidth: number
		maxHeight: number
		minWidth: number
		minHeight: number
		exactWidth: number
		exactHeight: number
	}
}
