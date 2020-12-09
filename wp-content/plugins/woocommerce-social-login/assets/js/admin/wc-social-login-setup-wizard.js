"use strict";

jQuery( function( $ ) {

	let plugin = window.wc_social_login ? window.wc_social_login : {};

	// handle connection step
	if ( $( '#wc-social-login-setup-wizard-default-provider' ).length > 0 ) {

		// hide the standard Setup Wizard button
		$( 'p.wc-setup-actions.step' ).last().remove();

		$( '#wc-social-login-setup-wizard-default-provider' ).on( 'change', function() {

			let provider     = $( this ).val(),
				$emptyId     = $( '#wc-social-login-' + provider + '-empty-id' ),
				$emptySecret = $( '#wc-social-login-' + provider + '-empty-secret' );

			// toggle provider settings according to chosen provider to configure
			$( 'div.wc-social-login-setup-wizard-provider-configuration' ).each( function() {
				if ( $( this ).hasClass( provider ) ) {
					$( this ).show();
				} else {
					$( this ).hide();
				}
			} );

			// simple empty fields validation
			$( 'input[type="text"]' ).on( 'keypress keyup change', function() {
				if ( '' === $( '#wc-social-login-' + provider + '-id' ).val() ) {
					$emptyId.show();
				} else {
					$emptyId.hide();
				}
				if ( '' === $( '#wc-social-login-' + provider + '-secret' ).val() ) {
					$emptySecret.show();
				} else {
					$emptySecret.hide();
				}
			} );

			// save settings and attempt to connect
			$( 'a.button-social-login' ).on( 'click', function( e ) {
				e.preventDefault();

				let provider     = $( this ).data( 'provider' ), // ensures the button clicked is for the intended provider
					clientId     = $( '#wc-social-login-' + provider + '-id' ).val(),
					clientSecret = $( '#wc-social-login-' + provider + '-secret' ).val(),
					$container   = $( 'div.wc-social-login-setup-content' );

				$container.block( {
					message    : null,
					overlayCSS : {
						'border-radius' : '3px',
						'opacity'       : '0.2'
					}
				} );

				// validate before submit
				$( 'input[type="text"]' ).trigger( 'change' );

				if ( '' !== clientId && '' !== clientSecret ) {

					let authUrl = $( this ).attr( 'href' );

					$.post( plugin.ajax_url, {
						async:         false,
						action:        'wc_social_login_configure_provider',
						security:      plugin.configure_provider_nonce,
						provider:      provider,
						client_id:     clientId,
						client_secret: clientSecret
					}, function( response ) {

						if ( ! response || ! response.success ) {
							console.log( response );
							$container.unblock();
						} else {
							// minimizes race conditions while saving preferences before redirecting
							setTimeout( function() {
								window.location.replace( authUrl )
							}, 1000 );
						}
					} );

				} else {

					$container.unblock();
				}
			} );

		} ).trigger( 'change' );

	} else if ( $( '#wc-social-login-setup-wizard-fail' ).length > 0 ) {

		// hide the standard Setup Wizard button
		$( 'p.wc-setup-actions.step' ).last().remove();

	} else if ( $( '#wc-social-login-almost-ready' ).length > 0 ) {

		// if the provider wasn't configured properly, adjust the finish screen heading
		$( '.wc-social-login-setup-content h1' ).text( plugin.i18n.almost_ready );
	}


	// toggle warning about having both checkout and checkout notice selected at the same time
	$( 'input[name^="wc_social_login_display"]' ).on( 'change', function() {
		if ( $( '#wc-social-login-display-checkout' ).is( ':checked' ) && $( '#wc-social-login-display-checkout-notice' ).is( ':checked' ) ) {
			$( '.wc-social-login-multiple-checkout-buttons' ).show();
		} else {
			$( '.wc-social-login-multiple-checkout-buttons' ).hide();
		}
	} );


	// handle newsletter signups
	let button = $( 'button.newsletter-signup' ),
	    requestURL = 'https://api.jilt.com/v2/shops/0f017a8a-d26a-4572-81fd-c9364ae30f90/customer_sessions',
	    requestData = {
			customer: {
				email:             button.data( 'user-email' ),
				accepts_marketing: true,
				contact_source:    'onboarding-social-login',
				tags:              [ 'customer', 'social-login' ]
			}
		};

	button.on( 'click', function( e ) {
		e.preventDefault();

		$( '.newsletter-prompt .spinner' ).css( 'visibility', 'visible' );

		$.post( requestURL, requestData, function() { } ).always( function() {
			$( 'div.newsletter-prompt' ).html( '<p>' + button.data( 'thank-you' ) + '</p>' );
		} );
	} );

} );
