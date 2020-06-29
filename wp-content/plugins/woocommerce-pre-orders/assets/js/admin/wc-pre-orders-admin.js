jQuery( document ).ready( function( $ ) {
	'use strict';

	var $actionEmailMessage = $( 'textarea[name="wc_pre_orders_action_email_message"]' ),
		$dateTimeField      = null;

	// Get the proper datetime field (either on the edit product page or the pre-orders > actions tab
	if ( $( 'input[name="_wc_pre_orders_availability_datetime"]' ).length ) {
		$dateTimeField = $( 'input[name="_wc_pre_orders_availability_datetime"]' );
	} else if ( $( 'input[name="wc_pre_orders_action_new_availability_date"]').length ) {
		$dateTimeField = $( 'input[name="wc_pre_orders_action_new_availability_date"]' );
	}

	// Add Pre-Order DateTimePicker (see http://trentrichardson.com/examples/timepicker/)
	if ( null !== $dateTimeField ) {
		$dateTimeField.datetimepicker({
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1
		});
	}

	// Hide email notification message textarea when "send email notification" is disabled
	if ( $actionEmailMessage.length ) {
		$( 'input[name="wc_pre_orders_action_enable_email_notification"]').change( function() {
			if ( ! $( this ).is( ':checked' ) ) {
				$actionEmailMessage.removeAttr( 'required' );
				$actionEmailMessage.closest( 'tr' ).hide();
			} else {
				$actionEmailMessage.closest( 'tr' ).show();
				$actionEmailMessage.attr( 'required', 'required' );
			}
		}).change();
	}
});
