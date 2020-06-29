<?php
if( !defined('ABSPATH')){
    exit;
}

if( !class_exists( 'YITH_Funds_Aelia_Currency_Switcher' ) ){

    class YITH_Funds_Aelia_Currency_Switcher {

        
        protected  static $base_currency;
        
        public function __construct()
        {
            add_filter( 'yith_show_available_funds', array( $this, 'convert_base_currency_amount_to_user_currency' ), 10, 2);
            add_filter( 'yith_min_deposit', array( $this, 'convert_base_currency_amount_to_user_currency' ), 10, 2 );
            add_filter( 'yith_max_deposit', array( $this, 'convert_base_currency_amount_to_user_currency' ), 10, 2 );
            add_filter( 'yith_amount_to_deposit', array( $this, 'convert_base_currency_amount_to_user_currency' ), 10, 2 );
            add_filter( 'yith_show_used_funds', array( $this, 'convert_base_currency_amount_to_user_currency' ) , 10, 2 );
            add_filter( 'yith_admin_deposit_funds', array( $this, 'convert_amount_to_base_currency' ), 10 ,2);
            add_filter( 'yith_admin_order_total', array( $this, 'convert_amount_to_base_currency' ), 10 ,2 );
            add_filter( 'yith_admin_order_totals_user_available', array( $this, 'admin_order_totals_user_available' ), 10, 2 );
            add_filter( 'yith_show_funds_used_into_order_currency', array( $this, 'admin_order_totals_user_available' ), 10, 2 );
            add_filter( 'yith_refund_amount_base_currency', array( $this, 'convert_amount_to_base_currency' ), 10 ,2 );
            add_filter( 'yith_how_refund_base_currency', array( $this, 'convert_amount_to_base_currency' ), 10 ,2 );
            add_filter( 'yith_discount_value', array( $this, 'convert_discount_value' ), 10 ,2 );
            add_filter( 'yith_fund_into_customer_email', array( $this, 'convert_into_user_currency'), 10, 2 );
            add_filter( 'woocommerce_order_amount_total_base_currency' , array( $this, 'order_amount_with_funds_base_currency' ), 10 , 2 );


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
         * Convert the amount from base currency to current currency
         *
         * @param float                  $amount
         * @param WC_Product $product
         *
         * @return float
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function convert_base_currency_amount_to_user_currency ( $funds, $currency = null ) {
            
            $funds = self::get_amount_in_currency ( $funds, null, $currency );

            return $funds;
        }

        /**
         * Basic integration with WooCommerce Currency Switcher, developed by Aelia
         * (https://aelia.co). This method can be used by any 3rd party plugin to
         * return prices converted to the active currency.
         *
         * @param double $amount        The source price.
         * @param string $to_currency   The target currency. If empty, the active currency
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

        
        public function  convert_amount_to_base_currency( $deposit, $order_id ){

            $order =  wc_get_order( $order_id ) ;

            $order_currency =  $order->get_currency();
                       
            return $this->convert_manual_amount_to_base_currency( $deposit, $order_currency );
        }
        /**
         * @param $amount
         * @param $currency
         * @return float
         */
        public function convert_manual_amount_to_base_currency( $amount, $currency ) {

            $amount = $this->get_amount_in_currency( $amount, self::base_currency(), $currency );
            
            return $amount;
        }


        public function admin_order_totals_user_available( $funds, $order_id ){

            $order =  wc_get_order( $order_id ) ;

            $order_currency = $order->get_currency();

            $funds = self::get_amount_in_currency ( $funds, $order_currency );

            return $funds;
        }

        public function convert_discount_value( $discount, $type ){

            if( $type == 'fixed_cart' ){

                $discount = $this->convert_base_currency_amount_to_user_currency( $discount );
            }

            return $discount;
        }
        
        public function convert_into_user_currency( $value, $to_currency ){
            
            return self::get_amount_in_currency( $value, $to_currency );
        }

        /**
         * @param string $total
         * @param WC_Order $order
         */
        public function order_amount_with_funds_base_currency ( $total, $order ){

            $funds =   $order->get_meta( '_order_funds' );

            if( !empty( $funds ) && !ywf_order_has_deposit( $order ) ){

                $total+=floatval( $funds );
            }
            
            return $total;
        }
    }
}


new YITH_Funds_Aelia_Currency_Switcher();