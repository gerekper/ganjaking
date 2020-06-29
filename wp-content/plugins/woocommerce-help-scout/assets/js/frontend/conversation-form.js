/* global woocommerce_help_scout_form_params */
(function ( $ ) {
	'use strict';

	$(function () {
		var conversation_form = $( '#wc-help-scout-conversation-form' );

		/**
		 * Create a conversation.
		 */
		conversation_form.on( 'submit', function ( e ) {
			e.preventDefault();

			$.blockUI({
				message: woocommerce_help_scout_form_params.processing,
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

			var conversation_data = {
				action:   'wc_help_scout_create_conversation',
				security: woocommerce_help_scout_form_params.security
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
				url: woocommerce_help_scout_form_params.ajax_url,
				cache: false,
				dataType: 'json',
				data: conversation_data,
				success: function ( data ) {
					// Remove the blockUI.
					$.unblockUI();

					if ( null !== data && 1 === data.status ) {
						// Remove the form and show a success message.
						conversation_form.empty().prepend( '<div class="woocommerce-message">' + woocommerce_help_scout_form_params.success + '</div>' );
					} else {
						var error_message = woocommerce_help_scout_form_params.error;
						if ( data && data.status ) {
							error_message = data.status;
						}

						$( '.woocommerce-error', conversation_form ).remove();
						conversation_form.prepend( '<div class="woocommerce-error">' + error_message + '</div>' );
					}
				},
				error: function () {
					$.unblockUI();

					$( '.woocommerce-error', conversation_form ).remove();
					conversation_form.prepend( '<div class="woocommerce-error">' + woocommerce_help_scout_form_params.error + '</div>' );
				}
			});
		});
	});

}( jQuery ));
