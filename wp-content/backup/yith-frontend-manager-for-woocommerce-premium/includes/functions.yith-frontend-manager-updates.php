<?php

/**
 * Update version db 1.0.1
 *
 * Prevent no default page are set
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 * @return void
 * @since 1.0.9
 */
function yith_wcfm_update_db_1_0_1() {
	$db_version = get_option( 'yith_wcfm_db_version', '1.0.0' );
	if ( $db_version && version_compare( $db_version, '1.0.1', '<' ) ) {
		$default_page_id = get_option( 'yith_wcfm_main_page_id', false );

		if( $default_page_id ){
			update_option( 'yith_wcfm_default_main_page_id', $default_page_id );
			update_option( 'yith_wcfm_db_version', '1.0.1' );
		}
	}
}

add_action( 'admin_init', 'yith_wcfm_update_db_1_0_1' );