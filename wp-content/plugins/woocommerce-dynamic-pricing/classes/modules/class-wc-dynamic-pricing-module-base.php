<?php

abstract class WC_Dynamic_Pricing_Module_Base {

	public $module_id;
	public $module_type;

	public function __construct( $module_id, $module_type ) {
		$this->module_id   = $module_id;
		$this->module_type = $module_type;
	}

	public abstract function adjust_cart( $cart );

	public function get_price_to_discount( $cart_item, $cart_item_key, $stack_rules = false ) {
		global $woocommerce;

		$result = false;
		do_action( 'wc_memberships_discounts_disable_price_adjustments' );

		$filter_cart_item = $cart_item;
		if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
			$filter_cart_item = WC()->cart->cart_contents[ $cart_item_key ];

			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] ) ) {
				if ( $this->is_cumulative( $cart_item, $cart_item_key ) || $stack_rules ) {
					$result = WC()->cart->cart_contents[ $cart_item_key ]['discounts']['price_adjusted'];
				} else {
					$result = WC()->cart->cart_contents[ $cart_item_key ]['discounts']['price_base'];
				}
			} else {

				if ( apply_filters( 'wc_dynamic_pricing_get_use_sale_price', true, $filter_cart_item['data'] ) ) {
					$result = WC()->cart->cart_contents[ $cart_item_key ]['data']->get_price( 'edit' );
				} else {
					$result = WC()->cart->cart_contents[ $cart_item_key ]['data']->get_regular_price( 'edit' );
				}
			}
		}

		do_action( 'wc_memberships_discounts_enable_price_adjustments' );

		return apply_filters( 'woocommerce_dynamic_pricing_get_price_to_discount', $result, $filter_cart_item, $cart_item_key );
	}

	protected function is_item_discounted( $cart_item, $cart_item_key, $set_id = false ) {
		global $woocommerce;
		if ( $set_id ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts']['applied_discounts'] ) && is_array( WC()->cart->cart_contents[ $cart_item_key ]['discounts']['applied_discounts'] ) ) {
				$applied_rules = wp_list_pluck( WC()->cart->cart_contents[ $cart_item_key ]['discounts']['applied_discounts'], 'set_id' );

				return in_array( $set_id, $applied_rules );
			} else {
				return false;
			}
		} else {
			return isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );
		}
	}

	protected function is_cumulative( $cart_item, $cart_item_key, $default = false ) {
		//Check to make sure the item has not already been discounted by this module.  This could happen if update_totals is called more than once in the cart. 
		$cart = WC()->cart->get_cart();

		if ( isset( $cart ) && is_array( $cart ) && isset( $cart[ $cart_item_key ]['discounts'] ) && in_array( $this->module_id, WC()->cart->cart_contents[ $cart_item_key ]['discounts']['by'] ) ) {
			return apply_filters( 'woocommerce_dynamic_pricing_is_cumulative', $default, $this->module_id, $cart_item, $cart_item_key );
		} else {
			return apply_filters( 'woocommerce_dynamic_pricing_is_cumulative', $default, $this->module_id, $cart_item, $cart_item_key );
		}
	}

	protected function reset_cart_item( &$cart_item, $cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] ) && in_array( $this->module_id, WC()->cart->cart_contents[ $cart_item_key ]['discounts']['by'] ) ) {
			foreach ( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] as $module ) {

			}
		}
	}

	/**
	 * @param $product WC_Product
	 */
	protected function get_product_category_ids( $product ) {
		return WC_Dynamic_Pricing_Compatibility::get_product_category_ids( $product );
	}

}
