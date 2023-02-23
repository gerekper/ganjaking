jQuery( function( $ ) {
	var depositsFormOriginal = $( '.wc-deposits-wrapper' );

	$( document )
		.on( 'change', '.wc-deposits-wrapper input[name="wc_deposit_option"]', function() {
			$deposits = $( this ).closest( '.wc-deposits-wrapper' );
			if ( 'yes' === $(this).val() ) {
				$deposits.find( '.wc-deposits-payment-plans, .wc-deposits-payment-description' ).slideDown( 200 );
			} else {
				$deposits.find( '.wc-deposits-payment-plans, .wc-deposits-payment-description' ).slideUp( 200 );
			}
		});
		
	$deposits = $(this).closest( '.wc-deposits-wrapper' );
	
	if ( 'yes' === $( 'input[name="wc_deposit_option"]' ).val() ) {
		$deposits.find( '.wc-deposits-payment-plans, .wc-deposits-payment-description' ).slideDown( 200 );
	}

	$( '.variations_form' )
		.on( 'show_variation', function( event, variation, purchasable ) {
			$('.wc-deposits-wrapper').replaceWith(variation.deposits_form);
			$( document ).find( 'input[name="wc_deposit_payment_plan"]' ).first().trigger( 'change' );
		} )
		.on( 'hide_variation', function() {
			$('.wc-deposits-wrapper').replaceWith(depositsFormOriginal);
		} );

	$( document ).on( 'change', 'input[name="wc_deposit_payment_plan"]', function() {
		const selectedPlan = $( document ).find( 'input[name="wc_deposit_payment_plan"]:checked' ).first();
		if ( selectedPlan.length > 0 ) {
			const planId = selectedPlan.val();
			$( '.wc-deposits-payment-description p' ).hide();
			$( '#payment-plan-description-' + planId ).show();
		}
	} );
	$( document ).find( 'input[name="wc_deposit_payment_plan"]' ).first().trigger( 'change' );
});
