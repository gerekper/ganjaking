<?php
/**
 * Plugin Name: WooCommerce Product Finder
 * Description: An advanced search for WooCommerce that helps your customers find your products more easily.
 * Version: 1.2.17
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Requires at least: 3.3
 * Tested up to: 5.6
 * WC requires at least: 2.6
 * WC tested up to: 4.7
 * Copyright: © 2020 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 163906:bc4e288ac15205345ce9c506126b3f75
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce fallback notice.
 *
 * @since 1.2.12
 */
function woocommerce_product_finder_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Product Finder requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-product-finder' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! class_exists( 'WC_Product_Finder' ) ) :
	define( 'WC_PRODUCT_FINDER_VERSION', '1.2.17' ); // WRCS: DEFINED_VERSION.

	/**
	 * Main extension class.
	 *
	 * @since 1.2.12
	 * @return void
	 */
	class WC_Product_Finder {
		/**
		 * Constructor
		 *
		 * @since 1.2.12
		 * @return void
		 */
		public function __construct() {
			require 'classes/class-woocommerce-product-finder.php';
			require 'woocommerce-product-finder-functions.php';
			require 'classes/class-woocommerce-product-finder-widget.php';

			if ( is_admin() ) {
				require 'classes/class-woocommerce-product-finder-admin.php';
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		}

		/**
		 * Loads scripts.
		 *
		 * @since 1.2.12
		 * @return void
		 */
		public function load_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Load Javascript.
			wp_register_script( 'wc_product_finder', plugins_url( 'assets/js/scripts' . $suffix . '.js', __FILE__ ), array( 'jquery' ), WC_PRODUCT_FINDER_VERSION, true );
			wp_enqueue_script( 'wc_product_finder' );

			// Localise Javascript.
			wp_localize_script( 'wc_product_finder', 'wc_product_finder_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

			// Load CSS.
			wp_register_style( 'wc_product_finder', plugins_url( 'assets/css/style.css', __FILE__ ), array(), WC_PRODUCT_FINDER_VERSION );
			wp_enqueue_style( 'wc_product_finder' );
		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_product_finder_init' );

/**
 * Initializes the extension.
 *
 * @since 1.2.12
 * @return void
 */
function woocommerce_product_finder_init() {
	load_plugin_textdomain( 'woocommerce-product-finder', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_product_finder_missing_wc_notice' );
		return;
	}

	new WC_Product_Finder();
}
