/**
 * Subscription widget.
 *
 * @package WC_Newsletter_Subscription/Assets/Js/Frontend
 * @since   2.5.0
 */

/* global wc_newsletter_subscription_widget_params */
(function( $ ) {

	'use strict';

	$(function() {
		if ( typeof wc_newsletter_subscription_widget_params === 'undefined' ) {
			return false;
		}

		var wc_newsletter_subscription_widget = {

			/**
			 * Init.
			 */
			init: function () {
				$( 'form#subscribeform' ).on( 'submit', function( event ) {
					var $form   = $( this ),
					    $submit = $form.find( 'input[type="submit"]' );

					event.preventDefault();

					$submit.prop( 'disabled', true );

					$.post({
						url: wc_newsletter_subscription_widget_params.ajax_url,
						data: $form.serialize(),
						dataType: 'json',
						success: function( result ) {
							var $notice = $( '.wc-subscribe-to-newsletter-notice' )
								.html( result.data.message )
								.removeClass( 'woocommerce-message woocommerce-error' )
								.addClass( result.success ? 'woocommerce-message' : 'woocommerce-error' );

							$form.before( $notice );

							if ( result.success ) {
								$form.remove();
							}
						},
						complete: function () {
							$submit.prop( 'disabled', false );
						}
					});

					return false;
				});
			}
		};

		wc_newsletter_subscription_widget.init();
	});
})( jQuery );
