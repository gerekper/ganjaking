<?php
/**
 * Main File
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'rc_dynamic_init' ) ) {
	function rc_dynamic_init( $params ) {

		if ( is_admin() ) :

			$menu_slug    = isset( $params['menu']['slug'] ) ? $params['menu']['slug'] : false;
			$current_page = isset( $_GET['page'] ) ? $_GET['page'] : false;

			/**
			 * Attach SDK to current page
			 */
			$params['current_page'] = $current_page;
			$params['menu_slug']    = $menu_slug;

			/**
			 * Include SDK
			 */
			require_once dirname( __FILE__ ) . '/notice.php';
			if ( function_exists( 'rc_sdk_automate' ) ) {
				rc_sdk_automate( $params );
			}

		endif;
	}
}
