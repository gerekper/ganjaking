jQuery( function($){

	// Hide the "Grouping" field.
	$( '#linked_product_data .grouping.show_if_simple, #linked_product_data .form-field.show_if_grouped' ).addClass( 'hide_if_mix-and-match' );

	// Simple type options are valid for mnm.
	$( '.show_if_simple:not(.hide_if_mix-and-match)' ).addClass( 'show_if_mix-and-match' );

	// Mix and Match type specific options
	$( 'body' ).on( 'woocommerce-product-type-change', function( event, select_val ) {

		if ( select_val === 'mix-and-match' ) {

			$( '.show_if_external' ).hide();
			$( '.show_if_mix-and-match' ).show();

			$( 'input#_manage_stock' ).change();

		}

	} );

	// Trigger product type change.
	$( 'select#product-type' ).change();

	// Hide/Show Per-Item related fields.
	$( '#_mnm_per_product_pricing' ).change( function() {

		if( 'mix-and-match' === $( 'select#product-type' ).val() ) {

			var $nyp = $( '#_nyp' ).closest( 'label' ).hide();
			var $dependents = $(this).closest('#mnm_product_data').find('.show_if_per_item_pricing');

			if( $( this ).prop( 'checked' ) ) {
				$nyp.hide();
				$dependents.slideDown();
			} else {
				$nyp.show();
				$dependents.slideUp();
			}

		}

	} ).change();

} );
