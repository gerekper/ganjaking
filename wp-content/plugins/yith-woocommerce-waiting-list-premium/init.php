<?php
/**
 * Plugin Name: YITH WooCommerce Waiting List Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-waiting-list
 * Description: The <code><strong>YITH WooCommerce Waiting List</strong></code> plugin allows your customers to request an email notification when an out-of-stock product comes back into stock. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.7.2
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-waiting-list
 * Domain Path: /languages/
 * WC requires at least: 3.6
 * WC tested up to: 4.2
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.7.2
 */
/*  Copyright 2015-2020 - YITH  (email : plugins@yithemes.com)

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

function yith_wcwtl_premium_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce Waiting List is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-waiting-list' ); ?></p>
	</div>
	<?php
}


if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCWTL_FREE_INIT', plugin_basename( __FILE__ ) );

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! defined( 'YITH_WCWTL_VERSION' ) ) {
	define( 'YITH_WCWTL_VERSION', '1.7.2' );
}

if ( ! defined( 'YITH_WCWTL_PREMIUM' ) ) {
	define( 'YITH_WCWTL_PREMIUM', '1' );
}

if ( ! defined( 'YITH_WCWTL' ) ) {
	define( 'YITH_WCWTL', true );
}

if ( ! defined( 'YITH_WCWTL_FILE' ) ) {
	define( 'YITH_WCWTL_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCWTL_URL' ) ) {
	define( 'YITH_WCWTL_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCWTL_DIR' ) ) {
	define( 'YITH_WCWTL_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCWTL_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCWTL_TEMPLATE_PATH', YITH_WCWTL_DIR . 'templates' );
}

if ( ! defined( 'YITH_WCWTL_ASSETS_URL' ) ) {
	define( 'YITH_WCWTL_ASSETS_URL', YITH_WCWTL_URL . 'assets' );
}

if ( ! defined( 'YITH_WCWTL_INIT' ) ) {
	define( 'YITH_WCWTL_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCWTL_SLUG' ) ) {
	define( 'YITH_WCWTL_SLUG', 'yith-woocommerce-waiting-list' );
}

if ( ! defined( 'YITH_WCWTL_SECRET_KEY' ) ) {
	define( 'YITH_WCWTL_SECRET_KEY', 'JvymVYqUNb4JRY92pKV4' );
}

// plugin meta. Deprecated
! defined( 'YITH_WCWTL_META' ) && define( 'YITH_WCWTL_META', '_yith_wcwtl_users_list' );
! defined( 'YITH_WCWTL_META_EXCLUDE' ) && define( 'YITH_WCWTL_META_EXCLUDE', '_yith_wcwtl_exclude_list' );
! defined( 'YITH_WCWTL_META_USER' ) && define( 'YITH_WCWTL_META_USER', '_yith_wcwtl_products_list' );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCWTL_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_WCWTL_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_WCWTL_DIR );

function yith_wcwtl_premium_init() {

	load_plugin_textdomain( 'yith-woocommerce-waiting-list', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Load required classes and functions
	require_once 'includes/function.yith-wcwtl.php';
	require_once 'includes/class.yith-wcwtl.php';

	// Let's start the game!
	YITH_WCWTL();
}

add_action( 'yith_wcwtl_premium_init', 'yith_wcwtl_premium_init' );


function yith_wcwtl_premium_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcwtl_premium_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wcwtl_premium_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wcwtl_premium_install', 11 );