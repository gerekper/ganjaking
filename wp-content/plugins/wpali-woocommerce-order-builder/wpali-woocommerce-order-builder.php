<?php
/**
 * @link              https://wpali.com
 * @since             1.1.5
 * @package           Wpali_Woocommerce_Order_Builder
 *
 * @wordpress-plugin
 * Plugin Name:       WPAli: Woocommerce Order Builder
 * Plugin URI:        https://wpali.com/woocommerce-order-builder
 * Description:       Plugin to create unlimited combo products and enable your customers to build their order easily in one page and see the price instantly..
 * Version:           1.1.5
 * Author:            ALI KHALLAD
 * Author URI:        https://wpali.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpali-woocommerce-order-builder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WWOB_VERSION', '1.1.5' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpali-woocommerce-order-builder-activator.php
 */
function activate_wpali_woocommerce_order_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpali-woocommerce-order-builder-activator.php';
	Wpali_Woocommerce_Order_Builder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpali-woocommerce-order-builder-deactivator.php
 */
function deactivate_wpali_woocommerce_order_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpali-woocommerce-order-builder-deactivator.php';
	Wpali_Woocommerce_Order_Builder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpali_woocommerce_order_builder' );
register_deactivation_hook( __FILE__, 'deactivate_wpali_woocommerce_order_builder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpali-woocommerce-order-builder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpali_woocommerce_order_builder() {

	$plugin = new Wpali_Woocommerce_Order_Builder();
	$plugin->run();

}
run_wpali_woocommerce_order_builder();