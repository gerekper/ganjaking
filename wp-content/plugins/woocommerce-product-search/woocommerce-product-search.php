<?php
/**
 * woocommerce-product-search.php
 *
 * Copyright (c) 2014-2023 "kento" Karim Rahimpur www.itthinx.com
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
 * Plugin URI: https://woo.com/products/woocommerce-product-search/
 * Description: The best Search Engine and Search Experience for WooCommerce.
 * Version: 5.0.3
 * Author: itthinx
 * Author URI: https://www.itthinx.com
 * WC requires at least: 7.9
 * WC tested up to: 8.3
 * Woo: 512174:c84cc8ca16ddac3408e6b6c5871133a8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WOO_PS_PLUGIN_VERSION', '5.0.3' );
define( 'WOO_PS_PLUGIN_DOMAIN', 'woocommerce-product-search' );
define( 'WOO_PS_FILE', __FILE__ );
if ( !defined( 'WOO_PS_LOG' ) ) {
	define( 'WOO_PS_LOG', false );
}
if ( !defined( 'WPS_DEBUG_VERBOSE' ) ) {
	define( 'WPS_DEBUG_VERBOSE', false );
}
if ( !defined( 'WPS_DEBUG' ) ) {
	define( 'WPS_DEBUG', false || WPS_DEBUG_VERBOSE );
}
if ( !defined( 'WPS_DEBUG_SCRIPTS' ) ) {
	define( 'WPS_DEBUG_SCRIPTS', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
}
if ( !defined( 'WPS_DEBUG_STYLES' ) ) {
	define( 'WPS_DEBUG_STYLES', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
}
if ( !defined( 'WPS_DEBUG_DOM' ) ) {
	define( 'WPS_DEBUG_DOM', false );
}
if ( !defined( 'WPS_CACHE_DEBUG' ) ) {
	define( 'WPS_CACHE_DEBUG', false );
}
if ( !defined( 'WPS_RENDER_CACHE' ) ) {
	define( 'WPS_RENDER_CACHE', false );
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
if ( !defined( 'WPS_LEGACY_WIDGETS' ) ) {
	define( 'WPS_LEGACY_WIDGETS', false );
}
if ( !defined( 'WPS_ADMIN_BAR_STATUS' ) ) {
	define( 'WPS_ADMIN_BAR_STATUS', false );
}

/**
 * Boots the plugin.
 */
function woocommerce_product_search_boot() {
	$lib = '/lib';
	define( 'WOO_PS_CORE_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
	define( 'WOO_PS_CORE_LIB', WOO_PS_CORE_DIR . $lib . '/core' );
	define( 'WOO_PS_ADMIN_LIB', WOO_PS_CORE_DIR . $lib . '/admin' );
	define( 'WOO_PS_BLOCKS_LIB', WOO_PS_CORE_DIR . $lib . '/blocks' );
	define( 'WOO_PS_CACHE_LIB', WOO_PS_CORE_DIR . $lib . '/cache' );
	define( 'WOO_PS_CONTROL_LIB', WOO_PS_CORE_DIR . $lib . '/control' );
	define( 'WOO_PS_ENGINE_LIB', WOO_PS_CORE_DIR . $lib . '/engine' );
	define( 'WOO_PS_VIEWS_LIB', WOO_PS_CORE_DIR . $lib . '/views' );
	define( 'WOO_PS_EXT_LIB', WOO_PS_CORE_DIR . $lib . '/ext' );
	define( 'WOO_PS_COMPAT_LIB', WOO_PS_CORE_DIR . $lib . '/compat' );
	define( 'WOO_PS_PLUGIN_URL', plugins_url( 'woocommerce-product-search' ) );

	require_once WOO_PS_CORE_LIB . '/class-woocommerce-product-search.php';
}

woocommerce_product_search_boot();
