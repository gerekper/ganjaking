/**
 * WooCommerce Twilio SMS Notifications admin scripts.
 *
 * @since 1.0
 */
jQuery( document ).ready( function ( $ ) {

	/* global wc_twilio_sms_admin, alert */

	'use strict';

	$( '#wc_twilio_sms_shorten_urls, #wc_twilio_sms_url_shortener_service' ).on( 'change', function() {

		var $serviceInput        = $( '#wc_twilio_sms_url_shortener_service' ),
		    $serviceField        = $serviceInput.closest( 'tr' ),
		    $googleApiKeyField   = $( '#wc_twilio_sms_google_url_shortener_api_key' ).closest( 'tr' ),
		    $firebaseApiKeyField = $( '#wc_twilio_sms_firebase_dynamic_links_api_key' ).closest( 'tr' ),
		    $firebaseDomainField = $( '#wc_twilio_sms_firebase_dynamic_links_domain' ).closest( 'tr' );

		if ( $( '#wc_twilio_sms_shorten_urls' ).is( ':checked' ) ) {

			$serviceField.show();

			if ( 'firebase-dynamic-links' === $serviceInput.val() ) {
				$googleApiKeyField.hide();
				$firebaseApiKeyField.show();
				$firebaseDomainField.show();
			} else {
				$googleApiKeyField.show();
				$firebaseApiKeyField.hide();
				$firebaseDomainField.hide();
			}

		} else {

			$serviceField.hide();
			$googleApiKeyField.hide();
			$firebaseApiKeyField.hide();
			$firebaseDomainField.hide();
		}

	} ).change();

	// Hide admin notification settings if unchecked
	$( '#wc_twilio_sms_enable_admin_sms' ).on( 'change', function() {

		if( $( this ).is( ':checked' ) ) {
			$( this ).closest( 'tr' ).nextUntil( 'p' ).show();
		} else {
			$( this ).closest( 'tr' ).nextUntil( 'p' ).hide();
		}
	} ).change();

	// handle SMS test send
	$( 'a.wc_twilio_sms_test_sms_button' ).on( 'click', function( e ) {

		e.preventDefault();

		var number  = $( 'input#wc_twilio_sms_test_mobile_number' );
		var message = $( 'textarea#wc_twilio_sms_test_message' );

		// make sure values are not empty
		if ( ( ! number.val() ) || ( ! number.val() ) ) {
			alert( wc_twilio_sms_admin.test_sms_error_message );
		}

		// block UI
		number.closest( 'table' ).block( {
			message: null,
			overlayCSS: {
				background: '#fff url(' + wc_twilio_sms_admin.assets_url + ') no-repeat center',
				opacity: 0.6
			}
		} );

		// build data
		var data = {
		    mobile_number: number.val(),
		    message:       message.val(),
		    security:      wc_twilio_sms_admin.test_sms_nonce,
		    action:        'woocommerce_twilio_sms_send_test_sms'
		};

		jQuery.post( wc_twilio_sms_admin.ajax_url, data, function( response ) {

			// unblock UI
			number.closest( 'table' ).unblock();

			// clear posted values
			number.val( '' );
			message.val( '' );

			// Display Success or Failure message from response
			alert( response );

		} );

	} );

	// AJAX toggle messages
	$( '#wc_twilio_sms_toggle_order_updates' ).change( function() {

		var $section  = $( 'div#wc_twilio_sms_order_meta_box' ),
		    $checkbox = $( this );

		if ( $section.is( '.processing' ) ) {
			return;
		}

		$section.addClass( 'processing' ).block( {
			message: null,
			overlayCSS: {
				background: '#fff url(' + wc_twilio_sms_admin.assets_url + ') no-repeat center',
				backgroundSize: '16px 16px',
				opacity: 0.6
			}
		} );

		var data = {
			action:   'wc_twilio_sms_toggle_order_updates',
			security: wc_twilio_sms_admin.toggle_order_updates_nonce,
			order_id: wc_twilio_sms_admin.edit_order_id
		};

		$.ajax( {
			type:    'POST',
			url:     wc_twilio_sms_admin.ajax_url,
			data:    data,
			success: function( response ) {

				$section.removeClass( 'processing' ).unblock();

				if ( response ) {
					$checkbox.blur();
				}
			},
			dataType: 'html'
		} );

	} );

	// character count
	$( '#wc_twilio_sms_order_message' ).on( 'change keyup input', function() {

		$( '#wc_twilio_sms_order_message_char_count' ).text( $( this ).val().length );

		if ( $( this ).val().length > 160 ) {
			$( '#wc_twilio_sms_order_message_char_count' ).css( 'color', 'red' );
		}
	} );

	// AJAX message send
	$( 'a#wc_twilio_sms_order_send_message' ).click( function( e ) {

		e.preventDefault();

		var $section = $( 'div#wc_twilio_sms_order_meta_box' ),
		    $message = $( 'textarea#wc_twilio_sms_order_message' );

		if ( $section.is( '.processing' ) ) {
			return;
		}

		$section.addClass( 'processing' ).block( {
			message: null,
			overlayCSS: {
				background: '#fff url(' + wc_twilio_sms_admin.assets_url + ') no-repeat center',
				backgroundSize: '16px 16px',
				opacity: 0.6
			}
		} );

		var data = {
			action:   'wc_twilio_sms_send_order_sms',
			security: wc_twilio_sms_admin.send_order_sms_nonce,
			order_id: wc_twilio_sms_admin.edit_order_id,
			message:  $message.val()
		};

		$.ajax( {
			type:     'POST',
			url:      wc_twilio_sms_admin.ajax_url,
			data:     data,
			success:  function( response ) {

				$section.removeClass( 'processing' ).unblock();

				if ( response ) {
					$section.block( {
						message: response,
						timeout: 900
					} );
					$message.val( '' );
					$( '#wc_twilio_sms_order_message_char_count' ).text( '0' );
				}
			},
			dataType: 'html'
		} );

	} );

	/**
	 * Bookings integration methods
	 */

	// admin settings toggles
	$( '.wc_twilio_sms_enable' ).each( function() {

		var notification = $( this ).data( 'notification' );

		$( this ).on( 'change', function() {

			if ( $( this ).is( ':checked' ) ) {

				$( 'input#wc_twilio_sms_bookings_' + notification + '_recipients' ).closest( 'tr' ).show();
				$( 'input#wc_twilio_sms_bookings_' + notification + '_schedule_number' ).closest( 'tr' ).show();
				$( 'textarea#wc_twilio_sms_bookings_' + notification + '_template' ).closest( 'tr' ).show();

			} else {

				$( 'input#wc_twilio_sms_bookings_' + notification + '_recipients' ).closest( 'tr' ).hide();
				$( 'input#wc_twilio_sms_bookings_' + notification + '_schedule_number' ).closest( 'tr' ).hide();
				$( 'textarea#wc_twilio_sms_bookings_' + notification + '_template' ).closest( 'tr' ).hide();
			}

		} ).change();
	} );

	// bookings integration product tab

	// open / close SMS tab on product
	$( '#wc-twilio-sms-bookings-data .wc-metaboxes-wrapper' )
		.on( 'click', '.expand_all', function() {
			$( this ).closest('.wc-metaboxes-wrapper').find( '.wc-metabox > .wc-metabox-content' ).show();
			return false;
		} )
		.on( 'click', '.close_all', function(){
			$( this ).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .wc-metabox-content' ).hide();
			return false;
		} );

	// hide notification settings if "override" is not selected
	$( '.wc_twilio_sms_notification_toggle' ).each( function() {

		var notification   = $( this ).data( 'notification' ),
		    scheduleNumber = $( 'input#wc_twilio_sms_bookings_' + notification + '_schedule_number' );

		$( this ).on( 'change', function() {

			if ( 'override' === $( this ).find( 'option:selected' ).val() ) {

				scheduleNumber.closest( 'p' ).show();
				scheduleNumber.closest( 'p' ).next( '.wc-twilio-sms-post-field' ).show();
				$( 'textarea#wc_twilio_sms_bookings_' + notification + '_template' ).closest( 'p' ).show();

			} else {

				scheduleNumber.closest( 'p' ).hide();
				scheduleNumber.closest( 'p' ).next( '.wc-twilio-sms-post-field' ).hide();
				$( 'textarea#wc_twilio_sms_bookings_' + notification + '_template' ).closest( 'p' ).hide();
			}
		} ).change();

	} );

} );
