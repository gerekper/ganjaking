import {disableSubmitButton, enableSubmitButton} from '../helpers/toggleSubmitButton';
import getFormFieldValues from "../helpers/getFormFieldValues";
import {ILiveMergeTagValues} from "./GPPALiveMergeTags";
import initTinyMCE from '../helpers/initTinyMCE';
import uniq from 'lodash/uniq';
import uniqWith from 'lodash/uniqWith';
import isEqual from 'lodash/isEqual';
import debounce from 'lodash/debounce';

const $ = window.jQuery;

export type formId = number|string;
export type fieldID = number|string;

export interface fieldMapFilter {
	gf_field: string
	operator?: string
	property?: string
}

export interface fieldMap {
	[fieldId: string]: fieldMapFilter[]
}

export interface gravityViewMeta {
	search_fields: any
}

export interface fieldDetails {
	field: fieldID
	filters?: fieldMapFilter[]
	$el?: JQuery
	hasChosen: boolean
}

export default class GPPopulateAnything {

	public currentPage = 1;
	public populatedFields:fieldID[] = [];
	public postedValues:{ [input: string]: string } = {};
	public gravityViewMeta?: gravityViewMeta;
	public eventId = 0;
	private triggerChangeAfterCalculate:boolean = false;
	private triggerChangeExecuted:boolean = false;
	private triggerChangeOnFields: { field_id: number, formula: string, rounding: string }[] = [];

	private _lastRequestPromise : { [inputId: string]: JQueryXHR } = {};

	constructor(public formId: formId, public fieldMap: fieldMap) {

		if ('GPPA_POSTED_VALUES_' + formId in window) {
			this.postedValues = (window as any)['GPPA_POSTED_VALUES_' + formId];
		}

		if ('GPPA_GRAVITYVIEW_META_' + formId in window) {
			this.gravityViewMeta = (window as any)['GPPA_GRAVITYVIEW_META_' + formId];
		}

		jQuery(document).on('gform_post_render', this.postRenderSetCurrentPage);
		jQuery(document).on('gform_post_render', this.postRender);

		// Update prices when fields are updated. By default, Gravity Forms does not trigger recalculations when
		// hidden inputs in product fields are updated.
		jQuery(document).on('gppa_updated_batch_fields', () => window.gformCalculateTotalPrice(formId));

		// Store a boolean in `triggerChangeAfterCalculate` to use once GPPA is initialized
		window.gform.addAction( 'gform_post_calculation_events',
			( mergeTagArr:object, formulaField:{field_id:number, formula:string, rounding:string}, formId:number, calcObj:object ) => {
			this.triggerChangeAfterCalculate = true;
			this.triggerChangeOnFields.push(formulaField);
		});

		/**
		 * gform_post_render doesn't fire in the admin entry detail view so we'll call post render manually.
		 *
		 * Likewise for the GravityView search widget.
		 */
		if ($('#wpwrap #entry_form').length || this.gravityViewMeta) {
			this.postRender(null, formId, 0);
		}
		/**
		 * Disable conditional logic reset for fields populated by GPPA
		 */
		window.gform.addFilter('gform_reset_pre_conditional_logic_field_action', (reset:boolean, formId:number, targetId:string, defaultValues:string|Array<string>, isInit:boolean) => {
			if (isInit) {
				return reset;
			}

			// Trigger forceReload when conditional fields used in LMTs are shown/hidden
			const id = targetId.split('_').pop();
			if (window.gppaLiveMergeTags[this.formId].hasLiveAttrOnPage(id as string)) {
				return false;
			}

			// Cancel GF reset on multi input fields (e.g. address) that have LMTs
			if ( $( targetId ).find('input[data-gppa-live-merge-tag-value]').length ) {
				return false;
			}

			for (const field in this.fieldMap) {
				if ('field_' + formId + '_' + field === targetId.substr(1)) {
					return false;
				}
			}
			return reset;
		});

		// Force reload conditionally shown fields that are used in LMTs
		window.gform.addAction('gform_post_conditional_logic_field_action', (formId:string, action:string, targetId:string, defaultValues:string|Array<string>, isInit:boolean) => {
			if (isInit) {
				return;
			}

			const id = targetId.split('_').pop();

			if (window.gppaLiveMergeTags[this.formId].hasLiveAttrOnPage(id as string)) {
				$(targetId).find('input, select, textarea').trigger('forceReload');
			}
		});
	}

