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

	private _onChangeHandlers: { [inputId: string]: () => void } = {};

	constructor(public formId: formId, public fieldMap: fieldMap) {

		if ('GPPA_POSTED_VALUES_' + formId in window) {
			this.postedValues = (window as any)['GPPA_POSTED_VALUES_' + formId];
		}

		if ('GPPA_GRAVITYVIEW_META_' + formId in window) {
			this.gravityViewMeta = (window as any)['GPPA_GRAVITYVIEW_META_' + formId];
		}

		jQuery(document).on('gform_post_render', this.postRenderSetCurrentPage);
		jQuery(document).on('gform_post_render', this.postRender);

		/**
		 * gform_post_render doesn't fire in the admin entry detail view so we'll call post render manually.
		 *
		 * Likewise for the GravityView search widget.
		 */
		if ($('#wpwrap #entry_form').length || this.gravityViewMeta) {
			this.postRender(null, formId, 0);
		}

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
		this.clearHandlers();

		$form.on('keyup.gppa change.gppa DOMAutoComplete.gppa', '[name^="' + inputPrefix + '"]', (event) => {

			const $el = $(event.target);

			/**
			 * Ignore change event if the input is a text input (e.g. single line or paragraph) since blurring the
			 * input will fire a redundant event. keyup has us covered here.
			 *
			 * Change still needs to be listened to for non-text inputs such as selects, checkboxes, radios, etc.
			 */
			if (event.type === 'change' && $el.is(':text')) {
				if( $el.data( 'lastValue' ) == $el.val() ) {
					return;
				}
			}
			$el.data( 'lastValue', $el.val() );

			const inputId = String( $el.attr('name') ).replace(new RegExp(`^${inputPrefix}`), '');
			if( ! inputId ) {
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

			if (JSON.stringify($form.data(lastFieldValuesDataId)) === JSON.stringify(getFormFieldValues(this.formId, !!this.gravityViewMeta))) {
				console.debug('not firing due to field values matching last request');
				return;
			}

			this.onChangeFactory(inputId)();
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

	};

	/**
	 * Generate a debounced onChange handler for each input that way the debounce isn't shared between inputs.
	 *
	 * @param inputId
	 */
	onChangeFactory = (inputId: string) : () => void => {
		if (this._onChangeHandlers[inputId]) {
			return this._onChangeHandlers[inputId];
		}

		const $form = this.getFormElement();
		const lastFieldValuesDataId = 'gppa-batch-ajax-last-field-values';

		this._onChangeHandlers[inputId] = debounce(() => {
			const lmt = window.gppaLiveMergeTags[this.formId];

			$form.data(lastFieldValuesDataId, getFormFieldValues(this.formId, !!this.gravityViewMeta));

			const fieldId = parseInt(inputId);

			let dependentFields: { field: fieldID, filters?: fieldMapFilter[] }[] = this.getDependentFields( inputId );

			lmt.getDependentInputs(fieldId).each((_: number, dependentInputEl: Element) => {
				const $el = $(dependentInputEl);
				const inputName = $el.attr('name');

				if (!inputName) {
					return;
				}

				const fieldId:number = +inputName.replace('input_', '');

				dependentFields.push({field: fieldId});
				dependentFields.push(...this.getDependentFields(fieldId));
			});

			dependentFields = uniqWith(dependentFields, isEqual);

			if (dependentFields.length || lmt.hasLiveAttrOnPage(fieldId) || lmt.hasLiveMergeTagOnPage(fieldId)) {
				this.batchedAjax($form, dependentFields, inputId );
			}
		}, 250);

		return this._onChangeHandlers[inputId];
	};

	clearHandlers = () => {
		this._onChangeHandlers = {};
	};

	bindNestedForms() {
		for( const prop in window ) {
			if (typeof prop === 'string' && prop.indexOf(`GPNestedForms_${this.formId}_`) !== 0) {
				continue;
			}

			const nestedFormFieldId = prop.replace(`GPNestedForms_${this.formId}_`, '');

			(window[prop] as any).viewModel.entries.subscribe(() => {
				this.onChangeFactory(nestedFormFieldId)();
			});
		}
	}

	bindConditionalLogicPricing() {
		window.gform.addAction('gpcp_after_update_pricing', (triggerFieldId: string) => {
			// When GPCP is initalized there is no trigger field.
			if( triggerFieldId ) {
				this.onChangeFactory(triggerFieldId)();
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

	batchedAjax($form: JQuery, requestedFields: { field: fieldID, filters?: fieldMapFilter[] }[], triggerInputId: fieldID ) : void {

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

		window.gppaLiveMergeTags[this.formId].showLoadingIndicators( triggerInputId );

		const data = window.gform.applyFilters('gppa_batch_field_html_ajax_data', {
			'action': 'gppa_get_batch_field_html',
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
			'lmt-nonces': JSON.stringify(window.gppaLiveMergeTags[this.formId].whitelist),
			'current-merge-tag-values': window.gppaLiveMergeTags[this.formId].currentMergeTagValues,
			'security': window.GPPA_NONCE,
			'event-id': this.eventId,
		}, this.formId);

		disableSubmitButton(this.getFormElement());

		$.post(window.GPPA_AJAXURL, data, (response: { merge_tag_values: ILiveMergeTagValues, fields: any, event_id: any }) => {

			// Skip out of order responses unless payload contains new markup
			if( this.eventId > response.event_id && response.fields.length < 1 ) {
				return;
			}

			if (Object.keys(response.fields).length) {
				for ( const fieldDetails of fields ) {
					var fieldID = fieldDetails.field;
					var $field = fieldDetails.$el!;
					var $fieldContainer = $field.children('.clear-multi, .gform_hidden, .ginput_container, p').first();

					/**
					 * Documented above
					 *
					 * We don't include removeClass or addClass here since $fieldContainer gets entirely replaced.
					 */
					[ $fieldContainer ] = window.gform.applyFilters( 'gppa_loading_field_target_meta', [ $fieldContainer ], $field, 'replace' );

					if (!this.gravityViewMeta) {
						$fieldContainer.replaceWith(response.fields[fieldID]);
					} else {
						var $results = $(response.fields[fieldID]);

						$fieldContainer.replaceWith($results.find('p'));
					}

					this.populatedFields.push(fieldID);

					if( fieldDetails.hasChosen ) {
						window.gformInitChosenFields( ('#input_{0}_{1}' as any).format( this.formId, fieldID ), window.GPPA_I18N.chosen_no_results );
					}

					if ( $fieldContainer.find('.wp-editor-area').length ) {
						initTinyMCE();
					}

					if ( $fieldContainer.find('.datepicker').length && window.gformInitDatepicker ) {
						window.gformInitDatepicker();
					}

					$fieldContainer.find(':input').each((index, $el) => {
						window.gform.doAction('gform_input_change', $el, this.formId, fieldID);
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
				}

				this.runAndBindCalculationEvents();
				if( typeof ($.fn as any).ionRangeSlider !== 'undefined' ) {
					($( '.js-range-slider' ) as any).ionRangeSlider();
				}

				$(document).trigger('gppa_updated_batch_fields', this.formId);
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

		}, 'json');

	}

	/**
	 * Run the calculation events for any field that is dependent on a GPPA-populated field that has been updated.
	 */
	runAndBindCalculationEvents() {

		if (!window.gf_global || !window.gf_global.gfcalc || !window.gf_global.gfcalc[this.formId]) {
			return;
		}

		var GFCalc = window.gf_global.gfcalc[this.formId];

		for (var i = 0; i < GFCalc.formulaFields.length; i++) {
			var formulaField = $.extend({}, GFCalc.formulaFields[i]);
			// @todo: Will previously bound events stack and create a performance issue?
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
