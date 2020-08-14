<?php
/**
 * Plugin Name: YITH WooCommerce Quick View Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-quick-view
 * Description: The <code><strong>YITH WooCommerce Quick View</strong></code> plugin allows your customers to have a quick look about products. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.5.3
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-quick-view
 * Domain Path: /languages/
 * WC requires at least: 3.7
 * WC tested up to: 4.2
 *
 * @author  YITH
 * @package YITH WooCommerce Quick View
 * @version 1.5.3
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}


function yith_wcqv_premium_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'YITH WooCommerce Quick View is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-quick-view' ); ?></p>
	</div>
	<?php
}


if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCQV_FREE_INIT', plugin_basename( __FILE__ ) );


if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( ! defined( 'YITH_WCQV_VERSION' ) ) {
	define( 'YITH_WCQV_VERSION', '1.5.3' );
}

if ( ! defined( 'YITH_WCQV_PREMIUM' ) ) {
	define( 'YITH_WCQV_PREMIUM', '1' );
}

if ( ! defined( 'YITH_WCQV' ) ) {
	define( 'YITH_WCQV', true );
}

if ( ! defined( 'YITH_WCQV_FILE' ) ) {
	define( 'YITH_WCQV_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCQV_URL' ) ) {
	define( 'YITH_WCQV_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCQV_DIR' ) ) {
	define( 'YITH_WCQV_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCQV_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCQV_TEMPLATE_PATH', YITH_WCQV_DIR . 'templates' );
}

if ( ! defined( 'YITH_WCQV_ASSETS_URL' ) ) {
	define( 'YITH_WCQV_ASSETS_URL', YITH_WCQV_URL . 'assets' );
}

if ( ! defined( 'YITH_WCQV_INIT' ) ) {
	define( 'YITH_WCQV_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCQV_SLUG' ) ) {
	define( 'YITH_WCQV_SLUG', 'yith-woocommerce-quick-view' );
}

if ( ! defined( 'YITH_WCQV_SECRET_KEY' ) ) {
	define( 'YITH_WCQV_SECRET_KEY', 'th2XOPOaqaCLzOyYtt63' );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCQV_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCQV_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCQV_DIR );

function yith_wcqv_premium_init() {

	load_plugin_textdomain( 'yith-woocommerce-quick-view', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Load required classes and functions
	require_once 'includes/functions.yith-wcqv.php';
	require_once 'includes/class.yith-wcqv.php';

	// Let's start the game!
	YITH_WCQV();
}

add_action( 'yith_wcqv_premium_init', 'yith_wcqv_premium_init' );

function yith_wcqv_premium_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcqv_premium_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wcqv_premium_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wcqv_premium_install', 11 );

// require controller.php for compatibility
if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX && class_exists( 'YITH_WCQV_Frontend' ) && isset( $_REQUEST['action'] ) && $_REQUEST['action'] == YITH_WCQV_Frontend()->quick_view_ajax_action
		|| isset( $_REQUEST['_wpcf7_is_ajax_call'] ) ) && defined( 'WPCF7_PLUGIN_DIR' ) && file_exists( WPCF7_PLUGIN_DIR . '/includes/controller.php' )
) {
	require_once WPCF7_PLUGIN_DIR . '/includes/controller.php';
}

