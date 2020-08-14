<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_YWRBP_AeliaCS_Module' ) ) {

	class YITH_YWRBP_AeliaCS_Module {

		protected static $_instance;

		protected static $base_currency;

		public function __construct() {

		add_action( 'wc_aelia_cs_exchange_rates_updated', 'ywcrbp_delete_transient' , 10  );
		add_action( 'wc_aelia_currencyswitcher_settings_saved', 'ywcrbp_delete_transient' , 10  );

		add_filter( 'ywcrb_get_discount_value', array( $this, 'ywcrb_convert_discount_value' ) );

		}

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}



		/**
		 * Convenience method. Returns WooCommerce base currency.
		 *
		 * @return string
		 * @since 1.0.6
		 */
		public static function base_currency() {


			if ( empty( self::$base_currency ) ) {
				self::$base_currency = get_option( 'woocommerce_currency' );
			}

			return self::$base_currency;
		}

		/**
		 * Basic integration with WooCommerce Currency Switcher, developed by Aelia
		 * (https://aelia.co). This method can be used by any 3rd party plugin to
		 * return prices converted to the active currency.
		 *
		 * @param double $amount The source price.
		 * @param string $to_currency The target currency. If empty, the active currency
		 *                              will be taken.
		 * @param string $from_currency The source currency. If empty, WooCommerce base
		 *                              currency will be taken.
		 *
		 * @return double The price converted from source to destination currency.
		 * @author Aelia <support@aelia.co>
		 * @link   https://aelia.co
		 * @since  1.0.6
		 */
		public static function get_amount_in_currency( $amount, $to_currency = null, $from_currency = null ) {
			if ( empty( $from_currency ) ) {
				$from_currency = self::base_currency();
			}
			if ( empty( $to_currency ) ) {
				$to_currency = get_woocommerce_currency();
			}

			return apply_filters( 'wc_aelia_cs_convert', $amount, $from_currency, $to_currency );
		}

		/**
		 * convert the single discount value
		 * @param float $amount
		 *
		 * @return float
		 */
		public function ywcrb_convert_discount_value( $amount ){

			return self::get_amount_in_currency( $amount );
		}
        /**
         * Returns a product's base currency. A product's base currency is the point
         * of reference to calculate other prices, and it can differ from shop's base
         * currency.
         * For example, if a shop's base currency is USD, a product's base currency
         * can be EUR. In such case, product prices in other currencies can be
         * calculated automatically, as long as the EUR one is entered.
         *
         * @param int product_id A product ID.
         * @param string default_currency The default currency to use if the product
         * doesn't have a base currency.
         * @return string A currency code.
         */
        public function get_product_base_currency($product_id, $default_currency = null) {
            if(empty($default_currency)) {
                $default_currency = self::base_currency();
            }
            return apply_filters('wc_aelia_cs_product_base_currency', $default_currency, $product_id);
        }

		/**
		 * Convert the amount from base currency to current currency
		 *
		 * @param float $amount
		 * @param WC_Product $product
		 *
		 * @return float
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function convert_base_currency_amount_to_user_currency( $amount, $product ) {


			if ( 'no_price' === $amount || '' === $amount ) {
				return $amount;
			}

			$product_id = $product->get_id();
            $product_base_currency = $this->get_product_base_currency($product_id);
            $active_currency = get_woocommerce_currency();

            return self::get_amount_in_currency( $amount, $active_currency, $product_base_currency );

		}

        public static function enabled_currencies() {
            return apply_filters('wc_aelia_cs_enabled_currencies', array(self::base_currency()));
        }
	}
}

YITH_YWRBP_AeliaCS_Module::get_instance();