	postRenderSetCurrentPage = (event: JQueryEventObject, formId: any, currentPage: number) => {
		this.currentPage = currentPage;
	};

	postRender = (event: JQueryEventObject|null, formId: any, currentPage: number) => {

		if (formId != this.formId) {
			return;
		}

		/**
		 * Reset LMT values if present to improve compatibility with GP Nested Forms
		 */
		const lmt = window.gppaLiveMergeTags[this.formId];

		if (lmt?.currentMergeTagValues) {
			lmt.populateCurrentMergeTagValues();
		}

		let inputPrefix = 'input_';

		/* Bind to change. */
        // We have to target the form a little strangely as some plugins (i.e. WC GF Product Add-ons) don't use the
        // default form element.
		let $form = this.getFormElement();

		if (this.gravityViewMeta) {
			inputPrefix = 'filter_';
		}

		const lastFieldValuesDataId = 'gppa-batch-ajax-last-field-values';

		$form.off( '.gppa' );
		$form.on('keyup.gppa change.gppa DOMAutoComplete.gppa paste.gppa forceReload.gppa', '[name^="' + inputPrefix + '"]', (event) => {

			const $el = $(event.target);

			const inputId = String( $el.attr('name') ).replace(new RegExp(`^${inputPrefix}`), '');

			if( ! inputId ) {
				return;
			}

			/**
			 * Flag to disable listener to prevent recursion.
			 */
			if ($el.data('gppaDisableListener')) {
				return;
			}

			/**
			 * keyup truly means keyup so we need to suppress the event for certain keys.
			 */
			const ignoredKeyUpKeys = ['Tab', 'Shift', 'Meta', 'Alt', 'Control'];

			if (event.type === 'keyup' && (ignoredKeyUpKeys.indexOf(event.key) !== -1)) {
				console.debug('not firing due to ignored keyup');
				return;
			}

			// Do not fire if values didn't change and `forceReload` is not the source event type
			if (JSON.stringify($form.data(lastFieldValuesDataId)) === JSON.stringify(getFormFieldValues(this.formId, !!this.gravityViewMeta)) &&
				event.type !== 'forceReload' ) {
				console.debug('not firing due to field values matching last request');
				return;
			}

			/**
			 * Override when fields and Live Merge Tag values are refreshed when dependent inputs change.
			 *
			 * A common use of this filter is to require a certain number of characters in an input before triggering
			 * an update.
			 *
			 * @since 1.0-beta-5.20
			 *
			 * @param boolean triggerChange Whether or not to trigger update of fields and Live Merge Tags.
			 * @param string formId The current form ID.
			 * @param string inputId The ID of the input that had a change event triggered.
			 * @param JQuery $el Input element that had change event.
			 * @param JQueryEventObject event Original event on input.
			 *
			 * @returns boolean Whether or not to trigger update of fields and Live Merge Tags
			 */
			if ( ! window.gform.applyFilters('gppa_should_trigger_change', true, this.formId, inputId, $el, event) ) {
				return;
			}

			/**
			 * Ignore change event if the input is a text input (e.g. single line or paragraph) since blurring the
			 * input will fire a redundant event. keyup has us covered here.
			 *
			 * Change still needs to be listened to for non-text inputs such as selects, checkboxes, radios, etc.
			 */
			if (event.type === 'change' && $el.is('input[type=text], input[type=number], input[type=time], textarea')) {
				if( $el.data( 'lastValue' ) == $el.val() ) {
					return;
				}
			}

			$el.data( 'lastValue', $el.val() );

			this.onChange(inputId);
		});

		$form.on('submit.gppa', ({ currentTarget: form }) => {
			$(form).find('[name^="' + inputPrefix + '"]').each( (index, el: Element) => {
				var $el = $(el);
				var id = $el.attr('name').replace(inputPrefix, '');
				var fieldId = parseInt(id);

				if (this.getFieldPage(fieldId) != this.currentPage) {
					return;
				}

				this.postedValues[id] = $el.val();
			});
		});

		this.bindNestedForms();
		this.bindConditionalLogicPricing();

		// Trigger change event on calculated fields only once on initial load
		if ( this.triggerChangeAfterCalculate && !this.triggerChangeExecuted ) {
			for ( let i=0, max=this.triggerChangeOnFields.length; i<max; i++ ) {
				$form.find('[name="' + inputPrefix + this.triggerChangeOnFields[i].field_id + '"]').trigger('change');
			}
			this.triggerChangeExecuted = true;
		}

	};

