<?php

class WC_Dynamic_Pricing_FrontEnd_UX {

	private static $instance;

	public static function init() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_FrontEnd_UX();
		}
	}

	public function __construct() {
		add_action( 'init', array($this, 'on_init'), 0 );
	}

	public function on_init() {
		//Filter for the cart adjustment for advanced rules.
		add_filter( 'woocommerce_cart_item_price', array($this, 'on_display_cart_item_price_html'), 10, 3 );
	}

	public function on_display_cart_item_price_html( $html, $cart_item, $cart_item_key ) {
		if ( $this->is_cart_item_discounted( $cart_item ) ) {
			$_product = $cart_item['data'];

			if ( function_exists( 'get_product' ) ) {

				if (isset($cart_item['is_deposit']) && $cart_item['is_deposit']) {
					$price_to_calculate = isset( $cart_item['discounts'] ) ? $cart_item['discounts']['price_adjusted'] : $cart_item['data']->get_price();
				} else {
					//Just use the price from the product, it has already been set during cart_loaded_from_session.
					$price_to_calculate = $cart_item['data']->get_price();
				}

                if ( $price_to_calculate == $_product->get_price() ) {
                    //TODO:  Correct this for memberships.
                    //return $html;
                }

				$price_adjusted = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax($_product, array('price' => $price_to_calculate, 'qty' => 1)) : wc_get_price_including_tax($_product, array('price' => $price_to_calculate, 'qty' => 1));
				$price_base = $cart_item['discounts']['display_price'];
			} else {
				if ( get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' ) :
					$price_adjusted = wc_get_price_excluding_tax($cart_item['data']);
					$price_base = $cart_item['discounts']['display_price'];
				else :
					$price_adjusted = $cart_item['data']->get_price();
					$price_base = $cart_item['discounts']['display_price'];
				endif;
			}

			if ( !empty( $price_adjusted ) || $price_adjusted === 0 || $price_adjusted === 0.00 ) {
				if ( apply_filters( 'wc_dynamic_pricing_use_discount_format', true ) ) {
					$html = '<del>' . WC_Dynamic_Pricing_Compatibility::wc_price( $price_base ) . '</del><ins> ' . WC_Dynamic_Pricing_Compatibility::wc_price( $price_adjusted ) . '</ins>';
				} else {
					$html = '<span class="amount">' . WC_Dynamic_Pricing_Compatibility::wc_price( $price_adjusted ) . '</span>';
				}
			}
		}

		return $html;
	}

	public function is_cart_item_discounted( $cart_item ) {
		return isset( $cart_item['discounts'] );
	}

}


