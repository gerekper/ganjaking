<?php
/**
 * Plugin Name: YITH WooCommerce Category Accordion Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-category-accordion/
 * Description: With <code><strong>YITH WooCommerce Category Accordion Premium</strong></code> you can add an accordion menu to your sidebars in a few clicks to view product or post categories! <a href ="https://yithemes.com">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Version: 2.2.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-category-accordion
 * Domain Path: /languages/
 * WC requires at least: 8.0
 * WC tested up to: 8.2
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\CategoryAccordion
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Yith_ywcca_install_woocommerce_admin_notice
 *
 * @return void
 */
function yith_ywcca_install_woocommerce_admin_notice() {
	?>
		<div class="error">
			<p><?php esc_html_e( 'YITH WooCommerce Category Accordion Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-category-accordion' ); ?></p>
		</div>
	<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );

// Define constants

if ( ! defined( 'YWCCA_VERSION' ) ) {
	define( 'YWCCA_VERSION', '2.2.0' );
}

if ( ! defined( 'YWCCA_PREMIUM' ) ) {
	define( 'YWCCA_PREMIUM', '1' );
}

if ( ! defined( 'YWCCA_INIT' ) ) {
	define( 'YWCCA_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YWCCA_FILE' ) ) {
	define( 'YWCCA_FILE', __FILE__ );
}

if ( ! defined( 'YWCCA_DIR' ) ) {
	define( 'YWCCA_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YWCCA_URL' ) ) {
	define( 'YWCCA_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YWCCA_ASSETS_URL' ) ) {
	define( 'YWCCA_ASSETS_URL', YWCCA_URL . 'assets/' );
}

if ( ! defined( 'YWCCA_TEMPLATE_PATH' ) ) {
	define( 'YWCCA_TEMPLATE_PATH', YWCCA_DIR . 'templates/' );
}

if ( ! defined( 'YWCCA_INC' ) ) {
	define( 'YWCCA_INC', YWCCA_DIR . 'includes/' );
}

if ( ! defined( 'YWCCA_SLUG' ) ) {
	define( 'YWCCA_SLUG', 'yith-woocommerce-category-accordion' );
}

if ( ! defined( 'YWCCA_SECRET_KEY' ) ) {
	define( 'YWCCA_SECRET_KEY', 'gcfSVRU7TDQ4bSL5gjcZ' );

}

if ( ! defined( 'FS_CHMOD_FILE' ) ) {
	define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWCCA_DIR . 'plugin-fw/init.php' ) ) {
	require_once YWCCA_DIR . 'plugin-fw/init.php';
}

yit_maybe_plugin_fw_loader( YWCCA_DIR );


if ( ! function_exists( 'YITH_Category_Accordion_Premium_Init' ) ) {
	/**
	 * Unique access to instance of YITH_Category_Accordion class
	 *
	 * @since 1.0.4
	 */
	function YITH_Category_Accordion_Premium_Init() { // phpcs:ignore WordPress.NamingConventions
		load_plugin_textdomain( 'yith-woocommerce-category-accordion', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		require_once YWCCA_INC . 'class.yith-woocommerce-category-accordion.php';
		require_once YWCCA_INC . 'class.yith-woocommerce-category-accordion-premium.php';

		global $YIT_Category_Accordion; // phpcs:ignore WordPress.NamingConventions
		$YIT_Category_Accordion = YITH_WC_Category_Accordion_Premium::get_instance(); // phpcs:ignore WordPress.NamingConventions

	}
}

add_action( 'yith_wc_category_accordion_premium_init', 'YITH_Category_Accordion_Premium_Init' );

if ( ! function_exists( 'yith_category_accordion_premium_install' ) ) {
	/**
	 * Install category accordion
	 *
	 * @since 1.0.4
	 */
	function yith_category_accordion_premium_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywcca_install_woocommerce_admin_notice' );
		} else {
			add_action( 'before_woocommerce_init', 'ywcca_add_support_hpos_system' );
			do_action( 'yith_wc_category_accordion_premium_init' );
		}

	}
}

add_action( 'plugins_loaded', 'yith_category_accordion_premium_install', 11 );

if ( ! function_exists( 'ywcca_add_support_hpos_system' ) ) {
	function ywcca_add_support_hpos_system() {
			 if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
                 \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', YWCCA_INIT );
             }
    }
}
