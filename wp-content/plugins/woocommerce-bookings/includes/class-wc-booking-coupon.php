<?php

/**
 * WC_Booking_Coupon class.
 */
class WC_Booking_Coupon {

	/**
	 * Holds discount amounts for each applied coupon. Keys are coupon code,
	 * values are total discount amounts.
	 *
	 * @var array
	 */
	private $amounts = array();

	/**
	 * Holds booking items with coupon applied.
	 *
	 * @var array
	 */
	private $already_applied = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_coupon_discount_types', array( $this, 'add_coupon_type' ) );
		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_coupon_valid' ), 10, 3 );
		add_filter( 'woocommerce_coupon_custom_discounts_array', array( $this, 'coupon_custom_discounts' ), 10, 2 );
		add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'update_coupon_discount_amount' ), 10, 5 );

		// Make our custom coupon type 'booking_person' valid for the cart. `is_valid_for_cart()`.
		add_filter( 'woocommerce_cart_coupon_types', array( $this, 'cart_coupon_types' ) );
	}

	/**
	 * Adds a new coupon type that allows a discount per person booked.
	 *
	 * @param  array $types Coupon types
	 * @return array        Altered coupon types with booking_person added
	 */
	public function add_coupon_type( $types ) {
		$types['booking_person'] = esc_html__( 'Booking Person Discount (Amount Off Per Person)', 'woocommerce-bookings' );

		return $types;
	}

	/**
	 * Filter the list of coupon types that are valid for the cart.
	 *
	 * @param array $types Valid coupon types for the cart.
	 *
	 * @return array
	 */
	public function cart_coupon_types( $types ) {
		if ( is_array( $types ) ) {
			array_push( $types, 'booking_person' );
		}

		return $types;
	}

	/**
	 * Looks through our cart to see if we actually have a booking product
	 * before applying our coupon.
	 *
	 * @param bool      $is_valid  Whether a given coupon is valid
	 * @param WC_Coupon $wc_coupon Coupon object
	 *
	 * @return bool Returns true if coupon is valid
	 */
	public function is_coupon_valid( $is_valid, $wc_coupon, $discount ) {
		if ( 'booking_person' !== self::get_coupon_prop( $wc_coupon, 'discount_type' ) ) {
			return $is_valid;
		}

		if ( ! WC()->cart->is_empty() ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$product = wc_get_product( $cart_item['product_id'] );
				if ( is_a( $product, 'WC_Product_Booking' ) && $product->has_persons() ) {
					return true;
				}
			}
		}

		// Ensure coupons apply correctly if added within the admin.
		if ( $discount->get_object() ) {
			check_ajax_referer( 'order-item', 'security' );

			$order_items = $discount->get_items_to_validate();
			foreach ( $order_items as $order_item ) {
				$product = wc_get_product( $order_item->object->get_product_id() );

				if ( is_a( $product, 'WC_Product_Booking' ) && $product->has_persons() ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Calculate discount for specific cart item and coupon,
	 * by multiplying person types by coupon discount amount.
	 *
	 * @param array     $cart_item  Cart item
	 * @param WC_Coupon $coupon     Coupon
	 * @return float Discount amount
	 */
	protected function get_discount( $cart_item, $coupon ) {
		$product_id = is_callable( array( $cart_item['data'], 'get_id' ) ) ? $cart_item['data']->get_id() : $cart_item['data']->id;
		$product    = wc_get_product( $product_id );

		if ( ! $product->is_type( 'booking' ) ) {
			return 0;
		}

		if ( ! $coupon->is_valid() ) {
			return 0;
		}

		if ( 'booking_person' !== self::get_coupon_prop( $coupon, 'discount_type' ) ) {
			return 0;
		}

		$discount_amount = self::get_coupon_prop( $coupon, 'amount' );
		$total_persons   = array_sum( $cart_item['booking']['_persons'] );
		$discount_amount = $discount_amount * $total_persons;

		return $discount_amount * 100;
	}

	/**
	 * Get coupon discount amount.
	 *
	 * @param  array $discounts
	 * @param  WC_Coupon $coupon
	 * @return float
	 */
	public function coupon_custom_discounts( $discounts, $coupon ) {
		if ( 'booking_person' !== self::get_coupon_prop( $coupon, 'discount_type' ) ) {
			return $discounts;
		}

		foreach ( WC()->cart->cart_contents as $key => $cart_item ) {
			if ( ! isset( $discounts[ $coupon->get_code() ][ $key ] ) ) {
				$discounts[ $key ] = 0;
			}

			$discounts[ $key ] += $this->get_discount( $cart_item, $coupon );
			$this->amounts[ $coupon->get_code() ] = $discounts[ $key ] / 100;
		}

		return $discounts;
	}

	/**
	 * Update coupon discount amount of an order item.
	 *
	 * @param float     $discount
	 * @param float     $discounting_amount
	 * @param object    $cart_item WC_Order_Item_Product
	 * @param bool      $single
	 * @param WC_Coupon $coupon
	 *
	 * @return float
	 */
	public function update_coupon_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		if ( 'booking_person' !== self::get_coupon_prop( $coupon, 'discount_type' ) ) {
			return $discount;
		}

		// When coupon applied on the cart/checkout page.
		if ( is_callable( array( $cart_item['data'], 'get_id' ) ) ) {
			$product_id    = $cart_item['data']->get_id();
			$total_persons = array_sum( $cart_item['booking']['_persons'] );
		} else {
			// When coupon applied on the order edit page (in back end).
			$product_id = $cart_item->get_product_id();
			$item_id    = $cart_item->get_id();

			// Get the booking ID.
			$booking_id = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $item_id );
			$booking_id = $booking_id[0] ?? false;

			// Return if booking ID not found from the given order item ID.
			if ( ! $booking_id ) {
				return $discount;
			}

			// Get the total persons.
			$booking       = new WC_Booking( $booking_id );
			$total_persons = $booking->get_persons_total();
		}
		$product = wc_get_product( $product_id );

		if ( ! $product->is_type( 'booking' ) ) {
			return $discount;
		}

		$discount_amount = self::get_coupon_prop( $coupon, 'amount' );

		return $discount_amount * $total_persons;
	}

	/**
	 * Get coupon property with compatibility check on order getter introduced
	 * in WC 3.0.
	 *
	 * @since 1.10.3
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 * @param string    $prop   Property name.
	 *
	 * @return mixed Property value
	 */
	public static function get_coupon_prop( $coupon, $prop ) {
		$getter = array( $coupon, 'get_' . $prop );
		return is_callable( $getter ) ? call_user_func( $getter ) : $coupon->{ $prop };
	}
}
