type JQueryInput = JQuery<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>;

const $ = window.jQuery;

export default class GPIProperties {
	public ajaxUrl: string;
	public formId: number;
	public targetFieldId: number;
	public triggerFieldIds: number[];
	public ajaxRefreshNonce: string;

	public $form: JQuery<HTMLFormElement>;
	public $targetField: JQueryInput;

	constructor(
		{ ajaxUrl, formId, targetFieldId, triggerFieldIds, ajaxRefreshNonce }
			: Pick<GPIProperties, 'ajaxUrl' | 'formId' | 'targetFieldId' | 'triggerFieldIds' | 'ajaxRefreshNonce'>
	) {
		this.ajaxUrl = ajaxUrl;
		this.formId = formId;
		this.targetFieldId = targetFieldId;
		this.triggerFieldIds = triggerFieldIds;
		this.ajaxRefreshNonce = ajaxRefreshNonce;

		this.$form = $(`#gform_wrapper_${this.formId}`);
		this.$targetField = $(`#field_${this.formId}_${this.targetFieldId}`);

		window.gform.addAction('gform_input_change', (elem: JQueryInput, formId: string, fieldId: string) => {
			if ($.inArray(parseInt(fieldId), this.triggerFieldIds) !== -1) {
				this.refresh({
					targetFieldId: this.targetFieldId,
					triggerFieldId: parseInt(fieldId),
					$targetField: this.$targetField,
					$triggerField: elem,
				});
			}
		});
	}

	refresh(args: { targetFieldId: number, triggerFieldId?: number, $targetField: JQueryInput, $triggerField?: JQueryInput, initialLoad?: boolean}) {
		if (args.initialLoad && this.$targetField.is('.gfield_error')) {
			return;
		}

		const formData: { [input: string]: any } = {};

		this.$form.find('input, select, textarea').each(function () {
			const name =$(this).attr('name');

			if (!name) {
				return;
			}

			if ((this as HTMLInputElement).type === 'radio' || (this as HTMLInputElement).type === 'checkbox') {
				if ((this as HTMLInputElement).checked) {
					formData[name] = $(this).val();
				}
			} else {
				formData[name] = $(this).val();
			}
		});

		const data:{
			action: string
			security: string
			form_id: number
			target_field_id: number
			trigger_field_id?: number
			[input: string]: any,
		} = {
			...formData,
			action: 'gpi_refresh_field',
			security: this.ajaxRefreshNonce,
			form_id: this.formId,
			target_field_id: args.targetFieldId,
			trigger_field_id: args.triggerFieldId,
			gpi_initial_property_refresh: args.initialLoad,
		};

		// Prevent AJAX-enabled forms from intercepting our AJAX request.
		delete data['gform_ajax'];

		this.$targetField.addClass('gpi-refreshing-field');

		$.post(this.ajaxUrl, data).done((response) => {
			this.$targetField.removeClass('gpi-refreshing-field');

			if (response.success) {
				this.$targetField.html(response.data);
			}

			/**
			 * Action fired after a field with inventory dependent on a property is refreshed.
			 *
			 * @since 1.0-beta-1.3
			 *
			 * @param {JQuery}	$targetField 	Field with inventory that was refreshed.
			 * @param {JQuery}	$triggerField 	Property field that caused the field with inventory to be refreshed.
			 * @param boolean	initialLoad		Whether the field was refreshed on the initial load of the form.
			 */
			window.gform.doAction('gpi_field_refreshed', this.$targetField, args.$triggerField, args.initialLoad);
		});
	}
}
