jQuery( function( $ ) {
	$( '.wc-deposits-wrapper' )
		.on( 'change', 'input[name="wc_deposit_option"]', function() {
			$deposits = $( this ).closest( '.wc-deposits-wrapper' );
			if ( 'yes' === $(this).val() ) {
				$deposits.find( '.wc-deposits-payment-plans, .wc-deposits-payment-description' ).slideDown( 200 );
			} else {
				$deposits.find( '.wc-deposits-payment-plans, .wc-deposits-payment-description' ).slideUp( 200 );
			}
		});
		
		$( document ).ready( function() {
			$deposits = $(this).closest( '.wc-deposits-wrapper' );
			
			if ( 'yes' === $( 'input[name="wc_deposit_option"]' ).val() ) {
				$deposits.find( '.wc-deposits-payment-plans, .wc-deposits-payment-description' ).slideDown( 200 );
			}	
		});
});
