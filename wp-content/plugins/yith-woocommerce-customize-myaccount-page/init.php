<?php
/**
 * Plugin Name: YITH WooCommerce Customize My Account Page Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-customize-myaccount-page
 * Description: The <code><strong>YITH WooCommerce Customize My Account Page</strong></code> lets you customize the layout of the "My Account" page, adds new endpoints and manage its content easily. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 4.2.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-customize-myaccount-page
 * Domain Path: /languages/
 * WC requires at least: 8.0
 * WC tested up to: 8.2
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 4.2.0
 */

/*
Copyright 2015-2023 Your Inspiration Solutions (email : plugins@yithemes.com)

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

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'yith_wcmap_install_woocommerce_admin_notice' ) ) {
	/**
	 * Add notice if WooCommerce is missing.
	 *
	 * @since  1.0.0
	 */
	function yith_wcmap_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php echo wp_kses_post( __( '<b>YITH WooCommerce Customize My Account Page</b> is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-customize-myaccount-page' ) ); ?></p>
		</div>
		<?php
	}
}

defined( 'YITH_WCMAP_VERSION' ) || define( 'YITH_WCMAP_VERSION', '4.2.0' );
defined( 'YITH_WCMAP_PREMIUM' ) || define( 'YITH_WCMAP_PREMIUM', true );
defined( 'YITH_WCMAP_INIT' ) || define( 'YITH_WCMAP_INIT', plugin_basename( __FILE__ ) );
defined( 'YITH_WCMAP' ) || define( 'YITH_WCMAP', true );
defined( 'YITH_WCMAP_FILE' ) || define( 'YITH_WCMAP_FILE', __FILE__ );
defined( 'YITH_WCMAP_URL' ) || define( 'YITH_WCMAP_URL', plugin_dir_url( __FILE__ ) );
defined( 'YITH_WCMAP_DIR' ) || define( 'YITH_WCMAP_DIR', plugin_dir_path( __FILE__ ) );
defined( 'YITH_WCMAP_TEMPLATE_PATH' ) || define( 'YITH_WCMAP_TEMPLATE_PATH', YITH_WCMAP_DIR . 'templates/' );
defined( 'YITH_WCMAP_ASSETS_URL' ) || define( 'YITH_WCMAP_ASSETS_URL', YITH_WCMAP_URL . 'assets' );
defined( 'YITH_WCMAP_SLUG' ) || define( 'YITH_WCMAP_SLUG', 'yith-woocommerce-customize-myaccount-page' );
defined( 'YITH_WCMAP_SECRET_KEY' ) || define( 'YITH_WCMAP_SECRET_KEY', 'cixkJNu5HBDxyL8inX8z' );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCMAP_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_WCMAP_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_WCMAP_DIR );

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}

// Require plugin autoload.
if ( ! class_exists( 'YITH_WCMAP_Autoloader', false ) ) {
	require_once YITH_WCMAP_DIR . 'includes/class-yith-wcmap-autoloader.php';
}

if ( ! function_exists( 'yith_wcmap_init' ) ) {
	/**
	 * Install plugin.
	 *
	 * @since  1.0.0
	 */
	function yith_wcmap_init() {
		if ( ! function_exists( 'yith_deactivate_plugins' ) ) {
			require_once 'plugin-fw/yit-deactive-plugin.php';
		}

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_wcmap_install_woocommerce_admin_notice' );
		} elseif ( defined( 'YITH_WCMAP_EXTENDED_INIT' ) ) {
			yith_deactivate_plugins( 'YITH_WCMAP_EXTENDED_INIT' );
		} else {
			// Load required classes and functions.
			require_once 'includes/yith-wcmap-functions-premium.php';
			require_once 'includes/yith-wcmap-functions.php';
			require_once 'includes/class-yith-wcmap.php';
			// Let's start the game!
			YITH_WCMAP();
		}
	}
}

add_action( 'plugins_loaded', 'yith_wcmap_init', 11 );
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );

add_action( 'before_woocommerce_init', 'yith_wcmap_declare_hpos_compatibility' );

if ( ! function_exists( 'yith_wcmap_declare_hpos_compatibility' ) ) {
	/**
	 * Declare HPOS compatibility
	 *
	 * @return void
	 * @since  3.19.0
	 */
	function yith_wcmap_declare_hpos_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
}
