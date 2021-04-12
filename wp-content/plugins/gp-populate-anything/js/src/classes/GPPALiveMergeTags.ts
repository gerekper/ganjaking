import {enableSubmitButton} from '../helpers/toggleSubmitButton';
import loUniq from 'lodash/uniq';
import {fieldID, formId} from "./GPPopulateAnything";

const $ = window.jQuery;

export interface ILiveMergeTagValues {
	[mergeTag: string]: string
}

export default class GPPALiveMergeTags {

	public formId:formId;
	public whitelist:{ [lmt: string]: string } = {};
	public liveAttrsOnPage:string[] = [];
	public currentMergeTagValues:ILiveMergeTagValues = {};

	constructor (formId:formId) {
		this.formId = formId;

		this.addHooks();
		this.getLiveAttrs();
		this.populateCurrentMergeTagValues();
		this.getWhitelist();
		this.getRegisteredEls();
	}

	addHooks() : void {
		/**
		 * Disable conditional logic reset for fields with LMTs
		 */
		window.gform.addFilter('gform_reset_pre_conditional_logic_field_action', (
			reset: boolean,
			formId: number,
			targetId: string,
			defaultValues: string | string[],
			isInit: boolean
		) => {
			if (isInit) return reset;

			// Loop through current merge tag values and compare them to a field's default value(s).
			for (let mergeTag in this.currentMergeTagValues) {
				if (typeof defaultValues === 'object') {
					for (const defaultValue of defaultValues) {
						if (mergeTag === defaultValue) {
							return false;
						}
					}
				} else if (mergeTag === defaultValues) {
					return false;
				}
			}
			return reset;
		});
	}

	getLiveAttrs () {
		const prefix = 'GPPA_LIVE_ATTRS_FORM_';

		if (prefix + this.formId in window) {
			this.liveAttrsOnPage = (window as any)[prefix + this.formId];
		}

		/**
		 * Include data-gppa-live-merge-tag since it is not included with the GPPA_LIVE_ATTRS_FORM_ var normally.
		 */
		this.liveAttrsOnPage.push('data-gppa-live-merge-tag');
	}

	getNestedFormsInstance() : any {
		for( const prop in window ) {
			if (!prop.match(new RegExp(`GPNestedForms_\\d+_${this.formId}$`))) {
				continue;
			}

			return window[prop];
		}

		return undefined;
	}

	populateCurrentMergeTagValues () {
		const gpnf = this.getNestedFormsInstance();
		const prefix = 'GPPA_CURRENT_LIVE_MERGE_TAG_VALUES_FORM_';

		/**
		 * Treat GP Nested Forms specially here due to the fact that we can't fetch
		 * GPPA_CURRENT_LIVE_MERGE_TAG_VALUES_FORM_ for nested forms easily.
		 */
		if (gpnf && gpnf.getCurrentEntryId()) {
			this.currentMergeTagValues = {};

			return;
		}

		if (prefix + this.formId in window) {
			this.currentMergeTagValues = (window as any)[prefix + this.formId];
		}
	}

	/**
	 * Copy in whitelist from window var into this instance.
	 */
	getWhitelist () {
		const prefix = 'GPPA_LMT_WHITELIST_';

		if (prefix + this.formId in window) {
			this.whitelist = (window as any)[prefix + this.formId];
		}
	}

	getRegisteredEls () : JQuery {
		const attributes = this.liveAttrsOnPage.map((attr) => {
			return '[' + attr + ']';
		});

		return $('#gform_wrapper_' + this.formId).find(attributes.join(','));
	}

	getRegisteredMergeTags () {
		const mergeTags:string[] = [];

		this.getRegisteredEls().each ((_, el: Element) => {
			const $el = $(el);

			for ( const dataAttr of this.liveAttrsOnPage ) {
				const mergeTag = $el.attr(dataAttr);

				if (mergeTag) {
					mergeTags.push(mergeTag);
				}
			}
		});

		return loUniq(mergeTags);
	}

