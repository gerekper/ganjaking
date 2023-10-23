<?php
/**
 * YITH Multi Currency Switcher for WooCommerce compatibility.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddons
 */

! defined( 'YITH_WCMCS' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_WCMCS_Compatibility' ) ) {
	/**
	 * Compatibility Class
	 *
	 * @class   YITH_WAPO_WCMCS_Compatibility
	 * @since   3.4.0
	 */
	class YITH_WAPO_WCMCS_Compatibility {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WAPO_WCMCS_Compatibility
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WAPO_WCMCS_Compatibility
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WAPO_WCMCS_Compatibility constructor
		 */
		private function __construct() {
			add_filter( 'yith_wapo_get_addon_sale_price', array( $this, 'modify_addon_price' ), 10, 5 );
			add_filter( 'yith_wapo_get_addon_price', array( $this, 'modify_addon_price' ), 10, 5 );
			add_filter( 'yith_wapo_convert_price', array( $this, 'modify_addon_price' ), 10, 5 );
			add_filter( 'yith_wapo_total_item_price', array( $this, 'modify_cart_item_price_with_addons' ), 10 );
			add_filter( 'yith_wapo_totals_price_args', array( $this, 'modify_totals_price_args' ), 10 );

            // Request a Quote
            add_filter( 'yith_wapo_ywraq_total_price', array( $this, 'modify_cart_item_price_with_addons' ), 10 );


        }

		/**
		 * Modify the current price depending on currency
		 *
		 * @param float   $price The current price.
		 * @param boolean $allow_modification Force to allow the convert of the price.
		 * @param string  $price_method The price method of the add-on option.
		 * @param string  $price_type The price type of the add-on option.
		 * @param float   $index The current index of the add-on.
		 *
		 * @return float
		 */
		public function modify_addon_price( $price, $allow_modification = false, $price_method = 'free', $price_type = 'fixed', $index = 0 ) {

			if ( 'free' !== $price_method || $allow_modification ) {
				if ( 'percentage' !== $price_type || $allow_modification ) {
					if ( function_exists( 'yith_wcmcs_convert_price' ) ) {
                        $args  = apply_filters( 'yith_wapo_wcmcs_convert_price_args', array() );
						$price = yith_wcmcs_convert_price( $price, $args );
					}
				}
			}

			return $price;
		}

		/**
		 * Modify the cart item price depending on currency
		 *
		 * @param float $price The current price.
		 *
		 * @return float
		 */
		public function modify_cart_item_price_with_addons( $price = 0 ) {

			if ( function_exists( 'yith_wcmcs_get_current_currency' ) ) {
				$currency   = yith_wcmcs_get_current_currency();
				$rate       = $currency->get_rate();
				$commission = $currency->get_commission();

				if ( $rate ) {
					$price = $price / ( $rate * ( 1 + $commission / 100 ) );
				}
			}

			return $price;
		}

		/**
		 * Modify the wc_price args.
		 *
		 * @param array $args The args to pass to wc_price function.
		 *
		 * @return array
		 */
		public function modify_totals_price_args( $args = array() ) {
			if ( function_exists( 'yith_wcmcs_get_current_currency_id' ) ) {
				$args['currency'] = yith_wcmcs_get_current_currency_id();
			}
			return $args;
		}
	}
}
