/// <reference types="tom-select" />
import getFormFieldValues from './helpers/getFormFieldValues';
import TomSelect from 'tom-select';
import { createPopper } from '@popperjs/core';

// eslint-disable-next-line import/no-unresolved
import type {
	RecursivePartial,
	TomLoadCallback,
	TomSettings,
} from 'tom-select/dist/types/types';

const $ = window.jQuery;

interface GPAdvancedSelectInitArgs {
	formId: formID;
	fieldId: fieldID;
	lazyLoad: boolean;
	usingSearchValue: boolean;
	hasImageChoices: boolean;
	fieldType: 'select' | 'multiselect' | 'address';
	minSearchLength: number;
	ignoreEmptySearchValue: boolean;
	placeholder: string;
}

interface GPAdvancedSelect extends GPAdvancedSelectInitArgs {}

class GPAdvancedSelect implements GPAdvancedSelectInitArgs {
	public ajaxCache: { [cacheKey: string]: JQuery.jqXHR } = {};

	constructor(initArgs: GPAdvancedSelectInitArgs) {
		this.formId = initArgs.formId;
		this.fieldId = initArgs.fieldId;
		this.lazyLoad = initArgs.lazyLoad;
		this.usingSearchValue = initArgs.usingSearchValue;
		this.hasImageChoices = initArgs.hasImageChoices;
		this.fieldType = initArgs.fieldType;
		// the minimum number of characters required to trigger a search.
		this.minSearchLength = initArgs.minSearchLength;
		// a flag to determine if the field should be loaded immediately if using a search value.
		this.ignoreEmptySearchValue = initArgs.ignoreEmptySearchValue;
		this.placeholder = initArgs.placeholder;

		this.initTomSelect();

		jQuery(document).on('gppa_updated_batch_fields', () => {
			this.initTomSelect();
		});
	}

	public initTomSelect() {
		for (const select of this.getSelectFields()) {
			const $select = $(select);
			// Prevent double-init or if the selector is not available on current page
			if ($select.siblings('.ts-wrapper').length || !$select.length) {
				return;
			}

			if (!select?.id) {
				return;
			}

			// Transform a selector like `input_1_2` or `input_1_2_3` into `GPAdvancedSelect_1_2` or `GPAdvancedSelect_1_2_3
			const selectorPieces = select?.id?.split('_');
			// Remove the first part of the selector, which should leave only numbers (e.g. form, field and subfield IDs).
			selectorPieces.shift();
			const namespace: GPAdvancedSelectTomSelectKey = `GPAdvancedSelect_${selectorPieces.join(
				'_'
			)}`;

			window[namespace] = new TomSelect(
				`#${select.id}`,
				this.tomSelectSettings(namespace)
			);
		}
	}
	/**
	 * Get all select fields for the current field.
	 *
	 * @return {HTMLSelectElement[]} An array of the select fields.
	 */
	public getSelectFields() {
		// address fields can have multiple selects
		if (this.fieldType === 'address') {
			const fieldSel = `#field_${this.formId}_${this.fieldId}`;
			return $(fieldSel).find('select').toArray();
		}

		const selectSelector = `#input_${this.formId}_${this.fieldId}`;
		return $(selectSelector).toArray();
	}

