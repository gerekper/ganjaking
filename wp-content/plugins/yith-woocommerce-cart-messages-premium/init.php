<?php
/**
 * Plugin Name: YITH WooCommerce Cart Messages Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-cart-messages
 * Description: <code><strong>YITH WooCommerce Cart Messages</strong></code> allows making your offers clearly visible by showing users a message at the very moment they pay the utmost attention, on the cart page. If you want to, you can also show them on the checkout or on the shop page. It's perfect to increase the total amount of every purchase. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.6.8
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-cart-messages
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.2.0
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Define constants ________________________________________.
if ( ! defined( 'YITH_YWCM_DIR' ) ) {
	define( 'YITH_YWCM_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_YWCM_PREMIUM' ) ) {
	define( 'YITH_YWCM_PREMIUM', '1' );
}

if ( defined( 'YITH_YWCM_VERSION' ) ) {
	return;
} else {
	define( 'YITH_YWCM_VERSION', '1.6.8' );
}

if ( ! defined( 'YITH_YWCM_SUFFIX' ) ) {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	define( 'YITH_YWCM_SUFFIX', $suffix );
}

if ( ! defined( 'YITH_YWCM_FILE' ) ) {
	define( 'YITH_YWCM_FILE', __FILE__ );
}

if ( ! defined( 'YITH_YWCM_URL' ) ) {
	define( 'YITH_YWCM_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_YWCM_ASSETS_URL' ) ) {
	define( 'YITH_YWCM_ASSETS_URL', YITH_YWCM_URL . 'assets' );
}

if ( ! defined( 'YITH_YWCM_INC' ) ) {
	define( 'YITH_YWCM_INC', YITH_YWCM_DIR . 'includes/' );
}

if ( ! defined( 'YITH_YWCM_TEMPLATE_PATH' ) ) {
	define( 'YITH_YWCM_TEMPLATE_PATH', YITH_YWCM_DIR . 'templates' );
}

if ( ! defined( 'YITH_YWCM_INIT' ) ) {
	define( 'YITH_YWCM_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWCM_SLUG' ) ) {
	define( 'YITH_YWCM_SLUG', 'yith-woocommerce-cart-messages' );
}

if ( ! defined( 'YITH_YWCM_SECRET_KEY' ) ) {
	define( 'YITH_YWCM_SECRET_KEY', '123456' );
}


// Free version deactivation if installed __________________.
if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_YWCM_FREE_INIT', plugin_basename( __FILE__ ) );

// Yith jetpack deactivation if installed __________________.
if ( function_exists( 'yith_deactive_jetpack_module' ) ) {
	global $yith_jetpack_1;
	yith_deactive_jetpack_module( $yith_jetpack_1, 'YITH_YWCM_PREMIUM', plugin_basename( __FILE__ ) );
}


// Plugin Framework Version Check __________________.
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWCM_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_YWCM_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_YWCM_DIR );

/* Register the plugin when activated */
register_deactivation_hook( __FILE__, 'ywcm_rewrite_rules' );

if ( ! function_exists( 'ywcm_rewrite_rules' ) ) {
	function ywcm_rewrite_rules() {
		delete_option( 'yith-ywcm-flush-rewrite-rules' );
	}
}

if ( ! function_exists( 'yith_ywcm_install_woocommerce_admin_notice' ) ) {
	/**
	 * Triggered when WooCommerce plugin is not installed.
	 */
	function yith_ywcm_install_woocommerce_admin_notice() {
		?>
		<div class="updated">
			<p><?php esc_html_e( 'You can\'t activate the plugin if you haven\'t activate woocommerce in advance.', 'yith-woocommerce-cart-messages' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'yith_ywcm_premium_install' ) ) {
	/**
	 * Start plugin.
	 */
	function yith_ywcm_premium_install() {
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywcm_install_woocommerce_admin_notice' );
		} else {
			do_action( 'yith_ywcm_init' );
		}
	}

	add_action( 'plugins_loaded', 'yith_ywcm_premium_install', 11 );
}

/**
 * Load required classes and functions _________________________.
 */
function yith_ywcm_premium_constructor() {

	// Woocommerce installation check _________________________.
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_ywcm_install_woocommerce_admin_notice' );
		return;
	}

	// Load YWCM text domain ___________________________________.
	load_plugin_textdomain( 'yith-woocommerce-cart-messages', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once YITH_YWCM_INC . 'compatibility/class.yith-cart-message-aelia-compatibility.php';
	require_once YITH_YWCM_INC . 'yith-cart-messages-functions.php';
	require_once YITH_YWCM_INC . 'class.yith-woocommerce-cart-messages.php';
	require_once YITH_YWCM_INC . 'class.yith-woocommerce-cart-messages-premium.php';
	require_once YITH_YWCM_INC . 'class.yith-woocommerce-cart-message.php';

	global $YWCM_Instance;
	$YWCM_Instance = new YWCM_Cart_Messages_Premium();
}

add_action( 'yith_ywcm_init', 'yith_ywcm_premium_constructor' );
