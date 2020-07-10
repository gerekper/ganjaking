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
		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_coupon_valid' ), 10, 2 );
		if ( version_compare( WC_VERSION, '3.4.0', '<' ) ) {
			add_filter( 'woocommerce_get_discounted_price', array( $this, 'apply_discount' ), 10, 3 );
		} else {
			add_filter( 'woocommerce_coupon_custom_discounts_array', array( $this, 'coupon_custom_discounts' ), 10, 5 );
		}
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
	 * Looks through our cart to see if we actually have a booking product
	 * before applying our coupon.
	 *
	 * @param bool      $is_valid  Whether a given coupon is valid
	 * @param WC_Coupon $wc_coupon Coupon object
	 *
	 * @return bool Returns true if coupon is valid
	 */
	public function is_coupon_valid( $is_valid, $wc_coupon ) {
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

		return false;
	}

	/**
	 * Applies the discount to the cart.
	 *
	 * @param mixed   $original_price Original price
	 * @param array   $cart_item      Cart item
	 * @param WC_Cart $cart           Cart object
	 *
	 * @return float Discounted price
	 */
	public function apply_discount( $original_price, $cart_item, $cart ) {
		$product_id = is_callable( array( $cart_item['data'], 'get_id' ) ) ? $cart_item['data']->get_id() : $cart_item['data']->id;
		$product    = wc_get_product( $product_id );

		if ( ! $product || ! $product->is_type( 'booking' ) ) {
			return $original_price;
		}

		$price = $original_price;
		if ( ! empty( WC()->cart->applied_coupons ) ) {
			foreach ( WC()->cart->applied_coupons as $code ) {
				$coupon = new WC_Coupon( $code );
				if ( $coupon->is_valid() ) {

					if ( in_array( $cart_item['booking']['_booking_id'], $this->already_applied ) ) {
						continue;
					}

					if ( 'booking_person' !== self::get_coupon_prop( $coupon, 'discount_type' ) ) {
						continue;
					}

					$discount_amount = ( $price < self::get_coupon_prop( $coupon, 'amount' ) ) ? $price : self::get_coupon_prop( $coupon, 'amount' );
					$total_persons   = array_sum( $cart_item['booking']['_persons'] );
					$discount_amount = $discount_amount * $total_persons;

					$price = $price - $discount_amount;
					if ( $price < 0 ) {
						$price = 0;
					}

					if ( empty( $this->amounts[ $code ] ) ) {
						$this->amounts[ $code ] = 0;
					}
					$this->amounts[ $code ] += $discount_amount;

					WC()->cart->discount_cart = WC()->cart->discount_cart + $discount_amount;
					$this->already_applied[]  = $cart_item['booking']['_booking_id'];
				}
			}
		}

		return $price;
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
	 * Get coupon discount amount
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
