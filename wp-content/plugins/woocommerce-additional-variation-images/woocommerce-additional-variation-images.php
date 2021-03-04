<?php
/**
 * Plugin Name: WooCommerce Additional Variation Images
 * Plugin URI: https://woocommerce.com/products/woocommerce-additional-variation-images/
 * Description: A WooCommerce plugin/extension that adds ability for shop/store owners to add variation specific images in a group.
 * Version: 1.9.0
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 *
 * Text Domain: woocommerce-additional-variation-images
 * Domain Path: /languages
 * Tested up to: 5.6
 * WC tested up to: 5.0
 * WC requires at least: 3.4
 * Woo: 477384:c61dd6de57dcecb32bd7358866de4539
 *
 * Copyright: © 2021 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-additional-variation-images
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Additional_Variation_Images' ) ) :

	define( 'WC_ADDITIONAL_VARIATION_IMAGES_VERSION', '1.9.0' ); // WRCS: DEFINED_VERSION.

	/**
	 * Main class.
	 *
	 * @package  WC_Additional_Variation_Images
	 */
	class WC_Additional_Variation_Images {

		/**
		 * Init.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			if ( class_exists( 'WooCommerce' ) ) {

				if ( is_admin() ) {
					include_once 'includes/class-wc-additional-variation-images-admin.php';
				}

				include_once 'includes/class-wc-additional-variation-images-frontend.php';

			} else {

				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );

			}

			return true;
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'wc_additional_variation_images_plugin_locale', get_locale(), 'woocommerce-additional-variation-images' );

			load_textdomain( 'woocommerce-additional-variation-images', trailingslashit( WP_LANG_DIR ) . 'woocommerce-additional-variation-images/woocommerce-additional-variation-images-' . $locale . '.mo' );

			load_plugin_textdomain( 'woocommerce-additional-variation-images', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

			return true;
		}

		/**
		 * WooCommerce fallback notice.
		 */
		public function woocommerce_missing_notice() {
			/* translators: 1: html link for downloading WC */
			echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Additional Variation Images Plugin requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-additional-variation-images' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
		}
	}

	add_action( 'plugins_loaded', 'woocommerce_additional_variation_images_init', 0 );

	/**
	 * Init function.
	 *
	 * @package  WC_Additional_Variation_Images
	 * @since 1.0.0
	 * @return bool
	 */
	function woocommerce_additional_variation_images_init() {
		new WC_Additional_Variation_Images();

		return true;
	}

endif;
