const $ = window.jQuery;

// Register field setting so Gravity Forms shows it in the sidebar.
$(function () {
	const { fieldSettings } = window;

	for (const fieldType in fieldSettings) {
		if (
			fieldSettings.hasOwnProperty(fieldType) &&
			$.inArray(fieldType, ['select', 'multiselect', 'address']) !== -1
		) {
			fieldSettings[fieldType] += ', .gpadvs-field-setting';
		}
	}

	bindEvents();

	addInitialFieldPreviewOptions();
});

/**
 * Override the SetFieldProperty function to trigger an action when the "Advanced Select"
 * checkbox is checked.
 */
const originalSetFieldProperty = window.SetFieldProperty;
window.SetFieldProperty = function (property: string, value: any) {
	window.gform.doAction('gpadvs_gform_set_field_property', property, value);

	originalSetFieldProperty(property, value);
};

/**
 * Override the UpdateFieldChoices function so that we can update the custom GPADVS
 * multiselect preview UI when the choices are updated.
 */
const originalUpdateFieldChoices = window.UpdateFieldChoices;
window.UpdateFieldChoices = function (fieldType: string) {
	const { gpadvsEnable, type } = window.field;
	if (gpadvsEnable && ['multiselect', 'select'].includes(type)) {
		showGPADVSPreviewUI(window.field);
	}

	originalUpdateFieldChoices(fieldType);
};

/**
 * Override the flyout onClose function to trigger an action when the flyout is closed.
 * In this case we update the GPAVDS multiselect field preview UI in case the choices
 * have changed.
 */
document.addEventListener('gform/flyout/pre_init', (event) => {
	// @ts-ignore
	if (event.detail.instance.options.id !== 'choices-ui-flyout') {
		return;
	}

	// @ts-ignore
	const origOnClose = event.detail.instance.options.onClose;
	// @ts-ignore
	event.detail.instance.options.onClose = (...args) => {
		const { gpadvsEnable, type } = window.field;
		if (gpadvsEnable && ['multiselect', 'select'].includes(type)) {
			showGPADVSPreviewUI(window.field);
		}

		// @ts-ignore
		origOnClose(...args);
	};
});

/**
 * Responsd to the "Placeholder" checkbox being checked by updating the corresponding field
 * preview with the placeholder text.
 */
window.gform.addAction(
	'gpadvs_gform_set_field_property',
	(property: string, value: any) => {
		const { field } = window;

		if (property !== 'placeholder' || !field.gpadvsEnable) {
			return;
		}

		showGPADVSPreviewUI({
			...field,
			placeholder: value,
		});
	}
);

/**
 * Responsd to the "Advanced Select" checkbox being checked by showing/hiding the "Placeholder"
 * setting in the "Appearance" tab as needed and toggling the "Preview UI" for multiselect
 * fields.
 */
window.gform.addAction(
	'gpadvs_gform_set_field_property',
	(property: string, value: any) => {
		if (property !== 'gpadvsEnable') {
			return;
		}

		const field = window.field;
		const { type } = window.field;

		if (type === 'multiselect') {
			if (value === true) {
				showPlaceholderSetting();
			} else {
				hidePlaceholderSetting();
			}
		}

		if (['multiselect', 'select'].includes(type)) {
			if (value === true) {
				showGPADVSPreviewUI(field);
			} else {
				showDefaultPreviewUI(field);
			}
		}
	}
);

// Allow Advanced Select fields to use JetSloth Image Choices
window.gform.addFilter(
	'gfic_field_can_have_images',
	function (canHaveImages: boolean, field: any) {
		if (field.gpadvsEnable) {
			return true;
		}

		return canHaveImages;
	}
);

const toggleEnhancedUINotice = (enabled: boolean) => {
	jQuery('#gpadvs-enhanced-ui-notice').remove();

	const $enhancedUI = jQuery('#gfield_enable_enhanced_ui');

	if (enabled) {
		const $notice =
			jQuery(`<div id="gpadvs-enhanced-ui-notice" class="gform-accessibility-warning field_setting gform-alert gform-alert--accessibility gform-alert--inline">
					<span class="gform-icon gform-icon--password gform-alert__icon"></span>
					<div class="gform-alert__message-wrap">
						<p class="gform-alert__message" style="margin: 0;padding-top:2px;">${window.GPADVS_FORM_EDITOR.strings?.not_compat_with_enhanced_ui}</p>
					</div>
				</div>`);

		$enhancedUI.prop('disabled', true);

		// Disable Enhanced UI if it's already checked.
		if (window.field?.enableEnhancedUI) {
			$enhancedUI.prop('checked', false).trigger('change');
			window.SetFieldEnhancedUI(false);
		}

		$notice.insertAfter('.enable_enhanced_ui_setting');
	} else {
		$enhancedUI.prop('disabled', false);
	}
};

