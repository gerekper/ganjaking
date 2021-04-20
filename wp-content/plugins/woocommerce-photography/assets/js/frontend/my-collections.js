/*global WCPhotographyMyCollectionsParams */
/*jshint devel: true */
( function( $ ) {

	$( function() {
		$( 'body' )
			.on( 'change', '.my-account-collections .collection-visibility select', function() {
				$( this ).closest( 'td' ).find( '.button' ).prop( 'disabled', false ).removeClass( 'disabled' );
			})
			.on( 'click', '.my-account-collections .collection-visibility .button', function() {
				var button = $( this ),
					wrap   = button.closest( 'td' ),
					select = $( 'select', wrap );

				select.prop( 'disabled', true );
				button.addClass( 'loading' );

				$.ajax({
					url:      WCPhotographyMyCollectionsParams.ajax_url,
					data:     {
						action:        'wc_photography_my_account_edit_visibility',
						security:      WCPhotographyMyCollectionsParams.security,
						collection_id: $( this ).attr( 'data-collection_id' ),
						customer:      WCPhotographyMyCollectionsParams.customer,
						visibility:    select.val()
					},
					type:     'POST',
					dataType: 'json',
					success:  function( response ) {
						if ( response.success ) {
							select.prop( 'disabled', false );
							button.removeClass( 'loading' ).addClass( 'disabled' );
							button.prop( 'disabled', true );
						}
					}
				});

				return false;
			});
	});

}( jQuery ) );
