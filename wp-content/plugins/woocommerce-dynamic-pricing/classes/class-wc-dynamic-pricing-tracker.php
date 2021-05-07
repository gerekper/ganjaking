<?php

class WC_Dynamic_Pricing_Tracker {

	private static $applied_cart_adjustments = array();

	public static function track_cart_adjustment( $cart_item_key, $base_price, $adjusted_price, $adjustment_module, $adjustment_group_id ) {
		$trackdata = array(
		    'price_base' => $base_price,
		    'price_adjusted' => $adjusted_price
		);

		if ( !isset( self::$applied_cart_adjustments[$cart_item_key] ) ) {
			self::$applied_cart_adjustments[$cart_item_key] = array();
		}

		if ( !isset( self::$applied_cart_adjustments[$cart_item_key][$adjustment_module] ) ) {
			self::$applied_cart_adjustments[$cart_item_key][$adjustment_module] = array();
		}

		self::$applied_cart_adjustments[$cart_item_key][$adjustment_module][$adjustment_group_id] = $trackdata;
	}

}