const toggleGPPALazyLoadSetting = () => {
	const field = window.field;

	const gpadvsEnabled = !!field?.gpadvsEnable;
	const choicePopulatedEnabled = !!field?.['gppa-choices-enabled'];

	// Show the "Lazy Load Populated Choices" if GPPA is populating the choices.
	if (window.GPPA && gpadvsEnabled && choicePopulatedEnabled) {
		$('#gpadvs-enable-child-settings').show();
		$('#gpadvs-gppa-lazy-load-row').show();
	} else {
		$('#gpadvs-enable-child-settings').hide();
		$('#gpadvs-gppa-lazy-load-row').hide();
	}
};

const hideGFAccessibilityWarning = () => {
	$('#general_tab > .gform-alert--accessibility').hide();

	$('#general_tab > .gform-alert--accessibility + li.field_setting').addClass(
		'gp-advanced-select-no-top-margin'
	);
};

const showGFAccessibilityWarning = () => {
	$('#general_tab > .gform-alert--accessibility').show();

	$(
		'#general_tab > .gform-alert--accessibility + li.field_setting'
	).removeClass('gp-advanced-select-no-top-margin');
};

/**
 * Shows the "Placeholder" setting in the "Appearance" tab.
 */
const showPlaceholderSetting = () => {
	$('.placeholder_setting').show();
};

/**
 * Hides the "Placeholder" setting in the "Appearance" tab.
 */
const hidePlaceholderSetting = () => {
	$('.placeholder_setting').hide();
};

const bindEvents = () => {
	$('#gpadvs-enable').on('change', function (this: HTMLInputElement) {
		window.SetFieldProperty('gpadvsEnable', this.checked);
		toggleEnhancedUINotice(this.checked);

		if (window.imageChoicesAdmin) {
			// trigger gform_load_field_choices to have JetSloth handle showing/hiding the image choices setting
			window.gform.doAction('gform_load_field_choices', [window.field]);
		}

		if (this.checked) {
			hideGFAccessibilityWarning();
		} else {
			showGFAccessibilityWarning();
		}

		toggleGPPALazyLoadSetting();
	});

	$('#gpadvs-gppa-lazy-load').on('change', function (this: HTMLInputElement) {
		window.SetFieldProperty('gpadvsGPPALazyLoad', this.checked);
	});

	$('#gppa').on(
		'change',
		'#gppa-choices-enabled',
		function (this: HTMLInputElement) {
			toggleGPPALazyLoadSetting();

			if (window.field.gpadvsEnable) {
				renderGPADVSMSPreviewOptions(window.field);
			}
		}
	);

	$(document).on('gform_load_field_settings', function (event, field, form) {
		const enabled = !!field.gpadvsEnable;
		const isMultiSelect = field.type === 'multiselect';

		if (enabled && isMultiSelect) {
			showPlaceholderSetting();
		}

		$('#gpadvs-enable').prop('checked', enabled);
		toggleEnhancedUINotice(enabled);

		/*
		 * GP Populate Anything Integration
		 */
		const $gppaLazyLoad = $('#gpadvs-gppa-lazy-load');
		$gppaLazyLoad.prop('checked', !!field.gpadvsGPPALazyLoad);

		toggleGPPALazyLoadSetting();

		// If there is a filter value using the "Advanced Select Search Value" special value, then check the checkbox
		// and disable it.
		$gppaLazyLoad.prop('disabled', false);

		filterGroups: for (const filterGroup of field?.[
			'gppa-choices-filter-groups'
		] ?? ([] as any[])) {
			for (const filter of filterGroup) {
				if (
					filter.value ===
					'special_value:advanced_select_search_value'
				) {
					$gppaLazyLoad.prop('checked', true).prop('disabled', true);

					break filterGroups;
				}
			}
		}

		/*
		 * JetSloth Image Choices Integration
		 */
		// Hide JetSloth Image Choices "Show labels" and "Use lightbox" settings
		const $settingsToToggle = $(
			'li.image-choices-setting-show-labels, li.image-choices-setting-use-lightbox'
		);

		// We use a class as the Jetty Boys use !important on some very specific selectors.
		const hiddenImageChoicesSettingClass =
			'gpadvs-hidden-image-choices-setting';

		if (enabled) {
			$settingsToToggle.addClass(hiddenImageChoicesSettingClass);
			hideGFAccessibilityWarning();
		} else {
			$settingsToToggle.removeClass(hiddenImageChoicesSettingClass);
			showGFAccessibilityWarning();
		}
	});
};

