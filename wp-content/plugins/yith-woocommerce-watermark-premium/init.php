<?php
/**
 * Plugin Name: YITH WooCommerce Watermark Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-watermark/
 * Description: <strong><code>YITH WooCommerce Watermark Premium</code></strong> allows you to set a watermark in your product image. You can create several watermarks and apply them also to your product pages. <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.2.7
 * Author: YITH
 * Author URI: https://yithemes.com/
 * WC requires at least: 3.4.0
 * WC tested up to: 4.1
 * Text Domain: yith-woocommerce-watermark
 * Domain Path: /languages/
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Watermark Premium
 * @version 1.2.7
 */

/*  Copyright 2013  Your Inspiration Themes  (email : plugins@yithemes.com)

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
}

//region    ****    Check if prerequisites are satisfied before enabling and using current plugin
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function yith_ywcwat_premium_install_woocommerce_admin_notice() {
	?>
    <div class="error">
        <p><?php _e( 'YITH WooCommerce Watermark Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-watermark' ); ?></p>
    </div>
	<?php
}

/**
 * Check if a free version is currently active and try disabling before activating this one
 */
if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YWCWAT_FREE_INIT', plugin_basename( __FILE__ ) );

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

//endregion

//region    ****    Define constants  ****
if ( ! defined( 'YWCWAT_VERSION' ) ) {
	define( 'YWCWAT_VERSION', '1.2.7' );
}
if ( ! defined( 'YWCWAT_PREMIUM' ) ) {
	define( 'YWCWAT_PREMIUM', '1' );
}
if ( ! defined( 'YWCWAT_DB_VERSION' ) ) {
	define( 'YWCWAT_DB_VERSION', '1.2.0' );
}
if ( ! defined( 'YWCWAT_INIT' ) ) {
	define( 'YWCWAT_INIT', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'YWCWAT_FILE' ) ) {
	define( 'YWCWAT_FILE', __FILE__ );
}

if ( ! defined( 'YWCWAT_DIR' ) ) {
	define( 'YWCWAT_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YWCWAT_URL' ) ) {
	define( 'YWCWAT_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YWCWAT_ASSETS_URL' ) ) {
	define( 'YWCWAT_ASSETS_URL', YWCWAT_URL . 'assets/' );
}

if ( ! defined( 'YWCWAT_ASSETS_PATH' ) ) {
	define( 'YWCWAT_ASSETS_PATH', YWCWAT_DIR . 'assets/' );
}

if ( ! defined( 'YWCWAT_TEMPLATE_PATH' ) ) {
	define( 'YWCWAT_TEMPLATE_PATH', YWCWAT_DIR . 'templates/' );
}

if ( ! defined( 'YWCWAT_INC' ) ) {
	define( 'YWCWAT_INC', YWCWAT_DIR . 'includes/' );
}

if ( ! defined( 'YWCWAT_BACKUP_FILE' ) ) {
	define( 'YWCWAT_BACKUP_FILE', '_ywcwat_original_' );
}

if ( ! defined( 'YWCWAT_SLUG' ) ) {
	define( 'YWCWAT_SLUG', 'yith-woocommerce-watermark' );
}

if ( ! defined( 'YWCWAT_SECRET_KEY' ) ) {
	define( 'YWCWAT_SECRET_KEY', 'SlDh0GZSh7giQSXwGdur' );
}

if ( ! defined( 'YWCWAT_PRIVATE_DIR' ) ) {
	define( 'YWCWAT_PRIVATE_DIR', 'yith_watermark_backup' );
}
//endregion


/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWCWAT_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YWCWAT_DIR . 'plugin-fw/init.php' );
}

yit_maybe_plugin_fw_loader( YWCWAT_DIR );

if ( ! function_exists( 'YITH_Watermark_Premium_Init' ) ) {

	function YITH_Watermark_Premium_Init() {
		load_plugin_textdomain( 'yith-woocommerce-watermark', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		require_once( YWCWAT_INC . 'functions.yith-wc-watermark.php' );
		require_once( YWCWAT_INC . 'classes/class.yith-woocommerce-watermark-premium.php' );

		global $YWC_Watermark_Instance;
		$YWC_Watermark_Instance = YITH_WC_Watermark_Premium::get_instance();
	}
}
add_action( 'ywcwat_premium_init', 'YITH_Watermark_Premium_Init' );

if ( ! function_exists( 'ywcwat_premium_install' ) ) {

	function ywcwat_premium_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywcwat_premium_install_woocommerce_admin_notice' );
		} else {
			do_action( 'ywcwat_premium_init' );
		}
	}
}

add_action( 'plugins_loaded', 'ywcwat_premium_install', 11 );
