<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Scheduled_Order_Manager class.
 *
 * Handles scheduled orders, e.g. emailing users when they are due for payment.
 */
class WC_Deposits_Scheduled_Order_Manager {

	/** @var object Class Instance */
	private static $instance;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_invoice_scheduled_orders', array( __CLASS__, 'invoice_scheduled_orders' ) );
		add_action( 'wp_trash_post', array( __CLASS__, 'trash_post' ) );
		add_action( 'untrash_post', array( __CLASS__, 'untrash_post' ) );
		add_action( 'before_delete_post', array( __CLASS__, 'before_delete_post' ) );

		// Ensure WooCommerce treats 'pending-deposit' as 'pending'.
		add_filter( 'woocommerce_order_has_status', array( __CLASS__, 'deposit_pending_status' ), 10, 3 );
	}

	/**
	 * Schedule all orders for a payment plan.
	 * This is important because the tax point is when the order is placed.
	 *
	 * @param  WC_Deposits_Plan $payment_plan
	 * @param  int $original_order_id
	 */
	public static function schedule_orders_for_plan( $payment_plan, $original_order_id, $item ) {
		$schedule          = $payment_plan->get_schedule();
		$current_timestamp = current_time( 'timestamp' );
		$payment_number    = 2;
		$line_price        = self::_get_normalized_price_before_plan( $payment_plan, $item );

		// Skip first payment - that was taken already
		$first_payment = array_shift( $schedule );
		$percent_remaining = $payment_plan->get_total_percent() - $first_payment->amount; // this is a percent, e.g. 100 - 25 = 75

		// Enforce sanity
		if ( $percent_remaining <= 0 ) {
			$original_order = wc_get_order( $original_order_id );
			$original_order->add_order_note( __( 'Error: Unable to schedule orders for product with payment plan. Reason: Already fully paid.', 'woocommerce-deposits' ) );
		}

		foreach ( $schedule as $schedule_row ) {
			// Work out relative timestamp
			$current_timestamp = strtotime( "+{$schedule_row->interval_amount} {$schedule_row->interval_unit}", $current_timestamp );

			// Work out how much the payment will be for
			// Note: $schedule_row->amount is a percent (e.g. 25)
			$item['subtotal'] = ( $line_price / 100 ) * $schedule_row->amount; // prior to any discounts
			$row_discount = round( ( $item['deposit_deferred_discount_ex_tax'] / $percent_remaining ) * $schedule_row->amount, 2 );
			$item['total'] = $item['subtotal'] - $row_discount;

			// Create order
			WC_Deposits_Order_Manager::create_order( $current_timestamp, $original_order_id, $payment_number, $item, 'scheduled-payment' );
			$payment_number++;
		}
	}

	/**
	 * Get normalized price before plan.
	 *
	 * The price_excluding_tax in order item is calculated with total percents
	 * from payment plan. This method normalize the price again.
	 *
	 * @param WC_Deposits_Plan $plan Plan
	 * @param array            $item Order item
	 *
	 * @return float Line price
	 */
	private static function _get_normalized_price_before_plan( $plan, $item ) {
		$total_percent = $plan->get_total_percent();

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$price_excluding_tax = $item['product']->get_price_excluding_tax( $item['qty'] );
		} else {
			$price_excluding_tax = wc_get_price_excluding_tax( $item['product'], array( 'qty' => $item['qty'] ) );
		}

		$price_after_plan = ! empty( $item['price_excluding_tax'] ) ? $item['price_excluding_tax'] : $price_excluding_tax;
		$line_price       = ( $price_after_plan * 100 ) / $total_percent;

		return $line_price;
	}

	/**
	 * Send an invoice for a scheduled order when the post date passes the current date.
	 *
	 * @version 1.3.2
	 * @since 1.0.0
	 */
	public static function invoice_scheduled_orders() {
		$mailer = WC_Emails::instance();
		$date   = date( "Y-m-d H:i:s", current_time( 'timestamp' ) );

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			global $wpdb;
			$due_orders = $wpdb->get_col( $wpdb->prepare( "
				SELECT 	posts.ID
				FROM 	{$wpdb->posts} AS posts
				WHERE 	posts.post_type = 'shop_order'
				AND 	posts.post_status = 'wc-scheduled-payment'
				AND 	posts.post_date < %s
			", $date ) );
		} else {
			$due_orders = wc_get_orders( array( 'date_before' => $date, 'status' => 'wc-scheduled-payment', 'return' => 'ids' ) );
		}

		if ( $due_orders ) {
			foreach ( $due_orders as $due_order ) {
				$order = wc_get_order( $due_order );
				$order->update_status( 'pending-deposit', __( 'Scheduled order ready for payment.', 'woocommerce-deposits' ) );
				$mailer->customer_invoice( $order );
			}
		}

	}

	/**
	 * Hook into WC_Order::has_status to make the custom order statuses that Deposits creates behave predictably.
	 * For example, an order with "pending-deposit" status should behave the same as an order with "pending" status.
	 * @since 1.3.1
	 * @version 1.4.14
	 *
	 * @param bool $retval
	 * @param WC_Order $order
	 * @param string|array $status_list
	 *
	 * @return bool
	 */
	public static function deposit_pending_status( $retval, $order, $status_list ) {
		if ( ! is_array( $status_list ) ) {
			$status_list = array( $status_list );
		}
		$order_status = $order->get_status();
		$status_map = array(
			'partial-payment'   => 'completed',
			'scheduled-payment' => 'pending',
			'pending-deposit'   => 'pending',
		);
		return isset( $status_map[ $order_status ] ) ? in_array( $status_map[ $order_status ], $status_list ) : $retval;
	}

	/**
	 * Get related orders created by deposits for an order ID.
	 *
	 * @param  int $order_id
	 * @return array
	 */
	public static function get_related_orders( $order_id ) {
		$order_ids    = array();
		$found_orders = wc_get_orders( array(
			'parent'         => $order_id,
			'posts_per_page' => -1,
		) );

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			foreach ( $found_orders as $found_order ) {
				if ( 'wc_deposits' === $found_order->created_via ) {
					$order_ids[] = $found_order->id;
				}
			}
		} else {
			foreach ( $found_orders as $found_order ) {
				if ( is_a( $found_order, 'WC_Order_Refund' ) ) {
					continue;
				}
				if ( 'wc_deposits' === $found_order->get_created_via() ) {
					$order_ids[] = $found_order->get_id();
				}
			}
		}

		return $order_ids;
	}

	/**
	 * When a post is trashed, if its an order, sync scheduled payments.
	 *
	 * @param int $id
	 */
	public static function trash_post( $id ) {
		if ( in_array( get_post_type( $id ), wc_get_order_types() ) ) {
			foreach ( self::get_related_orders( $id ) as $order_id ) {
				wp_trash_post( $order_id );
			}
		}
	}

	/**
	 * When a post is untrashed, if its an order, sync scheduled payments.
	 *
	 * @param int $id
	 */
	public static function untrash_post( $id ) {
		if ( in_array( get_post_type( $id ), wc_get_order_types() ) ) {
			foreach ( self::get_related_orders( $id ) as $order_id ) {
				wp_untrash_post( $order_id );
			}
		}
	}

	/**
	 * When a post is deleted, if its an order, sync scheduled payments.
	 *
	 * @param int $id
	 */
	public static function before_delete_post( $id ) {
		if ( in_array( get_post_type( $id ), wc_get_order_types() ) ) {
			foreach ( self::get_related_orders( $id ) as $order_id ) {
				wp_delete_post( $order_id, true );
			}
		}
	}
}

WC_Deposits_Scheduled_Order_Manager::get_instance();