	public tomSelectSettings(
		selectNamespace: GPAdvancedSelectTomSelectKey
	): RecursivePartial<TomSettings> {
		const gpadvs = this;

		const settings: RecursivePartial<TomSettings> = {
			plugins: {
				change_listener: {},
			},
			valueField: 'id',
			labelField: 'text',
			openOnFocus: true,
			loadThrottle: 0,
			maxOptions: undefined,
			searchField: ['text'],
			onInitialize(this: TomSelect) {
				// Add modifier to prevent the dropdown from going under the Nested Forms/Tingle modal footer.
				const modifiers = [];

				if ($(this.control).closest('.tingle-modal-box').length) {
					modifiers.push({
						name: 'preventOverflow',
						options: {
							boundary: $(this.control).closest(
								'.gform_wrapper'
							)[0],
						},
					});
				}

				this.popper = createPopper(this.control, this.dropdown, {
					modifiers,
				});
			},
			onDropdownOpen(this: TomSelect) {
				this.popper.update();
			},
			onLoad(this: TomSelect) {
				this.popper.update();
			},
			onChange(this: TomSelect) {
				this.popper.update();
			},
		};

		if (gpadvs.fieldType === 'multiselect') {
			// @ts-ignore because with the RecursivePartial type, TS cannot infer that this IS an array based on what is hardcoded in the settings object above.
			settings.plugins?.remove_button = {
				options: {
					title: window.GPADVS.strings?.remove_this_item,
				},
			};
		}

		if (this.placeholder) {
			settings.placeholder = this.placeholder;
		}

		if (this.hasImageChoices) {
			settings.render = {
				// Options in the dropdown
				option(data: any, escape: boolean) {
					if (!data.imageSrc) {
						return `<div>${data.text}</div>`;
					}

					return `<div><img src="${data.imageSrc}" class="gpadvs-image">${data.text}</div>`;
				},
				// The select item
				item(item: any, escape: boolean) {
					if (!item.imageSrc) {
						return `<div>${item.text}</div>`;
					}

					return `<div><img src="${item.imageSrc}" class="gpadvs-image">${item.text}</div>`;
				},
			};
		}

		if (this.lazyLoad) {
			if (this.usingSearchValue) {
				// Disable client-side searching
				settings.searchField = [];
			}

			// @ts-ignore because with the RecursivePartial type, TS cannot infer that this IS an array based on what is hardcoded in the settings object above.
			settings.plugins?.virtual_scroll = {};

			// Add first URL callback to determine the first URL (page 1 results)
			settings.firstUrl = (query: string) => {
				return (
					window.GPPA.AJAXURL +
					'?action=gp_advanced_select_get_gppa_results'
				);
			};

			if (this.usingSearchValue) {
				// Require a minimum input length when querying the server
				settings.shouldLoad = (query: string) => {
					const shouldLoad =
						(this.ignoreEmptySearchValue && query.length === 0) ||
						query.length >= this.minSearchLength;

					if (!shouldLoad) {
						// Clear all options when an item is added so that a "fresh" list is loaded when the dropdown is opened again.
						window[selectNamespace]?.clearOptions();
					}

					return shouldLoad;
				};
			} else {
				settings.shouldLoad = () => true;
			}

			if (!this.usingSearchValue || this.ignoreEmptySearchValue) {
				// preload choices upon field focus
				settings.preload = 'focus';
			}

			// Wire-up the load callback
			settings.load = function (
				query: string,
				callback: TomLoadCallback
			) {
				gpadvs.loadResults.call(this as any, query, callback, gpadvs);
			};
		}

		/**
		 * Filter the Tom Select settings.
		 *
		 * Tom Select is a <select> control with a bunch of extra features such auto complete and native feeling
		 * keyboard navigation.
		 * See the docs for complete settings options: https://tom-select.js.org/docs/
		 *
		 * @param {Object} settings The Tom Select settings.
		 * @param {Object} gpadvs   The GPAdvancedSelect instance.
		 *
		 * @since 1.0-beta-1
		 */
		return window.gform.applyFilters('gpadvs_settings', settings, gpadvs);
	}

	public loadResults(
		this: TomSelect,
		query: string,
		callback: TomLoadCallback,
		gpadvs: GPAdvancedSelect
	) {
		const url = this.getUrl(query);

		const $request = $.ajax({
			url,
			contentType: 'application/json',
			dataType: 'json',
			method: 'POST',
			/*
			 * This should always be false to try to get fresh results from the server. There is a browser
			 * cache in place once fetched for the current page load.
			 */
			cache: false,
			data: JSON.stringify({
				fieldId: gpadvs.fieldId,
				security: window.GPPA.NONCE,
				term: query,
				// The kebab-case of the properties below are already expected by existing methods
				'form-id': gpadvs.formId,
				'field-values': getFormFieldValues(
					gpadvs.formId,
					!!window.gppaForms[gpadvs.formId].gravityViewMeta
				),
			}),
		});

		$request.then((response) => {
			if (response?.pagination?.nextPage) {
				const { nextPage } = response.pagination;

				this.setNextUrl(
					query,
					`${window.GPPA.AJAXURL}?action=gp_advanced_select_get_gppa_results&page=${nextPage}`
				);
			}

			// Types are wacky for the parameters.
			(callback as any)(response.results);
		});

		$request.fail(() => (callback as any)());
	}
}

window.GPAdvancedSelect = GPAdvancedSelect;
