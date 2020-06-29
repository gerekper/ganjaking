<?php
/**********************************************************************************
* Resets the database back to its initial status just like a fresh installation
**********************************************************************************/
function DBR_wp_reset(){
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $current_user, $wpdb;
	$blogname = get_option( 'blogname' );
	$admin_email = get_option( 'admin_email' );
	$blog_public = get_option( 'blog_public' );
	if ( $current_user->user_login != 'admin' ){
		$user = get_user_by( 'login', 'admin' );
	}
	if ( empty( $user->user_level ) || $user->user_level < 10 ){
		$user = $current_user;
	}
	$prefix = str_replace( '_', '\_', $wpdb->prefix );
	$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$prefix}%'" );
	foreach ( $tables as $table ) {
		$wpdb->query( "DROP TABLE $table" );
	}
	// Install wordpress
	$result = wp_install( $blogname, $user->user_login, $user->user_email, $blog_public);
	extract( $result, EXTR_SKIP );
	// Set user password
	$query = $wpdb->prepare( "UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $user->user_pass, $user_id );
	$wpdb->query( $query );
	// Test for functions
	$get_user_meta = function_exists( 'get_user_meta' ) ? 'get_user_meta' : 'get_usermeta';
	$update_user_meta = function_exists( 'update_user_meta' ) ? 'update_user_meta' : 'update_usermeta';
	// Say to wordpress that we will not use generated password
	if ( $get_user_meta( $user_id, 'default_password_nag' ) ){
		$update_user_meta( $user_id, 'default_password_nag', false );
	}
	if ( $get_user_meta( $user_id, $wpdb->prefix . 'default_password_nag' ) ){
		$update_user_meta( $user_id, $wpdb->prefix . 'default_password_nag', false );
	}
	// Add a small file to invite users rate the plugin
	$aDBc_upload_dir = wp_upload_dir();
	$aDBc_file_path = str_replace('\\' ,'/', $aDBc_upload_dir['basedir']) . "/DBR.txt";
	if(!file_exists($aDBc_file_path)){
		$handle = fopen($aDBc_file_path, "w");
		if($handle){
			fwrite($handle, "1");
		}
	}

	// Reactivate the plugin
	@activate_plugin(DBR_PLUGIN_BASENAME);
	// Clear all cookies associated with authentication
	//wp_clear_auth_cookie();
	// Set the authentication cookies based User ID
	//wp_set_auth_cookie( $user_id );
	// Redirect user to admin pannel
	//wp_redirect( admin_url()."tools.php?page=advanced_wp_reset&reset-db=done");
}

?>