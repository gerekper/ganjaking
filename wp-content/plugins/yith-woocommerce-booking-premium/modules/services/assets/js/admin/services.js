/* globals jQuery, pagenow, adminpage */
( function ( $ ) {

	if ( 'edit-yith_booking_service' === pagenow ) {

		if ( 'edit-tags-php' === adminpage ) {
			// Services List Table.
			var form       = $( '#addtag' ),
				submit_btn = form.find( '#submit' );

			$( document ).on( 'yith_wcbk_service_taxonomy_form_reset', function () {
				var numbers  = form.find( '.form-field input[type=number]' ),
					checkbox = form.find( '.form-field input[type=checkbox]' );

				numbers.val( '' );
				checkbox.prop( 'checked', false );
			} );

			$( function () {
				submit_btn.on( 'click', function () {
					$( document ).trigger( 'yith_wcbk_service_taxonomy_form_reset' );
				} );
			} );

			var blankState      = $( '.yith-plugin-fw__list-table-blank-state' ),
				blankStateStyle = $( '#yith-wcbk-blank-state-style' ),
				tableBody       = $( '#posts-filter .wp-list-table #the-list' );

			if ( blankState.length && blankStateStyle.length && tableBody.length ) {

				if ( typeof MutationObserver !== 'undefined' ) {
					var removeBlankState = function () {
							blankState.remove();
							blankStateStyle.remove();
							observer.disconnect();
						},
						observer         = new MutationObserver( removeBlankState );

					observer.observe( tableBody.get( 0 ), { childList: true } );
				} else {
					var removed = false;
					submit_btn.on( 'click', function () {
						if ( !removed ) {
							blankState.remove();
							blankStateStyle.remove();
							removed = true;
						}
					} );
				}
			}
		}

		if ( 'term-php' === adminpage ) {
			// Edit service.
			$( 'form#edittag table.form-table' ).addClass( 'yith-plugin-ui' );

			$( document ).on( 'click', '.yith-wcbk-booking-service-form-section-checkbox span.description', function () {
				$( this ).siblings( 'input[type=checkbox]' ).first().trigger( 'click' );
			} );
		}

	}
} )( jQuery );
