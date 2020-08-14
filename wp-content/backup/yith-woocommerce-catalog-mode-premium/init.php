<?php
/**
 * Plugin Name: YITH WooCommerce Catalog Mode Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-catalog-mode/
 * Description: <code><strong>YITH WooCommerce Catalog Mode</strong></code> allows hiding product prices, cart and checkout from your store and turning it into a performing product catalogue. You will be able to adjust your catalogue settings as you prefer based on your requirements. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Author: YITH
 * Text Domain: yith-woocommerce-catalog-mode
 * Version: 2.0.8
 * Author URI: https://yithemes.com/
 * WC requires at least: 4.0.0
 * WC tested up to: 4.3.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function ywctm_install_premium_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p>
			<?php
			/* translators: %s name of the plugin */
			echo sprintf( esc_html__( '%s is enabled but not effective. In order to work, it requires WooCommerce.', 'yith-woocommerce-catalog-mode' ), 'YITH WooCommerce Catalog Mode' );
			?>
		</p>
	</div>
	<?php
}

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}

yit_deactive_free_version( 'YWCTM_FREE_INIT', plugin_basename( __FILE__ ) );

if ( function_exists( 'yith_deactive_jetpack_module' ) ) {
	global $yith_jetpack_1;
	yith_deactive_jetpack_module( $yith_jetpack_1, 'YWCTM_PREMIUM', plugin_basename( __FILE__ ) );
}

! defined( 'YWCTM_VERSION' ) && define( 'YWCTM_VERSION', '2.0.8' );
! defined( 'YWCTM_INIT' ) && define( 'YWCTM_INIT', plugin_basename( __FILE__ ) );
! defined( 'YWCTM_SLUG' ) && define( 'YWCTM_SLUG', 'yith-woocommerce-catalog-mode' );
! defined( 'YWCTM_SECRET_KEY' ) && define( 'YWCTM_SECRET_KEY', '8KywmSzFxgb5m0SFKMac' );
! defined( 'YWCTM_PREMIUM' ) && define( 'YWCTM_PREMIUM', '1' );
! defined( 'YWCTM_FILE' ) && define( 'YWCTM_FILE', __FILE__ );
! defined( 'YWCTM_DIR' ) && define( 'YWCTM_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'YWCTM_URL' ) && define( 'YWCTM_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YWCTM_ASSETS_URL' ) && define( 'YWCTM_ASSETS_URL', YWCTM_URL . 'assets/' );
! defined( 'YWCTM_ASSETS_PATH' ) && define( 'YWCTM_ASSETS_PATH', YWCTM_DIR . 'assets/' );
! defined( 'YWCTM_TEMPLATE_PATH' ) && define( 'YWCTM_TEMPLATE_PATH', YWCTM_DIR . 'templates/' );


/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWCTM_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YWCTM_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YWCTM_DIR );

function ywctm_premium_init() {
	/* Load YWCTM text domain */
	load_plugin_textdomain( 'yith-woocommerce-catalog-mode', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	$GLOBALS['YITH_WC_Catalog_Mode'] = YITH_WCTM();

}

add_action( 'ywctm_premium_init', 'ywctm_premium_init' );

function ywctm_premium_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'ywctm_install_premium_woocommerce_admin_notice' );
	} else {
		do_action( 'ywctm_premium_init' );
	}

}

add_action( 'plugins_loaded', 'ywctm_premium_install', 11 );

/**
 * Init default plugin settings
 */
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'YITH_WCTM' ) ) {

	/**
	 * Unique access to instance of YITH_WC_Catalog_Mode
	 *
	 * @return  YITH_WooCommerce_Catalog_Mode|YITH_WooCommerce_Catalog_Mode_Premium
	 * @since   1.1.5
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function YITH_WCTM() { //phpcs:ignore

		// Load required classes and functions
		require_once( YWCTM_DIR . 'class-yith-woocommerce-catalog-mode.php' );

		if ( defined( 'YWCTM_PREMIUM' ) && file_exists( YWCTM_DIR . 'class-yith-woocommerce-catalog-mode-premium.php' ) ) {

			require_once( YWCTM_DIR . 'class-yith-woocommerce-catalog-mode-premium.php' );

			return YITH_WooCommerce_Catalog_Mode_Premium::get_instance();
		}

		return YITH_WooCommerce_Catalog_Mode::get_instance();

	}
}
