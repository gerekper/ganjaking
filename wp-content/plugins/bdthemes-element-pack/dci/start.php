<?php
/**
 * Main File
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'dci_dynamic_init' ) ) {
	function dci_dynamic_init( $params ) {

		if ( ! is_admin() ) {
			return;
		}

		$menu_slug = isset( $params['menu']['slug'] ) ? $params['menu']['slug'] : false;

		if ( ! $menu_slug ) {
			return;
		}

		$current_page = isset( $_GET['page'] ) ? $_GET['page'] : false;
		/**
		 * Attach SDK to current page
		 */
		$params['current_page'] = $current_page;
		$params['menu_slug']    = $menu_slug;

		/**
		 * Include SDK
		 */
		require_once dirname( __FILE__ ) . '/insights.php';
		if ( function_exists( 'dci_sdk_insights' ) ) {
			dci_sdk_insights( $params );
		}

	}
}