	/**
	 * We maintain both a instance properties and scoped variables for the following due to bulkBatchedAjax() being
	 * debounced.
	 */
	dependentFieldsToLoad: { field: fieldID, filters?: fieldMapFilter[] }[] = [];
	triggerInputIds: fieldID[] = [];

	onChange = (inputId: string) : void => {
		const $form: JQuery = this.getFormElement();
		const lastFieldValuesDataId = 'gppa-batch-ajax-last-field-values';

		let dependentFieldsToLoad: { field: fieldID, filters?: fieldMapFilter[] }[] = [];
		let triggerInputIds: fieldID[] = [];

		const lmt = window.gppaLiveMergeTags[this.formId];

		const fieldId = parseInt(inputId);

		if (dependentFieldsToLoad.length === 0) {
			dependentFieldsToLoad = this.getDependentFields( inputId );
		}

		lmt.getDependentInputs(fieldId).each((_: number, dependentInputEl: Element) => {
			const $el = $(dependentInputEl);
			const inputName = $el.attr('name');

			if (!inputName) {
				return;
			}

			const fieldId:number = +inputName.replace('input_', '');

			dependentFieldsToLoad.push({field: fieldId});
			dependentFieldsToLoad.push(...this.getDependentFields(fieldId));
		});

		dependentFieldsToLoad = uniqWith([...dependentFieldsToLoad, ...this.dependentFieldsToLoad], isEqual);
		this.dependentFieldsToLoad = [...dependentFieldsToLoad];

		if (dependentFieldsToLoad.length || lmt.hasLiveAttrOnPage(fieldId) || lmt.hasLiveMergeTagOnPage(fieldId)) {
			triggerInputIds.push(fieldId);

			triggerInputIds = uniqWith([...triggerInputIds, ...this.triggerInputIds], isEqual);
			this.triggerInputIds = [...triggerInputIds];

			$form.data(lastFieldValuesDataId, getFormFieldValues(this.formId, !!this.gravityViewMeta));

			this.bulkBatchedAjax(dependentFieldsToLoad, triggerInputIds);
		}
	};

	bulkBatchedAjax = debounce((
		dependentFieldsToLoad: { field: fieldID, filters?: fieldMapFilter[] }[],
		triggerInputIds: fieldID[]
	) : JQueryPromise<JQueryXHR> => {
		const $form: JQuery = this.getFormElement();

		this.dependentFieldsToLoad = [];
		this.triggerInputIds = [];

		return this.batchedAjax($form, dependentFieldsToLoad, triggerInputIds);
	}, 250);

	bindNestedForms() {
		for( const prop in window ) {
			if (typeof prop === 'string' && prop.indexOf(`GPNestedForms_${this.formId}_`) !== 0) {
				continue;
			}

			const nestedFormFieldId = prop.replace(`GPNestedForms_${this.formId}_`, '');

			(window[prop] as any).viewModel.entries.subscribe(() => {
				this.onChange(nestedFormFieldId);
			});
		}
	}

