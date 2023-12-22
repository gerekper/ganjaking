<?php
/**
 * Handles the discounts of the 'store_credit' coupons in an order.
 *
 * @package WC_Store_Credit/Discounts
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Discounts_Order.
 */
class WC_Store_Credit_Discounts_Order extends WC_Store_Credit_Discounts {

	/**
	 * The status of the 'recalculate coupons' process.
	 *
	 * @var string
	 */
	protected $recalculate_coupons_status = '';

	/**
	 * Gets the order object.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Order
	 */
	public function order() {
		return $this->get_object();
	}

	/**
	 * Gets the status of the 'recalculate coupons' process.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_recalculate_coupon_status() {
		return $this->recalculate_coupons_status;
	}

	/**
	 * Sets the status of the 'recalculate coupons' process.
	 *
	 * @since 3.0.0
	 *
	 * @param string $status The new status.
	 */
	public function set_recalculate_coupon_status( $status = '' ) {
		$this->recalculate_coupons_status = $status;
	}

	/**
	 * Sets the credit used on this object.
	 *
	 * @since 3.0.0
	 */
	public function set_credit_used_from_object() {
		$credit_used = wc_get_store_credit_used_for_order( $this->order(), 'per_coupon' );

		$this->set_credit_used( $credit_used );
	}

	/**
	 * Sets the items to discount.
	 *
	 * @since 3.0.0
	 */
	public function set_items_from_object() {
		$this->set_items( $this->order()->get_items() );
	}

	/**
	 * Sets the shipping items to discount.
	 *
	 * @since 3.0.0
	 */
	public function set_shipping_items_from_object() {
		$shipping_items = $this->order()->get_shipping_methods();

		$this->set_shipping_items( $shipping_items );
	}

	/**
	 * Gets if prices include tax.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function get_prices_include_tax() {
		return $this->order()->get_prices_include_tax();
	}

	/**
	 * Calculates the order total discounts.
	 *
	 * Fixes discrepancies in the total discounts.
	 *
	 * @since 3.0.0
	 */
	public function calculate_discount_totals() {
		$cart_discount     = 0;
		$cart_discount_tax = 0;

		foreach ( $this->order()->get_items( 'coupon' ) as $coupon ) {
			$cart_discount     += $coupon->get_discount();
			$cart_discount_tax += $coupon->get_discount_tax();
		}

		// Round totals at the end instead of per item.
		$cart_discount_tax = round( $cart_discount_tax, wc_get_price_decimals() );

		// WC 3.9+ stores the cart discount with high precision when rounding tax at subtotal.
		if ( version_compare( WC_VERSION, '3.9', '<' ) || ! wc_store_credit_round_tax_at_subtotal() ) {
			$cart_discount = round( $cart_discount, wc_get_price_decimals() );
		}

		$cart_discount_diff     = round( $this->order()->get_discount_total( 'edit' ) - $cart_discount, wc_get_price_decimals() );
		$cart_discount_tax_diff = round( $this->order()->get_discount_tax( 'edit' ) - $cart_discount_tax, wc_get_price_decimals() );

		// There is a discrepancy in the cart discount.
		if ( 0 != $cart_discount_diff ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseNotEqual
			$this->order()->set_discount_total( $cart_discount );
		}

		// There is a discrepancy in the cart discount tax.
		if ( 0 != $cart_discount_tax_diff ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseNotEqual
			$this->order()->set_discount_tax( $cart_discount_tax );
		}
	}

	/**
	 * Calculates the order totals after discounts.
	 *
	 * Fixes discrepancies in the order's totals when applying coupons with tax included.
	 *
	 * @since 3.0.0
	 */
	public function calculate_totals() {
		// There is no need to recalculate the totals.
		if ( ! $this->coupon_discounts()->has_with( 'inc_tax' ) ) {
			return;
		}

		$this->calculate_discount_totals();

		/*
		 * When applying coupons with tax included, it might be a discrepancy of 0.01 in the order total.
		 * We use the original values instead of the rounded to 2dp to obtain the order total.
		 */
		$cart_subtotal     = 0;
		$cart_subtotal_tax = 0;
		$fees_total        = 0;

		foreach ( $this->order()->get_items() as $item ) {
			$taxes = $item->get_taxes();

			$cart_subtotal     += $item->get_subtotal();
			$cart_subtotal_tax += array_sum( $taxes['subtotal'] );
		}

		foreach ( $this->order()->get_fees() as $item ) {
			$fees_total += $item->get_total();
		}

		$order_total = round(
			$cart_subtotal +
			$cart_subtotal_tax +
			$fees_total +
			(float) $this->order()->get_shipping_total( 'edit' ) +
			(float) $this->order()->get_shipping_tax( 'edit' ) -
			(float) $this->order()->get_discount_total( 'edit' ) -
			(float) $this->order()->get_discount_tax( 'edit' ),
			wc_get_price_decimals()
		);

		$this->order()->set_total( $order_total );
	}

