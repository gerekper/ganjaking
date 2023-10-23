jQuery( function ($) {
	var fromDatepicker                = $( '#_yith_wcbm_badge_from_date' ),
		toDatepicker                  = $( '#_yith_wcbm_badge_to_date' ),
		handleScheduleInputVisibility = function (element) {
			var $onOff  = element ? $( element ) : $( this ),
				onOffID = $onOff.prop( 'id' );
			if ( onOffID && !! onOffID.match( /yith-wcbm-badge-options-.*[0-9]-schedule/g ).length ) {
				var $scheduleContainer = $onOff.closest( '.yith-wcbm-variation-badge-options' ).find( '.yith-wcbm-variation-field-row__schedule-from, .yith-wcbm-variation-field-row__schedule-to' );
				if ( $onOff.is( ':checked' ) ) {
					$scheduleContainer.show();
				} else {
					$scheduleContainer.hide();
				}
			}
		},
		initVariations                = function () {
			$( document ).trigger( 'yith_fields_init' );
			$( '.yith-wcbm-variation-field-row__schedule input' ).each( function (index, el) {handleScheduleInputVisibility( el );} );
		};

	// Datepicker
	fromDatepicker.on( 'change', function () {
		toDatepicker.datepicker( 'option', 'minDate', fromDatepicker.val() );
	} );

	$( document.body ).on( 'woocommerce_variations_added', initVariations );
	$( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', initVariations );
	$( document ).on( 'change', '.yith-wcbm-variation-field-row__schedule input', handleScheduleInputVisibility );

	$( '#the-list' ).on( 'click', '.editinline', function () {
		var row      = $( this ).closest( 'tr' ),
			badgeIDs = row.find( 'input[type="hidden"].yith-wcbm-product-badges' ).val(),
			select   = $( '#' + (row.attr( 'id' ).replace( 'post', 'edit' )) ).find( '#yith_wcbm_quick_badge_ids' ).addClass( 'yith-post-search' );
		if ( badgeIDs ) {
			badgeIDs = JSON.parse( badgeIDs );
			for ( var badgeID in badgeIDs ) {
				select.append( '<option value="' + badgeID + '" selected>' + badgeIDs[ badgeID ] + '</option>' );
			}
		}
		$( document.body ).trigger( 'yith-framework-enhanced-select-init' );
	} );

} );
