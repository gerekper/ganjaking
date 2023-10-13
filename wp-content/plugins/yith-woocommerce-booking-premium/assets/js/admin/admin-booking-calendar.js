jQuery( function ( $ ) {
	"use strict";

	var all_bookings        = $( '.yith-wcbk-booking-calendar-single-booking' ),
		booking_hover_class = 'yith-wcbk-hover',
		reset_all_bookings  = function () {
			all_bookings.removeClass( booking_hover_class );
		},
		fast_search_input   = $( '#yith-wcbk-booking-calendar-fast-search' ),
		all_booking_datas   = $( '.yith-wcbk-booking-calendar-single-booking-data' ),
		calendarWrap        = $( '.yith-wcbk-booking-calendar' ).first(),
		wpAdminBar          = $( '#wpadminbar' );

	$( document )
		.on( 'mouseenter', '.yith-wcbk-booking-calendar-single-booking', function ( e ) {
			var booking_container = $( this ),
				booking_class     = booking_container.data( 'booking-class' );

			reset_all_bookings();

			if ( booking_class ) {
				$( document ).find( '.' + booking_class ).addClass( booking_hover_class );
			}
		} )

		.on( 'mouseleave', '.yith-wcbk-booking-calendar-single-booking', reset_all_bookings )

		.on( 'click', '.yith-wcbk-booking-calendar-single-booking .yith-wcbk-booking-calendar-single-booking-title', function () {
			var booking                = $( this ).closest( '.yith-wcbk-booking-calendar-single-booking' ),
				booking_data_container = booking.find( '.yith-wcbk-booking-calendar-single-booking-data' ),
				is_open                = booking_data_container.is( '.open' );

			if ( is_open ) {
				booking_data_container.trigger( 'close' );
			} else {
				all_booking_datas.trigger( 'hide' );
				booking_data_container.trigger( 'open' );
			}
		} )

		.on( 'click', '.yith-wcbk-booking-calendar-single-booking-data-action-close', function () {
			var booking                = $( this ).closest( '.yith-wcbk-booking-calendar-single-booking' ),
				booking_data_container = booking.find( '.yith-wcbk-booking-calendar-single-booking-data' );
			booking_data_container.trigger( 'close' );
		} )

		.on( 'close', '.yith-wcbk-booking-calendar-single-booking-data', function () {
			$( this ).removeClass( 'open' ).fadeOut( 300 );
		} )

		.on( 'hide', '.yith-wcbk-booking-calendar-single-booking-data', function () {
			$( this ).removeClass( 'open' ).hide();
		} )

		.on( 'open updatePosition', '.yith-wcbk-booking-calendar-single-booking-data', function () {
			var wrap                  = $( this ).parent(),
				wrapOffset            = wrap.offset(),
				calendarWrapperOffset = calendarWrap.offset(),
				spacing               = 15,
				maxTopWindow          = window.innerHeight - $( this ).outerHeight() - spacing,
				maxTopWrapper         = calendarWrapperOffset.top + calendarWrap.outerHeight() - window.scrollY - $( this ).outerHeight() - spacing,
				maxTop                = Math.min( maxTopWindow, maxTopWrapper ),
				maxLeftWindow         = window.innerWidth - $( this ).outerWidth() - spacing,
				maxLeftWrapper        = calendarWrapperOffset.left + calendarWrap.outerWidth() - window.scrollX - $( this ).outerWidth() - spacing,
				maxLeft               = Math.min( maxLeftWindow, maxLeftWrapper ),
				minTop                = Math.max( calendarWrapperOffset.top - window.scrollY + spacing, wpAdminBar.outerHeight() + spacing ),
				minLeft               = Math.max( calendarWrapperOffset.left - window.scrollX + spacing, spacing );

			wrapOffset.top -= window.scrollY;
			wrapOffset.left -= window.scrollX;

			var top  = wrapOffset.top,
				left = wrapOffset.left + wrap.outerWidth() + spacing;

			if ( left > maxLeft ) {
				// Show on the left.
				left = wrapOffset.left - $( this ).outerWidth() - spacing;
			}

			top  = Math.max( Math.min( top, maxTop ), minTop );
			left = Math.max( Math.min( left, maxLeft ), minLeft );

			$( this ).css(
				{
					position: 'fixed',
					top     : top,
					left    : left
				}
			);

			$( this ).addClass( 'open' ).fadeIn( 300 );
		} )

		.on( 'click', function ( event ) {
			var booking = $( event.target ).closest( '.yith-wcbk-booking-calendar-single-booking' );
			if ( booking.length <= 0 ) {
				all_booking_datas.trigger( 'close' );
			}
		} );

	$( window ).on( 'scroll resize', function () {
		var openedData = $( '.yith-wcbk-booking-calendar-single-booking-data.open' );
		if ( openedData.length ) {
			openedData.trigger( 'updatePosition' );
		}
	} );

	// FAST SEARCH
	fast_search_input.on( 'keyup', function () {
		var search_value = $( this ).val();

		if ( search_value.length > 2 ) {

			search_value = search_value.toLowerCase();

			all_bookings.each( function ( e ) {
				var target = $( this ),
					text   = target.html().toLowerCase();

				if ( text.indexOf( search_value ) > -1 ) {
					$( this ).fadeIn();
				} else {
					$( this ).fadeOut();
				}
			} );
		} else {
			all_bookings.fadeIn();
		}
	} );

	$( document ).on( 'change', '.yith-wcbk-booking-calendar__select-view', function () {
		window.location.href = $( this ).val();
	} );

	$( document ).on( 'click', '.yith-wcbk-booking-calendar-single-booking, .yith-wcbk-booking-calendar-day__number', function ( e ) {
		e.stopPropagation();
	} );
} );
