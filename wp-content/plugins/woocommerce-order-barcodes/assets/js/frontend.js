jQuery( document ).ready( function () {

	// Focus on barcode input field
	jQuery( '#barcode-scan-form input#scan-code' ).focus( function() {
		jQuery( this ).select();
	});

	// Detect if USB scanner has been used and submit automatically
	jQuery( '#barcode-scan-form input#scan-code' ).scannerDetection( function() {
		jQuery( '#barcode-scan-form form' ).submit();
	});

	// Handle form submission
	jQuery( '#barcode-scan-form form' ).submit( function(e) {
		e.preventDefault();

		// Show loading text
		jQuery( '#barcode-scan-loader' ).show();

		// Empty existing results
		jQuery( '#barcode-scan-result' ).html('');

		var input = jQuery( '#barcode-scan-form input#scan-code' ).val();
		var input_action = jQuery( '#barcode-scan-form #scan-action' ).val();

		jQuery.post(
			wc_order_barcodes.ajaxurl,
			{
				action: 'scan_barcode',
				barcode_input: input,
				scan_action: input_action,
				woocommerce_order_barcodes_scan_nonce: wc_order_barcodes.scan_nonce
			}
		).done( function( response ) {

			// Focus on barcode input field
			jQuery( '#barcode-scan-form input#scan-code' ).focus();

			if( ! response ) {
				return;
			}

			// Hide loading text
			jQuery( '#barcode-scan-loader' ).hide();

			// Display response
			jQuery( '#barcode-scan-result' ).html( response );

		});

	});

});