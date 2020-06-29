<?php
/**
 * Rate Handler class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Rate_Handler' ) ) {
	/**
	 * WooCommerce Rate Handler
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Rate_Handler {
		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Rate_Handler
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/* === HELPER METHODS === */

		/**
		 * Get rate for an affiliate or a product
		 *
		 * @param $affiliate int|mixed Affiliate ID or affiliate array
		 * @param $product   int|\WC_Product|bool Product id or product object
		 * @param $order_id  int|bool Order id
		 *
		 * @return float Rate (product specific rate, if any; otherwise, affiliate specific rate, if any; otherwise, general rate)
		 * @since 1.0.0
		 */
		public function get_rate( $affiliate = false, $product = false, $order_id = false ) {
			// get user id
			if ( is_numeric( $affiliate ) ) {
				$affiliate_id = $affiliate;
			} elseif ( isset( $affiliate['ID'] ) ) {
				$affiliate_id = $affiliate['ID'];
			} else {
				$affiliate_id = false;
			}

			// get product id
			if ( is_numeric( $product ) ) {
				$product_id = $product;
				$product    = wc_get_product( $product_id );
				$parent_id  = is_object( $product ) ? yit_get_prop( $product, 'post_parent', true ) : false;
			} elseif ( is_object( $product ) && $product instanceof WC_Product ) {
				$product_id = yit_get_product_id( $product );
				$parent_id  = yit_get_prop( $product, 'post_parent', true );
			} else {
				$product_id = false;
				$parent_id  = false;
			}

			$rate          = 0;
			$general_rate  = get_option( 'yith_wcaf_general_rate' );
			$affiliate     = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id );
			$product_rates = get_option( 'yith_wcaf_product_rates', 0 );

			if ( $product_id && isset( $product_rates[ $product_id ] ) ) {
				$rate = floatval( $product_rates[ $product_id ] );
			} elseif ( $parent_id && isset( $product_rates[ $parent_id ] ) ) {
				$rate = floatval( $product_rates[ $parent_id ] );
			} elseif ( $affiliate_id && is_numeric( $affiliate['rate'] ) ) {
				$rate = floatval( $affiliate['rate'] );
			} else {
				$rate = floatval( $general_rate );
			}

			/**
			 * Lets third party plugin to filter affiliate rate
			 *
			 * @since 1.0.9
			 */
			return apply_filters( 'yith_wcaf_affiliate_rate', $rate, $affiliate, $product, $order_id );
		}

		/**
		 * Return corrected rate for persistent commission calculation
		 *
		 * @param $rate  double Original rate
		 * @param $token string Affiliate token
		 * @param $item  mixed Current item
		 *
		 * @return double Corrected rate
		 * @since 1.0.0
		 */
		public function get_persistent_rate( $rate, $token = '', $item = false ) {
			$persistent_rate = apply_filters( 'yith_wcaf_persistent_rate', get_option( 'yith_wcaf_persistent_rate' ), $token, $item );

			return floatval( $persistent_rate * (double) $rate / 100 );
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Rate_Handler
		 * @since 1.0.2
		 */
		public static function get_instance() {
			if ( class_exists( 'YITH_WCAF_Rate_Handler_Premium' ) ) {
				return YITH_WCAF_Rate_Handler_Premium::get_instance();
			} else {
				if ( is_null( YITH_WCAF_Rate_Handler::$instance ) ) {
					YITH_WCAF_Rate_Handler::$instance = new YITH_WCAF_Rate_Handler;
				}

				return YITH_WCAF_Rate_Handler::$instance;
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Rate_Handler class
 *
 * @return \YITH_WCAF_Rate_Handler
 * @since 1.0.0
 */
function YITH_WCAF_Rate_Handler() {
	return YITH_WCAF_Rate_Handler::get_instance();
}