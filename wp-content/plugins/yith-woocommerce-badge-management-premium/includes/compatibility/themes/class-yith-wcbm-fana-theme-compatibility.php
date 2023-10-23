<?php
/**
 * Fana Theme Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManegement\Compatibility
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Fana_Theme_Compatibility' ) ) {
	/**
	 * Fana Theme Compatibility Class
	 */
	class YITH_WCBM_Fana_Theme_Compatibility {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Fana_Theme_Compatibility
		 */
		protected static $instance;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBM_Fana_Theme_Compatibility
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBM_Fana_Theme_Compatibility constructor.
		 */
		private function __construct() {
			add_action( 'tbay_woocommerce_before_content_product', array( $this, 'maybe_avoid_wc_sale_flash' ), 5 );
			add_action( 'woocommerce_before_single_product_summary', array( $this, 'maybe_avoid_wc_sale_flash' ), 5 );
		}

		/**
		 * Avoid the Sale Flash WooCommerce badge rendering if not needed
		 *
		 * @return void
		 */
		public function maybe_avoid_wc_sale_flash() {
			global $product, $post;
			if ( ! $product instanceof WC_Product && $post instanceof WP_Post ) {
				$product = wc_get_product( $post );
			}

			if ( $product && ! yith_wcbm_frontend()->is_default_on_sale_wc_badge_allowed( $product ) ) {
				remove_action( 'tbay_woocommerce_before_content_product', 'woocommerce_show_product_loop_sale_flash' );
				if ( function_exists( 'Fana_WooCommerce' ) ) {
					remove_action( 'tbay_woocommerce_before_content_product', array( Fana_WooCommerce(), 'only_feature_product_label' ) );
					remove_action( 'woocommerce_before_single_product_summary', array( Fana_WooCommerce(), 'only_feature_product_label' ), 15 );
				}
			} else {
				add_action( 'tbay_woocommerce_before_content_product', 'woocommerce_show_product_loop_sale_flash' );
				if ( function_exists( 'Fana_WooCommerce' ) ) {
					add_action( 'tbay_woocommerce_before_content_product', array( Fana_WooCommerce(), 'only_feature_product_label' ) );
					add_action( 'woocommerce_before_single_product_summary', array( Fana_WooCommerce(), 'only_feature_product_label' ), 15 );
				}
			}
		}

	}
}
