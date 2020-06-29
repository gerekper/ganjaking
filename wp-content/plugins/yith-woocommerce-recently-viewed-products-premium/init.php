<?php
/**
 * Plugin Name: YITH WooCommerce Recently Viewed Products Premium
 * Plugin URI: https://yithemes.com/
 * Description: The <code><strong>YITH WooCommerce Recently Viewed Products</strong></code> lets you offer a quick summary to your users about what they have recently seen and what they could be interested in. It helps your customers find products of interest and turn them into sales. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.5.11
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-recently-viewed-products
 * Domain Path: /languages/
 * WC requires at least: 3.8
 * WC tested up to: 4.2
 *
 * @author YITH
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.5.11
 */
/*  Copyright 2015-2019  YITH  (email : plugins@yithemes.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function yith_wrvp_premium_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'YITH WooCommerce Recently Viewed Products Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-recently-viewed-products' ); ?></p>
	</div>
<?php
}


if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WRVP_FREE_INIT', plugin_basename( __FILE__ ) );


if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( ! defined( 'YITH_WRVP_VERSION' ) ){
	define( 'YITH_WRVP_VERSION', '1.5.11' );
}

if ( ! defined( 'YITH_WRVP_PREMIUM' ) ) {
	define( 'YITH_WRVP_PREMIUM', '1' );
}

if ( ! defined( 'YITH_WRVP_INIT' ) ) {
	define( 'YITH_WRVP_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WRVP' ) ) {
	define( 'YITH_WRVP', true );
}

if ( ! defined( 'YITH_WRVP_FILE' ) ) {
	define( 'YITH_WRVP_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WRVP_URL' ) ) {
	define( 'YITH_WRVP_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WRVP_DIR' ) ) {
	define( 'YITH_WRVP_DIR', plugin_dir_path( __FILE__ )  );
}

if ( ! defined( 'YITH_WRVP_TEMPLATE_PATH' ) ) {
	define( 'YITH_WRVP_TEMPLATE_PATH', YITH_WRVP_DIR . 'templates' );
}

if ( ! defined( 'YITH_WRVP_ASSETS_URL' ) ) {
	define( 'YITH_WRVP_ASSETS_URL', YITH_WRVP_URL . 'assets' );
}

if ( ! defined( 'YITH_WRVP_SLUG' ) ) {
	define( 'YITH_WRVP_SLUG', 'yith-woocommerce-recently-viewed-products' );
}

if ( ! defined( 'YITH_WRVP_SECRET_KEY' ) ) {
	define( 'YITH_WRVP_SECRET_KEY', 'k8zRaPGIFSlmXqDQOx0r' );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WRVP_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WRVP_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WRVP_DIR  );

function yith_wrvp_premium_init() {

	load_plugin_textdomain( 'yith-woocommerce-recently-viewed-products', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );

	// Load required classes and functions
	require_once('includes/functions.yith-wrvp.php');
	require_once('includes/class.yith-wrvp.php');

	// widget
	require_once('includes/widgets/class.yith-wrvp-widget.php');

	// Let's start the game!
	YITH_WRVP();
}
add_action( 'yith_wrvp_premium_init', 'yith_wrvp_premium_init' );


function yith_wrvp_premium_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wrvp_premium_install_woocommerce_admin_notice' );
	}
	else {
		do_action( 'yith_wrvp_premium_init' );
	}
}
add_action( 'plugins_loaded', 'yith_wrvp_premium_install', 11 );