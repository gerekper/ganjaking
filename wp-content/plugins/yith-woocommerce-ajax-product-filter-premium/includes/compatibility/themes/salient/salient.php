<?php
/**
 * Salient theme compatibility
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Ajax Product FIlter
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

add_action( 'wp_enqueue_scripts', 'yith_wcan_salient_style', 20 );
add_action( 'admin_init', 'yith_wcan_salient_support', 20 );

if ( ! function_exists( 'yith_wcan_salient_style' ) ) {
	/**
	 * Enqueue custom style for Salient theme
	 *
	 * @return void
	 */
	function yith_wcan_salient_style() {
		// Style.
		if ( yith_wcan_can_be_displayed() ) {
			wp_enqueue_style( 'yith-wcan-salient', YITH_WCAN_URL . 'compatibility/themes/salient/salient.css', array( 'yith-wcan-frontend' ), YITH_WCAN()->version );
		}
	}
}

if ( ! function_exists( 'yith_wcan_salient_support' ) ) {
	/**
	 * Update default options to work with Salient theme
	 *
	 * @return void
	 */
	function yith_wcan_salient_support() {
		$options = get_option( 'yit_wcan_options' );
		if ( 'h4' !== $options['yith_wcan_ajax_widget_title_class'] ) {
			$options['yith_wcan_ajax_widget_title_class'] = 'h4';
			update_option( 'yit_wcan_options', $options );
		}
	}
}
