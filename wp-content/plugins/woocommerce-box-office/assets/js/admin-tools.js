/* globals jQuery, wcBoxOfficeParams */
( function( $ ) {

	var self = $.extend(
		{
			emailSubjectSelector: '#email-subject',
			emailPreviewButtonSelector: '#preview-email',
			emailBodyIframeSelector: '#ticket_email_editor_ifr',
			productId: '#product-id',
			emailPreviewSelector: '#email-preview-container',
		},
		wcBoxOfficeParams
	);

	self.init = function() {
		self.bindEvents();
	};

	self.bindEvents = function() {
		$( self.emailPreviewButtonSelector ).on( 'click', self.previewEmail );
		$( self.pastEmailsSelector ).on( 'click', self.deletePastEmailSelector, self.deletePastEmail );
	};

	self.previewEmail = function( e ) {
		var subject = $( self.emailSubjectSelector ).val(),
			productId = $( self.productId ).val(),
			emailBodyIframe = $( self.emailBodyIframeSelector ),
			emailBody = emailBodyIframe.contents().find( '#tinymce' ).html();

		// Disable button.
		$( self.emailPreviewButtonSelector ).attr( 'disabled', 'disabled' ).css( 'cursor', 'wait' );

		if ( ! productId || ! emailBody ) {
			alert( self.i18n_previewEmptyProductOrBody );
			$( self.emailPreviewButtonSelector ).removeAttr( 'disabled' ).css( 'cursor', 'pointer' );
			return;
		}

		$.post(
			self.ajaxurl,
			{
				action: self.previewEmailAction,
				product_id: productId,
				content: emailBody,
				subject: subject,
				wc_box_office_admin_test_email_nonce: self.previewEmailNonce
			}
		).done( self.previewEmailDone );
	};

	self.previewEmailDone = function( response ) {
		$( self.emailPreviewSelector ).html( response ).slideDown( 'fast' );
		$( self.emailPreviewButtonSelector ).removeAttr( 'disabled' ).css( 'cursor', 'pointer' );
	};

	$( self.init );

} )( jQuery );
