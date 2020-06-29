/* global woocommerce_admin_meta_boxes, woocommerce_help_scout_shop_order_params */
(function ( $ ) {
	'use strict';

	$(function () {
		// Order notes
		$( '#help-scout-conversation' ).on( 'click', '#open-conversation', function ( e ) {
			e.preventDefault();

			$( '#help-scout-conversation' ).block({
				message: null,
				overlayCSS: {
					background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center',
					opacity: 0.6
				}
			});

			var conversation_fields = $( '#help-scout-conversation #conversation-fields' ),
				conversation_data = {
				action:   'wc_help_scout_create_conversation',
				order_id: woocommerce_admin_meta_boxes.post_id,
				security: woocommerce_help_scout_shop_order_params.security
			};

			// Takes the data from the form dynamically.
			// This way you can send the customized form data.
			$( '.conversation-field' ).each( function () {
				var current = $( this ),
					key     = current.attr( 'name' ).replace( '-', '_' ).replace( 'conversation_', '' ),
					value   = current.val();

				conversation_data[ key ] = value;
			});

			$.ajax({
				type: 'POST',
				url: woocommerce_help_scout_shop_order_params.ajax_url,
				cache: false,
				dataType: 'json',
				data: conversation_data,
				success: function ( data ) {
					$( '#help-scout-conversation' ).unblock();
					var conversation_url    = '',
						error_message = '';

					if ( null !== data && 1 === data.status ) {
						// Remove the form and show a success message.
						conversation_url = '<a href="' + woocommerce_help_scout_shop_order_params.admin_url + 'conversation/' + data.id + '/' + data.number + '/">' + woocommerce_help_scout_shop_order_params.view + '</a>';
						conversation_fields
							.empty()
							.prepend( '<div class="conversation-message updated inline"><p>' + woocommerce_help_scout_shop_order_params.success + ' ' + conversation_url + '</p></div>' );
					} else {
						error_message = woocommerce_help_scout_shop_order_params.error;
						if ( null !== data && null !== data.status && '' !== data.status ) {
							error_message = data.status;
						}

						$( '.conversation-error', conversation_fields ).remove();
						conversation_fields
							.prepend( '<div class="conversation-error error inline"><p>' + error_message + '</p></div>' );
					}
				},
				error: function () {
					$( '#help-scout-conversation' ).unblock();

					$( '.conversation-error', conversation_fields ).remove();
					conversation_fields
						.prepend( '<div class="conversation-error error inline"><p>' + woocommerce_help_scout_shop_order_params.error + '</p></div>' );
				}
			});
		});
	});

}( jQuery ));