	bindConditionalLogicPricing() {
		window.gform.addAction('gpcp_after_update_pricing', (triggerFieldId: string) => {
			// When GPCP is initalized there is no trigger field.
			if( triggerFieldId ) {
				this.onChange(triggerFieldId);
			}
		});
	}

	getFieldFilterValues($form: JQuery, filters:fieldMapFilter[]) {

		let prefix = 'input_';

		if (this.gravityViewMeta) {
			prefix = 'filter_';
		}

		/* Use entry form if we're in the Gravity Forms admin entry view. */
		if ($('#wpwrap #entry_form').length) {
			$form = $('#entry_form');
		}

		const formInputValues = $form.serializeArray();
		const gfFieldFilters:string[] = [];
		const values:{ [input: string]: string } = {};

		for ( const filter of filters ) {
			gfFieldFilters.push(filter.gf_field);
		}

		for ( const input of formInputValues ) {
			const inputName = input.name.replace(prefix, '');
			const fieldId = Math.abs(parseInt(inputName)).toString();

			if (gfFieldFilters.indexOf(fieldId) === -1) {
				continue;
			}

			values[inputName] = input.value;
		}

		return values;

	}

	/**
	 * This is primarily used for field value objects since it has to traverse up
	 * and figure out what other filters are required.
	 *
	 * Regular filters work without this since all of the filters are present in the single field.
	 **/
	recursiveGetDependentFilters(filters:fieldMapFilter[]) {
		let dependentFilters:fieldMapFilter[] = [];

		for ( const filter of filters ) {
			if ('property' in filter || !('gf_field' in filter)) {
				continue;
			}

			var currentField = filter.gf_field;

			if (!(currentField in this.fieldMap)) {
				continue;
			}

			dependentFilters = dependentFilters
				.concat(this.fieldMap[currentField])
				.concat(this.recursiveGetDependentFilters(this.fieldMap[currentField]));
		}

		return dependentFilters;

	}

