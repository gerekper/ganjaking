<?php
/**
 * Class file for deposits integration.
 *
 * @package WooCommerce/Bookings
 */

/**
 * Deposits integration class.
 */
class WC_Bookings_Deposits {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_order_status_on-hold_to_partial-payment', array( $this, 'handle_on_hold_to_partial_payment' ), 20, 2 );
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'handle_partial_payment' ), 20, 2 );
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'handle_completed_payment' ), 40, 2 );
		add_action( 'init', array( $this, 'register_custom_post_status' ) );
		add_filter( 'woocommerce_bookings_get_wc_booking_statuses', array( $this, 'add_custom_status' ) );
		add_filter( 'woocommerce_bookings_get_status_label', array( $this, 'add_custom_status' ) );
		add_filter( 'woocommerce_booking_is_paid_statuses', array( $this, 'add_custom_paid_status' ) );
		add_action( 'woocommerce_payment_complete', array( $this, 'save_order_status' ) );
	}

	/**
	 * Process partial payments for on hold status.
	 *
	 * @since 1.15.12
	 *
	 * @param integer $order_id to state which order we're working with.
	 * @param object  $order we are working with.
	 */
	public function handle_on_hold_to_partial_payment( $order_id, $order ) {
		$this->handle_partial_payment( $order->get_status(), $order_id );
	}

	/**
	 * Process partial payments
	 *
	 * @since 1.11.0
	 *
	 * @param string  $order_status to be changed for filter.
	 * @param integer $order_id to state which order we're working with.
	 */
	public function handle_partial_payment( $order_status, $order_id ) {
		// Deposits order status support.
		if ( 'partial-payment' === $order_status ) {
			$this->set_status_for_bookings_in_order( $order_id, 'wc-partial-payment' );
		}

		return $order_status;
	}

	/**
	 * Go through all booking for an order and update the status for each.
	 *
	 * @since 1.11.0
	 *
	 * @param integer $order_id To find bookings.
	 * @param string  $new_status To set to bookings of order.
	 */
	public function set_status_for_bookings_in_order( $order_id, $new_status ) {
		$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order_id );

		foreach ( $booking_ids as $booking_id ) {
			$booking = new WC_Booking( $booking_id );
			$booking->set_status( $new_status );
			$booking->save();
		}
	}

	/**
	 * Process partial payments.
	 *
	 * @since 1.11.0
	 *
	 * @param string  $order_status To filter/change.
	 * @param integer $order_id To which this applies.
	 */
	public function handle_completed_payment( $order_status, $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return $order_status;
		}
		if ( 'processing' !== $order_status
			|| ! $order->has_status( 'pending-deposit' ) ) {
			return $order_status;
		}

		if ( count( $order->get_items() ) < 1 ) {
			return $order_status;
		}
		$virtual_booking_order = false;

		foreach ( $order->get_items() as $item ) {
			if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
				if ( 'line_item' === $item['type'] ) {
					$product               = $order->get_product_from_item( $item );
					$virtual_booking_order = $product && $product->is_virtual() && $product->is_type( 'booking' );
				}
			} else {
				if ( $item->is_type( 'line_item' ) ) {
					$product               = $item->get_product();
					$virtual_booking_order = $product && $product->is_virtual() && $product->is_type( 'booking' );
				}
			}
			if ( ! $virtual_booking_order ) {
				break;
			}
		}

		// Virtual order, mark as completed.
		if ( $virtual_booking_order ) {
			return 'completed';
		}

		return $order_status;
	}

	/**
	 * Register the Deposits integration post status.
	 *
	 * @since 1.11.0
	 */
	public function register_custom_post_status() {
		if ( is_admin() && isset( $_GET['post_type'] ) && 'wc_booking' === $_GET['post_type'] ) {
			register_post_status( 'wc-partial-payment', array(
				'label'                     => '<span class="status-partial-payment tips" data-tip="' . wc_sanitize_tooltip( _x( 'Partially Paid', 'woocommerce-bookings', 'woocommerce-bookings' ) ) . '">' . _x( 'Partially Paid', 'woocommerce-bookings', 'woocommerce-bookings' ) . '</span>',
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: 1: count, 2: count */
				'label_count'               => _n_noop( 'Partially Paid <span class="count">(%s)</span>', 'Partially Paid <span class="count">(%s)</span>', 'woocommerce-bookings' ),
			) );
		}
	}

	/**
	 * Add custom status to the list of standard bookings status.
	 *
	 * @since 1.11.0
	 *
	 * @param array $statuses to be changed in this function.
	 */
	public function add_custom_status( $statuses ) {
		$statuses['wc-partial-payment'] = __( 'Partially Paid','woocommerce-bookings' );
		return $statuses;
	}

	/**
	 * Make martial payment count as paid so items are added to Google calendar.
	 *
	 * @param array $statuses Current paid statuses.
	 *
	 * @return array
	 */
	public function add_custom_paid_status( $statuses ) {
		$statuses[] = 'wc-partial-payment';
		return $statuses;
	}

	/**
	 * Saves the order status from pending to wc-partial-payment
	 * so that the reminder cron job can pick it up.
	 *
	 * @see https://github.com/woocommerce/woocommerce-bookings/issues/2379
	 * @since 1.15.14
	 * @return void
	 */
	public function save_order_status( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( 'partial-payment' === $order->get_status() ) {
			$this->set_status_for_bookings_in_order( $order_id, 'wc-partial-payment' );
		}
	}
}
