<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_Helper {

	/**
	 * Initialises and connects the WordPress Filesystem
	 */
	public static function filesystem(){
	
		if( ! defined( 'FS_METHOD' ) ){
			define( 'FS_METHOD', 'direct' );
			WP_Filesystem();
		}
		
		if( ! defined( 'FS_CHMOD_DIR' ) ){
			define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
		}
		
		if( ! defined( 'FS_CHMOD_FILE' ) ){
			define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
		}
		
		global $wp_filesystem;
	
		if( empty( $wp_filesystem ) ){
			require_once wp_normalize_path( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

}
