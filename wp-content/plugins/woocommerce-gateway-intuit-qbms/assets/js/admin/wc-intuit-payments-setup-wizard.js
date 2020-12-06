/**
 * WooCommerce Intuit Payments Setup Wizard scripts
 *
 * @since 2.5.0
 */

"use strict";

if ( typeof intuit !== "undefined" ) {

	intuit.ipp.anywhere.setup( {
		grantUrl:    wc_intuit_payments.connect_url,
		datasources: {
			quickbooks: false,
			payments:   true
		},
		paymentOptions: {
			intuitReferred: true
		}
	} );

}


jQuery( document ).ready( $ => {

	const $clientId     = $( `#wc_gateway_${wc_intuit_payments.gateway_id}_client_id` );
	const $clientSecret = $( `#wc_gateway_${wc_intuit_payments.gateway_id}_client_secret` );

	if ( $clientId.length && $clientSecret.length ) {

		const $form = $clientId.closest( 'form' );

		/**
		 * Show a connection error message and troubleshooting suggestions when the
		 * connection fails.
		 *
		 * @since 2.5.0
		 *
		 * @param {string} error error message passed by SkyVerge\WooCommerce\Intuit\Handlers\Connection::handle_oauth_connect_error()
		 */
		window.onQuickBooksConnectionFailed = function( error ) {

			$form.find( 'h1, .wc-intuit-payments-setup-connection-settings, .wc-setup-actions' ).hide();

			if ( error ) {

				if ( error.endsWith( '.' ) ) {
					error = error.substring( 0, error.length - 1 );
				}

				$form.find( '.wc-intuit-payments-connection-error-with-message' ).show().find( 'span' ).text( error );

			} else {

				$form.find( '.wc-intuit-payments-connection-error' ).show();
			}

			$form.find( '.wc-intuit-payments-setup-connection-suggestions' ).show();
		};

		/**
		 * Redirect to the next step when the connection is established.
		 *
		 * @see SkyVerge\WooCommerce\Intuit\Handlers\Connection::handle_oauth_connect_success()
		 *
		 * @since 2.5.0
		 */
		window.onQuickBooksSuccessfulConnection = function() {
			window.location.href = $form.find( '.button-next' ).attr( 'href' );
		};

		// show error messages if the field is empty
		$form.on( 'keypress keyup change', '#' + $clientId.attr( 'id' ) + ', #' + $clientSecret.attr( 'id' ), function() {

			const $field   = $( this );
			const $wrapper = $field.closest( '.form-row' );
			const value    = $field.val();

			if ( value === '' ) {

				$wrapper.addClass( 'woocommerce-invalid' );
				$( 'label[for="' + $field.attr( 'id' ) + '"].wc-intuit-payments-error-message-empty' ).show();

			// values 9 or fewer characters long, or that contain non-alphanumeric characters trigger an invalid API response
			} else if ( ! value.match( /^[a-zA-Z0-9]{10,}$/ ) ) {

				$wrapper.addClass( 'woocommerce-invalid' );
				$( 'label[for="' + $field.attr( 'id' ) + '"].wc-intuit-payments-error-message-invalid' ).show();

			} else {

				$wrapper.removeClass( 'woocommerce-invalid' );
				$( 'label[for="' + $field.attr( 'id' ) + '"].wc-intuit-payments-error-message' ).hide()
			}
		} );

		// make sure the Client ID and Client Secret values were entered and attempt to connect to QuickBooks
		$form.on( 'click', '.button-next', function( e ) {

			e.preventDefault();

			// trigger validation
			$clientId.add( $clientSecret ).trigger( 'change' );

			// if one of the fields is invalid, move focus to that field
			if ( $clientId.closest( '.form-row' ).hasClass( 'woocommerce-invalid' ) || $clientSecret.closest( '.form-row' ).hasClass( 'woocommerce-invalid' ) ) {

				$form.find( '.woocommerce-invalid input' ).first().focus();

				return;
			}

			const data = {
				action:        'wc_intuit_payments_update_connection_settings',
				nonce:         wc_intuit_payments.update_connection_settings_nonce,
				gateway_id:    wc_intuit_payments.gateway_id,
				client_id:     $clientId.val(),
				client_secret: $clientSecret.val(),
			};

			$.post( wc_intuit_payments.ajaxurl, data, ( response ) => {

				if ( response.success ) {
					intuit.ipp.anywhere.controller.onConnectToIntuitClicked( $form.find( '.button-next' ).get( 0 ) );
				} else {
					onQuickBooksConnectionFailed( response.data );
				}
			} );
		} );
	}

	// handle newsletter sign up
	let button = $( 'button.newsletter-signup' ),
		requestURL  = 'https://api.jilt.com/v2/shops/0f017a8a-d26a-4572-81fd-c9364ae30f90/customer_sessions',
		requestData = {
			customer: {
				email:             button.data( 'user-email' ),
				accepts_marketing: true,
				contact_source:    'onboarding-intuit',
				tags:              [ 'customer', 'gateway-intuit-payments' ]
			}
		};

	button.on( 'click', function( e ) {
		e.preventDefault();

		$( '.wc-intuit-payments-newsletter-prompt .spinner' ).css( 'visibility', 'visible' );

		$.post( requestURL, requestData, function() { } ).always( function() {
			$( '.wc-intuit-payments-newsletter-prompt div' ).html( '<p>' + button.data( 'thank-you' ) + '</p>' );
		} );
	} );

} );
