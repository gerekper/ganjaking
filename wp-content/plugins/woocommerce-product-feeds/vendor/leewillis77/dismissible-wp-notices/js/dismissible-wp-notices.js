function ademti_dismissible_wp_notices_handle( elem, action ) {
	const slug = jQuery( elem ).data( 'ademti-notice-slug' );
	if ( slug === '' ) {
		return;
	}
	const data = {
		action: action,
		slug: slug,
		nonce: dismissibleWpNotices.nonce,
	};
	jQuery.ajax(
		{
			type: 'POST',
			url: dismissibleWpNotices.ajaxUrl,
			data: data
		}
	);
	jQuery( elem ).closest('.notice').fadeOut();
	return;
}

jQuery( function () {
	jQuery( '.ademti-dismiss' ).click( function ( e ) {
		ademti_dismissible_wp_notices_handle( this, 'ademti_dismissible_wp_notices_dismiss' );
	} );
	jQuery( '.ademti-snooze' ).click( function ( e ) {
		ademti_dismissible_wp_notices_handle( this, 'ademti_dismissible_wp_notices_snooze' );
	} );
} );
