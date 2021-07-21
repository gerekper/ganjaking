<?php
/**
 * Handles the discounts of the 'store_credit' coupons in the cart.
 *
 * @package WC_Store_Credit/Discounts
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Discounts_Cart class.
 */
class WC_Store_Credit_Discounts_Cart extends WC_Store_Credit_Discounts {

	/**
	 * Gets the cart instance.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Cart
	 */
	public function cart() {
		return $this->get_object();
	}

	/**
	 * Sets the credit used on this object.
	 *
	 * @since 3.9.0
	 */
	public function set_credit_used_from_object() {
		// Include the credit used in the pending payment order.
		$order_id = WC()->session->get( 'order_awaiting_payment' );

		if ( $order_id ) {
			$credit_used = wc_get_store_credit_used_for_order( $order_id, 'per_coupon' );
			$this->set_credit_used( $credit_used );
		}
	}

	/**
	 * Sets the items to discount.
	 *
	 * @since 3.0.0
	 */
	public function set_items_from_object() {
		$this->set_items( $this->cart()->get_cart() );
	}

	/**
	 * Sets the shipping items to discount.
	 *
	 * @since 3.0.0
	 */
	public function set_shipping_items_from_object() {
		if ( $this->cart()->show_shipping() ) {
			$shipping_items = $this->cart()->calculate_shipping();

			$this->set_shipping_items( $shipping_items );
		}
	}

	/**
	 * Gets if prices include tax.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function get_prices_include_tax() {
		return wc_prices_include_tax();
	}

	/**
	 * Calculates the shipping totals.
	 *
	 * Applies the shipping discounts to the shipping costs.
	 *
	 * @since 3.0.0
	 */
	public function calculate_shipping_totals() {
		$shipping_discounts = $this->coupon_discounts()->get_discounts_by_type( 'shipping' );

		// Set shipping totals.
		$shipping_total = $this->cart()->get_shipping_total() - array_sum( wp_list_pluck( $shipping_discounts, 'shipping' ) );
		$shipping_tax   = $this->cart()->get_shipping_tax() - array_sum( wp_list_pluck( $shipping_discounts, 'shipping_tax' ) );

		$this->cart()->set_shipping_total( $shipping_total );
		$this->cart()->set_shipping_tax( $shipping_tax );

		// Set shipping taxes.
		$taxes_discounts = array_map( 'wc_store_credit_get_negative', wc_store_credit_combine_amounts( wp_list_pluck( $shipping_discounts, 'shipping_taxes' ) ) );
		$shipping_taxes  = wc_store_credit_combine_amounts(
			array(
				'shipping_taxes'  => $this->cart()->get_shipping_taxes(),
				'taxes_discounts' => $taxes_discounts,
			)
		);

		$this->cart()->set_shipping_taxes( $shipping_taxes );
	}

	/**
	 * Calculates the cart totals after discounts.
	 *
	 * @since 3.0.0
	 */
	public function calculate_totals() {
		if ( 0 >= $this->coupon_discounts()->get_total_discount( 'shipping' ) ) {
			return;
		}

		$shipping_total = $this->cart()->get_shipping_total();
		$shipping_tax   = $this->cart()->get_shipping_tax();

		$this->calculate_shipping_totals();

		$shipping_total_diff = $shipping_total - $this->cart()->get_shipping_total();
		$shipping_tax_diff   = $shipping_tax - $this->cart()->get_shipping_tax();

		$cart_total_tax = $this->cart()->get_total_tax();
		$cart_total     = $this->cart()->get_total( 'edit' );

		$this->cart()->set_total_tax( $cart_total_tax - $shipping_tax_diff );
		$this->cart()->set_total( $cart_total - $shipping_total_diff - $shipping_tax_diff );
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

		// It's a deposit product.
		$price = ( isset( $item['deposit_amount'] ) ? $item['deposit_amount'] : $item['data']->get_price() );

		$object->product   = $item['data'];
		$object->quantity  = $item['quantity'];
		$object->price     = ( $price * $object->quantity );
		$object->tax_class = $item['data']->get_tax_class();
		$object->tax_rates = WC_Tax::get_rates( $object->tax_class );

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
		$taxes  = $shipping_item->get_taxes();

		$object->label     = $shipping_item->get_label();
		$object->total     = $shipping_item->get_cost();
		$object->taxes     = $taxes;
		$object->tax_rates = ( ! empty( $taxes ) ? WC_Tax::get_shipping_tax_rates() : array() );

		return $object;
	}
}
