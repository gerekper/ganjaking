<?php
/**
 * Plugin Name: YITH WooCommerce Ajax Product Filter Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-ajax-product-filter/
 * Description:<code><strong>YITH WooCommerce AJAX Product Filter</strong></code> allows your users to find the product they are looking for as quickly as possible. Thanks to the plugin you will be able to set up one or more search filters for your WooCommerce products and improve the user experience of your shop. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 4.26.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-ajax-navigation
 * Domain Path: /languages/
 *
 * WC requires at least: 7.9
 * WC tested up to: 8.1
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter
 * @version 4.26.0
 */

/**
 * Copyright 2023  YITH  (email : plugins@yithemes.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// define required constants.

! defined( 'YITH_WCAN' ) && define( 'YITH_WCAN', true );
! defined( 'YITH_WCAN_URL' ) && define( 'YITH_WCAN_URL', plugin_dir_url( __FILE__ ) );
! defined( 'YITH_WCAN_DIR' ) && define( 'YITH_WCAN_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCAN_INC' ) && define( 'YITH_WCAN_INC', YITH_WCAN_DIR . 'includes/' );
! defined( 'YITH_WCAN_ASSETS' ) && define( 'YITH_WCAN_ASSETS', YITH_WCAN_URL . 'assets/' );
! defined( 'YITH_WCAN_VERSION' ) && define( 'YITH_WCAN_VERSION', '4.26.0' );
! defined( 'YITH_WCAN_DB_VERSION' ) && define( 'YITH_WCAN_DB_VERSION', '4.0.0' );
! defined( 'YITH_WCAN_PREMIUM' ) && define( 'YITH_WCAN_PREMIUM', true );
! defined( 'YITH_WCAN_FILE' ) && define( 'YITH_WCAN_FILE', __FILE__ );
! defined( 'YITH_WCAN_SLUG' ) && define( 'YITH_WCAN_SLUG', 'yith-woocommerce-ajax-navigation' );
! defined( 'YITH_WCAN_SECRET_KEY' ) && define( 'YITH_WCAN_SECRET_KEY', 'VsQ4mRdupNhzcONEx1mj' );
! defined( 'YITH_WCAN_INIT' ) && define( 'YITH_WCAN_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCAN_PREMIUM_INIT' ) && define( 'YITH_WCAN_PREMIUM_INIT', plugin_basename( __FILE__ ) );

// define required functions.

if ( ! function_exists( 'yith_wcan_register_activation' ) ) {
	/**
	 * Register plugins among recently activated ones
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	function yith_wcan_register_activation() {
		if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
			require_once 'plugin-fw/yit-plugin-registration-hook.php';
		}

		register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

		if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
			include_once 'plugin-upgrade/functions-yith-licence.php';
		}

		register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );
	}
}

if ( ! function_exists( 'yith_wcan_install' ) ) {
	/**
	 * Installs plugin and start the processing
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	function yith_wcan_install() {
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_wcan_install_woocommerce_admin_notice' );
		} else {
			/**
			 * Instance main plugin class
			 */
			global $yith_wcan;

			if ( ! function_exists( 'yith_deactivate_plugins' ) ) {
				require_once 'plugin-fw/yit-deactive-plugin.php';
			}

			yith_deactivate_plugins( array( 'YITH_WCAN_FREE_INIT', 'YITH_WCAN_EXTENDED_INIT' ) );

			$yith_wcan = yith_wcan_initialize();
		}
	}
}

if ( ! function_exists( 'yith_wcan_initialize' ) ) {
	/**
	 * Unique access to instance of YITH_Vendors class.
	 *
	 * @return YITH_WCAN
	 * @since 1.0.0
	 */
	function yith_wcan_initialize() {
		// load plugin text domain.
		load_plugin_textdomain( 'yith-woocommerce-ajax-navigation', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// load required classes and functions.
		require_once YITH_WCAN_INC . 'class-yith-wcan.php';

		if ( defined( 'YITH_WCAN_PREMIUM' ) && file_exists( YITH_WCAN_DIR . 'includes/class-yith-wcan-premium.php' ) ) {
			require_once YITH_WCAN_INC . 'class-yith-wcan-extended.php';
			require_once YITH_WCAN_INC . 'class-yith-wcan-premium.php';
			return YITH_WCAN_Premium();
		} elseif ( defined( 'YITH_WCAN_EXTENDED' ) && file_exists( YITH_WCAN_DIR . 'includes/class-yith-wcan-extended.php' ) ) {
			require_once YITH_WCAN_INC . 'class-yith-wcan-extended.php';
			return YITH_WCAN_Extended();
		}

		return YITH_WCAN();
	}
}

if ( ! function_exists( 'yith_wcan_install_plugin_framework' ) ) {
	/**
	 * Performs check over plugin framework, and maybe loads it
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	function yith_wcan_install_plugin_framework() {
		// plugin framework version check.
		if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCAN_DIR . 'plugin-fw/init.php' ) ) {
			require_once YITH_WCAN_DIR . 'plugin-fw/init.php';
		}

		yit_maybe_plugin_fw_loader( YITH_WCAN_DIR );
	}
}

if ( ! function_exists( 'yith_wcan_install_woocommerce_admin_notice' ) ) {
	/**
	 * Print an admin notice if woocommerce is deactivated
	 *
	 * @return void
	 *
	 * @since 1.0
	 * @use admin_notices hooks
	 */
	function yith_wcan_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'YITH WooCommerce Ajax Product Filter is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-ajax-navigation' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'yith_wcan_deactivate_lower_tier_notice' ) ) {
	/**
	 * Print an admin notice if trying to activate this version when an higher tier is already enabled
	 *
	 * @return void
	 * @use    admin_notices hooks
	 * @since  1.0
	 */
	function yith_wcan_deactivate_lower_tier_notice() {

		?>
		<div class="notice">
			<p><?php esc_html_e( 'YITH WooCommerce Ajax Product Filter was deactivated as you\'re running an higher tier version of the same plugin.', 'yith-woocommerce-ajax-navigation' ); ?></p>
		</div>
		<?php
	}
}

// register activation.
yith_wcan_register_activation();

// load plugin framework.
yith_wcan_install_plugin_framework();

// install plugin.
add_action( 'plugins_loaded', 'yith_wcan_install', 11 );
