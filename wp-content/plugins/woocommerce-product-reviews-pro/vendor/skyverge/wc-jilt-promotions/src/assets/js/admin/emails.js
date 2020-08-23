jQuery( document ).ready( function( $ ) {

	// handle the "install" CTA
	$( '#sv-wc-jilt-emails-install-prompt .sv-wc-jilt-prompt-install-cta' ).click( function( event ) {

		event.preventDefault();

		new $.JiltPromotions.InstallPluginModal({
			messageID: sv_wc_jilt_email_prompt.prompt_id,
			target   : 'sv-wc-jilt-promotions-install-plugin-modal'
		});

	} );


	// handle the "hide" CTA
	$( '#sv-wc-jilt-emails-install-prompt .sv-wc-jilt-prompt-hide-cta' ).click( function( event ) {

		event.preventDefault();

		$table = $( this ).parents( 'table' );

		$table.hide();

		// hide the preceding h2 & description
		$table.prevUntil( 'table' ).hide();

		$.post(
			ajaxurl,
			{
				action: 'sv_wc_jilt_hide_emails_prompt',
				nonce:  sv_wc_jilt_email_prompt.nonces.hide_prompt,
			}
		);

	} );

} );
