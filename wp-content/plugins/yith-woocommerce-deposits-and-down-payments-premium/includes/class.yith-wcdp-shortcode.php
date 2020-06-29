<?php
/**
 * Shortcode class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 *
 * /*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCDP_Shortcode' ) ) {
	/**
	 * WooCommerce Deposits Shortcode
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Shortcode {

		/**
		 * Performs all required add_shortcode
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public static function init() {
			$shortcodes = array(
				'yith_wcdp_deposit_value' => __CLASS__ . '::yith_wcdp_desposit_value', // print deposit price.
			);

			foreach ( $shortcodes as $shortcode => $function ) {
				add_shortcode( $shortcode, $function );
			}
		}

		/**
		 * ShortCode Deposit price
		 *
		 * @param array $atts Shortcode attributes.
		 * @return string
		 * @since 1.0.0
		 */
		public static function yith_wcdp_desposit_value( $atts ) {

			$message    = '';
			$product_id = 0;
			$atts       = shortcode_atts(
				array(
					'product_id' => 0,
				),
				$atts
			);
			extract( $atts ); // phpcs:ignore WordPress.PHP.DontExtract

			$product_id = intval( $product_id );
			if ( ! $product_id ) {
				global $product;
			} else {
				$product = wc_get_product( $product_id );
			}

			$deposit_value = apply_filters( 'yith_wcdp_deposist_value', min( YITH_WCDP_Premium()->get_deposit( $product->get_id(), false, 'view' ), $product->get_price() ), $product );

			if ( $deposit_value ) {
				$message = wc_price( $deposit_value );
			}

			return $message;

		}

	}
}