	/**
	 * Updates the shipping discount items.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order Optional. Order object.
	 */
	public function update_shipping_discount_items( $order = null ) {
		if ( is_null( $order ) ) {
			$order = $this->order();
		}

		parent::update_shipping_discount_items( $order );
	}

	/**
	 * Updates the store credit used.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order Optional. Order object.
	 */
	public function update_credit_used( $order = null ) {
		if ( is_null( $order ) ) {
			$order = $this->order();
		}

		parent::update_credit_used( $order );

		// The order discounts are up to date now.
		$order->delete_meta_data( '_store_credit_version' );
		$order->delete_meta_data( '_store_credit_before_tax' );
	}

	/**
	 * Updates the store credit discounts.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order Optional. Order object.
	 */
	public function update_credit_discounts( $order = null ) {
		if ( is_null( $order ) ) {
			$order = $this->order();
		}

		parent::update_credit_discounts( $order );
	}

	/**
	 * Parses the item before including it in the list.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $key  Item key.
	 * @param mixed $item Item object.
	 * @return stdClass
	 */
	protected function parse_item( $key, $item ) {
		$object = parent::parse_item( $key, $item );

		$object->product   = $item->get_product();
		$object->quantity  = $item->get_quantity();
		$object->price     = $item->get_subtotal();
		$object->tax_class = $item->get_tax_class();
		$object->tax_rates = WC_Tax::get_rates( $item->get_tax_class() );

		if ( $this->get_object()->get_prices_include_tax() ) {
			$object->price += $item->get_subtotal_tax();
		}

		return $object;
	}

	/**
	 * Parses a shipping item before including it in the list.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $key           Shipping key.
	 * @param mixed $shipping_item Shipping item.
	 * @return stdClass
	 */
	protected function parse_shipping_item( $key, $shipping_item ) {
		$object = parent::parse_shipping_item( $key, $shipping_item );

		$taxes = $shipping_item->get_taxes();
		$taxes = ( isset( $taxes['total'] ) ? $taxes['total'] : array() );

		$object->label = $shipping_item->get_name();
		$object->total = $shipping_item->get_total();
		$object->taxes = $taxes;

		$tax_rates = array();

		foreach ( $taxes as $rate_id => $amount ) {
			$tax_rate = $this->get_tax_rate( $rate_id );

			if ( ! empty( $tax_rate ) ) {
				$tax_rates[ $rate_id ] = array(
					'rate'     => (float) $tax_rate['tax_rate'],
					'name'     => ( isset( $tax_rate['tax_rate_name'] ) ? $tax_rate['tax_rate_name'] : '' ),
					'shipping' => wc_bool_to_string( $tax_rate['tax_rate_shipping'] ),
					'compound' => wc_bool_to_string( $tax_rate['tax_rate_compound'] ),
				);
			}
		}

		$object->tax_rates = $tax_rates;

		return $object;
	}

	/**
	 * Gets the tax rate data.
	 *
	 * @since 4.1.0
	 *
	 * @param int $rate_id Tax rate ID.
	 * @return array
	 */
	protected function get_tax_rate( $rate_id ) {
		$tax_rate = ( is_numeric( $rate_id ) ? WC_Tax::_get_tax_rate( $rate_id ) : array() );

		/**
		 * Filters the tax rate data when calculating the Store Credit discounts for an order.
		 *
		 * @since 4.1.0
		 *
		 * @param array    $tax_rate Tax rate data.
		 * @param int      $rate_id  Tax rate ID.
		 * @param WC_Order $order    Order object.
		 */
		return apply_filters( 'wc_store_credit_discounts_order_tax_rate', $tax_rate, $rate_id, $this->get_object() );
	}
}
