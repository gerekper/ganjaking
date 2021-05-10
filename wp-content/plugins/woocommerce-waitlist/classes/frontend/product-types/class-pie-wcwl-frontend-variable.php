<?php
/**
 * Frontend Class for Variable Products.
 *
 * @package WooCommerce Waitlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Variable' ) ) {
	/**
	 * Loads up the waitlist for variable products
	 *
	 * @package  WooCommerce Waitlist
	 */
	class Pie_WCWL_Frontend_Variable {

		/**
		 * Load up hooks if product is out of stock
		 *
		 * @param WC_Product $product Current product object.
		 */
		public function init( WC_Product $product ) {
			add_filter( 'woocommerce_get_stock_html', array( __CLASS__, 'output_waitlist_elements' ), 10, 2 );
		}

		/**
		 * Output waitlist elements via waitlist-single template
		 *
		 * Instead of outputting on an action we need to dynamically adjust the content based on the selected variation
		 * This is done by appending our template HTML to the stock template HTML
		 *
		 * @param string     $html     current stock HTML.
		 * @param WC_Product $product currently selected variation object.
		 *
		 * @return string updated stock HTML
		 */
		public static function output_waitlist_elements( $html, $product ) {
			$elements = wcwl_get_waitlist_fields( $product->get_id() );
			$html    .= $elements;

			return $html;
		}
	}
}
