<?php

/**
 * Plugin Name: YITH Product Shipping for WooCommerce Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-product-shipping-for-woocommerce/
 * Description: <code><strong>YITH Product Shipping for WooCommerce Premium</strong></code> is the plugin that allows you to create general shipping rules or rules per single product. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.0.26
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-product-shipping-for-woocommerce
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages/
 * Requires at least: 4.5
 * Tested up to: 5.4
 * WC requires at least: 3.0
 * WC tested up to: 4.2
 *
 * @author  YITH
 * @package YITH Product Shipping for WooCommerce Premium
 *
 * Copyright 2012-2018 - Your Inspiration Themes - All right reserved.
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
}

if ( ! function_exists( 'yith_wcps_install_free_woocommerce_admin_notice' ) ) {
	/**
	 * Print an admin notice if woocommerce is deactivated
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 * @since  1.0
	 * @return void
	 * @use admin_notices hooks
	 */
	function yith_wcps_install_free_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( 'YITH Product Shipping for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 'yith-product-shipping-for-woocommerce' ); ?></p>
		</div>
	<?php
	}
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

! defined( 'YITH_WCPS_DIR' ) && define( 'YITH_WCPS_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin Framework Version Check
 */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCPS_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCPS_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCPS_DIR );

/**
 * This version can't be activate if premium version is active
 */
if ( defined( 'YITH_WCPS_PREMIUM' ) ) {
	function yith_wcps_install_free_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( 'You can\'t activate the free version of YITH Product Shipping for WooCommerce while you are using the premium one.', 'yith-product-shipping-for-woocommerce' ); ?></p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'yith_wcps_install_free_admin_notice' );
	deactivate_plugins( plugin_basename( __FILE__ ) );
	return;
}

/**
 * Plugin Constant
 */
! defined( 'YITH_WCPS' )						&& define( 'YITH_WCPS', true );
! defined( 'YITH_WCPS_URL' )					&& define( 'YITH_WCPS_URL', plugin_dir_url( __FILE__ ) );
! defined( 'YITH_WCPS_NAME' )					&& define( 'YITH_WCPS_NAME', 'YITH Product Shipping for WooCommerce Premium' );
! defined( 'YITH_WCPS_TEMPLATE_PATH' )			&& define( 'YITH_WCPS_TEMPLATE_PATH', YITH_WCPS_DIR . 'templates' );
! defined( 'YITH_WCPS_TEMPLATE_ADMIN_PATH' )	&& define( 'YITH_WCPS_TEMPLATE_ADMIN_PATH', YITH_WCPS_TEMPLATE_PATH . '/yith_wcps/admin/' );
! defined( 'YITH_WCPS_TEMPLATE_FRONTEND_PATH' )	&& define( 'YITH_WCPS_TEMPLATE_FRONTEND_PATH', YITH_WCPS_TEMPLATE_PATH . '/yith_wcps/frontend/' );
! defined( 'YITH_WCPS_ASSETS_URL' )				&& define( 'YITH_WCPS_ASSETS_URL', YITH_WCPS_URL . 'assets' );
! defined( 'YITH_WCPS_VERSION' )				&& define( 'YITH_WCPS_VERSION', '1.0.26' );
! defined( 'YITH_WCPS_DB_VERSION' )				&& define( 'YITH_WCPS_DB_VERSION', '1.0.24' );
! defined( 'YITH_WCPS_FILE' )					&& define( 'YITH_WCPS_FILE', __FILE__ );
! defined( 'YITH_WCPS_SLUG' )					&& define( 'YITH_WCPS_SLUG', 'yith-product-shipping-for-woocommerce' );
! defined( 'YITH_WCPS_LOCALIZE_SLUG' )			&& define( 'YITH_WCPS_LOCALIZE_SLUG', 'yith-product-shipping-for-woocommerce' );
! defined( 'YITH_WCPS_SECRET_KEY' )				&& define( 'YITH_WCPS_SECRET_KEY', 'QpQrmhtxFy1cOKvY3IDG' );
! defined( 'YITH_WCPS_INIT' )					&& define( 'YITH_WCPS_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCPS_FREE_INIT' )				&& define( 'YITH_WCPS_FREE_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCPS_WPML_CONTEXT' )			&& define( 'YITH_WCPS_WPML_CONTEXT', 'YITH Product Shipping for WooCommerce' );
! defined( 'YITH_WCPS_DOCUMENTATION' )			&& define( 'YITH_WCPS_DOCUMENTATION', 'https://docs.yithemes.com/yith-product-shipping-for-woocommerce' );
! defined( 'YITH_WCPS_PREMIUM' )				&& define( 'YITH_WCPS_PREMIUM', '1' );

if ( ! function_exists( 'YITH_WCPS' ) ) {
	/**
	 * Unique access to instance of the class
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function YITH_WCPS() {

		/**
		 * Load Functions
		 */
		require_once( YITH_WCPS_DIR . 'includes/functions/yith-wc-product-shipping-row.php' );
		require_once( YITH_WCPS_DIR . 'includes/functions/yith-wcps-is-wcfm.php' );

		/**
		 * Load Classes
		 */
		if ( is_admin() || defined( 'YITH_WCFM_VERSION' ) ) {
			global $yith_wc_product_shipping_admin;
			require_once( YITH_WCPS_DIR . 'includes/class.yith-woocommerce-product-shipping-admin.php' );
			$yith_wc_product_shipping_admin = new YITH_WooCommerce_Product_Shipping_Admin();
		}

		require_once( YITH_WCPS_DIR . 'includes/class.yith-woocommerce-product-shipping.php' );
		require_once( YITH_WCPS_DIR . 'includes/class.yith-woocommerce-product-shipping-method.php' );

		/**
		 * Istance
		 */
		YITH_WooCommerce_Product_Shipping::instance();

		/**
		 * Premium version
		 */
		if ( defined( 'YITH_WCPS_PREMIUM' ) && file_exists( YITH_WCPS_DIR . 'includes/class.yith-woocommerce-product-shipping-premium.php' ) ) {
			require_once( YITH_WCPS_DIR . 'includes/class.yith-woocommerce-product-shipping-premium.php' );
			YITH_WooCommerce_Product_Shipping_Premium::instance();
		}
		
	}
}

/**
 * Require core files
 *
 * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
 * @since  1.0
 * @return void
 * @use Load plugin core
 */
function yith_wcps_free_init() {
	load_plugin_textdomain( YITH_WCPS_LOCALIZE_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	YITH_WCPS();
}
add_action( 'yith_wcps_free_init', 'yith_wcps_free_init' );

/**
 * YITH WooCommerce Product Shipping Free Install
 *
 * @since  1.0
 */
function yith_wcps_free_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wcps_install_free_woocommerce_admin_notice' );
	} else {
		do_action( 'yith_wcps_free_init' );
	}

}
add_action( 'plugins_loaded', 'yith_wcps_free_install', 12 );
