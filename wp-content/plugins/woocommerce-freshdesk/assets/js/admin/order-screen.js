/* global woocommerce_admin_meta_boxes, woocommerce_freshdesk_shop_order_params */
(function ( $ ) {
	'use strict';

	$(function () {
		// Order notes
		$( '#freshdesk-tickets' ).on( 'click', '#open-ticket', function ( e ) {
			e.preventDefault();

			$( '#freshdesk-tickets' ).block({
				message: null,
				overlayCSS: {
					background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center',
					opacity: 0.6
				}
			});

			var ticket_fields = $( '#freshdesk-tickets #order-tickets-fields' ),
				ticket_data = {
				action:   'wc_freshdesk_process_tickets',
				order_id: woocommerce_admin_meta_boxes.post_id,
				security: woocommerce_freshdesk_shop_order_params.security
			};

			// Takes the data from the form dynamically.
			// This way you can send the customized form data.
			$( '.ticket-field' ).each( function () {
				var current = $( this ),
					key     = current.attr( 'name' ).replace( '-', '_' ).replace( 'ticket_', '' ),
					value   = current.val();

				ticket_data[ key ] = value;
			});

			$.ajax({
				type: 'POST',
				url: woocommerce_freshdesk_shop_order_params.ajax_url,
				cache: false,
				dataType: 'json',
				data: ticket_data,
				success: function ( data ) {
					$( '#freshdesk-tickets' ).unblock();
					var ticket_url    = '',
						error_message = '';

					if ( null !== data && 1 === data.status ) {
						// Remove the form and show a success message.
						ticket_url = '<a href="' + woocommerce_freshdesk_shop_order_params.ticket_url + data.id + '">' + woocommerce_freshdesk_shop_order_params.view_ticket + '</a>';
						ticket_fields
							.empty()
							.prepend( '<div class="ticket-message updated inline"><p>' + woocommerce_freshdesk_shop_order_params.success + ' ' + ticket_url + '</p></div>' );
					} else {
						error_message = woocommerce_freshdesk_shop_order_params.error;
						if ( null !== data && null !== data.message && '' !== data.message ) {
							error_message = data.message;
						}

						$( '.ticket-error', ticket_fields ).remove();
						ticket_fields
							.prepend( '<div class="ticket-error error inline"><p>' + error_message + '</p></div>' );
					}
				},
				error: function () {
					$( '#freshdesk-tickets' ).unblock();

					$( '.ticket-error', ticket_fields ).remove();
					ticket_fields
						.prepend( '<div class="ticket-error error inline"><p>' + woocommerce_freshdesk_shop_order_params.error + '</p></div>' );
				}
			});
		});
	});

}( jQuery ));
