/*!
 * WooCommerce Chase Paymentech
 *
 * Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * Licensed under the GNU General Public License v3.0
 * http://www.gnu.org/licenses/gpl-3.0.html
 */

/* jshint unused: false */

/* global wc_chase_paymentech_params */

/**
 * Invoked when pay form "Cancel" button is clicked, transitions client to the
 * pay page with the list of available payment methods
 *
 * Hosted Pay Form Callback
 */
function cancelCREPayment() {
	location.href = wc_chase_paymentech_params.cancel_url;
}


/**
 * Invoked when the "What's this?" link next to the CSC field is clicked.
 * Displays an explanation and image at the top of the page.
 *
 * Hosted Pay Form Callback
 */
function whatCVV2() {

	// message is already displayed
	if ( jQuery( '.woocommerce-message-cvv' ).length > 0 ) {
		return;
	}

	// add errors
	jQuery( 'form.pay-page-checkout, form#add_payment_method' ).prepend(
		'<ul class="woocommerce-message woocommerce-message-cvv"><img src="' + wc_chase_paymentech_params.what_is_csc_image_url + '" style="float:left;" /><p style="margin:0px 0px 0px 70px;">' + wc_chase_paymentech_params.what_is_csc + '</p></ul>'
	);

	// scroll to top
	jQuery( 'html, body' ).animate( {
		scrollTop: ( jQuery( '.woocommerce-message-cvv' ).offset().top - 100 )
	}, 1000 );

}


/**
 * Called when the "Complete" button is clicked and the transaction process begins.
 *
 * @since 1.8.1-1
 */
function startCREPayment() {

	// block the form so the customer knows something is happening
	jQuery( 'form.pay-page-checkout, form#add_payment_method' ).block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );
}


/**
 * Invoked when the pay form "Complete" button is clicked and there are errors
 * with the transaction.  Renders the appropriate error messages
 *
 * Hosted Pay Form Callback
 *
 * @param string errorCode pipe-delimited list of error codes, with a trailing pipe
 */
function creHandleErrors( errorCode ) {

	// delegate to other error handler
	creHandleDetailErrors( errorCode, '', '' );
}


/**
 * Invoked when the pay form "Complete" button is clicked and there are errors
 * with the transaction.  Renders the appropriate error messages
 *
 * Hosted Pay Form Callback
 *
 * @param string errorCode pipe-delimited list of error codes, with a trailing pipe
 * @param string gatewayCode the proc status code returned by the Orbital Gateway. These are common response messages to all Orbital Gateway interfaces
 * @param string gatewayMessage the proc status text reported by the Orbital Gateway for the transaction. These are common response messages to all Orbital Gateway interfaces.
 */
function creHandleDetailErrors( errorCode, gatewayCode, gatewayMessage ) {

	var errorCodes = errorCode.replace( /\|$/, '' ).split( '|' );
	var errors = [];
	var messages = [];

	// notify the backend since error responses don't generate an IPN
	jQuery.get(
		wc_chase_paymentech_params.ajaxurl,
		{
			action:         'wc_payment_gateway_chase_paymentech_handle_error',
			orderId:        wc_chase_paymentech_params.order_id,
			errorCode:      errorCode,
			gatewayCode:    gatewayCode,
			gatewayMessage: gatewayMessage
		},
		function ( response ) {

			// certification mode? just redirect to the Thank You page
			if ( wc_chase_paymentech_params.is_certification_mode ) {

				location.href = wc_chase_paymentech_params.return_url;

			} else {

				jQuery( 'form.pay-page-checkout, form#add_payment_method' ).unblock();

				var errors = response.data;

				if ( 0 === errors.length ) {
					errors.push( wc_chase_paymentech_params.general_error );
				}

				// hide and remove any previous errors
				jQuery( '.woocommerce-error, .woocommerce-message' ).remove();

				// add errors
				var messagesClass = '';

				if ( errors.length > 0 ) {
					jQuery( 'form.pay-page-checkout, form#add_payment_method' ).prepend( '<ul class="woocommerce-error"><li>' + errors.join( '</li><li>' ) + '</li></ul>' );
					messagesClass = 'woocommerce-error';
				}

				if ( messages.length > 0 ) {
					jQuery( 'form.pay-page-checkout, form#add_payment_method' ).prepend( '<ul class="woocommerce-message"><li>' + messages.join( '</li><li>' ) + '</li></ul>' );
					messagesClass = 'woocommerce-message';
				}

				// scroll to top
				jQuery( 'html, body' ).animate( {
					scrollTop: ( jQuery( '.' + messagesClass ).offset().top - 100 )
				}, 1000 );
			}
		}
	);
}


/**
 * Invoked when the pay form "Complete" button is clicked and completes
 * successfully.  Transitions client to the "thank you" page
 *
 * Hosted Pay Form Callback
 */
function completeCREPayment( transaction ) {
	location.href = wc_chase_paymentech_params.return_url;
}


