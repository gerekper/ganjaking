<?php
/**
 * Plugin Name: WooCommerce Free Gift Coupons
 * Plugin URI: http://www.woocommerce.com/products/free-gift-coupons/
 * Description: Add a free product to the cart when a coupon is entered
 * Version: 2.4.6
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com
 * Woo: 414577:e1c4570bcc412b338635734be0536062
 * Requires at least: 4.4
 * Tested up to: 5.3.0
 * WC requires at least: 3.1.0
 * WC tested up to: 4.2.0
 *
 * Text Domain: wc_free_gift_coupons
 * Domain Path: /languages/
 *
 * @package WooCommerce Free Gift Coupons
 * @package Core
 * @author Kathy Darling
 *
 * Copyright: © 2012 Kathy Darling.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Boot up the plugin
 *
 * @since   1.2.0
 */
function wc_free_gift_coupons_init() {
	if ( ! defined( 'WC_FGC_PLUGIN_NAME' ) ) {
		define( 'WC_FGC_PLUGIN_NAME', plugin_basename( __FILE__ ));
	}
	require_once  'includes/legacy/class-wc-free-gift-coupons-legacy.php' ;
	require_once  'includes/class-wc-free-gift-coupons.php' ;
	WC_Free_Gift_Coupons::init();
}
add_action( 'plugins_loaded', 'wc_free_gift_coupons_init' );
