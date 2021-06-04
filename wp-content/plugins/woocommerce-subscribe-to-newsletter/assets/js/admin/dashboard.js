/**
 * Dashboard.
 *
 * @package WC_Newsletter_Subscription/Assets/JS/Admin
 * @since   3.1.0
 */

/* global wc_newsletter_subscription_dashboard_params, ajaxurl */
(function ( $, ajaxurl ) {

	'use strict';

	$( function () {
		if ( typeof wc_newsletter_subscription_dashboard_params === 'undefined' ) {
			return false;
		}

		var wcNewsletterSubscriptionDashboard = {

			/**
			 * Init.
			 */
			init: function () {
				var $statsWidgetWrapper = $( '#wc_newsletter_subscription_stats' );

				$statsWidgetWrapper.on( 'click', '.refresh-stats', function () {
					$statsWidgetWrapper.find( '.refresh-stats' ).prop( 'disabled', true );

					$.post( {
						url: ajaxurl,
						data: {
							action: 'wc_newsletter_subscription_refresh_stats_widget',
							_ajax_nonce: wc_newsletter_subscription_dashboard_params.nonce
						},
						dataType: 'json',
						success: function ( result ) {
							$statsWidgetWrapper.find( '.inside' ).html( result );
							$statsWidgetWrapper.find( '.wc-newsletter-subscription-stats-list' ).hide().fadeIn( 800 );
						}
					} );

					return false;
				} );
			}
		};

		wcNewsletterSubscriptionDashboard.init();
	} );
})( jQuery, ajaxurl );
