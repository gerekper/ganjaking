jQuery( function( $ ){
	// product level
	$( 'body' ).on( 'change', 'select._wc_deposit_enabled', function () {
		const $context = $(this).closest('.options_group');
		const value = $(this).val();
		const type = $context.parent().data('type');

		$context.find( '._wc_deposit_payment_plans_field' ).hide();
		$context.find( '._wc_deposit_amount_field' ).hide();
		$context.find( '._wc_deposit_multiple_cost_by_booking_persons_field' ).hide();
		$context.find( '._wc_deposit_type_field' ).hide();

		if ( 'optional' === value || 'forced' === value ) {
			$context.find( '._wc_deposit_type_field' ).show();
			$context.find( 'select._wc_deposit_type' ).trigger( 'change' );
		}

		if ( '' === value && 'no' !== $context.find( '._wc_deposits_default_enabled_field' ).val() ) {
			$context.find( '._wc_deposit_type_field' ).show();
			$context.find( 'select._wc_deposit_type' ).trigger( 'change' );
		}
	} );

	// product level
	$( 'body' ).on( 'change', 'select._wc_deposit_type', function() {
		const $context = $(this).closest('.options_group');
		const value = $(this).val();
		
		$context.find( '._wc_deposit_payment_plans_field' ).hide();
		$context.find( '._wc_deposit_amount_field' ).hide();
		$context.find( '._wc_deposit_multiple_cost_by_booking_persons_field' ).hide();

		switch ( value ) {
			case 'percent':
				$context.find( '._wc_deposit_amount' ).attr( 'placeholder', $( '._wc_deposits_default_amount_field' ).val() );
				$context.find( '._wc_deposit_amount_field' ).show();
				break;

			case 'fixed':
				$context.find( '._wc_deposit_amount_field' ).show();
				$context.find( '._wc_deposit_amount' ).attr( 'placeholder', '0' );
				break;

			case 'plan':
				$context.find( '._wc_deposit_payment_plans_field' ).show();
				break;

			case '':
			default:
				var default_type = $context.find( '._wc_deposits_default_type_field' ).val();

				switch ( default_type ) {
					case 'plan':
						$context.find( '._wc_deposit_payment_plans_field' ).show();
						break;

					case 'percent':
						$context.find( '._wc_deposit_amount' ).attr( 'placeholder', $( '._wc_deposits_default_amount_field' ).val() );
						$context.find( '._wc_deposit_amount_field' ).show();
						break;

					case 'fixed':
						$context.find( '._wc_deposit_amount_field' ).show();
						$context.find( '._wc_deposit_amount' ).attr( 'placeholder', '0' );
						break;
				} // switch default_type

				break; // case '', default
		} // switch value

		if ( 'fixed' === value && 'booking' === $( '#product-type' ).val() ) {
			$context.find( '._wc_deposit_multiple_cost_by_booking_persons_field' ).show();
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

	$( document.body ).on( 'change', 'select._wc_deposit_selected_type', function() {
		var value = $( this ).val();
		const $context = $(this).closest('.options_group');
		// set the hidden element field to be saved
		$context.find( '._wc_deposits_default_selected_type_field' ).val( value );
	});

	const updateVariationPlansDescription = function() {
		const $variationPlansDescription = $('.woocommerce_variation_deposits ._wc_deposit_payment_plans_field .description em');
		const $productPlansSelector = $('#deposits ._wc_deposit_payment_plans');
		const selection = $productPlansSelector.val();
		const $productDepositType = $('#_wc_deposit_type');

		let plansString = $('#deposits ._wc_deposit_payment_plans_field .description em').text();

		if ( 'plan' === $productDepositType.val() && selection.length ) {
			const planNames = selection.map(function(id) {
				return $productPlansSelector.find(`option[value=${id}]`).text();
			});
			plansString = planNames.join(',');
		}

		$variationPlansDescription.text(plansString);
	}

	const initDeposits = function() {
		$( 'select._wc_deposit_type' ).trigger( 'change' );
		$( 'select._wc_deposit_enabled' ).trigger( 'change' );
		$( 'select._wc_deposit_selected_type' ).trigger( 'change' );
		$( 'select.wc_deposits_default_type' ).trigger( 'change' );

		var $paymentPlans = $( '._wc_deposit_payment_plans, #wc_deposits_default_plans' );

		// Set selected items order upon page load.
		$paymentPlans.each(function() {
			const $this = $( this );
			const plansData = $this.data('plans-order').toString();
			if ( plansData.indexOf( ',' ) > 0 ) {
				const preservedOrder = plansData.split( ',' );
				$.each( preservedOrder, function ( _, el ) {
					const option = $this.find( '[value=' + el + ']' );
					reorderOption( $this, option );
				} );
			}

			// Attach selected plans in order of selection.
			$this.on( 'select2:select', function( e ){
				var option = e.params.data.element;
				reorderOption( $this, option );
			});

			if ( $this.is('#_wc_deposit_payment_plans') ) {
				$this.on( 'change', function() {
					updateVariationPlansDescription();
				} );
			}

			$this.selectWoo({
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
		});

		updateVariationPlansDescription();
	}

	initDeposits();

	$(document).on('woocommerce_variations_loaded', initDeposits);

	$('#_wc_deposit_type').on( 'change', updateVariationPlansDescription)

	// Init default type select on admin settings page.
	$( 'select#wc_deposits_default_type' ).trigger( 'change' );

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
} );