	batchedAjax($form: JQuery, requestedFields: { field: fieldID, filters?: fieldMapFilter[] }[], triggerInputId: fieldID | fieldID[]) : JQueryPromise<any> {

		this.eventId++;

		const focusBeforeAJAX = $(':focus').attr('id');
		const fieldIDs:fieldID[] = [];
		const fields:fieldDetails[] = [];

		/* Process field array and populate filters */
		for ( const fieldDetails of requestedFields ) {
			const fieldID = fieldDetails.field;

			if (fieldIDs.includes(fieldID)) {
				continue;
			}

			let $el = $form.find('#field_' + this.formId + '_' + fieldID);
			let hasChosen = !!$form.find('#input_' + this.formId + '_' + fieldID).data('chosen');

			if (this.gravityViewMeta) {
				const $searchBoxFilter = $form.find('#search-box-filter_' + fieldID);
				let $searchBox = $searchBoxFilter.closest('.gv-search-box');

				/* Add data attribute so we can find the element after it's replaced via AJAX. */
				if ($searchBox.length) {
					$searchBox.attr('data-gv-search-box', fieldID);
				}

				if (!$searchBox.length) {
					$searchBox = $('[data-gv-search-box="' + fieldID + '"]');
				}

				$el = $searchBox;
				hasChosen = !!$searchBox.data('chosen');
			}

			fields.push(Object.assign({}, fieldDetails, {
				$el,
				hasChosen,
			}));

			fieldIDs.push(fieldID);
		}

		fields.sort((a, b) => {
			const idAttrPrefix = this.gravityViewMeta ? '[id^=search-box-filter]' : '[id^=field]';

			const aIndex = a.$el!.index(idAttrPrefix);
			const bIndex = b.$el!.index(idAttrPrefix);

			return aIndex - bIndex;
		});

		$.each(fields, function (index, fieldDetails) {

			var fieldID = fieldDetails.field;
			var $el = fieldDetails.$el!;
			var $fieldContainer = $el.children('.clear-multi, .gform_hidden, .ginput_container, p').first();

			/* Prevent multiple choices hidden inputs */
			$el
				.closest('form')
				.find('input[type="hidden"][name="choices_' + fieldID + '"]')
				.remove();

			var isEmpty  = $fieldContainer.find( '.gppa-requires-interaction' ).length > 0,
                addClass = isEmpty ? 'gppa-empty' : '';

			addClass += ' gppa-loading';

			/**
			 * Specify which element is used to indicate that a field is about to be replaced with
			 * fresh data and which element will be replaced when that data is fetched.
			 *
			 * @param array targetMeta
			 *
			 *      @var {jQuery} $fieldContainer    The element that should show the loading indicator and be replaced.
			 *      @var string   loadingClass       The class that will be applied to the target element.
			 *
			 * @param {jQuery} $el      The field element. By default, the the field container will start pulsing.
			 * @param string   context  The context of the target meta. Will be 'loading' or 'replace'.
			 */
			[ $fieldContainer, addClass ] = window.gform.applyFilters( 'gppa_loading_field_target_meta', [ $fieldContainer, addClass ], $el, 'loading' );

			$fieldContainer.addClass( addClass );

		});

		if (Array.isArray(triggerInputId)) {
			for ( const inputId of triggerInputId ) {
				window.gppaLiveMergeTags[this.formId].showLoadingIndicators( inputId );
			}
		} else {
			window.gppaLiveMergeTags[this.formId].showLoadingIndicators( triggerInputId );
		}

		const data = window.gform.applyFilters('gppa_batch_field_html_ajax_data', {
			'form-id': this.formId,
			'lead-id': window.gform.applyFilters('gppa_batch_field_html_entry_id', null, this.formId),
			'field-ids': fields.map((field) => {
				return field.field;
			}),
			'gravityview-meta': this.gravityViewMeta,
			'field-values': getFormFieldValues(this.formId, !!this.gravityViewMeta),
			'merge-tags': window.gppaLiveMergeTags[this.formId].getRegisteredMergeTags(),
			/**
			 * JSON is used here due to issues with modifiers causing merge tags to be truncated in $_REQUEST and $_POST
			 */
			'lmt-nonces': window.gppaLiveMergeTags[this.formId].whitelist,
			'current-merge-tag-values': window.gppaLiveMergeTags[this.formId].currentMergeTagValues,
			'security': window.GPPA.NONCE,
			'event-id': this.eventId,
		}, this.formId);

		disableSubmitButton(this.getFormElement());

		return $.ajax({
			url: window.GPPA.AJAXURL + '?action=gppa_get_batch_field_html',
			contentType: 'application/json',
			dataType: 'json',
			data: JSON.stringify(data),
			type: 'POST',
		}).done((response: { merge_tag_values: ILiveMergeTagValues, fields: any, event_id: any }) => {

			// Skip out of order responses unless payload contains new markup
			if (this.eventId > response.event_id && response.fields.length < 1) {
				return;
			}

			if (Object.keys(response.fields).length) {
				const updatedFieldIDs = [];  // Stores updated field IDs for `gppa_updated_batch_fields`
				for ( const fieldDetails of fields ) {
					const fieldID = fieldDetails.field;
					const $field = fieldDetails.$el!;
					const containerSelector = '.clear-multi, .gform_hidden, .ginput_container, p, .ginput_complex';
					let $fieldContainer = $field.children(containerSelector).first();
					// If container is not a direct descendent, attempt to use find() as a last resort
					$fieldContainer = ($fieldContainer.length) ? $fieldContainer : $field.find(containerSelector).first();

					/**
					 * Documented above
					 *
					 * We don't include removeClass or addClass here since $fieldContainer gets entirely replaced.
					 */
					[ $fieldContainer ] = window.gform.applyFilters( 'gppa_loading_field_target_meta', [ $fieldContainer ], $field, 'replace' );

					// Gravity Flow Vacation Plugin uses its own container around the field input.
					// This causes overwriting it to duplicate the "current balance" DOM. Detect this class and use it instead.
					var $gravityflowVacationContainer = $fieldContainer.parents( '.gravityflow-vacation-request-container' );
					if ( $gravityflowVacationContainer.length ) {
						$fieldContainer = $gravityflowVacationContainer;
					}
					if (!this.gravityViewMeta) {
						$fieldContainer = $(response.fields[fieldID]).replaceAll($fieldContainer);
					} else {
						const $results = $(response.fields[fieldID]);

						$fieldContainer = $results.find('p').replaceAll($fieldContainer);
					}

					this.populatedFields.push(fieldID);

					if( fieldDetails.hasChosen ) {
						window.gformInitChosenFields( ('#input_{0}_{1}' as any).format( this.formId, fieldID ), window.GPPA.I18N.chosen_no_results );
					}

					if ( $fieldContainer.find('.wp-editor-area').length ) {
						initTinyMCE();
					}

					if ( $fieldContainer.find('.datepicker').length && window.gformInitDatepicker ) {
						window.gformInitDatepicker();
					}

					$fieldContainer.find(':input').each((index, el) => {
						$(el).data('gppaDisableListener', true);
						window.gform.doAction('gform_input_change', el, this.formId, fieldID);
						$(el).trigger('change');
						$(el).removeData('gppaDisableListener');
					});

					/**
					 * Support JetSloth's Image Choices plugin
					 * https://jetsloth.com/support/gravity-forms-image-choices/
					 */
					if ($field.hasClass('image-choices-field')) {
						if (typeof (window as any).imageChoices_SetUpFields === 'function') {
							(window as any).imageChoices_SetUpFields();
						}
					}

					// Initialize any read only (GPRO) Datepicker fields for GPLD
					if ( window.GPLimitDates ) {
						$field.find('.gpro-disabled-datepicker').each((index, elem) => {
							const $elem = $( elem );
							window.GPLimitDates.initDisabledDatepicker( $elem );
							$elem.trigger( 'change' );
						});
					}
					updatedFieldIDs.push(fieldID);
				}

				this.runAndBindCalculationEvents();
				if( typeof ($.fn as any).ionRangeSlider !== 'undefined' ) {
					($( '.js-range-slider' ) as any).ionRangeSlider();
				}

				$(document).trigger('gppa_updated_batch_fields', [this.formId, updatedFieldIDs]);
			}

			window.gppaLiveMergeTags[this.formId].replaceMergeTagValues(response.merge_tag_values);

			enableSubmitButton(this.getFormElement());

			/**
			 * Refocus input if current input was updated with AJAX
			 */
			const $focus = $('#' + focusBeforeAJAX);

			if ($focus.length && !$(':focus').length) {
				const focusVal = $focus.val();

				/* Simply using .focus() will set the cursor at the beginning instead of the end. */
				$focus.val('');
				$focus.val(focusVal);
				$focus.focus();
			}

		});

	}

