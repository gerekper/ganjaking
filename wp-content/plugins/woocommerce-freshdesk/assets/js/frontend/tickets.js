/* global woocommerce_freshdesk_params */
(function ( $ ) {
	'use strict';

	$(function () {
		var ticket_form = $( '#wc-freshdesk-ticket-form' );

		function block_ui( msg ) {
			if ( ! msg ) {
				return;
			}

			$.blockUI({
				message: msg,
				baseZ: 99999,
				overlayCSS: {
					background: '#fff',
					opacity:    0.6
				},
				css: {
					padding:         '20px',
					zindex:          '9999999',
					textAlign:       'center',
					color:           '#555',
					border:          '3px solid #aaa',
					backgroundColor: '#fff',
					cursor:          'wait',
					lineHeight:      '24px'
				}
			});
		}

		ticket_form.on( 'submit', function ( e ) {
			e.preventDefault();

			block_ui( woocommerce_freshdesk_params.processing );

			var ticket_data = {
				action:   'wc_freshdesk_process_tickets',
				security: woocommerce_freshdesk_params.security
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
				url: woocommerce_freshdesk_params.ajax_url,
				cache: false,
				dataType: 'json',
				data: ticket_data,
				success: function ( data ) {
					// Remove the blockUI.
					$.unblockUI();

					if ( null !== data && 1 === data.status ) {
						// Remove the form and show a success message.
						ticket_form
							.empty()
							.prepend( '<div class="woocommerce-message">' + woocommerce_freshdesk_params.success + '</div>' );
					} else {
						var error_message = woocommerce_freshdesk_params.error;
						if ( null !== data && null !== data.message && '' !== data.message ) {
							error_message = data.message;
						}

						$( '.woocommerce-error', ticket_form ).remove();
						ticket_form
							.prepend( '<div class="woocommerce-error">' + error_message + '</div>' );
					}
				},
				error: function () {
					$.unblockUI();

					$( '.woocommerce-error', ticket_form ).remove();
					ticket_form
						.prepend( '<div class="woocommerce-error">' + woocommerce_freshdesk_params.error + '</div>' );
				}
			});
		});
	});

}( jQuery ));
