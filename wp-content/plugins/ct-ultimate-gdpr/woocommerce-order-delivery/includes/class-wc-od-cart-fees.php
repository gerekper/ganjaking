<?php
/**
 * Class to handle the cart fees.
 *
 * @package WC_OD
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cart fees class.
 */
class WC_OD_Cart_Fees {

	/**
	 * Cart object.
	 *
	 * @var WC_Cart
	 */
	protected $cart;

	/**
	 * An array of fee objects.
	 *
	 * @var array
	 */
	private $fees = array();

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Cart $cart Cart object.
	 */
	public function __construct( $cart ) {
		$this->cart = $cart;
	}

	/**
	 * Gets the fees to add to the cart.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_fees() {
		return $this->fees;
	}

	/**
	 * Adds the fees to the cart.
	 *
	 * @since 2.0.0
	 */
	public function add_fees() {
		if ( empty( $this->fees ) ) {
			return;
		}

		$fees_api = $this->cart->fees_api();

		array_map( array( $fees_api, 'add_fee' ), $this->fees );
	}

	/**
	 * Calculates the fees to add to the cart.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args An array with arguments.
	 */
	public function calculate_fees( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'delivery_date' => '',
				'time_frame'    => '',
			)
		);

		$fees = array();

		if ( $args['delivery_date'] ) {
			$fees[] = $this->get_fee_for_date( $args['delivery_date'] );
		}

		if ( $args['time_frame'] ) {
			$fees[] = $this->get_fee_for_time_frame( $args['time_frame'] );
		}

		// Remove falsy values and re-index.
		$fees = array_values( array_filter( $fees ) );

		/**
		 * Filters the fees to add to the cart.
		 *
		 * @since 2.0.0
		 *
		 * @param array   $fees An array with the fees' data.
		 * @param array   $args An array with arguments.
		 * @param WC_Cart $cart Cart object.
		 */
		$this->fees = apply_filters( 'wc_od_cart_fees', $fees, $args, $this->cart );
	}

	/**
	 * Gets the fee for the specified delivery date.
	 *
	 * @since 2.0.0
	 *
	 * @param string $date The delivery date.
	 * @return array|false
	 */
	protected function get_fee_for_date( $date ) {
		$day_id = wc_od_localize_date( $date, 'w' );

		if ( ! $day_id ) {
			return false;
		}

		$delivery_day = wc_od_get_delivery_day( $day_id );

		// The fee is handled in the time frames.
		if ( $delivery_day->has_time_frames() ) {
			return false;
		}

		$fee_data = $this->get_fee_data( $delivery_day, 'delivery_date_fee' );

		return ( ! empty( $fee_data ) ? $fee_data : false );
	}

	/**
	 * Gets the fee for the specified time frame.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $time_frame Time frame object, ID, or an array with data.
	 * @return array|false
	 */
	protected function get_fee_for_time_frame( $time_frame ) {
		$time_frame = wc_od_get_time_frame( $time_frame );

		if ( ! $time_frame ) {
			return false;
		}

		$fee_data = $this->get_fee_data( $time_frame, 'delivery_time_frame_fee' );

		return ( ! empty( $fee_data ) ? $fee_data : false );
	}

	/**
	 * Gets the fee data for the specified data object.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_OD_Data $object Data object.
	 * @param string     $id     Optional. The fee ID. Default empty.
	 * @return array
	 */
	protected function get_fee_data( $object, $id = '' ) {
		if ( ! method_exists( $object, 'get_fee_amount' ) || 0 >= $object->get_fee_amount() ) {
			return array();
		}

		$fee_label = $object->get_fee_label();

		return array(
			'id'        => $id,
			'amount'    => $object->get_fee_amount(),
			'name'      => ( $fee_label ? $fee_label : __( 'Delivery fee', 'woocommerce-order-delivery' ) ),
			'taxable'   => ( 'taxable' === $object->get_fee_tax_status() ),
			'tax_class' => $object->get_fee_tax_class(),
		);
	}
}
