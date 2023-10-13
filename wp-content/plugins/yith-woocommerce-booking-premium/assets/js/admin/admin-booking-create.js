/* global jQuery, yith, wcbk_admin, bk, ajaxurl, wp */
( function ( $ ) {
	var template               = wp.template( 'yith-wcbk-create-booking' ),
		blockParams            = bk.blockParams,
		panelPage              = wcbk_admin.panelPage,
		isBookingsCalendarPage = 'panel/dashboard/bookings-calendar' === panelPage,
		startDate, startTime;

	$( document ).on( 'click', '.yith-wcbk-create-booking', function ( e ) {
		e.preventDefault();
		var content = $( template( {} ) ),
			cancel  = content.find( '.yith-wcbk-create-booking__cancel' ),
			modal;

		startDate = $( this ).data( 'create-start-date' );
		startTime = $( this ).data( 'create-start-time' );

		modal = yith.ui.modal(
			{
				title                     : wcbk_admin.i18n.create_booking,
				content                   : content,
				classes                   : {
					wrap: 'yith-wcbk-create-booking-modal'
				},
				scrollContent             : false,
				width                     : '100%',
				closeWhenClickingOnOverlay: true,
				onClose                   : function () {
					startDate = '';
					startTime = '';
				}
			}
		);

		cancel.on( 'click', modal.close );

		$( document.body ).trigger( 'yith-framework-enhanced-select-init' );
		$( document.body ).trigger( 'wc-enhanced-select-init' );

		content.find( '#yith-wcbk-create-booking__assign-order' ).trigger( 'change' );
		content.find( '#yith-wcbk-create-booking__product-id' ).trigger( 'change' );
	} );

	$( document ).on( 'change', '#yith-wcbk-create-booking__product-id', function () {
		var wrapper            = $( this ).closest( '.yith-wcbk-create-booking__wrapper' ),
			bookingFormWrapper = wrapper.find( '.yith-wcbk-create-booking__booking-form' ),
			product_id         = $( this ).val(),
			post_data          = {
				product_id     : product_id,
				action         : 'yith_wcbk_get_product_booking_form',
				security       : wcbk_admin.nonces.get_booking_form,
				show_price     : true,
				bk_context     : 'create_booking',
				bk_page        : panelPage,
				additional_data: {
					bk_context   : 'create_booking',
					bk_page      : panelPage,
					bk_page_nonce: wcbk_admin.nonces.panelPageNonce
				}
			};

		if ( product_id ) {
			bookingFormWrapper.block( blockParams );

			$.ajax( {
						type    : "POST",
						data    : post_data,
						url     : ajaxurl,
						success : function ( response ) {
							bookingFormWrapper.html( response );
							if ( isBookingsCalendarPage ) {
								if ( startDate ) {
									var startDatePicker = bookingFormWrapper.find( '.yith-wcbk-booking-start-date.yith-wcbk-date-picker' );
									startDatePicker.val( startDate );
								}

								if ( startTime ) {
									var startTimeInput = bookingFormWrapper.find( '.yith-wcbk-booking-start-date-time' );
									if ( startTimeInput.length ) {
										startTimeInput.append( $( '<option></option>' ).attr( 'key', startTime ).html( startTime ) );
										startTimeInput.val( startTime );
									}
								}
							}

							$( document.body ).trigger( 'yith-wcbk-init-booking-form' );
							$( document ).trigger( 'yith-wcbk-init-fields:help-tip' );
							$( document ).trigger( 'yith-wcbk-init-fields:selector' );
							$( document ).trigger( 'yith-wcbk-init-fields:select-list' );
						},
						complete: function () {
							bookingFormWrapper.unblock();
						}
					} );
		}
	} );

	$( document ).on( 'change', '#yith-wcbk-create-booking__assign-order', function () {
		var assignOrder  = $( this ),
			optionsTable = assignOrder.closest( '.yith-wcbk-create-booking__options' ),
			orderIdRow   = optionsTable.find( '.yith-wcbk-create-booking__order-id__row' );

		if ( 'specific' === assignOrder.val() ) {
			orderIdRow.show();
		} else {
			orderIdRow.hide();
		}
	} );

} )( jQuery );