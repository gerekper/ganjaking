<?php
/**
 * Plugin Name: YITH WooCommerce Customize My Account Page
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-customize-myaccount-page
 * Description: The <code><strong>YITH WooCommerce Customize My Account Page</strong></code> lets you customize the layout of the "My Account" page, adds new endpoints and manage its content easily. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 2.6.4
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-customize-myaccount-page
 * Domain Path: /languages/
 * WC requires at least: 3.6
 * WC tested up to: 4.3
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 2.6.4
 */
/*  Copyright 2015-2019  YITH (email : plugins@yithemes.com)

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

function yith_wcmap_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce Customize My Account Page is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-customize-myaccount-page' ); ?></p>
	</div>
	<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( ! defined( 'YITH_WCMAP_VERSION' ) ) {
	define( 'YITH_WCMAP_VERSION', '2.6.4' );
}

if ( ! defined( 'YITH_WCMAP_PREMIUM' ) ) {
	define( 'YITH_WCMAP_PREMIUM', '1' );
}

if ( ! defined( 'YITH_WCMAP_INIT' ) ) {
	define( 'YITH_WCMAP_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCMAP' ) ) {
	define( 'YITH_WCMAP', true );
}

if ( ! defined( 'YITH_WCMAP_FILE' ) ) {
	define( 'YITH_WCMAP_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCMAP_URL' ) ) {
	define( 'YITH_WCMAP_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCMAP_DIR' ) ) {
	define( 'YITH_WCMAP_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCMAP_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCMAP_TEMPLATE_PATH', YITH_WCMAP_DIR . 'templates' );
}

if ( ! defined( 'YITH_WCMAP_ASSETS_URL' ) ) {
	define( 'YITH_WCMAP_ASSETS_URL', YITH_WCMAP_URL . 'assets' );
}

if ( ! defined( 'YITH_WCMAP_SLUG' ) ) {
	define( 'YITH_WCMAP_SLUG', 'yith-woocommerce-customize-myaccount-page' );
}

if ( ! defined( 'YITH_WCMAP_SECRET_KEY' ) ) {
	define( 'YITH_WCMAP_SECRET_KEY', 'cixkJNu5HBDxyL8inX8z' );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCMAP_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCMAP_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCMAP_DIR );

function yith_wcmap_init() {

	load_plugin_textdomain( 'yith-woocommerce-customize-myaccount-page', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Load required classes and functions
	require_once( 'includes/functions.yith-wcmap.php' );
	require_once( 'includes/class.yith-wcmap.php' );

	// Let's start the game!
	YITH_WCMAP();
}

add_action( 'yith_wcmap_init', 'yith_wcmap_init' );


function yith_wcmap_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcmap_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wcmap_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wcmap_install', 11 );