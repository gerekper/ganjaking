<?php
/**
 * Class to handle the discounts of a store credit coupon.
 *
 * @package WC_Store_Credit/Coupon Discounts
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Coupon_Discount class.
 */
class WC_Store_Credit_Coupon_Discount {

	/**
	 * Coupon object.
	 *
	 * @var WC_Coupon
	 */
	protected $coupon;

	/**
	 * The coupon amount includes taxes.
	 *
	 * @var bool
	 */
	protected $inc_tax;

	/**
	 * The coupon can be applied to the shipping costs.
	 *
	 * @var bool
	 */
	protected $apply_to_shipping;

	/**
	 * Item discounts.
	 *
	 * @var WC_Store_Credit_Item_Discounts
	 */
	protected $item_discounts;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @throws Exception If the coupon or the item discounts are not valid.
	 *
	 * @param mixed $the_coupon     Coupon object, ID or code.
	 * @param mixed $item_discounts Optional. Item discounts.
	 */
	public function __construct( $the_coupon, $item_discounts = array() ) {
		$coupon = wc_store_credit_get_coupon( $the_coupon );

		if ( false === $coupon ) {
			throw new Exception( esc_html_x( 'Invalid coupon.', 'exception message', 'woocommerce-store-credit' ) );
		}

		if ( is_array( $item_discounts ) ) {
			$item_discounts = new WC_Store_Credit_Item_Discounts( $item_discounts );
		}

		if ( ! $item_discounts instanceof WC_Store_Credit_Item_Discounts ) {
			throw new Exception( esc_html_x( 'Invalid item discounts.', 'exception message', 'woocommerce-store-credit' ) );
		}

		$this->coupon         = $coupon;
		$this->item_discounts = $item_discounts;
	}

	/**
	 * Gets the coupon object.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Coupon
	 */
	public function get_coupon() {
		return $this->coupon;
	}

	/**
	 * Gets if the coupon amount includes taxes.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function inc_tax() {
		if ( is_null( $this->inc_tax ) ) {
			$this->inc_tax = wc_store_credit_coupon_include_tax( $this->get_coupon() );
		}

		return $this->inc_tax;
	}

	/**
	 * Gets if the coupon can be applied to the shipping costs.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function apply_to_shipping() {
		if ( is_null( $this->apply_to_shipping ) ) {
			$this->apply_to_shipping = wc_store_credit_coupon_apply_to_shipping( $this->get_coupon() );
		}

		return $this->apply_to_shipping;
	}

	/**
	 * Gets the item discounts.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Store_Credit_Item_Discounts
	 */
	public function item_discounts() {
		return $this->item_discounts;
	}

	/**
	 * Gets the discounts applied by the coupon.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_discounts() {
		return $this->item_discounts()->get_total_discounts();
	}

	/**
	 * Gets a coupon discount.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key The discount key.
	 * @return float|false The discount amount. False if not found.
	 */
	public function get_discount( $key ) {
		return $this->item_discounts()->get_total_discount( $key );
	}

	/**
	 * Gets the discount keys.
	 *
	 * Use the type `coupon` to returns the applicable discounts to the coupon.
	 *
	 * @since 3.0.0
	 *
	 * @see wc_store_credit_discount_type_keys
	 *
	 * @param string $type The discount type.
	 * @return array
	 */
	public function get_discount_type_keys( $type ) {
		if ( 'coupon' !== $type ) {
			$keys = wc_store_credit_discount_type_keys( $type );
		} else {
			// Return the applicable discounts for the coupon.
			$keys = array( 'cart' );

			if ( $this->inc_tax() ) {
				$keys[] = 'cart_tax';
			}

			if ( $this->apply_to_shipping() ) {
				$keys[] = 'shipping';

				if ( $this->inc_tax() ) {
					$keys[] = 'shipping_tax';
				}
			}

			/**
			 * Filters the discount keys for the coupon.
			 *
			 * @since 3.0.0
			 *
			 * @param array     $keys   The discount keys.
			 * @param WC_Coupon $coupon Coupon object.
			 */
			$keys = apply_filters( 'wc_store_credit_coupon_discount_keys', $keys, $this->get_coupon() );
		}

		return $keys;
	}

	/**
	 * Gets the coupon discounts by type.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type The discount type.
	 * @return array
	 */
	public function get_discounts_by_type( $type ) {
		$discounts = $this->get_discounts();
		$type_keys = $this->get_discount_type_keys( $type );

		if ( ! empty( $type_keys ) ) {
			$discounts = array_intersect_key( $discounts, array_flip( $type_keys ) );
		} else {
			$discounts = array();
		}

		return $discounts;
	}

	/**
	 * Gets the remaining credit for the coupon.
	 *
	 * @since 3.0.0
	 *
	 * @param float $credit Optional. Overwrite coupon credit.
	 * @return float
	 */
	public function get_remaining_credit( $credit = null ) {
		$discounts = $this->get_discounts_by_type( 'coupon' );

		if ( is_null( $credit ) ) {
			$credit = $this->get_coupon()->get_amount();
		}

		return ( $credit - array_sum( $discounts ) );
	}
}
