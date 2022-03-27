<?php

/**
 * Template functions.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! function_exists( 'srp_get_template' ) ) {

	/**
	 *  Get other templates from themes.
	 * 
	 * @return void
	 */
	function srp_get_template( $template_name, $args = array() ) {
			
				$plugin_path = SRP_PLUGIN_PATH . '/templates/' ;

		wc_get_template( $template_name , $args , 'rewardsystem/' , $plugin_path ) ;
	}

}

if ( ! function_exists( 'srp_get_template_html' ) ) {

	/**
	 *  Like srp_get_template, but returns the HTML instead of outputting.
	 *
	 *  @return string
	 */
	function srp_get_template_html( $template_name, $args = array() ) {

		ob_start() ;
		srp_get_template( $template_name , $args ) ;
		return ob_get_clean() ;
	}

}
