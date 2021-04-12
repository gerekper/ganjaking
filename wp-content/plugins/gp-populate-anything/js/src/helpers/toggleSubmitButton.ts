const getSubmitButton = ($form: JQuery) : JQuery => {
	return $form
		.find('.gform_footer, .gform_page_footer')
		.find('input[type="submit"], input[type="button"]');
};

const toggleSubmitButton = ($form: JQuery, disabled: boolean) : void => {
	/**
	 * Disable toggling of form navigation when data is loading.
	 *
	 * @param bool disabled Return true to disable form navigation toggling. Defaults to false.
	 */
	if( window.gform.applyFilters( 'gppa_disable_form_navigation_toggling', false ) ) {
		return;
	}

	const formClass = 'gppa-navigation-disabled';

	// Disable form submission while XHRs are active
	if (disabled) {
		$form
			.addClass( formClass )
			.on('submit.gppa', (e) => {
				e.preventDefault();
				return false;
			});
	} else {
		$form
			.off('submit.gppa')
			.removeClass( formClass );
	}

	getSubmitButton($form).prop('disabled', disabled);
};

const disableSubmitButton = ($form: JQuery) : void => toggleSubmitButton($form, true);
const enableSubmitButton = ($form: JQuery) : void => toggleSubmitButton($form, false);

export { disableSubmitButton, enableSubmitButton };
