jQuery( function( $ ){
	// product level
	$( 'body' ).on( 'change', 'select#_wc_deposit_enabled', function () {
		$( '._wc_deposit_payment_plans_field' ).hide();
		$( '._wc_deposit_amount_field' ).hide();
		$( '._wc_deposit_multiple_cost_by_booking_persons_field' ).hide();
		$( '._wc_deposit_type_field' ).hide();

		if ( 'optional' === $(this).val() || 'forced' == $(this).val() ) {
			$( '._wc_deposit_type_field' ).show();
			$( 'select#_wc_deposit_type' ).trigger( 'change' );
		}

		if ( '' === $(this).val() && 'no' !== $( '._wc_deposits_default_enabled_field' ).val() ) {
			$( '._wc_deposit_type_field' ).show();
			$( 'select#_wc_deposit_type' ).trigger( 'change' );
		}
	} );

	// product level
	$( 'body' ).on( 'change', 'select#_wc_deposit_type', function() {
		$( '._wc_deposit_payment_plans_field' ).hide();
		$( '._wc_deposit_amount_field' ).hide();
		$( '._wc_deposit_multiple_cost_by_booking_persons_field' ).hide();

		if ( 'percent' === $(this).val() )  {
			$( '#_wc_deposit_amount' ).attr( 'placeholder', $( '._wc_deposits_default_amount_field' ).val() );
			$( '._wc_deposit_amount_field' ).show();
		} else if ( 'fixed' === $(this).val() ) {
			$( '._wc_deposit_amount_field' ).show();
			$( '#_wc_deposit_amount' ).attr( 'placeholder', '0' );
		} else if ( 'plan' === $(this).val() ) {
			$( '._wc_deposit_payment_plans_field' ).show();
		} else if ( '' === $(this).val() ) {
			var default_type = $( '._wc_deposits_default_type_field' ).val();
			if ( 'plan' === default_type ) {
				$( '._wc_deposit_payment_plans_field' ).show();
			} else if ( 'percent' === default_type ) {
				$( '#_wc_deposit_amount' ).attr( 'placeholder', $( '._wc_deposits_default_amount_field' ).val() );
				$( '._wc_deposit_amount_field' ).show();
			} else if ( 'fixed' === default_type ) {
				$( '._wc_deposit_amount_field' ).show();
				$( '#_wc_deposit_amount' ).attr( 'placeholder', '0' );
			}
		}

		if ( 'fixed' === $(this).val() && 'booking' === $( '#product-type' ).val() ) {
			$( '._wc_deposit_multiple_cost_by_booking_persons_field' ).show();
		}
	} );

	// storewide level
	$( document.body ).on( 'change', 'select#wc_deposits_default_type', function() {
		$( '#wc_deposits_default_plans' ).parents( 'tr' ).eq(0).hide();
		$( '#wc_deposits_default_amount' ).parents( 'tr' ).eq(0).hide();

		switch ( $( this ).val() ) {
			case 'percent':
				$( '#wc_deposits_default_amount' ).parents( 'tr' ).eq(0).show();
				break;

			case 'fixed':
				$( '#wc_deposits_default_amount' ).parents( 'tr' ).eq(0).show();
				break;

			case 'plan':
				$( '#wc_deposits_default_plans' ).parents( 'tr' ).eq(0).show();
				break;
		}
	});

	$( document.body ).on( 'change', 'select#_wc_deposit_selected_type', function() {
		var value = $( this ).val();

		// set the hidden element field to be saved
		$( '._wc_deposits_default_selected_type_field' ).val( value );
	});

	$( 'select#_wc_deposit_type' ).trigger( 'change' );
	$( 'select#_wc_deposit_enabled' ).trigger( 'change' );
	$( 'select#_wc_deposit_selected_type' ).trigger( 'change' );
	$( 'select#wc_deposits_default_type' ).trigger( 'change' );

	var $paymentPlans = $( '#_wc_deposit_payment_plans, #wc_deposits_default_plans' );

	/**
	 * Change option position in the selectWoo element
	 * @param {jQuery} $select 
	 * @param {HTMLOptionElement} option 
	 */
	function reorderOption( $select, option ) {
		var $option = $( option );
		$option.detach();
		$select.append( $option );
		$select.trigger( 'change' );
	}

	// Set selected items order upon page load.
	var plansData = $paymentPlans.data('plans-order').toString();
	if ( plansData.indexOf( ',' ) > 0 ) {
		var preservedOrder = plansData.split( ',' );
		$.each( preservedOrder, function ( _, el ) {
			var option = $paymentPlans.find( '[value=' + el + ']' );
			reorderOption( $paymentPlans, option );
		} );
	}

	// Attach selected plans in order of selection.
	$paymentPlans.on( 'select2:select', function( e ){
		var option = e.params.data.element;
		reorderOption( $( this ), option );
	});
 
	$paymentPlans.selectWoo({
		// Sort dropdown elements in the initial order.
		sorter: function( data ) {
			return data.sort( function( a, b ) {
				if ( a.id < b.id ) {
					return -1;
				} else if ( a.id > b.id ) {
					return 1;
				} else {
					return 0;
				}
			} );
		}
	});
} );