	/**
	 * Gravity Forms does not have a built-in function to remove calculation events.
	 *
	 * This method was created to unbind any calculation events on inputs as GPPA re-binds calculation events after
	 * fields are reloaded using batch AJAX.
	 *
	 * @param formulaField
	 */
	removeCalculationEvents(formulaField: any) {
		const { formId } = this;
		const matches = window.GFMergeTag.parseMergeTags( formulaField.formula );

		for(var i in matches) {
			if (!matches.hasOwnProperty(i))
				continue;

			const inputId = matches[i][1];
			const fieldId = parseInt(inputId, 10);
			const input = jQuery('#field_' + formId + '_' + fieldId)
				.find('input[name="input_' + inputId + '"], select[name="input_' + inputId + '"]');

			input.each(function() {
				// @ts-ignore
				const $this: JQuery = $(this);
				// @ts-ignore - _data is not in JQueryStatic typings but it's been around for as long as I can remember.
				const events: { [event: string]: any } = jQuery._data(this, 'events');

				if (!events) {
					return;
				}

				for ( const [event, eventHandlers] of Object.entries(events) ) {
					for ( const eventHandler of eventHandlers ) {
						const handlerStr = eventHandler.handler.toString();

						if ( handlerStr.indexOf('.bindCalcEvent(') === -1 ) {
							continue;
						}

						$this.unbind(event, eventHandler.handler);
					}
				}
			});
		}
	}

