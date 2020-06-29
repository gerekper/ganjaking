jQuery( document ).ready( function ($) {

	if( $( '#ticket-print-content-container' ).length ) {

		var ticket = $( '#ticket-print-content-container' ).html();

		$( 'body' ).empty();
		$( 'body' ).html( ticket );

		imagesLoaded( '#ticket-print-content', function() {
			window.print();
		} );
	}

	if( $( '#ticket-scan-form' ).length ) {

		// Focus on barcode input field
		$( '#ticket-scan-form input#scan-code' ).focus( function() {
			$( this ).select();
		});

		// Detect if USB scanner has been used and submit automatically
		$( '#ticket-scan-form input#scan-code' ).scannerDetection( function() {
			$( '#ticket-scan-form form' ).submit();
		});

		// Handle form submission
		$( '#ticket-scan-form form' ).submit( function(e) {
			e.preventDefault();

			// Show loading text
			$( '#ticket-scan-loader' ).show();

			// Empty existing results
			$( '#ticket-scan-result' ).html('');

			var input = $( '#ticket-scan-form input#scan-code' ).val();
			var input_action = $( '#ticket-scan-form #scan-action' ).val();

			$.post(
				wc_order_barcodes.ajaxurl,
				{
					action: 'scan_ticket',
					barcode_input: input,
					scan_action: input_action,
					woocommerce_box_office_scan_nonce: wc_box_office.scan_nonce
				}
			).done( function( response ) {

				// Focus on ticket barcode input field
				$( '#ticket-scan-form input#scan-code' ).focus();

				if( ! response ) {
					return;
				}

				// Hide loading text
				$( '#ticket-scan-loader' ).hide();

				// Display response
				$( '#ticket-scan-result' ).html( response );

			});

		});
	}

});
