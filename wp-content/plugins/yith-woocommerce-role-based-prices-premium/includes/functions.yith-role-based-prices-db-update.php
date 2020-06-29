<?php
if( !defined( 'ABSPATH')){
	exit;
}

add_action( 'admin_init', 'yith_role_based_update_db_1_0_1' );

function yith_role_based_update_db_1_0_1(){

	$db_option = get_option( 'yith_role_based_db_version', '1.0.0' );

	if( version_compare( $db_option, '1.0.1', '<' ) ){

		delete_site_transient( 'ywcrb_rolebased_prices' );

		update_option( 'yith_role_based_db_version', '1.0.1' );
	}
}