<?php
/**
 * Collection of WC_Store_Credit_Coupon_Discount objects.
 *
 * @package WC_Store_Credit/Coupon Discounts
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Coupon_Discounts class.
 */
class WC_Store_Credit_Coupon_Discounts {

	/**
	 * Discounts per coupon.
	 *
	 * An array of `WC_Store_Credit_Coupon_Discount` objects.
	 *
	 * @var array
	 */
	protected $coupon_discounts = array();

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $coupon_discounts Optional. Initialize with the specified discounts.
	 */
	public function __construct( $coupon_discounts = array() ) {
		$this->add( $coupon_discounts );
	}

	/**
	 * Gets all the coupon discounts.
	 *
	 * @since 3.0.0
	 *
	 * @return array An array of `WC_Store_Credit_Coupon_Discount` objects.
	 */
	public function all() {
		return $this->coupon_discounts;
	}

	/**
	 * Adds 'Coupon discount' objects to the list.
	 *
	 * @since 3.0.0
	 *
	 * @throws InvalidArgumentException If the argument is not a `WC_Store_Credit_Coupon_Discount` object or an array of them.
	 *
	 * @param mixed $coupon_discounts A 'Coupon discount' object or an array of them.
	 */
	public function add( $coupon_discounts ) {
		if ( ! is_array( $coupon_discounts ) ) {
			$coupon_discounts = array( $coupon_discounts );
		}

		foreach ( $coupon_discounts as $coupon_discount ) {
			$this->add_coupon_discount( $coupon_discount );
		}
	}

	/**
	 * Gets the coupon discount object.
	 *
	 * @since 3.0.0
	 *
	 * @param string $coupon_code Coupon code.
	 * @return WC_Store_Credit_Coupon_Discount|false The coupon discount object. False if not found.
	 */
	public function get( $coupon_code ) {
		return ( isset( $this->coupon_discounts[ $coupon_code ] ) ? $this->coupon_discounts[ $coupon_code ] : false );
	}

	/**
	 * Gets if there are coupons in the collection with the specified option enabled.
	 *
	 * @since 3.0.0
	 *
	 * @param string $option The option to check. Accepts 'inc_tax', 'apply_to_shipping'.
	 * @return bool
	 */
	public function has_with( $option ) {
		if ( ! in_array( $option, array( 'inc_tax', 'apply_to_shipping' ), true ) ) {
			return false;
		}

		$has_with = false;

		foreach ( $this->all() as $coupon_discount ) {
			if ( call_user_func( array( $coupon_discount, $option ) ) ) {
				$has_with = true;
				break;
			}
		}

		return $has_with;
	}

	/**
	 * Gets the discounts per coupon for the specified type.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type The discount type.
	 * @return array
	 */
	public function get_discounts_by_type( $type ) {
		$discounts = array();

		foreach ( $this->coupon_discounts as $coupon_code => $coupon_discount ) {
			$discounts[ $coupon_code ] = $coupon_discount->get_discounts_by_type( $type );
		}

		return array_filter( $discounts );
	}

	/**
	 * Gets the total discount per coupon.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type Optional. The discount type. Default 'coupon'.
	 * @return array An array with pairs [coupon_code => total_discount]
	 */
	public function get_total_discounts( $type = 'coupon' ) {
		if ( 'taxes' === $type ) {
			$type = 'tax';
		}

		return array_map( 'array_sum', $this->get_discounts_by_type( $type ) );
	}

	/**
	 * Gets the total discount.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type Optional. The discount type. Default 'coupon'.
	 * @return float
	 */
	public function get_total_discount( $type = 'coupon' ) {
		return array_sum( $this->get_total_discounts( $type ) );
	}

	/**
	 * Adds a coupon discount to the list.
	 *
	 * @since 3.0.0
	 *
	 * @throws InvalidArgumentException If the argument is not a `WC_Store_Credit_Coupon_Discount` object.
	 *
	 * @param WC_Store_Credit_Coupon_Discount $coupon_discount Coupon discount object.
	 */
	protected function add_coupon_discount( $coupon_discount ) {
		if ( ! $coupon_discount instanceof WC_Store_Credit_Coupon_Discount ) {
			/* translators: %s: class name */
			throw new InvalidArgumentException( sprintf( esc_html_x( 'The argument must be an instance of the class %s.', 'exception message', 'woocommerce-store-credit' ), 'WC_Store_Credit_Coupon_Discount' ) );
		}

		$coupon_code = $coupon_discount->get_coupon()->get_code();

		$this->coupon_discounts[ $coupon_code ] = $coupon_discount;
	}
}