	getInputsWithLMTs () : JQuery {

		let inputs : JQuery = jQuery();

		for ( const liveAttr of this.liveAttrsOnPage ) {
			const attr = liveAttr.replace(/^data-gppa-live-merge-tag-/, '');

			if (!['innerHtml', 'value'].includes(attr)) {
				continue;
			}

			if (attr === 'innerHtml') {
				inputs = inputs
					.add(this.getRegisteredEls().filter('[' + liveAttr + ']')
						.filter('textarea'));

				continue;
			}

			inputs = inputs
				.add(this.getRegisteredEls().filter('[' + liveAttr + ']')
					.filter('input, select'));
		}

		return inputs;

	}

	/**
	 * Check if merge tag references a specific field ID
	 *
	 * @param value string Merge tag to check
	 * @param fieldId number Field ID to look for
	 */
	checkMergeTagForFieldId(value: string, fieldId : fieldID): RegExpMatchArray | null {
		return value.match(new RegExp(`:${fieldId}(\\.\\d+)?[}:]`, 'g')) ||
			value.match(new RegExp(`:id=${fieldId}(\\.\\d+)?[}:]`, 'g')) || // @{score:id=xx}
			value.match(/{all_fields(:.*)?}/g) ||
			value.match(/{order_summary(:.*)?}/g);
	}

	getDependentInputs (fieldId: number) : JQuery {

		let dependentInputs : JQuery = jQuery();

		this.getInputsWithLMTs().each((_: number, el: Element) => {
			const $el = $(el);

			for ( const liveAttr of this.liveAttrsOnPage ) {
				const liveAttrValue = $el.attr(liveAttr);
				if (liveAttrValue && this.checkMergeTagForFieldId(liveAttrValue, fieldId)) {
					dependentInputs = dependentInputs.add(el);
				}
			}
		});

		return dependentInputs;

	}

	/**
	 * Check if a particular input is referenced in any Live attrs on the current form/page.
	 */
	hasLiveAttrOnPage (fieldId: fieldID) : boolean {

		for (const liveAttr of this.liveAttrsOnPage) {
			const $els = $(`[${liveAttr}]`);

			for (const el of $els.toArray()) {
				const $el = $(el);
				const liveAttrValue = $el.attr(liveAttr);

				if (liveAttrValue && this.checkMergeTagForFieldId(liveAttrValue, fieldId)) {
					return true;
				}
			}
		}

		return false;

	}

	/**
	 * Check if a particular input is referenced in any LMTs on the current form/page.
	 */
	hasLiveMergeTagOnPage (fieldId: fieldID) : boolean {

		const lmts = $('[data-gppa-live-merge-tag]');

		for (const lmt of lmts.toArray()) {
			const $lmt = $(lmt);
			const lmtValue = $lmt.attr('data-gppa-live-merge-tag');

			if (lmtValue && this.checkMergeTagForFieldId(lmtValue, fieldId)) {
				return true;
			}
		}

		return false;

	}