jQuery( function ( $ ) {

	'use strict';

	var updateTimer;
	var xhr;

	// handle payment methods on checkout->pay page
	handleSavedPaymentMethods();

	$( 'body' ).bind( 'updated_pay_page_checkout', function() {
		handleSavedPaymentMethods();
	} );


	// show/hide the saved payment methods when a saved payment method is de-selected/selected
	function handleSavedPaymentMethods() {

		window.wc_chase_paymenttech_selected_payment_method = get_selected_payment_method();

		$( 'input.js-wc-chase-paymentech-payment-token' ).change( function() {

			var tokenizedPaymentMethodSelected = $( 'input.js-wc-chase-paymentech-payment-token:checked' ).val(),
				$newPaymentMethodSection = $( 'div.js-wc-chase-paymentech-new-payment-method-form' );

			if ( get_selected_payment_method() !== window.wc_chase_paymenttech_selected_payment_method ) {

				window.wc_chase_paymenttech_selected_payment_method = get_selected_payment_method();
				update_checkout();
				return;
			}

			if ( tokenizedPaymentMethodSelected ) {
				// using an existing tokenized payment method, hide the 'new method' fields
				$newPaymentMethodSection.slideUp( 200 );

				// show the checkout
				$( '#place_order' ).show();
			} else {
				// use new payment method, display the 'new method' fields
				$newPaymentMethodSection.slideDown( 200 );

				// hide the checkout button since the hosted pay form has its own
				$( '#place_order' ).hide();
			}
		} ).change();

	}


	// Reload the checkout form when the "tokenize payment method" option is selected, so we can regenerate the iframe
	$( 'form.pay-page-checkout' ).on( 'change', '.js-wc-chase-paymentech-tokenize-payment-method', function() {
		update_checkout();
	} );


	/**
	 * Gets the selected payment method value.
	 *
	 * @since 1.13.0
	 *
	 * @returns string
	 */
	function get_selected_payment_method() {
		var $value = $( 'input.js-wc-chase-paymentech-payment-token:checked' ).val();

		return '' === $value ? 0 : $value;
	}


	/**
	 * render any new errors and bring them into the viewport
	 */
	function renderErrors( $form, errors ) {

		// hide and remove any previous errors
		$( '.woocommerce-error, .woocommerce-message' ).remove();

		// add errors
		$form.prepend( '<ul class="woocommerce-error"><li>' + errors.join( '</li><li>' ) + '</li></ul>' );

		// unblock UI
		$form.removeClass( 'processing' ).unblock();

		$form.find( '.input-text, select' ).blur();

		// scroll to top
		$( 'html, body' ).animate( {
			scrollTop: ( $form.offset().top - 100 )
		}, 1000 );

	}


	/**
	 * Update the pay page checkout form, invoked when the "Tokenize" input
	 * is toggled for a new card
	 */
	function update_checkout() {

		if (xhr) { xhr.abort(); }

		$( 'form.pay-page-checkout' ).block( { message: null, overlayCSS: { background: '#fff url(' + wc_chase_paymentech_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } } );

		var data = {
			action:                            'wc-chase-paymentech-update-checkout',
			security:                          wc_chase_paymentech_params.update_checkout_nonce,
			order_id:                          wc_chase_paymentech_params.order_id,
			should_tokenize:                   $( '.js-wc-chase-paymentech-tokenize-payment-method' ).is( ':checked' ) ? 'yes' : 'no',
			tokenized_payment_method_selected: get_selected_payment_method()
		};

		xhr = $.ajax( {
			type:    'POST',
			url:     wc_chase_paymentech_params.ajaxurl,
			data:    data,
			success: function( response ) {
				if ( response ) {
					var checkout_form_output = $( response );
					$( 'form.pay-page-checkout' ).html( checkout_form_output.html() );
					$( 'body' ).trigger( 'updated_pay_page_checkout' );
				}
			}
		});
	}


	/**
	 * Perform the Pay Page checkout process
	 */
	$( 'form.pay-page-checkout' ).submit( function() {

		clearTimeout( updateTimer );

		var $form = $(this);

		if ( $form.is( '.processing' ) ) {
			return false;
		}
		$form.addClass( 'processing' );

		var form_data = $form.data();

		if ( 1 !== form_data['blockUI.isBlocked'] ) {
			$form.block( { message: null, overlayCSS: { background: '#fff url(' + wc_chase_paymentech_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } } );
		}

		var data = $form.serialize();

		$.ajax( {
			type:    'POST',
			url:      wc_chase_paymentech_params.checkout_url,
			order_id: wc_chase_paymentech_params.order_id,
			data:     data,
			success:  function( code ) {
				var result = '';

				try {
					// Get the valid JSON only from the returned string
					if ( code.indexOf( '<!--WC_START-->' ) >= 0 ) {
						code = code.split( '<!--WC_START-->' )[1]; // Strip off before after WC_START
					}

					if ( code.indexOf( '<!--WC_END-->' ) >= 0 ) {
						code = code.split( '<!--WC_END-->' )[0]; // Strip off anything after WC_END
					}

					// Parse
					result = $.parseJSON( code );

					if ( 'success' === result.result ) {

						window.location = decodeURI( result.redirect );

					} else if ( result.result === 'failure' ) {
						throw 'Result failure';
					} else {
						throw 'Invalid response';
					}
				} catch( err ) {
					// Remove old errors
					$('.woocommerce-error, .woocommerce-message').remove();

					// Add new errors
					if ( typeof( result.messages ) !== 'undefined' ) {
						$form.prepend( result.messages );
					} else {
						$form.prepend( code );
					}

					// Cancel processing
					$form.removeClass( 'processing' ).unblock();

					// Lose focus for all fields
					$form.find( '.input-text, select' ).blur();

					// Scroll to top
					$( 'html, body' ).animate( {
						scrollTop: $( 'form.pay-page-checkout' ).offset().top - 100
					}, 1000 );
				}
			},
			dataType: 'html'
		} );

		return false;
	} );

	// initial hide
	if ( $( '#add_payment_method #payment_method_chase_paymentech' ).is( ':checked' ) ) {
		$( '#add_payment_method #place_order' ).hide();
	}

	// Add Payment Method screen - show/hide the Add button
	$( 'form#add_payment_method [name="payment_method"]' ).change( function( e ) {

		if ( 'chase_paymentech' === $( this ).val() ) {
			$( '#add_payment_method #place_order' ).hide();
		} else {
			$( '#add_payment_method #place_order' ).show();
		}

	} );

} );
