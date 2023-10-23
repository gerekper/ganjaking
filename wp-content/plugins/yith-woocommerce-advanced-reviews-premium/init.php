<?php
/**
 * Plugin Name: YITH WooCommerce Advanced Reviews Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-advanced-reviews/
 * Description: <code><strong>YITH WooCommerce Advanced Reviews</strong></code> extends the basic functionality of WooCommerce reviews and add a histogram table to the reviews of your products, such as you see in most trendy e-commerce sites. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>.
 * Version: 1.32.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-advanced-reviews
 * Domain Path: /languages/
 * WC requires at least: 8.0
 * WC tested up to: 8.2
 *
 * @package       YITH\yit-woocommerce-advanced-reviews
 **/

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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


defined( 'YITH_YWAR_INIT' ) || define( 'YITH_YWAR_INIT', plugin_basename( __FILE__ ) );
defined( 'YITH_YWAR_SLUG' ) || define( 'YITH_YWAR_SLUG', 'yith-woocommerce-advanced-reviews' );
defined( 'YITH_YWAR_SECRET_KEY' ) || define( 'YITH_YWAR_SECRET_KEY', 'wbJGFwHx426IS4V4vYeB' );
defined( 'YITH_YWAR_VERSION' ) || define( 'YITH_YWAR_VERSION', '1.32.0' );
defined( 'YITH_YWAR_PREMIUM' ) || define( 'YITH_YWAR_PREMIUM', '1' );
defined( 'YITH_YWAR_FILE' ) || define( 'YITH_YWAR_FILE', __FILE__ );
defined( 'YITH_YWAR_DIR' ) || define( 'YITH_YWAR_DIR', plugin_dir_path( __FILE__ ) );
defined( 'YITH_YWAR_INCLUDES_DIR' ) || define( 'YITH_YWAR_INCLUDES_DIR', YITH_YWAR_DIR . '/includes/' );
defined( 'YITH_YWAR_URL' ) || define( 'YITH_YWAR_URL', plugins_url( '/', __FILE__ ) );
defined( 'YITH_YWAR_ASSETS_URL' ) || define( 'YITH_YWAR_ASSETS_URL', YITH_YWAR_URL . 'assets' );
defined( 'YITH_YWAR_TEMPLATES_DIR' ) || define( 'YITH_YWAR_TEMPLATES_DIR', YITH_YWAR_DIR . 'templates/' );
defined( 'YITH_YWAR_VIEWS_PATH' ) || define( 'YITH_YWAR_VIEWS_PATH', YITH_YWAR_DIR . 'views/' );

require_once YITH_YWAR_DIR . 'functions.php';

yith_initialize_plugin_fw( plugin_dir_path( __FILE__ ) );

yit_deactive_free_version( 'YITH_YWAR_FREE_INIT', plugin_basename( __FILE__ ) );
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );

/**
 * Yith_ywar_premium_init
 *
 * @return void
 */
function yith_ywar_premium_init() {
	/**
	* Load text domain and start plugin
	*/
	load_plugin_textdomain( 'yith-woocommerce-advanced-reviews', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once YITH_YWAR_INCLUDES_DIR . 'class.yith-woocommerce-advanced-reviews.php';
	require_once YITH_YWAR_INCLUDES_DIR . 'class.ywar-review.php';
	require_once YITH_YWAR_INCLUDES_DIR . 'class.yith-woocommerce-advanced-reviews-premium.php';

	global $YWAR_AdvancedReview;// phpcs:ignore WordPress.NamingConventions
	$YWAR_AdvancedReview = YITH_YWAR();// phpcs:ignore WordPress.NamingConventions
}

if ( ! function_exists( 'yith_advanced_reviews_instance' ) ) {
	/**
	 * Get the plugin main class
	 *
	 * @author     YITH <plugins@yithemes.com>
	 * @deprecated YITH_YWAR
	 * @since      1.0.0
	 */
	function yith_advanced_reviews_instance() {

		return YITH_YWAR();
	}
}

if ( ! function_exists( 'YITH_YWAR' ) ) {
	/**
	 * Get the plugin main class
	 *
	 * @since  1.0.0
	 */
	function YITH_YWAR() {// phpcs:ignore WordPress.NamingConventions

		return YITH_WooCommerce_Advanced_Reviews_Premium::get_instance();
	}
}

if ( ! function_exists( 'yith_ywar_premium_install' ) ) {
	/**
	 * Start the plugin
	 *
	 * @since  1.0.0
	 */
	function yith_ywar_premium_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywar_install_woocommerce_admin_notice' );
		} else {
			/** DO_ACTION: yith_ywar_premium_init
			*
			* Adds an action on installing.
			*/
			do_action( 'yith_ywar_premium_init' );
		}
	}
}

add_action( 'yith_ywar_premium_init', 'yith_ywar_premium_init' );
add_action( 'plugins_loaded', 'yith_ywar_premium_install', 11 );
