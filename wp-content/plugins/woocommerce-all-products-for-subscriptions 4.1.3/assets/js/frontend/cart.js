/* global wcsatt_cart_params */
jQuery( function( $ ) {

	// Ensure wcsatt_cart_params exists to continue.
	if ( typeof wcsatt_cart_params === 'undefined' ) {
		return false;
	}

	var $document = $( document ),
		$body     = $( document.body );

	// Load matching subscription schemes when checking the "Add to subscription" box.
	$document.on( 'change', '.wcsatt-add-cart-to-subscription-action-input', function() {

		var $cart_totals                 = $( 'div.cart_totals' ),
			$add_to_subscription         = $( this ),
			$add_to_subscription_wrapper = $add_to_subscription.closest( '.wcsatt-add-cart-to-subscription-wrapper' ),
			$add_to_subscription_options = $add_to_subscription_wrapper.find( '.wcsatt-add-cart-to-subscription-options' ),
			is_checked                   = $add_to_subscription.is( ':checked' );

		if ( is_checked ) {

			$add_to_subscription_wrapper.block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );

			var data = {
				add_to_subscription_checked: $add_to_subscription.is( ':checked' ) ? 'yes' : 'no',
				action:                      'wcsatt_load_subscriptions_matching_cart'
			};

			$.post( wcsatt_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', data.action ), data, function( response ) {

				if ( 'success' === response.result ) {

					$add_to_subscription_options.html( response.html );
					$add_to_subscription_wrapper.removeClass( 'closed' );
					$add_to_subscription_wrapper.addClass( 'open' );
					$add_to_subscription_options.slideDown( 200 );

				} else {

					window.alert( wcsatt_cart_params.i18n_subs_load_error );
				}

				$add_to_subscription_wrapper.unblock();

			} );

		} else {

			$add_to_subscription_wrapper.removeClass( 'open' );
			$add_to_subscription_wrapper.addClass( 'closed' );
			$add_to_subscription_options.slideUp( 200 );
		}

	} );

	// AJAX handler for the add-cart-to-subscription form in the checkout page.
	$document.on( 'click', '.woocommerce-checkout .wcsatt-add-to-subscription-button', function( event ) {

		// Prevent submitting the form, as this leads to a new order and subscription.
		event.preventDefault();

		var $add_to_subscription_button  = $( this ),
			$add_to_subscription_wrapper = $add_to_subscription_button.closest( '.wcsatt-add-cart-to-subscription-options' ),
			$checkout_form               = $( '.woocommerce-checkout form' );

		$checkout_form.block( {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		} );

		var data = {
			'add-cart-to-subscription': parseInt( $add_to_subscription_button.val(), 10 ),
			'add-to-subscription-checked': 'yes',
			action:                      'wcsatt_add_cart_to_subscription_from_checkout',
			wcsatt_nonce: $add_to_subscription_wrapper.find( '#wcsatt_nonce' ).val()
		};

		$.post( wcsatt_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', data.action ), data, function( response ) {

			if ( 'success' === response.result ) {

				if ( isValidUrl( response.url ) ) {
					// For a successful request, redirect customer to the subscription page under "My Account".
					location.href = response.url;
				}

			} else {
				// For failed requests, refresh the checkout page for error notices to be displayed.
				$body.trigger("update_checkout");
			}

			$checkout_form.unblock();

		} );

		var isValidUrl = function( string ) {
			try {
				new URL( string );
				return true;
			} catch ( err ) {
				return false;
			}
		};
	} );

} );
