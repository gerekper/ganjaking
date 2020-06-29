/* global jQuery, Cardinal, wc_paypal_pro_cruise_checkout_params */
jQuery( document ).ready( function( $ ) {

	// Enable logging if SCRIPT_DEBUG is switched on.
	Cardinal.configure( {
		logging: {
			level: wc_paypal_pro_cruise_checkout_params.debug ? 'on' : 'off',
		},
	} );

	// Initialize.
	Cardinal.setup( 'init', {
		jwt: wc_paypal_pro_cruise_checkout_params.jwt,
	} );

	// Include sessionId in checkout form data to pass to lookup request.
	Cardinal.on( 'payments.setupComplete', function( response ) {
		$( '#paypal_pro-card-number' ).closest( 'form' )
			.append( $( '<input type="hidden" name="paypal_pro-cardinal-sessionId" /> ' )
			.attr( 'value', response.sessionId ) );

		continueAuthentication();
	} );

	// Bin processing.
	$( document ).on( 'input', '#paypal_pro-card-number', function() {
		var cardVal = $( this ).val();
		cardVal = cardVal.replace( /\s/g, '' );
		if ( 6 <= cardVal.length ) {
			var cardBin = cardVal.substring( 0, 6 );
			Cardinal.trigger( 'bin.process', cardBin ).then( function( results ) {
				if ( wc_paypal_pro_cruise_checkout_params.debug && ! results.Status ) {
					console.error( 'BIN profiling on prefix ' + cardBin + ' failed.' );
				}
			} )
			.catch( function( error ) {
				console.error( error );
			} );
		}
	} );

	var redirectURL = null;

	function continueAuthentication() {
		var pattern  = /^#?cardinal-continue-([^:]*):([^:]*):([^:]*):(.*)$/;
		var match    = window.location.hash.match( pattern );

		if ( ! match ) {
			match = ( $( 'form#order_review' ).find( 'input[name="paypal_pro-continue-3dsecure"]' ).val() || '' ).match( pattern );

			if ( ! match ) {
				return;
			}
		}

		var acsUrl = decodeURIComponent( match[ 1 ] );
		var payload = match[ 2 ];
		var transactionId = match[ 3 ];
		redirectURL = decodeURIComponent( match[ 4 ] );

		window.location.hash = '';

		Cardinal.continue(
			'cca',
			{ AcsUrl: acsUrl, Payload: payload },
			{ OrderDetails: { TransactionId: transactionId } }
		);
	}

	window.addEventListener( 'hashchange', continueAuthentication );

	// Show error notice at top of checkout form
	var showError = function( errorMessage ) {
		var messageWrapper = '<ul class="woocommerce-error" role="alert">' + errorMessage + '</ul>';
		var $container = $( '.woocommerce-notices-wrapper, form.checkout' ).first();

		if ( ! $container.length ) {
			return;
		}

		// Adapted from https://github.com/woocommerce/woocommerce/blob/ea9aa8cd59c9fa735460abf0ebcb97fa18f80d03/assets/js/frontend/checkout.js#L514-L529
		$( '.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message' ).remove();
		$container.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + messageWrapper + '</div>' );
		$container.find( '.input-text, select, input:checkbox' ).trigger( 'validate' ).blur();

		var scrollElement = $( '.woocommerce-NoticeGroup-checkout' );
		if ( ! scrollElement.length ) {
			scrollElement = $container;
		}

		if ( $.scroll_to_notices ) {
			$.scroll_to_notices( scrollElement );
		} else {
			// Compatibility with WC <3.3
			$( 'html, body' ).animate( {
				scrollTop: ( $container.offset().top - 100 )
			}, 1000 );
		}

		$( document.body ).trigger( 'checkout_error' );
	}

	Cardinal.on( 'payments.validated', function( data ) {
		const processAuthorization = data.ActionCode === 'SUCCESS' || data.ActionCode === 'NOACTION';
		const payForOrderContinuation = data.Payment && ! $( '#paypal_pro-card-number' ).length; // Always redirect back from Pay for Order screen.

		if ( processAuthorization || payForOrderContinuation ) {
			// Pass information necessary for authorizing payment to the back-end.
			var $cardFields = $( 'form.checkout, form#order_review' ).find( 'input[name^="paypal_pro-card-"]' );
			$( '<form>' )
				.attr( 'method', 'POST' )
				.attr( 'action', redirectURL )
				.append( $cardFields.clone().attr( 'type', 'hidden' ) )
				.appendTo( document.body )
				.submit();
		} else {
			$( 'form.checkout' ).removeClass( 'processing' ).unblock();
			console.error( data.ActionCode, data );
			if ( data.Payment ) {
				showError( wc_paypal_pro_cruise_checkout_params.error );
			}
		}
	} );

} );
