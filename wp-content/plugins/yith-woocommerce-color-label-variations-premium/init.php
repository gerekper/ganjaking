<?php
/**
 * Plugin Name: YITH WooCommerce Color and Label Variations Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-color-and-label-variations/
 * Description: The <code><strong>YITH WooCommerce Color and Label Variations</strong></code> allows you to customize the drop-down select of your variable products and buy product variations directly from shop pages. A must-have for every e-commerce. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.15.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-color-label-variations
 * Domain Path: /languages/
 * WC requires at least: 4.5
 * WC tested up to: 5.3
 *
 * @author  YITH
 * @package YITH WooCommerce Color and Label Variations Premium
 * @version 1.15.0
 */
/*  Copyright 2015-2021  YITH  (email : plugins@yithemes.com)

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

if ( ! function_exists( 'yith_wccl_premium_install_woocommerce_admin_notice' ) ) {
	function yith_wccl_premium_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( 'YITH WooCommerce Color and Label Variations Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-color-label-variations' ); ?></p>
		</div>
		<?php
	}

}


if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

// Free version deactivation if installed __________________

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCCL_FREE_INIT', plugin_basename( __FILE__ ) );

if ( ! defined( 'YITH_WCCL_VERSION' ) ) {
	define( 'YITH_WCCL_VERSION', '1.15.0' );
}

if ( ! defined( 'YITH_WCCL' ) ) {
	define( 'YITH_WCCL', true );
}

if ( ! defined( 'YITH_WCCL_PREMIUM' ) ) {
	define( 'YITH_WCCL_PREMIUM', true );
}

if ( ! defined( 'YITH_WCCL_FILE' ) ) {
	define( 'YITH_WCCL_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCCL_URL' ) ) {
	define( 'YITH_WCCL_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCCL_DIR' ) ) {
	define( 'YITH_WCCL_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCCL_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCCL_TEMPLATE_PATH', YITH_WCCL_DIR . 'templates' );
}

if ( ! defined( 'YITH_WCCL_ASSETS_URL' ) ) {
	define( 'YITH_WCCL_ASSETS_URL', YITH_WCCL_URL . 'assets' );
}

if ( ! defined( 'YITH_WCCL_INIT' ) ) {
	define( 'YITH_WCCL_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCCL_SLUG' ) ) {
	define( 'YITH_WCCL_SLUG', 'yith-woocommerce-color-label-variations' );
}

if ( ! defined( 'YITH_WCCL_SECRET_KEY' ) ) {
	define( 'YITH_WCCL_SECRET_KEY', 'bnmQwc5wUlnX24pgLm8I' );
}

if ( ! defined( 'YITH_WCCL_DB_VERSION' ) ) {
	define( 'YITH_WCCL_DB_VERSION', '1.0.0' );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCCL_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCCL_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCCL_DIR );

// activate plugin
function yith_wccl_activation_process() {
	if ( ! function_exists( 'yith_wccl_activation' ) ) {
		require_once 'includes/function.yith-wccl-activation.php';
	}

	yith_wccl_activation();
}

register_activation_hook( __FILE__, 'yith_wccl_activation_process' );

function yith_wccl_premium_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wccl_premium_install_woocommerce_admin_notice' );
	} else {

		load_plugin_textdomain( 'yith-woocommerce-color-label-variations', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Load required classes and functions
		require_once( 'includes/function.yith-wccl.php' );
		require_once( 'includes/class.yith-wccl.php' );

		// Let's start the game!
		YITH_WCCL();
	}

	// check for update table
	if ( function_exists( 'yith_wccl_update_db_check' ) ) {
		yith_wccl_update_db_check();
	}
}

add_action( 'plugins_loaded', 'yith_wccl_premium_install', 11 );