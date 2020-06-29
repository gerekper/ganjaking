<?php
/**
 * Frontend Class for Grouped Products
 *
 * @package WooCommerce Waitlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Grouped' ) ) {
	/**
	 * Loads up the waitlist for grouped products
	 */
	class Pie_WCWL_Frontend_Grouped {
		/**
		 * Current grouped product object
		 *
		 * @var false|null|WC_Product
		 */
		public static $product;
		/**
		 * Current user object
		 *
		 * @var false|WP_User
		 */
		public $user;
		/**
		 * Does this grouped product contain any waitlist enabled products?
		 *
		 * @var bool
		 */
		public static $has_waitlist_enabled_products = false;
		/**
		 * Pie_WCWL_Frontend_Grouped constructor.
		 */
		public function __construct() {
			$this->user = get_user_by( 'id', get_current_user_id() );
		}
		/**
		 * Load up hooks if product is out of stock
		 *
		 * @param WC_Product $product Grouped product object.
		 */
		public function init( $product ) {
			self::$product = $product;
			if ( $this->has_out_of_stock_children() ) {
				add_filter( 'woocommerce_get_stock_html', array( __CLASS__, 'append_checkboxes' ) );
				add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'output_waitlist_elements' ) );
			}
		}
		/**
		 * Check if grouped product has out of stock child products
		 *
		 * @return bool
		 */
		public function has_out_of_stock_children() {
			foreach ( self::$product->get_children() as $child ) {
				$child = wc_get_product( $child );
				if ( wcwl_waitlist_should_show( $child ) ) {
					return true;
				}
			}
			return false;
		}
		/**
		 * Appends the waitlist button HTML to text string
		 *
		 * A new waitlist object is instantiated for each child product to ensure updates are shown on page reload
		 *
		 * @hooked   filter woocommerce_stock_html
		 *
		 * @param string $string Current stock HTML.
		 *
		 * @return string HTML with waitlist button appended if product is out of stock
		 *
		 * @access   public
		 * @since    1.0
		 */
		public static function append_checkboxes( $string ) {
			global $product, $sitepress;
			if ( WooCommerce_Waitlist_Plugin::is_variable( $product ) ) {
				return $string;
			}
			$product_id = $product->get_id();
			$lang       = '';
			if ( isset( $sitepress ) ) {
				$lang       = wpml_get_language_information( null, $product_id )['language_code'];
				$product_id = wcwl_get_translated_main_product_id( $product_id );
			}
			if ( ! wcwl_waitlist_should_show( $product ) ) {
				return $string;
			}
			$string .= apply_filters( 'wcwl_grouped_checkbox_html', wcwl_get_waitlist_checkbox( wc_get_product( $product_id ), $lang ) );
			self::$has_waitlist_enabled_products = true;
			return $string;
		}
		/**
		 * Output update button for grouped product waitlists
		 */
		public static function output_waitlist_elements() {
			if ( ! self::$has_waitlist_enabled_products ) {
				return;
			}
			echo wcwl_get_waitlist_fields( self::$product->get_id(), 'update' );
		}
	}
}
