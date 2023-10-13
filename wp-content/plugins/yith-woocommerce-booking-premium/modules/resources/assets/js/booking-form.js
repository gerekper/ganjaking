/* global jQuery, yith_booking */
( function ( $ ) {

	var getResourcesInForm = function ( theForm ) {
		var data         = theForm.serializeArray(),
			filteredData = data.filter( function ( item ) {
				return item.name === 'resource_ids[]';
			} );
		return filteredData.map(
			function ( item ) {
				return parseInt( item.value, 10 );
			}
		);
	};

	$( document ).on( 'change', '.yith-wcbk-booking-resource select', function ( e ) {
		var form      = $( this ).closest( '.yith-wcbk-booking-form' ),
			theForm   = form.closest( 'form' ),
			productID = form.data( 'product-id' ),
			resources = getResourcesInForm( theForm ),
			startDate = $( '.yith-wcbk-date-picker.yith-wcbk-booking-start-date' );

		yith_booking.ajax(
			{
				request   : 'resources_get_booking_availability',
				product_id: productID,
				resources : resources
			},
			{
				block: form
			}
		).done( function ( response ) {
			if ( response.success && response.data ) {
				var dateInfo          = response.data.date_info || {},
					nonAvailableDates = response.data.non_available_dates || [];
				if ( dateInfo.next_month ) {
					startDate.data( 'month-to-load', dateInfo.next_month );
				}

				if ( dateInfo.next_year ) {
					startDate.data( 'year-to-load', dateInfo.next_year );
				}

				if ( dateInfo.loaded_months ) {
					startDate.data( 'loaded-months', dateInfo.loaded_months );
				}

				startDate.data( 'not-available-dates', nonAvailableDates );

				// Refresh the datepicker: needed to update non-available dates if the datepicker is shown inline in the page.
				startDate.datepicker( 'refresh' );

				form.trigger( 'yith_wcbk_booking_form_update_time_slots' );
			}
		} );
	} );

	$( function () {
		$( '.yith-wcbk-booking-resource select' ).each( function () {
			if ( $( this ).val() ) {
				$( this ).trigger( 'change' );
			}
		} );
	} );
} )( jQuery );