<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YWGC_AeliaCS_Module' ) ) {
	
	/**
	 *
	 * @class   YWGC_AeliaCS_Module
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YWGC_AeliaCS_Module {
		
		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;
		
		/**
		 * Shop's base currency. Used for caching.
		 *
		 * @var string
		 * @since 1.0.6
		 */
		protected static $base_currency;
		
		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null ( self::$instance ) ) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		public function __construct() {
			
			/**
			 * Aelia  Multi-currency support
			 */
			add_filter ( 'wc_aelia_currencyswitcher_product_convert_callback', array(
				$this,
				'wc_aelia_currencyswitcher_product_convert_callback'
			), 10, 2 );
			
			add_filter ( 'yith_ywgc_submitting_manual_amount', array(
				$this,
				'set_amount_to_base_currency'
			) );

            add_filter ( 'yith_ywgc_submitting_select_amount', array(
                $this,
                'convert_amount_to_base_currency'
            ) );

            add_filter ( 'yith_ywgc_submitting_as_present_amount', array(
                $this,
                'convert_amount_to_base_currency'
            ) );
			
			/**
			 * Retrieve the array data key for the subtotal in the current currency
			 */
			add_filter ( 'yith_ywgc_line_subtotal', array(
				$this,
				'wc_aelia_line_subtotal'
			), 10, 2 );
			
			/**
			 * Retrieve the array data key for the subtotal tax in the  current currency
			 */
			add_filter ( 'yith_ywgc_line_subtotal_tax', array(
				$this,
				'wc_aelia_line_subtotal_tax'
			), 10, 2 );
			
			/**
			 * Show the amount of the gift card using the user currency
			 */
			add_filter ( 'yith_ywgc_gift_card_template_amount', array(
				$this,
				'get_amount_in_gift_card_currency'
			), 10, 2 );
			
			/**
			 * Set the amount from customer currency to base currency
			 */
			add_filter ( 'yith_ywgc_gift_card_amount_before_deduct',
				array(
					$this,
					'convert_user_currency_amount_to_base_currency'
				) );

			// In the last update of Aelia (Version 4.6.5.180828 ) this hook is not necessary because otherwise it would make it twice
			/*add_filter( 'yith_ywgc_set_cart_item_price', array(
				$this,
				'set_cart_item_price_currency'

			), 10, 2 );*/

			/**
			 * Set the amount from base currency to user currency
			 */
			add_filter ( 'yith_ywgc_gift_card_coupon_amount', array(
				$this,
				'convert_to_user_currency'
			), 10, 2 );

            add_filter ( 'ywgc_gift_card_amount_order_total_item', array(
                $this,
                'convert_to_user_currency'
            ), 10, 2 );

			/**
			 * Set the amount from gift card in my account
			 */
			add_filter ( 'yith_ywgc_get_gift_card_price', array(
				$this,
				'yith_ywgc_get_gift_card_price_call_back'
			), 10, 1 );

            /**
             * Get total order amounts on gift card table
             */
            add_filter('yith_ywgc_gift_card_orders_total',array($this,'get_gift_card_orders_total_in_base_currency'),10,2);

		}

		/**
		 * Set the amount from gift card in my account
		 */
		public function yith_ywgc_get_gift_card_price_call_back( $amount ) {

			$amount = self::get_amount_in_currency( $amount, null );

			return $amount;
		}

		public function set_cart_item_price_currency( $amount, $cart_item ) {
			return YWGC_AeliaCS_Module::get_amount_in_currency ( $amount );
		}
		
		/**
		 * @param YWGC_Gift_Card_Premium $gift_card
		 *
		 * @return float
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_amount_in_gift_card_currency( $amount, $gift_card ) {

            if ( ! empty( $gift_card->currency ) ) {

                $order = wc_get_order( $gift_card->order_id );

                $amount = 'none';
                foreach ( $order->get_items ( 'line_item' ) as $order_item_id => $order_item_data ) {

                    $product_id = $order_item_data["product_id"];

                    if ( $gift_card->product_id == $product_id ){

                        $gift_card_id = wc_get_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_POST_ID );

                        if ($gift_card_id == $gift_card->ID){
                            $line_subtotal     = $order_item_data[ "line_subtotal" ];
                            $line_subtotal_tax = $order_item_data[ "line_subtotal_tax" ];

                            //  Generate as many gift card code as the quantity bought
                            $quantity      = $order_item_data[ "qty" ];
                            $single_amount = (float) ( $line_subtotal / $quantity );
                            $single_tax    = (float) ( $line_subtotal_tax / $quantity );
                            $amount = $single_amount + $single_tax;
                        }
                    }

                }

                if ( $amount == 'none' )
                    $amount = self::get_amount_in_currency ( $gift_card->total_amount, $gift_card->currency, null );

			}
			return $amount . $gift_card->currency;
		}

        public function convert_to_user_currency( $amount, $gift_card ) {

            if ( $gift_card->total_amount == $amount && $gift_card->currency == get_woocommerce_currency() ){

                $order = wc_get_order( $gift_card->order_id );

                $amount = 'none';
                foreach ( $order->get_items ( 'line_item' ) as $order_item_id => $order_item_data ) {

                    $product_id = $order_item_data["product_id"];

                    if ( $gift_card->product_id == $product_id ){

                        $line_subtotal     = $order_item_data[ "line_subtotal" ];
                        $line_subtotal_tax = $order_item_data[ "line_subtotal_tax" ];

                        //  Generate as many gift card code as the quantity bought
                        $quantity      = $order_item_data[ "qty" ];

                        $single_amount = (float) ( $line_subtotal / $quantity );
                        $single_tax    = (float) ( $line_subtotal_tax / $quantity );
                        $amount = $single_amount + $single_tax;
                        break;

                    }

                }

            }
            else
                $amount = self::get_amount_in_currency ( $amount );

            if ( $amount == 'none' )
                $amount = self::get_amount_in_currency ( $gift_card->total_amount, $gift_card->currency, null );

            return $amount;
        }
		
		public function convert_amount_to_base_currency( $amount ) {
			
			return $this->convert_user_currency_amount_to_base_currency ( $amount );
		}
		
		/**
		 * Callback to support currency conversion of Gift Card products.
		 *
		 * @param callable   $callback The original callback passed by the Currency
		 *                             Switcher.
		 * @param WC_Product $product  The product to convers.
		 *
		 * @return callable The callback that will perform the conversion.
		 * @since  1.0.6
		 * @author Aelia <support@aelia.co>
		 */
		public function wc_aelia_currencyswitcher_product_convert_callback( $callback, $product ) {
			
			if ( $product instanceof WC_Product_Gift_Card ) {
				$callback = array( $this, 'convert_gift_card_prices' );
			}
			
			return $callback;
		}
		
		/**
		 * Converts the prices of a gift card product to the specified currency.
		 *
		 * @param WC_Product_Gift_Card $product  A variable product.
		 * @param string               $currency A currency code.
		 *
		 * @return WC_Product_Gift_Card The product with converted prices.
		 * @since  1.0.6
		 * @author Aelia <support@aelia.co>
		 */
		public function convert_gift_card_prices( $product, $currency ) {

			$min_price = yit_get_prop ( $product, 'min_price' );
			$max_price = yit_get_prop ( $product, 'max_price' );
			
			yit_set_prop ( $product, array(
				'min_price' => $this->get_amount_in_currency ( $min_price ),
				'max_price' => $this->get_amount_in_currency ( $max_price )
			) );
			
			$amounts = $product->get_product_amounts ();
			if ( $amounts ) {
				foreach ( $amounts as $idx => $amount ) {
					$amounts[ $idx ] = $this->get_amount_in_currency ( $amounts[ $idx ] );
				}
				$product->set_amounts ( $amounts );
			}
			
			$product_price = yit_get_prop ( $product, 'price' );
			if ( ! empty( $product_price ) ) {
				
				yit_set_prop ( $product, 'price', $this->get_amount_in_currency ( $product_price ) );
			}
			
			return $product;
		}
		
		/**
		 * Retrieve the array data key for the subtotal in the current currency
		 */
		public function wc_aelia_line_subtotal( $amount, $order_item_data ) {
			return $order_item_data["line_subtotal_base_currency"];
		}
		
		/**
		 * Retrieve the array data key for the subtotal in the current currency
		 */
		public function wc_aelia_line_subtotal_tax( $amount, $order_item_data ) {
			return $order_item_data["line_subtotal_tax_base_currency"];
		}
		
		/**
		 * Convert the amount from base currency to current currency
		 *
		 * @param float                  $amount
		 * @param YWGC_Gift_Card_Premium $gift_card
		 *
		 * @return float
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function convert_base_currency_amount_to_user_currency( $amount, $gift_card ) {
			
			if ( ! empty( $gift_card->currency ) ) {
				
				return self::get_amount_in_currency ( $amount, null, $gift_card->currency );
			}
			
			return self::get_amount_in_currency ( $amount );
		}
		
		/**
		 * Convert the amount from current currency to base currency
		 *
		 * @param float $amount
		 *
		 * @return float
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function convert_user_currency_amount_to_base_currency( $amount ) {
			
			return self::get_amount_in_currency ( $amount, self::base_currency (), get_woocommerce_currency () );
		}
		
		/**
		 * Convenience method. Returns WooCommerce base currency.
		 *
		 * @return string
		 * @since 1.0.6
		 */
		public static function base_currency() {
			
			if ( empty( self::$base_currency ) ) {
				self::$base_currency = get_option ( 'woocommerce_currency' );
			}
			
			return self::$base_currency;
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
				$from_currency = self::base_currency ();
			}
			
			if ( empty( $to_currency ) ) {
				$to_currency = get_woocommerce_currency ();
			}
			
			return apply_filters ( 'wc_aelia_cs_convert', $amount, $from_currency, $to_currency );
		}

        /**
         * return amount in base currency for manual amounts
         * @param $amount
         * @param null $to_currency
         * @param null $from_currency
         * @param null $price_decimals
         * @param bool $include_markup
         * @author Carlos Rodriguez <carlos.rodriguez@yithemes.com>
         * @return float|int|string
         */
        public function set_amount_to_base_currency($amount,$to_currency = null, $from_currency = null, $price_decimals = null, $include_markup = true )
        {

            if (empty($from_currency)) {
                $from_currency = self::base_currency(); //WooCommerce base Currency
            }

            if (empty($to_currency)) {
                $to_currency = get_woocommerce_currency(); //Actual Currency
            }

            if (!is_numeric($amount)) {
                return $amount;
            }

            // No need to convert a zero amount, it will stay zero
            if ($amount == 0) {
                return $amount;
            }

            // No need to spend time converting a currency to itself
            if ($from_currency == $to_currency) {
                return $amount;
            }

            $currency_switcher = $GLOBALS['woocommerce-aelia-currencyswitcher'];
            $from_currency_rate = $currency_switcher->settings_controller()->get_exchange_rate($from_currency, $include_markup);

            $to_currency_rate = $currency_switcher->settings_controller()->get_exchange_rate($to_currency, $include_markup);

            $exchange_rate = $from_currency_rate / $to_currency_rate;

            return apply_filters( 'yith_wcgc_set_amount_to_base_currency',$amount * $exchange_rate,$amount,$exchange_rate,$from_currency,$to_currency );

        }

        /**
         * Get total order amounts on gift card table
         *  @param $order_total
         *  @param $order
         *  @return float
         **/
        public function get_gift_card_orders_total_in_base_currency($order_total, $order) {

            return self::get_amount_in_currency ( $order_total, get_woocommerce_currency(), $order->get_currency() );
        }

    }
}

YWGC_AeliaCS_Module::get_instance ();