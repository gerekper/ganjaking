<?php
/**
 * Plugin Name: YITH WooCommerce Role Based Prices Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-role-based-prices/
 * Description: <code><strong>YITH WooCommerce Role Based Prices</strong></code> allows the admin to add a discount or markup price rule for users! It is also possible to show the price included or excluded tax by user role! <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 1.2.2
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-role-based-prices
 * Domain Path: /languages/
 * WC requires at least: 3.3.0
 * WC tested up to: 4.2
 * @author YITH
 * @package YITH WooCommerce Role Based Prices Premium
 * @version 1.2.2
 */

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function yith_wc_rbp_premium_install_woocommerce_admin_notice() {
	?>
    <div class="error">
        <p><?php _e( 'YITH WooCommerce Role Based Prices Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-role-based-prices' ); ?></p>
    </div>
	<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {

	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


register_activation_hook( __FILE__, 'yith_rolebased_flush_rules' );

function yith_rolebased_flush_rules() {
	// call your CPT registration function here (it should also be hooked into 'init')

	if ( ! function_exists( 'register_role_based_post_type' ) ) {
		require_once( YWCRBP_INC . 'class.yith-role-based-prices-post-type.php' );
		require_once( YWCRBP_INC . 'functions.yith-wc-role-based-prices.php' );

	}
	register_role_based_post_type();
	flush_rewrite_rules();
}


if ( ! defined( 'YWCRBP_VERSION' ) ) {
	define( 'YWCRBP_VERSION', '1.2.2' );
}

if ( ! defined( 'YWCRBP_PREMIUM' ) ) {
	define( 'YWCRBP_PREMIUM', '1' );
}

if ( ! defined( 'YWCRBP_INIT' ) ) {
	define( 'YWCRBP_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YWCRBP_FILE' ) ) {
	define( 'YWCRBP_FILE', __FILE__ );
}

if ( ! defined( 'YWCRBP_DIR' ) ) {
	define( 'YWCRBP_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YWCRBP_URL' ) ) {
	define( 'YWCRBP_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YWCRBP_ASSETS_URL' ) ) {
	define( 'YWCRBP_ASSETS_URL', YWCRBP_URL . 'assets/' );
}

if ( ! defined( 'YWCRBP_ASSETS_PATH' ) ) {
	define( 'YWCRBP_ASSETS_PATH', YWCRBP_DIR . 'assets/' );
}

if ( ! defined( 'YWCRBP_TEMPLATE_PATH' ) ) {
	define( 'YWCRBP_TEMPLATE_PATH', YWCRBP_DIR . 'templates/' );
}

if ( ! defined( 'YWCRBP_INC' ) ) {
	define( 'YWCRBP_INC', YWCRBP_DIR . 'includes/' );
}
if ( ! defined( 'YWCRBP_SLUG' ) ) {
	define( 'YWCRBP_SLUG', 'yith-woocommerce-role-based-prices' );
}

if ( ! defined( 'YWCRBP_SECRET_KEY' ) ) {

	define( 'YWCRBP_SECRET_KEY', 'I4EtR0CAAoow4Be7kDut' );
}


/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWCRBP_DIR . 'plugin-fw/init.php' ) ) {

	require_once( YWCRBP_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YWCRBP_DIR );


if ( ! function_exists( 'yith_role_based_prices_premium_init' ) ) {
	/**
	 * Unique access to instance of YITH_Role_Based_Prices class
	 *
	 * @return YITH_Role_Based_Prices
	 * @since 1.0.0
	 */
	function yith_role_based_prices_premium_init() {

		load_plugin_textdomain( 'yith-woocommerce-role-based-prices', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


		require_once( YWCRBP_INC . 'class.yith-role-based-prices-post-type.php' );
		require_once( YWCRBP_INC . 'functions.yith-wc-role-based-prices.php' );
		require_once( YWCRBP_INC . 'functions.yith-role-based-prices-db-update.php' );
		require_once( YWCRBP_INC . 'class.yith-role-based-prices-admin.php' );
		require_once( YWCRBP_INC . 'class.yith-role-based-prices-table.php' );
		require_once( YWCRBP_INC . 'class.yith-role-based-prices-product.php' );
		require_once( YWCRBP_INC . 'class.yith-role-based-prices.php' );
		require_once( YWCRBP_INC . '/third-party/class.yith-ywrbp-compatibilities.php' );


		global $YITH_Role_Based_Prices;

		$YITH_Role_Based_Prices = YITH_Role_Based_Prices::get_instance();

	}
}

add_action( 'ywcrbp_premium_init', 'yith_role_based_prices_premium_init' );

if ( ! function_exists( 'yith_role_based_prices_premium_install' ) ) {

	function yith_role_based_prices_premium_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_wc_rbp_premium_install_woocommerce_admin_notice' );
		} else {
			do_action( 'ywcrbp_premium_init' );
		}
	}
}

add_action( 'plugins_loaded', 'yith_role_based_prices_premium_install', 20 );
