<?php
/**
 * Plugin Name: WooCommerce Smart Coupons
 * Plugin URI: https://woo.com/products/smart-coupons/
 * Description: <strong>WooCommerce Smart Coupons</strong> lets customers buy gift certificates, store credits or coupons easily. They can use purchased credits themselves or gift to someone else.
 * Version: 8.13.0
 * Author: StoreApps
 * Author URI: https://www.storeapps.org/
 * Developer: StoreApps
 * Developer URI: https://www.storeapps.org/
 * Requires at least: 4.4
 * Tested up to: 6.4.2
 * WC requires at least: 3.0.0
 * WC tested up to: 8.2.2
 * Text Domain: woocommerce-smart-coupons
 * Domain Path: /languages
 * Woo: 18729:05c45f2aa466106a466de4402fff9dde
 * Copyright (c) 2014-2024 WooCommerce, StoreApps All rights reserved.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-smart-coupons
 */

/**
 * Include class having function to execute during activation & deactivation of plugin
 */
require_once 'includes/class-wc-sc-act-deact.php';

/**
 * On activation
 */
register_activation_hook( __FILE__, array( 'WC_SC_Act_Deact', 'smart_coupon_activate' ) );

/**
 * On deactivation
 */
register_deactivation_hook( __FILE__, array( 'WC_SC_Act_Deact', 'smart_coupon_deactivate' ) );

$active_plugins = (array) get_option( 'active_plugins', array() );

if ( is_multisite() ) {
	$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
}

if ( in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ) {

	if ( ! defined( 'WC_SC_PLUGIN_FILE' ) ) {
		define( 'WC_SC_PLUGIN_FILE', __FILE__ );
	}
	if ( ! defined( 'WC_SC_PLUGIN_DIRNAME' ) ) {
		define( 'WC_SC_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
	}

	include_once 'includes/class-wc-smart-coupons.php';

	$GLOBALS['woocommerce_smart_coupon'] = WC_Smart_Coupons::get_instance();

} // End woocommerce active check
