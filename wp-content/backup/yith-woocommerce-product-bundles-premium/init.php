<?php
/**
 * Plugin Name: YITH WooCommerce Product Bundles Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-product-bundles
 * Description: <code><strong>YITH WooCommerce Product Bundles</strong></code> allows you to bundle WooCommerce products and sell them at a unique price. You can configure bundled items as optional, set a discount, the minimum and maximum quantity, and so on! <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.3.9
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-product-bundles
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.3.x
 *
 * @author  yithemes
 * @package YITH WooCommerce Product Bundles Premium
 * @version 1.3.9
 */
/*  Copyright 2015  Your Inspiration Themes  (email : plugins@yithemes.com)

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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Free version deactivation if installed __________________

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCPB_FREE_INIT', plugin_basename( __FILE__ ) );

function yith_wcpb_pr_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'YITH WooCommerce Product Bundles Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-product-bundles' ); ?></p>
	</div>
	<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( ! defined( 'YITH_WCPB_VERSION' ) ) {
	define( 'YITH_WCPB_VERSION', '1.3.9' );
}

if ( ! defined( 'YITH_WCPB_PREMIUM' ) ) {
	define( 'YITH_WCPB_PREMIUM', '1' );
}

if ( ! defined( 'YITH_WCPB_INIT' ) ) {
	define( 'YITH_WCPB_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCPB' ) ) {
	define( 'YITH_WCPB', true );
}

if ( ! defined( 'YITH_WCPB_FILE' ) ) {
	define( 'YITH_WCPB_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCPB_URL' ) ) {
	define( 'YITH_WCPB_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCPB_DIR' ) ) {
	define( 'YITH_WCPB_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCPB_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCPB_TEMPLATE_PATH', YITH_WCPB_DIR . 'templates' );
}

if ( ! defined( 'YITH_WCPB_INCLUDES_PATH' ) ) {
	define( 'YITH_WCPB_INCLUDES_PATH', YITH_WCPB_DIR . 'includes' );
}

if ( ! defined( 'YITH_WCPB_ASSETS_URL' ) ) {
	define( 'YITH_WCPB_ASSETS_URL', YITH_WCPB_URL . 'assets' );
}

if ( ! defined( 'YITH_WCPB_ASSETS_PATH' ) ) {
	define( 'YITH_WCPB_ASSETS_PATH', YITH_WCPB_DIR . 'assets' );
}

if ( ! defined( 'YITH_WCPB_SLUG' ) ) {
	define( 'YITH_WCPB_SLUG', 'yith-woocommerce-product-bundles' );
}

if ( ! defined( 'YITH_WCPB_SECRET_KEY' ) ) {
	define( 'YITH_WCPB_SECRET_KEY', 'qhNNwsl95FnqhNIeby7z' );
}

function yith_wcpb_pr_init() {

	load_plugin_textdomain( 'yith-woocommerce-product-bundles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Load required classes and functions
	require_once 'includes/objects/class.yith-wc-product-bundle-premium.php';
	require_once 'includes/objects/class.yith-wc-bundled-item-premium.php';
	require_once 'includes/compatibility/class.yith-wcpb-compatibility.php';
	require_once 'includes/compatibility/class.yith-wcpb-compatibility-premium.php';
	require_once 'includes/class.yith-wcpb-shortcodes.php';
	require_once 'includes/class.yith-wcpb-bundle-widget.php';
	require_once 'includes/class.yith-wcpb-admin.php';
	require_once 'includes/class.yith-wcpb-frontend.php';
	require_once 'includes/class.yith-wcpb.php';
	require_once 'includes/class.yith-wcpb-admin-premium.php';
	require_once 'includes/class.yith-wcpb-frontend-premium.php';
	require_once 'includes/class.yith-wcpb-premium.php';
	require_once 'includes/functions.yith-wcpb.php';

	// Let's start the game!
	YITH_WCPB();
}

add_action( 'yith_wcpb_pr_init', 'yith_wcpb_pr_init' );


function yith_wcpb_pr_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcpb_pr_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wcpb_pr_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wcpb_pr_install', 11 );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );