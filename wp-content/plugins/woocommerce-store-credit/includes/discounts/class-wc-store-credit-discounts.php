<?php
/**
 * Handles the discounts of the 'store_credit' coupons.
 *
 * @package WC_Store_Credit/Discounts
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Discounts.
 */
abstract class WC_Store_Credit_Discounts {

	/**
	 * The object to apply the discounts.
	 *
	 * @var mixed
	 */
	protected $object;

	/**
	 * The credit used on this object.
	 *
	 * @var array
	 */
	protected $credit_used = array();

	/**
	 * An array of items to apply discounts.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * An array of shipping items to apply discounts.
	 *
	 * @var array
	 */
	protected $shipping_items = array();

	/**
	 * Shipping discount items.
	 *
	 * @var array
	 */
	protected $shipping_discount_items = array();

	/**
	 * Collection of `WC_Store_Credit_Coupon_Discount` objects.
	 *
	 * @var WC_Store_Credit_Coupon_Discounts
	 */
	protected $coupon_discounts;

	/**
	 * Constructor.
	 *
	 * @since 2.4.0
	 *
	 * @param mixed $object The object to apply the discounts.
	 */
	public function __construct( $object ) {
		$this->set_object( $object );
		$this->set_credit_used_from_object();

		$this->coupon_discounts = new WC_Store_Credit_Coupon_Discounts();
	}

	/**
	 * Gets the collection of `WC_Store_Credit_Coupon_Discount` objects.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Store_Credit_Coupon_Discounts
	 */
	public function coupon_discounts() {
		return $this->coupon_discounts;
	}

	/**
	 * Gets the object.
	 *
	 * @since 2.4.0
	 *
	 * @return mixed
	 */
	public function get_object() {
		return $this->object;
	}

	/**
	 * Sets the object.
	 *
	 * @since 2.4.0
	 *
	 * @param mixed $object The object to apply the discounts.
	 */
	public function set_object( $object ) {
		$this->object = $object;
	}

	/**
	 * Gets the credit used.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_credit_used() {
		return $this->credit_used;
	}

	/**
	 * Sets the credit used.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credit An array with the credit used per coupon.
	 */
	public function set_credit_used( $credit ) {
		$this->credit_used = $credit;
	}

	/**
	 * Gets the items to apply discounts.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Sets the items to apply discounts.
	 *
	 * @since 3.0.0
	 *
	 * @param array $items An array of items to apply discounts.
	 */
	public function set_items( $items ) {
		foreach ( $items as $item_key => $item ) {
			$this->items[ $item_key ] = $this->parse_item( $item_key, $item );
		}
	}

	/**
	 * Gets the shipping items to apply discounts.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_shipping_items() {
		return $this->shipping_items;
	}

	/**
	 * Gets the shipping discount items.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_shipping_discount_items() {
		return $this->shipping_discount_items;
	}

	/**
	 * Sets the shipping items to apply discounts.
	 *
	 * @since 3.0.0
	 *
	 * @param array $items An array of shipping items to apply discounts.
	 */
	public function set_shipping_items( $items ) {
		foreach ( $items as $item_key => $item ) {
			// Store the shipping discount items in a separate list.
			if ( 'store_credit_discount' === $item->get_method_id() ) {
				$this->shipping_discount_items[ $item_key ] = $this->parse_shipping_item( $item_key, $item );
			} else {
				$this->shipping_items[ $item_key ] = $this->parse_shipping_item( $item_key, $item );
			}
		}
	}

	/**
	 * Sets the credit used on this object.
	 *
	 * @since 3.0.0
	 */
	public function set_credit_used_from_object() {}

	/**
	 * Sets the items to discount.
	 *
	 * @since 3.0.0
	 */
	public function set_items_from_object() {}

	/**
	 * Sets the shipping items to discount.
	 *
	 * @since 3.0.0
	 */
	public function set_shipping_items_from_object() {}

	/**
	 * Gets if prices include tax.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function get_prices_include_tax() {
		return false;
	}

	/**
	 * Gets the credit available for the specified coupon.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 * @return float
	 */
	public function get_coupon_credit( $coupon ) {
		$coupon_code = $coupon->get_code();

		return (float) ( isset( $this->credit_used[ $coupon_code ] ) ? $this->credit_used[ $coupon_code ] : $coupon->get_amount() );
	}

	/**
	 * Calculate item discounts.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Coupon $coupon              Coupon object.
	 * @param array     $discounting_amounts Discounting amounts per item.
	 * @return array The item discounts.
	 */
	public function calculate_item_discounts( $coupon, $discounting_amounts ) {
		$this->set_items_from_object();

		$inc_tax        = $this->get_prices_include_tax();
		$item_discounts = new WC_Store_Credit_Item_Discounts();

		foreach ( $discounting_amounts as $item_key => $discounting_amount ) {
			$item_discount = new WC_Store_Credit_Item_Discount( $this->items[ $item_key ], $discounting_amount, $inc_tax );
			$item_discounts->add( $item_discount );
		}

		// There are no discounts applicable, but maybe the coupon can be applied to other costs like shipping.
		if ( 0 >= $item_discounts->get_total_discount( 'cart' ) ) {
			$this->register_coupon_discount( $coupon );

			return array();
		}

		$coupon_credit  = $this->get_coupon_credit( $coupon );
		$coupon_inc_tax = wc_store_credit_coupon_include_tax( $coupon );

		if ( $coupon_inc_tax ) {
			$discounting_amounts = wc_store_credit_combine_amounts(
				array(
					$item_discounts->get_discount( 'cart', 'base' ),
					$item_discounts->get_discount( 'cart', 'tax' ),
				)
			);
		} else {
			$discounting_amounts = $item_discounts->get_discount( 'cart', 'base' );
		}

		// Divide the discount between all items.
		$precision = ( $coupon_inc_tax || wc_store_credit_round_tax_at_subtotal() ? wc_get_rounding_precision() : wc_get_price_decimals() );
		$discounts = wc_store_credit_get_proportional_discounts( $discounting_amounts, $coupon_credit, $precision );

		$item_discounts = new WC_Store_Credit_Item_Discounts();

		foreach ( $discounts as $item_key => $discount ) {
			$item_discount = new WC_Store_Credit_Item_Discount( $this->items[ $item_key ], $discount, $coupon_inc_tax );
			$item_discounts->add( $item_discount );

			// Add or remove the tax discount depending on if the item prices or the coupon amount include tax.
			if ( $inc_tax && ! $coupon_inc_tax ) {
				$discounts[ $item_key ] = $discount + $item_discount->get_discount_tax();
			} elseif ( ! $inc_tax && $coupon_inc_tax ) {
				$discounts[ $item_key ] = $discount - $item_discount->get_discount_tax();
			}
		}

		// Register the discounts for this coupon.
		$coupon_discount = $this->register_coupon_discount( $coupon, $item_discounts );

		return ( $coupon_discount ? $discounts : array() );
	}

	/**
	 * Calculates the shipping discounts.
	 *
	 * @since 3.0.0
	 */
	public function calculate_shipping_discounts() {
		$this->set_shipping_items_from_object();

		foreach ( $this->get_shipping_items() as $key => $shipping_item ) {
			// Skip free shipping.
			if ( 0 >= $shipping_item->total ) {
				continue;
			}

			$discounted       = 0;
			$discounted_taxes = array();

			foreach ( $this->coupon_discounts()->all() as $coupon_code => $coupon_discount ) {
				if ( ! $coupon_discount->apply_to_shipping() ) {
					continue;
				}

				$coupon_discount->item_discounts()->clear( 'shipping' );

				$coupon_credit    = $this->get_coupon_credit( $coupon_discount->get_coupon() );
				$remaining_amount = $coupon_discount->get_remaining_credit( $coupon_credit );

				if ( 0 >= $remaining_amount ) {
					continue;
				}

				$coupon_inc_tax     = $coupon_discount->inc_tax();
				$discounting_amount = ( $shipping_item->total - $discounted );

				if ( $coupon_inc_tax ) {
					$shipping_taxes = wc_store_credit_combine_amounts(
						array(
							'shipping_taxes'   => $shipping_item->taxes,
							'discounted_taxes' => array_map( 'wc_store_credit_get_negative', $discounted_taxes ),
						)
					);

					$shipping_taxes = ( wc_store_credit_round_tax_at_subtotal() ? $shipping_taxes : array_map( 'wc_round_tax_total', $shipping_taxes ) );

					$discounting_amount += wc_round_tax_total( array_sum( $shipping_taxes ) );
				}

				$shipping_item_discount = new WC_Store_Credit_Item_Discount_Shipping( $shipping_item, min( $remaining_amount, $discounting_amount ), $coupon_inc_tax );
				$coupon_discount->item_discounts()->add( $shipping_item_discount );

				// Update already discounted amounts for the next coupon.
				$discounted += $shipping_item_discount->get_discount();

				if ( $coupon_inc_tax ) {
					$discounted_taxes = wc_store_credit_combine_amounts(
						array(
							'discounted_taxes'    => $discounted_taxes,
							'item_discount_taxes' => $shipping_item_discount->get_discount_taxes(),
						)
					);
				}
			}
		}
	}

	/**
	 * Gets the item discounts grouped by the shipping instance ID.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_discounts_by_shipping_instance_id() {
		$items = array();

		foreach ( $this->coupon_discounts()->all() as $coupon_code => $coupon_discount ) {
			$item_discounts = $coupon_discount->item_discounts()->get_by_group( 'shipping' );

			foreach ( $item_discounts as $item_discount ) {
				$item        = $item_discount->get_item();
				$instance_id = $item->object->get_instance_id();

				if ( ! isset( $items[ $instance_id ] ) ) {
					$items[ $instance_id ] = array(
						'item'    => $item,
						'coupons' => array(),
					);
				}

				// Pairs [coupon_code => WC_Store_Credit_Item_Discount_Shipping].
				$items[ $instance_id ]['coupons'][ $coupon_code ] = $item_discount;
			}
		}

		return $items;
	}

	/**
	 * Updates the shipping discount items.
	 *
	 * Applies the shipping discounts as negative WC_Order_Item_Shipping objects.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order Order object.
	 */
	public function update_shipping_discount_items( $order ) {
		$shipping_discounts = $this->get_discounts_by_shipping_instance_id();
		$instance_ids       = wp_list_pluck( $this->shipping_discount_items, 'instance_id' );

		// Remove unnecessary items.
		$instance_ids_diff = array_diff( $instance_ids, array_keys( $shipping_discounts ) );
		$remove_item_ids   = array_flip( $instance_ids_diff );

		foreach ( $remove_item_ids as $item_id ) {
			$order->remove_item( $item_id );

			unset( $this->shipping_discount_items[ $item_id ] );
		}

		foreach ( $shipping_discounts as $instance_id => $shipping_discount ) {
			$item_id = array_search( $instance_id, $instance_ids, true );

			if ( false === $item_id ) {
				$shipping_discount_item = $this->create_shipping_discount_item( $shipping_discount['item'], $order );
			} else {
				$shipping_discount_item = $this->shipping_discount_items[ $item_id ]->object;
			}

			try {
				$discount       = 0;
				$discount_taxes = array();

				foreach ( $shipping_discount['coupons'] as $coupon_code => $item_discount ) {
					$shipping_discount_item->update_meta_data( $coupon_code, $item_discount->get_discount() );

					$discount      += $item_discount->get_discount();
					$discount_taxes = wc_store_credit_combine_amounts(
						array(
							'discount' => $discount_taxes,
							'taxes'    => $item_discount->get_discount_taxes(),
						)
					);
				}

				$shipping_discount_item->set_total( - $discount );
				$shipping_discount_item->set_taxes( array( 'total' => array_map( 'wc_store_credit_get_negative', $discount_taxes ) ) );

				$order->add_item( $shipping_discount_item );
			} catch ( Exception $e ) {
				continue;
			}
		}
	}

	/**
	 * Updates the store credit discounts.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order Order object.
	 */
	public function update_credit_discounts( $order ) {
		$credit_discounts = array();
		$coupons          = $this->get_order_coupon_items( $order );

		foreach ( $this->coupon_discounts()->all() as $coupon_code => $coupon_discount ) {
			$discounts = $coupon_discount->get_discounts();

			// Round and convert to string the amounts to avoid invalid float values.
			foreach ( $discounts as $key => $discount ) {
				// Keep taxes unrounded to be able to recover the discounts with high precision.
				if ( is_array( $discount ) ) {
					$discounts[ $key ] = array_map( 'strval', $discount );
				} elseif ( in_array( $key, array( 'cart', 'cart_tax' ), true ) && isset( $coupons[ $coupon_code ] ) ) {
					// Use the coupon line values for these keys.
					$coupon_item = $coupons[ $coupon_code ];

					$discounts[ $key ] = ( 'cart' === $key ? $coupon_item->get_discount() : $coupon_item->get_discount_tax() );
				} else {
					$discounts[ $key ] = strval( round( $discount, wc_get_price_decimals() ) );
				}
			}

			$credit_discounts[ $coupon_code ] = $discounts;
		}

		if ( ! empty( $credit_discounts ) ) {
			$order->update_meta_data( '_store_credit_discounts', $credit_discounts );
		} else {
			$order->delete_meta_data( '_store_credit_discounts' );
		}
	}

