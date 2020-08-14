<?php
/**
 * Plugin Name: YITH WooCommerce Badge Management Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-badges-management/
 * Description: <code><strong>YITH WooCommerce Badge Management</strong></code> allows you to highlight your products through custom badges, that show offers, discounts, and so on. You can use text badges, CSS badges, image badges and advanced badges to attract your customers. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.4.3
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-badges-management
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.3.x
 *
 * @author  YITH
 * @package YITH WooCommerce Badge Management
 * @version 1.4.3
 * /*  Copyright 2015  Your Inspiration Themes  (email : plugins@yithemes.com)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Free version deactivation if installed!
if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCBM_FREE_INIT', plugin_basename( __FILE__ ) );

/**
 * Admin notice if WC is not installed
 */
function yith_wcbm_pr_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php esc_html_e( 'YITH WooCommerce Badge Management is enabled but not effective. It requires Woocommerce in order to work.', 'yith-woocommerce-badges-management' ); ?></p>
	</div>
	<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( ! defined( 'YITH_WCBM_VERSION' ) ) {
	define( 'YITH_WCBM_VERSION', '1.4.3' );
}

if ( ! defined( 'YITH_WCBM_PREMIUM' ) ) {
	define( 'YITH_WCBM_PREMIUM', '1' );
}

if ( ! defined( 'YITH_WCBM_INIT' ) ) {
	define( 'YITH_WCBM_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCBM' ) ) {
	define( 'YITH_WCBM', true );
}

if ( ! defined( 'YITH_WCBM_FILE' ) ) {
	define( 'YITH_WCBM_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCBM_URL' ) ) {
	define( 'YITH_WCBM_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCBM_DIR' ) ) {
	define( 'YITH_WCBM_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCBM_TEMPLATE_PATH' ) ) {
	define( 'YITH_WCBM_TEMPLATE_PATH', YITH_WCBM_DIR . 'templates' );
}

if ( ! defined( 'YITH_WCBM_ASSETS_URL' ) ) {
	define( 'YITH_WCBM_ASSETS_URL', YITH_WCBM_URL . 'assets' );
}

if ( ! defined( 'YITH_WCBM_SLUG' ) ) {
	define( 'YITH_WCBM_SLUG', 'yith-woocommerce-badges-management' );
}

if ( ! defined( 'YITH_WCBM_INCLUDES_PATH' ) ) {
	define( 'YITH_WCBM_INCLUDES_PATH', YITH_WCBM_DIR . 'includes' );
}

if ( ! defined( 'YITH_WCBM_COMPATIBILITY_PATH' ) ) {
	define( 'YITH_WCBM_COMPATIBILITY_PATH', YITH_WCBM_INCLUDES_PATH . '/compatibility' );
}

if ( ! defined( 'YITH_WCBM_SECRET_KEY' ) ) {
	define( 'YITH_WCBM_SECRET_KEY', 'u6YZJsaoKkwhYSIUgZ6X' );
}

/**
 * Init.
 */
function yith_wcbm_pr_init() {
	load_plugin_textdomain( 'yith-woocommerce-badges-management', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once 'includes/class-yith-wcbm-admin.php';
	require_once 'includes/class-yith-wcbm-frontend.php';
	require_once 'includes/class-yith-wcbm.php';
	require_once 'includes/class-yith-wcbm-admin-premium.php';
	require_once 'includes/class-yith-wcbm-frontend-premium.php';
	require_once 'includes/class-yith-wcbm-premium.php';
	require_once 'includes/class-yith-wcbm-post-types.php';
	require_once 'includes/functions.yith-wcbm-premium.php';
	require_once 'includes/functions.yith-wcbm.php';
	require_once 'includes/compatibility/class-yith-wcbm-compatibility.php';
	require_once 'includes/class-yith-wcbm-shortcodes.php';

	// Let's start the game!
	YITH_WCBM();
}

add_action( 'yith_wcbm_pr_init', 'yith_wcbm_pr_init' );

/**
 * Install
 */
function yith_wcbm_pr_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcbm_pr_install_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wcbm_pr_init' );
	}
}

add_action( 'plugins_loaded', 'yith_wcbm_pr_install', 11 );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );
