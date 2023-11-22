"use strict";

function initializeConfirmDialog($scope, $) {
	let settings = dceGetElementSettings($scope);
	if ( settings.dce_confirm_dialog_enabled !== 'yes' ) {
		return;
	}
	let title = settings.dce_confirm_dialog_title;
	let content = settings.dce_confirm_dialog_content;
	let confirm_text = settings.dce_confirm_dialog_confirm_button_text || 'Confirm';
	let confirm_color = settings.dce_confirm_dialog_confirm_button_color || 'default';
	let cancel_text = settings.dce_confirm_dialog_cancel_button_text || 'Cancel';
	let cancel_color = settings.dce_confirm_dialog_cancel_button_color || 'default';
	let widthSize = settings.dce_confirm_dialog_width.size;
	let widthUnit = settings.dce_confirm_dialog_width.unit || '%';
	let width = widthSize + widthUnit;
	let $form = $scope.find('form').first();
	
	if( ! widthSize ) { 
		width = '30%';
	}
	let confirmed = false;
	let $submit = $form.find('button[type="submit"]')
	// The event is on the click event of the button. This is very useful
	// because it is fired before the submit event and it ignores
	// programmaticaly triggered submit events, for example from Stripe.
	$submit.on('click', (event) => {
		if (confirmed) {
			confirmed = false;
			return;
		}
		event.preventDefault();
		event.stopImmediatePropagation();
		$.confirm({
			title: renderLiveHTML($form[0], title),
			content: renderLiveHTML($form[0], content),
			theme: settings.dce_confirm_dialog_theme,
			boxWidth: width,
			useBootstrap: false,
			buttons: {
				confirm: {
					text: confirm_text,
					btnClass: 'btn-' + confirm_color,
					action: function () {
						confirmed = true;
						$submit.click();
					},
				},
				cancel: {
					text: cancel_text,
					btnClass: 'btn-' + cancel_color,
					action: function () {
						// nothing for now.
					},
				},
			}
		});
	});
}

jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeConfirmDialog);
});
