<?php
/**
 * Plugin Name: YITH WooCommerce Stripe Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-stripe/
 * Description: <code><strong>YITH WooCommerce Stripe</strong></code> allows your users to pay with credit card thanks to the integration with Stripe, a powerful and flexible payment gateway. It lets you get payments with credit card and assures your users of the reliability of an international partner. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>
 * Version: 2.0.10
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-stripe
 * Domain Path: /languages
 * WC requires at least: 2.4.0
 * WC tested up to: 4.2
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.9.1
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
if( ! defined( 'ABSPATH' ) ){
	exit;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Woocommerce installation check _________________________

if ( ! function_exists( 'WC' ) ) {
	function yith_stripe_premium_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( 'YITH WooCommerce Stripe Payment Gateway is enabled but not effective. It requires Woocommerce in order to work.', 'yith-woocommerce-stripe' ); ?></p>
		</div>
	<?php
	}

	add_action( 'admin_notices', 'yith_stripe_premium_install_woocommerce_admin_notice' );
	return;
}

// Free version deactivation if installed __________________

if( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCSTRIPE_FREE_INIT', plugin_basename( __FILE__ ) );

// Register WP_Pointer Handling
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! defined( 'YITH_WCSTRIPE_PREMIUM' ) ) {
    define( 'YITH_WCSTRIPE_PREMIUM', true );
}

if ( defined( 'YITH_WCSTRIPE_VERSION' ) ) {
	return;
}else{
	define( 'YITH_WCSTRIPE_VERSION', '2.0.10' );
}

if( ! defined( 'YITH_WCSTRIPE_API_VERSION' ) ){
    define( 'YITH_WCSTRIPE_API_VERSION', '2020-03-02' );
}

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	define( 'YITH_WCSTRIPE', true );
}

if ( ! defined( 'YITH_WCSTRIPE_FILE' ) ) {
	define( 'YITH_WCSTRIPE_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WCSTRIPE_URL' ) ) {
	define( 'YITH_WCSTRIPE_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WCSTRIPE_DIR' ) ) {
	define( 'YITH_WCSTRIPE_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WCSTRIPE_INC' ) ) {
	define( 'YITH_WCSTRIPE_INC', YITH_WCSTRIPE_DIR . 'includes/' );
}

if ( ! defined( 'YITH_WCSTRIPE_INIT' ) ) {
	define( 'YITH_WCSTRIPE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WCSTRIPE_SLUG' ) ) {
	define( 'YITH_WCSTRIPE_SLUG', 'yith-woocommerce-stripe' );
}

if ( ! defined( 'YITH_WCSTRIPE_SECRET_KEY' ) ) {
	define( 'YITH_WCSTRIPE_SECRET_KEY', 'xN7pcYyiih52eOLet4yn' );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCSTRIPE_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCSTRIPE_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCSTRIPE_DIR  );

if ( ! function_exists( 'YITH_WCStripe' ) ) {
	/**
	 * Unique access to instance of YITH_WCStripe class
	 *
	 * @return \YITH_WCStripe|YITH_WCStripe_Premium
	 * @since 1.0.0
	 */
	function YITH_WCStripe() {
		// Load required classes and functions
		require_once( YITH_WCSTRIPE_INC . 'class-yith-stripe.php' );

		if ( defined( 'YITH_WCSTRIPE_PREMIUM' ) && file_exists( YITH_WCSTRIPE_INC . 'class-yith-stripe-premium.php' ) ) {
			require_once( YITH_WCSTRIPE_INC . 'class-yith-stripe-premium.php' );
			return YITH_WCStripe_Premium::get_instance();
		}

		return YITH_WCStripe::get_instance();
	}
}

if ( ! function_exists( 'yith_stripe_constructor' ) ) {
	function yith_stripe_constructor() {
		load_plugin_textdomain( 'yith-woocommerce-stripe', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		YITH_WCStripe();
	}
}
add_action( 'plugins_loaded', 'yith_stripe_constructor' );