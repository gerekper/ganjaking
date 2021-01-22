<?php
/**
 * woocommerce-product-search.php
 *
 * Copyright (c) 2014-2021 "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 1.0.0
 *
 * Plugin Name: WooCommerce Product Search
 * Plugin URI: https://woocommerce.com/products/woocommerce-product-search/
 * Description: The best Search Engine and Search Experience for WooCommerce.
 * Version: 3.5.1
 * Author: itthinx
 * Author URI: https://www.itthinx.com
 * WC requires at least: 3.6
 * WC tested up to: 4.9
 * Woo: 512174:c84cc8ca16ddac3408e6b6c5871133a8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once 'woo-includes/woo-functions.php';
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'c84cc8ca16ddac3408e6b6c5871133a8', '512174' );

// Check if WooCommerce is active
if ( ! is_woocommerce_active() ) {
	return;
}

define( 'WOO_PS_PLUGIN_VERSION', '3.5.1' );
define( 'WOO_PS_PLUGIN_DOMAIN', 'woocommerce-product-search' );
define( 'WOO_PS_FILE', __FILE__ );
if ( !defined( 'WOO_PS_LOG' ) ) {
	define( 'WOO_PS_LOG', false );
}
if ( !defined( 'WPS_DEBUG' ) ) {
	define( 'WPS_DEBUG', false );
}
if ( !defined( 'WPS_DEBUG_SCRIPTS' ) ) {
	define( 'WPS_DEBUG_SCRIPTS', false );
}
if ( !defined( 'WPS_DEBUG_STYLES' ) ) {
	define( 'WPS_DEBUG_STYLES', false );
}
if ( !defined( 'WPS_DEBUG_DOM' ) ) {
	define( 'WPS_DEBUG_DOM', false );
}
if ( !defined( 'WPS_EXT_PDS' ) ) {
	define( 'WPS_EXT_PDS', true );
}
if ( !defined( 'WPS_EXT_REST' ) ) {
	define( 'WPS_EXT_REST', true );
}
if ( !defined( 'WPS_ROLES_CACHE' ) ) {
	define( 'WPS_ROLES_CACHE', true );
}
if ( !defined( 'WPS_GROUPS_CACHE' ) ) {
	define( 'WPS_GROUPS_CACHE', true );
}
if ( !defined( 'WPS_OBJECT_LIMIT' ) ) {
	define( 'WPS_OBJECT_LIMIT', 'AUTO' );
}
if ( !defined( 'WPS_DEFER_VARIATIONS_THRESHOLD' ) ) {
	define( 'WPS_DEFER_VARIATIONS_THRESHOLD', 3 );
}

/**
 * Boots the plugin.
 */
function woocommerce_product_search_boot() {
	// @since 3.3.0 - since we already check if WooCommerce is active right at the top, we don't even get here unless it is, so these checks are not necessary
	// $active_plugins = get_option( 'active_plugins', array() );
	// if ( is_multisite() ) {
	// 	$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
	// 	$active_sitewide_plugins = array_keys( $active_sitewide_plugins );
	// 	$active_plugins = array_merge( $active_plugins, $active_sitewide_plugins );
	// }
	// $woocommerce_is_active = in_array( 'woocommerce/woocommerce.php', $active_plugins );
	// if ( $woocommerce_is_active ) {
	// 	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	// 	$woocommerce_plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . 'woocommerce/woocommerce.php' );
	// 	$woocommerce_version = isset( $woocommerce_plugin_data['Version'] ) ? $woocommerce_plugin_data['Version'] : '3.0.0';
	// 	if ( version_compare( $woocommerce_version, '3.0.0' ) >= 0 ) {
	// 		$lib = '/lib';
	// 	} else {
	// 		$lib = '/lib-2';
	// 	}
	// }
	// @since 3.3.0 we require at least WooCommerce 3.0 and the lib-2 folder has been removed.
	// Left the above for reference if we want to do something similar in future releases, although we should rather not keep too much of a backwards-compatibility.
	$lib = '/lib';
	define( 'WOO_PS_CORE_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
	define( 'WOO_PS_CORE_LIB', WOO_PS_CORE_DIR . $lib . '/core' );
	define( 'WOO_PS_ADMIN_LIB', WOO_PS_CORE_DIR . $lib . '/admin' );
	define( 'WOO_PS_VIEWS_LIB', WOO_PS_CORE_DIR . $lib . '/views' );
	define( 'WOO_PS_EXT_LIB', WOO_PS_CORE_DIR . $lib . '/ext' );
	define( 'WOO_PS_COMPAT_LIB', WOO_PS_CORE_DIR . $lib . '/compat' );
	define( 'WOO_PS_PLUGIN_URL', plugins_url( 'woocommerce-product-search' ) );

	require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search.php';
}

woocommerce_product_search_boot();
