/**
 * Settings
 *
 * @package WC_Newsletter_Subscription/Assets/JS/Admin
 * @since   2.8.0
 */

/* global wc_newsletter_subscription_settings_params, ajaxurl */
( function( $, ajaxurl ) {

	'use strict';

	if ( typeof wc_newsletter_subscription_settings_params === 'undefined' ) {
		return false;
	}

	var wcNewsletterSubscriptionSettings = {
		init: function() {
			var that = this;

			$( '#woocommerce_newsletter_service' ).on( 'change', function() {
				var $fields = $( '.newsletter-provider-fields' ),
					value   = $( this ).val();

				// Hide all fields.
				$fields.hide();

				// Show only the selected provider fields.
				if ( value ) {
					$fields.filter( '.' + value ).show();
				}
			} ).trigger( 'change' );

			$( '.forminp-provider_lists' ).on( 'click', '.refresh-lists', function( event ) {
				var $refreshButton = $( this ),
				    $lists         = $( '.forminp-provider_lists select' ),
				    selected       = $lists.val();

				event.preventDefault();
				$refreshButton.prop( 'disabled', true );

				$.post( {
					url: ajaxurl,
					data: {
						action: 'wc_newsletter_subscription_provider_lists',
						_ajax_nonce: wc_newsletter_subscription_settings_params.nonce,
						refresh: true
					},
					dataType: 'json',
					success: function( result ) {
						// Clear options:
						$lists.find( 'option' ).not( ':first' ).remove();

						// Update options:
						$.each( result.data, function( value, label ) {
							$lists.append( $( '<option />' ).val( value ).text( label ) );
						} );

						// Select previous value selected (if exists):
						if ( $lists.find( 'option[value="' + selected  + '"]' ).length ) {
							$lists.val( selected  );
						} else {
							$lists.selectedIndex = 0;
						}

						$lists.hide().fadeIn();
						$refreshButton.prop( 'disabled', false );
					}
				} );

			} );

			this.$productTags = $( 'input#woocommerce_newsletter_product_tags' );

			this.toggleProductTagFormat( this.$productTags.prop( 'checked' ) );

			this.$productTags.on( 'change', function() {
				that.toggleProductTagFormat( $( this ).prop( 'checked' ) );
			});
		},

		toggleProductTagFormat: function( visible ) {
			$( 'input#woocommerce_newsletter_product_tag_format' ).closest( 'tr' ).toggle( visible );
		}
	};

	wcNewsletterSubscriptionSettings.init();
} )( jQuery, ajaxurl );
