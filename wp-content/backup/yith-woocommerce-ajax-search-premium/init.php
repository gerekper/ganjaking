<?php
/**
 * Plugin Name: YITH WooCommerce Ajax Search Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-ajax-search/
 * Description: <code><strong>YITH WooCommerce Ajax Search Premium</strong></code> is the plugin that allows you to search for a specific product by inserting a few characters. Thanks to <strong>Ajax Search</strong>, users can quickly find the contents they are interested in without wasting time among site pages. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.7.11
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-ajax-search
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.3.0
 **/

/**
 * Init file
 *
 * @author YITH
 * @package YITH WooCommerce Ajax Search Premium
 * @version 1.7.10
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; } // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCAS_FREE_INIT', plugin_basename( __FILE__ ) );

if ( ! defined( 'YITH_WCAS_PREMIUM' ) ) {
	define( 'YITH_WCAS_PREMIUM', '1' );
}

if ( ! defined( 'YITH_WCAS_DIR' ) ) {
	define( 'YITH_WCAS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( defined( 'YITH_WCAS_VERSION' ) ) {
	return;
} else {
	define( 'YITH_WCAS_VERSION', '1.7.11' );
}


/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCAS_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_WCAS_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_WCAS_DIR );

/**
 * Start plugin.
 */
function yith_ajax_search_premium_constructor() {

	if ( ! function_exists( 'WC' ) ) {
		/**
		 * Check if WooCommerce is installed.
		 */
		function yith_wcas_premium_install_woocommerce_admin_notice() {
			?>
			<div class="error">
				<p><?php esc_html_e( 'YITH WooCommerce Ajax Search Premium is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-ajax-search' ); ?></p>
			</div>
			<?php
		}

		add_action( 'admin_notices', 'yith_wcas_premium_install_woocommerce_admin_notice' );
		return;
	}

	load_plugin_textdomain( 'yith-woocommerce-ajax-search', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	if ( ! defined( 'YITH_WCAS' ) ) {
		define( 'YITH_WCAS', true );
	}

	if ( ! defined( 'YITH_WCAS_FILE' ) ) {
		define( 'YITH_WCAS_FILE', __FILE__ );
	}

	if ( ! defined( 'YITH_WCAS_URL' ) ) {
		define( 'YITH_WCAS_URL', plugin_dir_url( __FILE__ ) );
	}

	if ( ! defined( 'YITH_WCAS_TEMPLATE_PATH' ) ) {
		define( 'YITH_WCAS_TEMPLATE_PATH', YITH_WCAS_DIR . 'templates' );
	}

	if ( ! defined( 'YITH_WCAS_ASSETS_URL' ) ) {
		define( 'YITH_WCAS_ASSETS_URL', YITH_WCAS_URL . 'assets' );
	}

	if ( ! defined( 'YITH_WCAS_ASSETS_IMAGES_URL' ) ) {
		define( 'YITH_WCAS_ASSETS_IMAGES_URL', YITH_WCAS_ASSETS_URL . '/images/' );
	}

	if ( ! defined( 'YITH_WCAS_INIT' ) ) {
		define( 'YITH_WCAS_INIT', plugin_basename( __FILE__ ) );
	}

	if ( ! defined( 'YITH_WCAS_SLUG' ) ) {
		define( 'YITH_WCAS_SLUG', 'yith-woocommerce-ajax-search' );
	}

	if ( ! defined( 'YITH_WCAS_SECRET_KEY' ) ) {
		define( 'YITH_WCAS_SECRET_KEY', 'SyKDKcXuRIOqRW6Aag5z' );
	}

	// Load required classes and functions.
	require_once YITH_WCAS_DIR.'/includes/functions.yith-wcas.php';
	require_once YITH_WCAS_DIR.'/includes/class.yith-wcas-admin.php';
	require_once YITH_WCAS_DIR.'/includes/class.yith-wcas-frontend.php';
	require_once YITH_WCAS_DIR.'/includes/widgets/class.yith-wcas-ajax-search.php';
	require_once YITH_WCAS_DIR.'/includes/class.yith-wcas.php';

	// Let's start the game!
	global $yith_wcas;
	$yith_wcas = new YITH_WCAS();
}
add_action( 'plugins_loaded', 'yith_ajax_search_premium_constructor' );
add_action( 'icl_current_language', 'wpml_fix_ajax_call' );

/**
 * Fix ajax calls when WPML is installed.
 *
 * @param string $lang Language.
 *
 * @return mixed
 */
function wpml_fix_ajax_call( $lang ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && 'yith_ajax_search_products' === $_REQUEST['action'] && isset( $_REQUEST['lang'] ) ) {
		$lang = sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) );
	}
	return $lang;
}

