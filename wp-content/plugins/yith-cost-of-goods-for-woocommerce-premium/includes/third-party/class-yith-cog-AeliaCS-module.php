<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_COG_AeliaCS_Module' ) ) {
	
	/**
	 * @class   YITH_COG_AeliaCS_Module
	 */
	class YITH_COG_AeliaCS_Module {
		
		/**
		 * Single instance of the class
		 */
		protected static $instance;
		
		/**
		 * Shop's base currency. Used for caching.
		 */
		protected static $base_currency;
		
		/**
		 * Returns single instance of the class
		 */
		public static function get_instance() {
			if ( is_null ( self::$instance ) ) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}

		public function __construct() {

            add_filter('yith_cog_convert_amounts', array( $this, 'set_amount_to_base_currency' ), 10, 2 );

            add_filter('yith_cog_convert_amounts_order_item_value', array( $this, 'set_amount_to_base_currency_order_item_value' ), 10, 2 );

            add_filter('yith_cog_convert_amounts_order_total', array( $this, 'set_amount_to_base_currency_order_total' ), 10, 2 );

		}


		/**
		 * Convenience method. Returns WooCommerce base currency.
		 */
		public static function base_currency() {
			
			if ( empty( self::$base_currency ) ) {
				self::$base_currency = get_option ( 'woocommerce_currency' );
			}
			
			return self::$base_currency;
		}
		

        public function set_amount_to_base_currency( $amount, $item_id ) {

		    $order_id = wc_get_order_id_by_order_item_id( $item_id );
		    $order = wc_get_order( $order_id );

		    $from_currency = $order->get_currency();
            $to_currency = self::base_currency();

            if (!is_numeric($amount)) {
                return $amount;
            }

            if ($amount == 0) {
                return $amount;
            }

            if ($from_currency == $to_currency) {
                return $amount;
            }

            $currency_switcher = $GLOBALS['woocommerce-aelia-currencyswitcher'];
            $from_currency_rate = $currency_switcher->settings_controller()->get_exchange_rate( $from_currency, true);
            $to_currency_rate = $currency_switcher->settings_controller()->get_exchange_rate( $to_currency, true );

            $exchange_rate = $to_currency_rate / $from_currency_rate;

            return apply_filters( 'yith_cog_set_amount_to_base_currency', $amount * $exchange_rate, $amount, $exchange_rate, $from_currency, $to_currency );
        }


        public function set_amount_to_base_currency_order_item_value( $amount, $item_id ) {

            $order_id = wc_get_order_id_by_order_item_id( $item_id );
            $order = wc_get_order( $order_id );

            $from_currency = $order->get_currency();
            $to_currency = self::base_currency();

            if (!is_numeric($amount)) {
                return $amount;
            }

            if ($amount == 0) {
                return $amount;
            }

            if ($from_currency == $to_currency) {
                return $amount;
            }

            $currency_switcher = $GLOBALS['woocommerce-aelia-currencyswitcher'];
            $from_currency_rate = $currency_switcher->settings_controller()->get_exchange_rate( $from_currency, true);
            $to_currency_rate = $currency_switcher->settings_controller()->get_exchange_rate( $to_currency, true );

            $exchange_rate = $from_currency_rate / $to_currency_rate;

            return apply_filters( 'yith_cog_set_amount_to_base_currency', $amount * $exchange_rate, $amount, $exchange_rate, $from_currency, $to_currency );
        }

        public function set_amount_to_base_currency_order_total( $amount, $order_id ) {

            $order = wc_get_order( $order_id );

            $from_currency = $order->get_currency();
            $to_currency = self::base_currency();

            if (!is_numeric($amount)) {
                return $amount;
            }

            if ($amount == 0) {
                return $amount;
            }

            if ($from_currency == $to_currency) {
                return $amount;
            }

            $currency_switcher = $GLOBALS['woocommerce-aelia-currencyswitcher'];
            $from_currency_rate = $currency_switcher->settings_controller()->get_exchange_rate( $from_currency, true);
            $to_currency_rate = $currency_switcher->settings_controller()->get_exchange_rate( $to_currency, true );

            $exchange_rate = $from_currency_rate / $to_currency_rate;

            return apply_filters( 'yith_cog_set_amount_to_base_currency', $amount * $exchange_rate, $amount, $exchange_rate, $from_currency, $to_currency );
        }



    }
}

YITH_COG_AeliaCS_Module::get_instance ();