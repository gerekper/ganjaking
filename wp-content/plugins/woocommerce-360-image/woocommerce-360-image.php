<?php
/**
 * Plugin Name: WooCommerce 360° Image
 * Plugin URI: https://woocommerce.com/products/woocommerce-360-image/
 * Description: Add a 360° image rotation display your product listings in WooCommerce.
 * Version: 1.4.0
 * Author: KoiLab
 * Author URI: https://koilab.com/
 * Requires PHP: 5.6
 * Requires at least: 4.9
 * Tested up to: 6.4
 * Domain: woocommerce-360-image
 * Domain Path: /languages
 *
 * WC requires at least: 3.7
 * WC tested up to: 8.5
 * Woo: 512186:24eb2cfa3738a66bf3b2587876668cd2
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_360_Image
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \KoiLab\WC_360_Image\Autoloader::init() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_360_IMAGE_FILE' ) ) {
	define( 'WC_360_IMAGE_FILE', __FILE__ );
}

/**
 * Initialize plugin.
 */
function woocommerce_360_image_init() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_360_image_woocommerce_deactivated' );
		return;
	}

	// Instance the main class.
	\KoiLab\WC_360_Image\Plugin::instance();
}
add_action( 'plugins_loaded', 'woocommerce_360_image_init' );

/**
 * WooCommerce Deactivated Notice.
 */
function woocommerce_360_image_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce 360 Image requires %s to be installed and active.', 'woocommerce-360-image' ), '<a href="https://woo.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}
