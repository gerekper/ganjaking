<?php

/*
 * Plugin Name: WooCommerce Conditional Content
 * Plugin URI: https://woocommerce.com/products/woocommerce-conditional-content/
 * Description: WooCommerce conditional content allows you to display additional or alternate content based on a set of criteria.  Criteria includes current users role, product categories, product tags, prices, cart contents, and many more.
 * Version: 2.1.4
 * Author: Lucas Stark
 * Author URI: https://www.elementstark.com/
 * Requires at least: 3.1
 * Tested up to: 5.5.1

 * Copyright: © 2009-2020 Lucas Stark.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html

 * WC requires at least: 3.0.0
 * WC tested up to: 4.5.2
 * Woo: 260119:015e3a0eb801d23217d6fecb97e1537b
 */

/**
 * Required functions
 */
if ( !function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '015e3a0eb801d23217d6fecb97e1537b', '260119' );

if ( is_woocommerce_active() ) {

	/**
	 * Localisation
	 * */
	load_plugin_textdomain( 'wc_conditional_content', false, dirname( plugin_basename( __FILE__ ) ) . '/' );


	//Include the compatibility class
	include 'classes/class-wc-conditional-content-compatibility.php';

	add_action('plugins_loaded', 'wc_conditional_content_plugins_loaded');

	function wc_conditional_content_plugins_loaded() {
		if ( WC_Conditional_Content_Compatibility::is_wc_version_gte_2_7() ) {
			require_once 'woocommerce-conditional-content-main.php';
		} else {
			require_once 'back_compat_less_27/woocommerce-conditional-content-main.php';
		}
	}
}
