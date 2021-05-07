<?php

class WC_Dynamic_Pricing_Cart_Query {

	public static function sort_by_price( $cart_item_a, $cart_item_b ) {
		if ( empty( $cart_item_a ) || empty( $cart_item_b ) ) {
			return 0;
		} else {
			$product_a = isset( $cart_item_a['data'] ) ? $cart_item_a['data'] : false;
			$product_b = isset( $cart_item_b['data'] ) ? $cart_item_b['data'] : false;
			if ( empty( $cart_item_a ) || empty( $cart_item_b ) ) {
				return 0;
			} else {
				return ( $product_a->get_price( 'edit' ) > $product_b->get_price( 'edit' ) ) ? -1 : 1;
			}
		}
	}

	public static function sort_by_price_desc( $cart_item_a, $cart_item_b ) {

		if ( empty( $cart_item_a ) || empty( $cart_item_b ) ) {
			return 0;
		} else {
			$product_a = isset( $cart_item_a['data'] ) ? $cart_item_a['data'] : false;
			$product_b = isset( $cart_item_b['data'] ) ? $cart_item_b['data'] : false;
			if ( empty( $cart_item_a ) || empty( $cart_item_b ) ) {
				return 0;
			} else {
				return ( $product_a->get_price( 'edit' ) < $product_b->get_price( 'edit' ) ) ? 1 : -1;
			}
		}
	}

}
