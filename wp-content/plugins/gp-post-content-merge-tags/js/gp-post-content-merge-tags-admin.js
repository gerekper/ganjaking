
( function( $ ) {

	var $confirmationPage, $enable, $editLink;

	function toggleEditLink() {
		if( ! $confirmationPage.val() || ! $enable.is( ':checked' ) ) {
			$editLink.hide();
		} else {
			$editLink.show();
			$editLink.find( 'a' ).attr( 'href', '{0}?post={1}&action=edit&form_id={2}'.format( GPPCMTAdminData.editBaseURL, $confirmationPage.val(), window.form.id ) );
		}
	}

	$( document ).ready( function() {

		$confirmationPage = $( '#page' );
		$enable = $( '#gppcmtenable' );

		$editLink = $( GPPCMTAdminData.editLinkMarkup );
		$editLink.insertAfter( $enable.next( 'label' ) );

		$confirmationPage.change( function() {
			toggleEditLink();
		} );

		$enable.change( function() {
			toggleEditLink();
		} );

		toggleEditLink();

	} );

} )( jQuery );