	/**
	 * Run the calculation events for any field that is dependent on a GPPA-populated field that has been updated.
	 */
	runAndBindCalculationEvents() {

		if (!window.gf_global || !window.gf_global.gfcalc || !window.gf_global.gfcalc[this.formId]) {
			return;
		}

		var GFCalc = window.gf_global.gfcalc[this.formId];

		// Remove all calculation events prior to binding to prevent unbinding in the loop after a binding has been done.
		for (var i = 0; i < GFCalc.formulaFields.length; i++) {
			this.removeCalculationEvents(GFCalc.formulaFields[i]);
		}

		for (var j = 0; j < GFCalc.formulaFields.length; j++) {
			var formulaField = $.extend({}, GFCalc.formulaFields[j]);

			GFCalc.bindCalcEvents( formulaField, this.formId );
			GFCalc.runCalc(formulaField, this.formId);
		}

	}

	getFieldPage(fieldId:fieldID) {

		var $field = $('#field_' + this.formId + '_' + fieldId);
		var $page = $field.closest('.gform_page');

		if (!$page.length) {
			return 1;
		}

		return $page.prop('id').replace('gform_page_' + this.formId + '_', '');

	}

	/**
	 * Get fields that are filtered by (or dependent on) the field/input that just changed.
	 *
	 * @param fieldId
	 */
	getDependentFields(fieldId:fieldID) : {field: fieldID, filters: fieldMapFilter[]}[] {

		const dependentFields = [];

		let currentFieldDependents;

		// We want to check for rules for top-level fields and specific inputs (i.e. 1.2 and 1).
		let currentFields = [ fieldId.toString(), parseInt( fieldId.toString() ).toString() ];

		while (currentFields) {

			currentFieldDependents = [];

			for ( const [field, filters] of Object.entries(this.fieldMap) ) {
				filter:
				for ( const filter of Object.values(filters) ) {
					if ('gf_field' in filter && currentFields.includes(filter.gf_field.toString())) {
						/**
						 * Check if field already processed to prevent recursion.
						 */
						for ( const dependent of dependentFields ) {
							if (dependent.field === field) {
								continue filter;
							}
						}

						currentFieldDependents.push(field);
						dependentFields.push({field: field, filters: filters});
					}
				}
			}

			if (!currentFieldDependents.length) {
				break;
			}

			currentFields = uniq(currentFieldDependents);

		}

		return dependentFields;

	}

	fieldHasPostedValue(fieldId:fieldID) {

		var hasPostedField = false;

		for ( const inputId of Object.keys(this.postedValues) ) {
			const currentFieldId = parseInt(inputId);

			if (currentFieldId == fieldId) {
				hasPostedField = true;

				break;
			}
		}

		return hasPostedField;

	}

	getFormElement() {

		let $form = $( 'input[name="is_submit_' + this.formId + '"]' ).parents( 'form' );

		if ( this.gravityViewMeta ) {
			$form = $( '.gv-widget-search' );
		}

		/* Use entry form if we're in the Gravity Forms admin entry view. */
		if ( $( '#wpwrap #entry_form' ).length ) {
			$form = $( '#entry_form' );
		}

		return $form;
	}

}