	showLoadingIndicators ( triggerInputId: fieldID ) {
		this.getRegisteredEls().each(function (this: Element) {

			let $elem = $( this );
			let mergeTag = $elem.data( 'gppa-live-merge-tag' );
			if ( ! mergeTag ) {
				return;
			}

			let targetFieldId = mergeTag.match(/:(\d+(\.?\d+)?)(:.+)?}/)?.[1];

			if ( ! targetFieldId ) {
				return;
			}

			if ( targetFieldId != triggerInputId ) {
				return;
			}

			let $target      = $elem.parents( 'label, .gfield_html, .ginput_container, .gfield_description' ).first();
			let loadingClass = 'gppa-loading';

			/**
			 * Specify which element is used to indicate that a live merge tag is about to be replaced with
			 * fresh data and which element will be replaced when that data is fetched.
			 *
			 * @param array targetMeta
			 *
			 *      @var {jQuery} $target      The element that should show the loading indicator and be replaced.
			 *      @var string   loadingClass The class that will be applied to the target element.
			 *
			 * @param {jQuery} $element The live merge tag element. By default, the live merge tag's parent element will get the loading indicator.
			 * @param string   context  The context of the target meta. Will be 'loading' or 'replace'.
			 */
			[ $target, loadingClass ] = window.gform.applyFilters( 'gppa_loading_target_meta', [ $target, loadingClass ], $( this ), 'loading' );

			$target.addClass( loadingClass );

		} );
	}

	replaceMergeTagValues = (mergeTagValues: ILiveMergeTagValues) => {
		this.getRegisteredEls().each( (_, el: Element) => {
			const $el = $(el);

			if ($el.data('gppa-live-merge-tag')) {
				this.handleElementLiveContent($el, mergeTagValues);
			} else {
				this.handleElementLiveAttr($el, mergeTagValues);
			}
		});

		this.currentMergeTagValues = mergeTagValues;

		enableSubmitButton(this.getFormElement());

		$(document).trigger('gppa_merge_tag_values_replaced', [this.formId]);

		return $.when();
	};

	handleElementLiveContent ($el: JQuery, mergeTagValues: any) {
		const elementMergeTag = $el.data('gppa-live-merge-tag');

		if (!(elementMergeTag in mergeTagValues)) {
			return;
		}

		var value       = mergeTagValues[ elementMergeTag ],
			removeClass = 'gppa-loading gppa-empty',
			$target     = $el.parents( 'label, .gfield_html, .ginput_container, .gfield_description' ).first();

		/** This filter is documented above. */
		[ $target, removeClass ] = window.gform.applyFilters( 'gppa_loading_target_meta', [ $target, removeClass ], $el, 'replace' );

		// Replace markup.
		$el.html(mergeTagValues[elementMergeTag]);
		// Also use `val()` for textarea. This solves an issue with conditionally shown/hidden textareas.
		if ($el.is('textarea')) {
			$el.val(mergeTagValues[elementMergeTag]);
		}

		var isMergeTagSpecific = $target == $el,
			isEmpty            = isMergeTagSpecific ? ! value && value !== 0 : ! $target.text(),
			addClass           = isEmpty ? 'gppa-empty' : '';

		$target.removeClass( removeClass ).addClass( addClass );

	}

	handleElementLiveAttr($el: JQuery, mergeTagValues: ILiveMergeTagValues) {
		for (const liveAttr of this.liveAttrsOnPage) {

			const elementMergeTag = $el.attr(liveAttr);
			const attr = liveAttr.replace(/^data-gppa-live-merge-tag-/, '');
			let attrVal;

			/**
			 * Special innerHtml attribute should be handled differently. innerHtml is a fake attribute utilized to replace
			 * live merge tags in <option>'s and <textarea>'s.
			 **/
			switch (attr) {
				case 'innerHtml':
					if ($el.is(':input')) {
						attrVal = $el.val();
					} else {
						attrVal = $el.html();
						// Update Chosen.js if field is using EnhancedUI
						if ($el.parents('.ginput_container').find('.chosen-container').length) {
							const inputID = $el.parent().attr('id');
							$(('#{0}' as any).format(inputID)).trigger("chosen:updated");
						}
					}
					break;
				case 'value':
					attrVal = $el.val();
					break;
				default:
					attrVal = $el.attr(attr);

					break;
			}

			var value       = mergeTagValues[ elementMergeTag ],
				removeClass = 'gppa-loading',
				$target     = $el.parents( 'label, .gfield_html, .ginput_container, .gfield_description' ).eq( 0 );

			/** This filter is documented above. */
			[ $target, removeClass ] = window.gform.applyFilters( 'gppa_loading_target_meta', [ $target, removeClass ], $el, 'replace' );

			$target.removeClass( removeClass );

			if (!(elementMergeTag in mergeTagValues)) {
				continue;
			}

			/**
			 * Handle decoupling
			 *
			 * Note, if the value differs but the current value is the same as the elementMergeTag, remain coupled. This
			 * can happen when using conditional logic with sections.
			 */
			if (
				elementMergeTag in this.currentMergeTagValues
				&& attrVal != this.currentMergeTagValues[elementMergeTag]
				&& attrVal != elementMergeTag
			) {
				continue;
			}

			switch (attr) {
				case 'innerHtml':
					if ($el.is(':input')) {
						$el.val( value );
					} else {
						$el.html( value );
					}

					break;
				case 'value':
					attrVal = $el.val(value);
					break;
				default:
					$el.attr(attr,  value);
					break;
			}

		}
	}

	getFormElement() {
		return $( 'input[name="is_submit_' + this.formId + '"]' ).parents( 'form' );
	}
}
