jQuery(
	function ( $ ) {
		$( document ).on(
			'click',
			'.notice-dismiss',
			function () {
				var t          = $( this ),
					wrapper_id = t.parent().attr( 'id' );

				if ( wrapper_id === 'yith-admin-notice-site-staging' ) {
					var cname  = 'hide_yith_admin_notice_site_staging',
						cvalue = 'yes';

					document.cookie = cname + "=" + cvalue + ";path=/";
				}
			}
		);
	}
);