// Add a special value for Filter values to fields with Advanced Select enabled.
window.gform.addFilter(
	'gppa_filter_special_values',
	function (specialValues: any[], Filter: any) {
		const inputType = Filter.field.inputType
			? Filter.field.inputType
			: Filter.field.type;

		if (
			$.inArray(inputType, ['select', 'multiselect', 'address']) === -1 ||
			!Filter.field.gpadvsEnable
		) {
			return specialValues;
		}

		specialValues.push({
			label: 'Advanced Select Search Value',
			value: 'special_value:advanced_select_search_value',
		});

		return specialValues;
	}
);

window.gform.addAction(
	'gppa_filter_value_updated',
	(value: string, $vueComponent: any) => {
		if (value === 'special_value:advanced_select_search_value') {
			$vueComponent.updateSelectedOperator('contains');
		}
	}
);
/**
 * Handle showing/hiding the custom GPADVS multiselect field preview options
 * on any field with GPADVS enabled.
 */
function addInitialFieldPreviewOptions() {
	for (const field of window.form.fields) {
		if (!field.gpadvsEnable) {
			continue;
		}

		if (field.type === 'multiselect') {
			renderGPADVSMSPreviewOptions(field);
		} else if (field.type === 'select') {
			renderGPADVSelectPreviewOption(field);
		}
	}
}

/**
 * Adds the field <option>'s to the custom GPADVS multiselect field preview UI.
 *
 * @param field Field object
 */
function renderGPADVSMSPreviewOptions(field: Field) {
	const placeholder = field.placeholder || '';
	const numChoicesToShow = placeholder.length ? 2 : 3;

	let choices = field.choices
		.filter((c) => c.text?.length)
		.slice(0, numChoicesToShow);

	if (field['gppa-choices-enabled']) {
		// create some blank options to fill out the preview
		// with some varying lengths to mimic the default GPPA styling.
		choices = [23, 31, 28].map((len, i) => ({
			// add a non-breaking space for each character in the option text
			// so that each option element will have a width.
			text: Array(len).fill('&nbsp;').join(''),
			isSelected: false,
			price: '',
			value: String(i),
		}));
	}

	const optionsMarkup = choices.map(({ text }) => {
		return `<div data-value="${text}" class="item" data-ts-item="">${text}<a href="javascript:void(0)" class="remove" tabindex="-1" title="Remove this item">×</a></div>`;
	});

	const tsControl = $(
		`#field_${field.id} > .ginput_container > .ts-wrapper > .ts-control`
	);

	// Remove old options and replace with the new ones.
	tsControl.find('.item').each((i, el) => {
		el.remove();
	});
	tsControl.prepend(optionsMarkup.join(''));

	tsControl.find('input').attr('placeholder', placeholder);
}

/**
 * Adds the field selected <option> to the custom GPADVS select field preview UI.
 *
 * @param field Field object
 */
function renderGPADVSelectPreviewOption(field: Field) {
	const choice = field.choices.filter((c) => c.text && c.value)[0];
	const { placeholder } = field;

	const tsControl = $(
		`#field_${field.id} > .ginput_container > .ts-wrapper > .ts-control`
	);

	// Remove old options so that we can replace with a placeholder or the new option.
	tsControl.find('.item').each((i, el) => {
		el.remove();
	});

	if (placeholder || !choice) {
		tsControl.find('input').attr('placeholder', field.placeholder || '');

		return;
	}

	tsControl.find('input').removeAttr('placeholder');

	tsControl.prepend(
		`<div data-value="${choice.text}" class="item" data-ts-item="">${choice.text}</div>`
	);
}

/**
 * Shows the custom GPADVS field preview UI.
 *
 * @param field Field object
 */
function showGPADVSPreviewUI(field: Field) {
	getDefaultPreviewElement(field).hide();
	getGPADVSPreviewElement(field).show();

	if (field.type === 'multiselect') {
		renderGPADVSMSPreviewOptions(field);
	} else if (field.type === 'select') {
		renderGPADVSelectPreviewOption(field);
	}
}

/**
 * Shows the default field preview UI.
 *
 * @param field Field object
 */
function showDefaultPreviewUI(field: Field) {
	getDefaultPreviewElement(field).show();
	getGPADVSPreviewElement(field).hide();
}

/**
 * Returns the default preview element for the field.
 *
 * @param field
 * @return A jQuery object for the default preview element for the field.
 */
function getDefaultPreviewElement(field: Field) {
	return $(`#field_${field.id} > .ginput_container > select`)
		.not('.tomselected')
		.closest('.ginput_container');
}

/**
 * Returns the custom GPADVS preview element for the field.
 *
 * @param field
 * @return A jQuery object for the GPADVS preview element for the field.
 */
function getGPADVSPreviewElement(field: Field) {
	return $(
		`#field_${field.id} > .ginput_container > select.tomselected`
	).closest('.ginput_container');
}

// Make this a module to avoid TypeScript error with block-scoped variables since we're not importing anything
export {};
