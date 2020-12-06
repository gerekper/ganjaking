/**
 * Send credit scripts.
 *
 * @package WC_Store_Credit/Assets/Js/Admin
 * @since   3.0.1
 */

(function( $ ) {

	'use strict';

	$(function() {
		var wc_store_credit_send_credit = {
			init: function() {
				$( '[name="expiration"]' )
					.on( 'change', this.toggleExpirationFields )
					.trigger( 'change' );

				this.initDatepickers();
			},

			toggleExpirationFields: function( event ) {
				var value = 'expiration_' + event.target.value;

				$( '[id^="expiration_"]' ).each( function() {
					var $field  = $( this );

					$field
						.closest( 'tr' )
						.toggle( value === $field.attr( 'id' ) );
				});
			},

			initDatepickers: function() {
				$( '.date-picker' ).each( function() {
					var minDate = $( this ).attr('data-minDate' ),
						params = {
							dateFormat: 'yy-mm-dd',
							numberOfMonths: 1,
							showButtonPanel: true
						};

					if ( minDate ) {
						params.minDate = minDate;
					}

					$( this ).datepicker( params );
				});
			}
		};

		wc_store_credit_send_credit.init();
	});
})( jQuery );
