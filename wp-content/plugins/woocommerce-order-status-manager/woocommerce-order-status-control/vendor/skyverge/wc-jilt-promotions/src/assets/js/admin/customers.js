jQuery( document ).ready( function ($) {


	let shouldDisplayModal = true;

	// open a promotional modal when the merchant clicks the Download button in the Customers page
	$( document ).on( 'click', '.woocommerce-table__download-button', function ( event ) {

		if ( shouldDisplayModal && '/customers' === wc?.navigation?.getPath?.() ) {

			// mark message as enabled so that we don't enqueue the scripts to show the modal again
			$.JiltPromotions.Messages.enableMessage( sv_wc_jilt_prompt_customers.download_message_id );

			let onCloseEventName = 'sv_wc_jilt_prompt_customers_modal_close';

			new $.JiltPromotions.InstallPluginModal( {
				messageID: sv_wc_jilt_prompt_customers.download_message_id,
				onClose: onCloseEventName
			} );

			$( document ).on( onCloseEventName, function() {

				$.JiltPromotions.Messages.dismissMessage( sv_wc_jilt_prompt_customers.download_message_id );
			} );

			shouldDisplayModal = false;
		}
	} );


} );