	/**
	 * Updates the store credit used.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order Order object.
	 */
	public function update_credit_used( $order ) {
		$credit_used     = $this->get_credit_used();
		$total_discounts = $this->coupon_discounts()->get_total_discounts();

		// Restore credit to the removed coupons.
		$removed_coupons = array_diff_key( $credit_used, $total_discounts );

		foreach ( $removed_coupons as $coupon_code => $discount ) {
			wc_update_store_credit_coupon_balance( $coupon_code, $discount, 'increase' );

			unset( $credit_used[ $coupon_code ] );
		}

		// Update credit to the applied coupons.
		foreach ( $total_discounts as $coupon_code => $discount ) {
			// Get the credit used balance.
			$balance = ( isset( $credit_used[ $coupon_code ] ) ? $discount - $credit_used[ $coupon_code ] : $discount );

			// Update the coupon credit.
			if ( $balance ) {
				wc_update_store_credit_coupon_balance( $coupon_code, abs( $balance ), ( 0 > $balance ? 'increase' : 'decrease' ) );
			}

			// Update the credit used.
			if ( 0 < $discount ) {
				$credit_used[ $coupon_code ] = $discount;
			} else {
				unset( $credit_used[ $coupon_code ] );
			}
		}

		$this->set_credit_used( $credit_used );

		wc_update_store_credit_used_for_order( $order, $credit_used, null, false );
	}

	/**
	 * Gets the order's coupon items.
	 *
	 * Uses the coupon codes as indices.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	protected function get_order_coupon_items( $order ) {
		$coupons      = array();
		$coupon_items = $order->get_items( 'coupon' );

		if ( $coupon_items ) {
			foreach ( $coupon_items as $coupon_item ) {
				$coupons[ $coupon_item->get_code() ] = $coupon_item;
			}
		}

		return $coupons;
	}

	/**
	 * Creates a shipping discount item from the specified shipping item.
	 *
	 * @since 3.0.0
	 *
	 * @param stdClass $shipping_item The shipping item.
	 * @param WC_Order $order         Order object.
	 * @return WC_Order_Item_Shipping
	 */
	protected function create_shipping_discount_item( $shipping_item, $order ) {
		$item = new WC_Order_Item_Shipping();
		$item->set_props(
			array(
				/* translators: %s: shipping method label */
				'method_title' => sprintf( _x( 'Discount: %s', 'shipping discount label', 'woocommerce-store-credit' ), $shipping_item->label ),
				'method_id'    => 'store_credit_discount',
				'instance_id'  => $shipping_item->instance_id,
				'order_id'     => $order->get_id(),
				'total'        => 0,
				'taxes'        => array(
					'total' => array(),
				),
			)
		);

		return $item;
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
		return $this->parse_item_base( $key, $item );
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
		$object = $this->parse_item_base( $key, $shipping_item );

		$object->method_id   = $shipping_item->get_method_id();
		$object->instance_id = intval( $shipping_item->get_instance_id() );

		return $object;
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
	protected function parse_item_base( $key, $item ) {
		$object         = new stdClass();
		$object->key    = $key;
		$object->object = $item;

		return $object;
	}

	/**
	 * Registers a coupon discount object.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Coupon $coupon         Coupon object.
	 * @param mixed     $item_discounts Optional. A collection of item discounts or an array of item discount objects.
	 * @return WC_Store_Credit_Coupon_Discount|false Coupon discount object. False on failure.
	 */
	protected function register_coupon_discount( $coupon, $item_discounts = array() ) {
		try {
			$coupon_discount = new WC_Store_Credit_Coupon_Discount( $coupon, $item_discounts );

			$this->coupon_discounts()->add( $coupon_discount );
		} catch ( Exception $e ) {
			$coupon_discount = false;
		}

		return $coupon_discount;
	}
}
