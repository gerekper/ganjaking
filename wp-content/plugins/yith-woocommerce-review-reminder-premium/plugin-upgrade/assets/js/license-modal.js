/* globals yith, ajaxurl, jQuery */

( function ( $ ) {
	$( function () {
		var content  = $( '.yith-plugin-upgrade-license-banner--modal' ),
			slug     = content.data( 'slug' ),
			security = content.data( 'security' );

		if ( content.length ) {
			yith.ui.modal(
				{
					content                   : content.clone(),
					classes                   : {
						wrap: 'yith-plugin-upgrade-license-modal'
					},
					width                     : '600px',
					closeWhenClickingOnOverlay: false,
					onClose                   : function () {
						content.removeClass( 'yith-plugin-upgrade-license-banner--modal' ).addClass( 'yith-plugin-upgrade-license-banner--inline' );
						$.post( ajaxurl, {
							action  : 'yith_plugin_upgrade_license_modal_dismiss',
							slug    : slug,
							security: security
						} );
					}
				}
			);
		}
	});

} )( jQuery );
