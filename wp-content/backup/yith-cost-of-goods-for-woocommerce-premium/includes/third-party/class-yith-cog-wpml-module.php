<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_COG_WPML_Module' ) ) {
	
	/**
	 * @class   YITH_COG_WPML_Module
	 */
	class YITH_COG_WPML_Module{

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

        public function __construct(){

            add_filter('yith_cog_convert_amounts', array( $this, 'convert_to_base_currency' ), 10, 2 );

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

        public function convert_to_base_currency( $amount, $item_id ) {
            global $woocommerce_wpml;

            $order_id = wc_get_order_id_by_order_item_id( $item_id );
            $order = wc_get_order( $order_id );

            $from_currency = apply_filters( 'yith_cog_wpml_from_currency', $order->get_currency() );
            $to_currency = apply_filters( 'yith_cog_wpml_to_currency', self::base_currency() );

            if (!is_numeric($amount)) {
                return $amount;
            }

            if ($amount == 0) {
                return $amount;
            }

            if ($from_currency == $to_currency) {
                return $amount;
            }

            $from_currency_rate = $woocommerce_wpml->settings['currency_options'][$from_currency]['rate'];
            $to_currency_rate = 1;
            
            $exchange_rate = $to_currency_rate / $from_currency_rate;

            return $amount * $exchange_rate;

        }

        public function set_amount_to_base_currency_order_item_value( $amount, $item_id ) {
            global $woocommerce_wpml;

            $order_id = wc_get_order_id_by_order_item_id( $item_id );
            
            $order = wc_get_order( $order_id );
            
            if( ! $order instanceof WC_Order ){
                return;
            }
            
            $from_currency = apply_filters( 'yith_cog_wpml_from_currency', $order->get_currency() );
            $to_currency = apply_filters( 'yith_cog_wpml_to_currency', self::base_currency() );

            if (!is_numeric($amount)) {
                return $amount;
            }

            if ($amount == 0) {
                return $amount;
            }

            if ($from_currency == $to_currency) {
                return $amount;
            }

            $from_currency_rate = $woocommerce_wpml->settings['currency_options'][$from_currency]['rate'];
            $to_currency_rate = 1;

            $exchange_rate = $from_currency_rate / $to_currency_rate;

            return $amount * $exchange_rate;

        }

        public function set_amount_to_base_currency_order_total( $amount, $order_id ) {
            global $woocommerce_wpml;

            $order = wc_get_order( $order_id );

            $from_currency = apply_filters( 'yith_cog_wpml_from_currency', $order->get_currency() );
            $to_currency = apply_filters( 'yith_cog_wpml_to_currency', self::base_currency() );

            if (!is_numeric($amount)) {
                return $amount;
            }

            if ($amount == 0) {
                return $amount;
            }

            if ($from_currency == $to_currency) {
                return $amount;
            }

            $from_currency_rate = $woocommerce_wpml->settings['currency_options'][$from_currency]['rate'];
            $to_currency_rate = 1;

            $exchange_rate = $from_currency_rate / $to_currency_rate;

            return $amount * $exchange_rate;

        }

    }

}

YITH_COG_WPML_Module::get_instance ();