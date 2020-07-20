<?php
/**
 * Mute Screamer API.
 *
 * @package Mute Screamer
 */

/**
 * Is the current request a banned request
 *
 * @return bool
 */
if ( ! function_exists( 'hmwp_ms_is_ban' ) ) {
	function hmwp_ms_is_ban() {
		return HMWP_MS_IDS::instance()->is_ban;
	}
}

/**
 * Filter for wp_title. Change the page title when displaying a 500 error template.
 *
 * @param string The current page title
 * @param string How to separate the various items within the page title.
 * @param string Direction to display title.
 * @return string
 */
if ( ! function_exists( 'hmwp_ms_filter_wp_title' ) ) {
	function hmwp_ms_filter_wp_title( $title, $sep, $seplocation ) {
		if ( hmwp_ms_is_ban() ) {
			return sprintf( __( 'Error %s ', 'mute-screamer' ), $sep );
		} else {
			return sprintf( __( 'An Error Was Encountered %s ', 'mute-screamer' ), $sep );
		}
	}
}

/**
 * Add additional body classes for the 500.php template
 *
 * @param array
 * @return void
 */
if ( ! function_exists( 'hmwp_ms_body_class' ) ) {
	function hmwp_ms_body_class( $classes ) {
		$classes[] = 'error404';
		$classes[] = 'error500';
		return $classes;
	}	
}
