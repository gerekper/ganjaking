<?php
/**
 * Simple Store Credit product
 *
 * @package WC_Store_Credit/Products
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Simple Store Credit product class.
 */
class WC_Store_Credit_Product extends WC_Product_Simple {

	/**
	 * Constructor.
	 *
	 * @since 3.2.0
	 *
	 * @param WC_Product|int $product Product instance or ID.
	 */
	public function __construct( $product = 0 ) {
		parent::__construct( $product );

		$this->set_virtual( true );
	}

	/**
	 * Get internal type.
	 *
	 * @since 3.2.0
	 *
	 * @return string
	 */
	public function get_type() {
		return 'store_credit';
	}

	/**
	 * Gets whether the product can be purchased.
	 *
	 * @since 4.2.2
	 *
	 * @return bool
	 */
	public function is_in_stock() {
		$in_stock = parent::is_in_stock();

		// The credit amount is always zero in this case.
		if ( $in_stock && 0 >= $this->get_credit_amount() && ! $this->allow_custom_amount() ) {
			$in_stock = false;
		}

		return $in_stock;
	}

	/**
	 * Gets the Store Credit amount.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_credit_amount() {
		$data = $this->get_meta( '_store_credit_data' );

		return ( ! empty( $data['amount'] ) ? $data['amount'] : $this->get_regular_price() );
	}

	/**
	 * Gets if the Store Credit product allows sending the credit to a different person.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function allow_different_receiver() {
		$data = $this->get_meta( '_store_credit_data' );

		return ( empty( $data['allow_different_receiver'] ) || wc_string_to_bool( $data['allow_different_receiver'] ) );
	}

	/**
	 * Gets if the Store Credit product allows setting a custom amount.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function allow_custom_amount() {
		$data = $this->get_meta( '_store_credit_data' );

		return ( isset( $data['allow_custom_amount'] ) && wc_string_to_bool( $data['allow_custom_amount'] ) );
	}

	/**
	 * Gets the minimum custom amount allowed.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_min_custom_amount() {
		$data = $this->get_meta( '_store_credit_data' );

		return ( isset( $data['min_custom_amount'] ) ? wc_format_decimal( $data['min_custom_amount'] ) : '' );
	}

	/**
	 * Gets the maximum custom amount allowed.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_max_custom_amount() {
		$data = $this->get_meta( '_store_credit_data' );

		return ( isset( $data['max_custom_amount'] ) ? wc_format_decimal( $data['max_custom_amount'] ) : '' );
	}

	/**
	 * Gets the custom amount step.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	public function get_custom_amount_step() {
		$data = $this->get_meta( '_store_credit_data' );

		return ( isset( $data['custom_amount_step'] ) ? wc_format_decimal( $data['custom_amount_step'] ) : '' );
	}

	/**
	 * Gets the preset amounts.
	 *
	 * @since 4.5.0
	 *
	 * @return array
	 */
	public function get_preset_amounts() {
		$data = $this->get_meta( '_store_credit_data' );

		return ( ! empty( $data['preset_amounts'] ) ? explode( '|', $data['preset_amounts'] ) : array() );
	}
}
