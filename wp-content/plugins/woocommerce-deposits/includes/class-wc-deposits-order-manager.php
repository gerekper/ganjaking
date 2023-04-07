<?php
/**
 * Deposits order manager
 *
 * @package woocommerce-deposits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Order_Manager class.
 */
class WC_Deposits_Order_Manager {

	/**
	 * Class Instance
	 *
	 * @var WC_Deposits_Order_Manager
	 */
	private static $instance;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_status' ), 9 );
		add_filter( 'wc_order_statuses', array( $this, 'add_order_statuses' ) );
		add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', array( $this, 'woocommerce_valid_order_statuses_for_payment_complete' ) );
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'woocommerce_payment_complete_order_status' ), 10, 2 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'process_deposits_in_order' ), 10, 1 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'process_deposits_in_order' ), 10, 1 );
		add_action( 'woocommerce_order_status_on-hold', array( $this, 'process_deposits_in_order' ), 10, 1 );
		add_action( 'woocommerce_order_status_partial-payment', array( $this, 'process_deposits_in_order' ), 10, 1 );
		add_filter( 'woocommerce_attribute_label', array( $this, 'woocommerce_attribute_label' ), 10, 2 );
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'woocommerce_hidden_order_itemmeta' ) );
		add_action( 'woocommerce_before_order_itemmeta', array( $this, 'woocommerce_before_order_itemmeta' ), 10, 3 );
		add_action( 'woocommerce_after_order_itemmeta', array( $this, 'woocommerce_after_order_itemmeta' ), 10, 3 );
		add_action( 'admin_init', array( $this, 'order_action_handler' ) );
		add_action( 'woocommerce_payment_complete', array( $this, 'payment_complete_handler' ) );

		// View orders.
		add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'woocommerce_my_account_my_orders_query' ) );
		add_filter( 'woocommerce_order_item_name', array( $this, 'woocommerce_order_item_name' ), 10, 2 );
		add_action( 'woocommerce_order_item_meta_end', array( $this, 'woocommerce_order_item_meta_end' ), 10, 3 );
		add_filter( 'woocommerce_get_order_item_totals', array( $this, 'woocommerce_get_order_item_totals' ), 10, 2 );
		add_filter( 'request', array( $this, 'request_query' ) );
		add_action( 'woocommerce_ajax_add_order_item_meta', array( $this, 'ajax_add_order_item_meta' ), 10, 2 );
		add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'display_item_total_payable' ), 10, 3 );

		// Add WC3.2 Coupons upgrade compatibility.
		add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'deposits_order_item_display_meta_value' ), 10, 2 );

		// Stock management.
		add_filter( 'woocommerce_payment_complete_reduce_order_stock', array( $this, 'allow_reduce_order_stock' ), 10, 2 );
		add_filter( 'woocommerce_can_reduce_order_stock', array( $this, 'allow_reduce_order_stock' ), 10, 2 );
		add_filter( 'woocommerce_prevent_adjust_line_item_product_stock', array( $this, 'prevent_adjust_line_item_product_stock' ), 10, 2 );

		// Downloads manager.
		add_filter( 'woocommerce_order_is_download_permitted', array( $this, 'maybe_alter_if_download_permitted' ), 20, 2 );

		// Add pending deposit payment to needs payment functions.
		add_filter( 'woocommerce_valid_order_statuses_for_payment', array( $this, 'add_status_to_needs_payment' ) );
		add_filter( 'woocommerce_valid_order_statuses_for_payment_complete', array( $this, 'add_status_to_needs_payment' ) );

		// Add partial payment as is paid status to WC.
		add_filter( 'woocommerce_order_is_paid_statuses', array( $this, 'add_is_paid_status' ) );

		// Filter stock status check.
		add_filter( 'woocommerce_product_is_in_stock', array( $this, 'maybe_bypass_stock_status' ), 10, 2 );
		add_filter( 'woocommerce_product_get_manage_stock', array( $this, 'maybe_bypass_manage_stock' ), 10, 2 );
		add_filter( 'woocommerce_product_variation_get_manage_stock', array( $this, 'maybe_bypass_manage_stock' ), 10, 2 );

		// Send WooCommerce emails on custom status transitions.
		add_filter( 'woocommerce_email_actions', array( $this, 'register_status_transitions_for_core_emails' ), 10, 1 );
		add_action( 'woocommerce_email', array( $this, 'send_core_emails_on_status_changes' ), 10, 1 );
	}

	/**
	 * Does the order contain a deposit?
	 *
	 * @version 1.2.1
	 * @param WC_Order|int $order Order.
	 * @return boolean
	 */
	public static function has_deposit( $order ) {
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order ) {
			return false;
		}

		foreach ( $order->get_items() as $item ) {
			if ( 'line_item' === $item['type'] && ! empty( $item['is_deposit'] ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if the order is follow up
	 *
	 * @param  int|WC_Order $order Order ID or object.
	 * @return boolean
	 */
	public static function is_follow_up_order( $order ) {
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order ) {
			return false;
		}

		$parent_order_id = $order->get_parent_id();

		$is_follow_up = $parent_order_id && self::has_deposit( $parent_order_id );

		/**
		 * Filter is follow up order.
		 *
		 * @param bool     $is_follow_up Flag order as Follow Up.
		 * @param WC_Order $order Order.
		 */
		return apply_filters( 'woocommerce_deposits_is_follow_up_order', $is_follow_up, $order );
	}

	/**
	 * Check if the order contains deposits that need future payments
	 *
	 * @param  int|WC_Order $order Order ID or object.
	 * @return boolean
	 */
	public static function order_has_future_deposit_payment( $order ) {
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order ) {
			return false;
		}

		foreach ( $order->get_items() as $item ) {
			if ( 'line_item' === $item['type'] && ! empty( $item['is_deposit'] ) ) {
				$deposit_full_amount       = (float) $item['_deposit_full_amount_ex_tax'];
				$deposit_deposit_amount    = (float) $item['_deposit_deposit_amount_ex_tax'];
				$deposit_deferred_discount = (float) $item['_deposit_deferred_discount'];
				if ( ( $deposit_full_amount - $deposit_deposit_amount ) > $deposit_deferred_discount ) {
					return true;
				}
			}
		}
			return false;
	}

	/**
	 * Check if the order contains a deposit without additional payments.
	 * E.g. discount was applied.
	 *
	 * @param  int|WC_Order $order Order ID or object.
	 * @return boolean
	 */
	public static function has_deposit_without_future_payments( $order ) {
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order ) {
			return false;
		}

		foreach ( $order->get_items() as $item ) {
			if ( 'line_item' === $item['type'] && ! empty( $item['is_deposit'] ) ) {
				$deposit_full_amount       = (int) $item['_deposit_full_amount_ex_tax'];
				$deposit_deposit_amount    = (int) $item['_deposit_deposit_amount_ex_tax'];
				$deposit_deferred_discount = (int) $item['_deposit_deferred_discount'];

				if ( $deposit_full_amount - $deposit_deposit_amount === $deposit_deferred_discount ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Register our custom post statuses, used for order status.
	 */
	public function register_post_status() {
		register_post_status(
			'wc-partial-payment',
			array(
				'label'                     => _x( 'Partially Paid', 'Order status', 'woocommerce-deposits' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: count label */
				'label_count'               => _n_noop( 'Partially Paid <span class="count">(%s)</span>', 'Partially Paid <span class="count">(%s)</span>', 'woocommerce-deposits' ),
			)
		);
		register_post_status(
			'wc-scheduled-payment',
			array(
				'label'                     => _x( 'Scheduled', 'Order status', 'woocommerce-deposits' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => true,
				/* translators: count label */
				'label_count'               => _n_noop( 'Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>', 'woocommerce-deposits' ),
			)
		);
		register_post_status(
			'wc-pending-deposit',
			array(
				'label'                     => _x( 'Pending Deposit Payment', 'Order status', 'woocommerce-deposits' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: count label */
				'label_count'               => _n_noop( 'Pending Deposit Payment <span class="count">(%s)</span>', 'Pending Deposits Payment <span class="count">(%s)</span>', 'woocommerce-deposits' ),
			)
		);
	}

	/**
	 * Add order statusus to WooCommmerce.
	 *
	 * @param  array $order_statuses Order statuses.
	 * @return array
	 */
	public function add_order_statuses( $order_statuses ) {
		$order_statuses['wc-partial-payment']   = _x( 'Partially Paid', 'Order status', 'woocommerce-deposits' );
		$order_statuses['wc-scheduled-payment'] = _x( 'Scheduled', 'Order status', 'woocommerce-deposits' );
		$order_statuses['wc-pending-deposit']   = _x( 'Pending Deposit Payment', 'Order status', 'woocommerce-deposits' );
		return $order_statuses;
	}

	/**
	 * Statuses that can be completed.
	 *
	 * @param  array $statuses Order statuses.
	 * @return array
	 */
	public function woocommerce_valid_order_statuses_for_payment_complete( $statuses ) {
		$statuses = array_merge( $statuses, array( 'partial-payment', 'scheduled-payment' ) );
		return $statuses;
	}

	/**
	 * Filters the order's status so that we can indicate if this order only has a partial payment
	 *
	 * @param string $status Order status (processing|completed).
	 * @param int    $order_id Order ID.
	 * @return string Order status (processing|completed|partial-payment)
	 */
	public function woocommerce_payment_complete_order_status( $status, $order_id ) {
		if ( empty( $order_id ) ) {
			// Not yet an order in the system - e.g. can happen during order creation when invoicing the remaining balance.
			return $status;
		}

		$order = wc_get_order( $order_id );

		// We want to skip status change for these (manual payment) methods because no payment actually occurred.
		$methods_skip_status = array(
			'bacs',
			'cheque',
			'cod',
		);

		$methods_skip_status = apply_filters( 'woocommerce_deposits_methods_skip_status', $methods_skip_status );

		if ( is_object( $order ) && self::has_deposit( $order ) && self::order_has_future_deposit_payment( $order )
			&& ! in_array( $order->get_payment_method(), $methods_skip_status, true ) ) {
			$status = 'partial-payment';
		}

		return $status;
	}

	/**
	 * Hide scheduled orders from account [age]
	 *
	 * @param array $query Query.
	 * @return array
	 */
	public function woocommerce_my_account_my_orders_query( $query ) {
		$statuses = wc_get_order_statuses();
		unset( $statuses['wc-scheduled-payment'] );
		$query['status'] = array_keys( $statuses );

		return $query;
	}

	/**
	 * Process deposits in an order after payment.
	 *
	 * @param int $order_id Order ID.
	 */
	public function process_deposits_in_order( $order_id ) {
		$order     = wc_get_order( $order_id );
		$parent_id = $order->get_parent_id();

		// Check if any items need scheduling.
		foreach ( $order->get_items() as $order_item_id => $item ) {
			if ( 'line_item' === $item['type'] && ! empty( $item['payment_plan'] ) && empty( $item['payment_plan_scheduled'] ) ) {
				$payment_plan             = new WC_Deposits_Plan( absint( $item['payment_plan'] ) );
				$deferred_discount        = ( empty( $item['deposit_deferred_discount'] ) ) ? 0 : $item['deposit_deferred_discount'];
				$deferred_discount_ex_tax = ( empty( $item['deposit_deferred_discount_ex_tax'] ) ) ? 0 : $item['deposit_deferred_discount_ex_tax'];
				WC_Deposits_Scheduled_Order_Manager::schedule_orders_for_plan(
					$payment_plan,
					$order_id,
					array(
						'product'                          => $item->get_product(),
						'qty'                              => $item['qty'],
						'price_excluding_tax'              => $item['deposit_full_amount_ex_tax'],
						'deposit_deferred_discount'        => $deferred_discount,
						'deposit_deferred_discount_ex_tax' => $deferred_discount_ex_tax,
					)
				);
				wc_add_order_item_meta( $order_item_id, '_payment_plan_scheduled', 'yes' );
			}
		}

		// Has parent? See if partially paid.
		if ( $parent_id ) {
			$parent_order = wc_get_order( $parent_id );
			if ( $parent_order && $parent_order->has_status( array( 'partial-payment', 'completed' ) ) ) {
				$paid = true;
				foreach ( $parent_order->get_items() as $order_item_id => $item ) {

					if ( WC_Deposits_Order_Item_Manager::is_deposit( $item )
						&& ! WC_Deposits_Order_Item_Manager::is_fully_paid( $item, $parent_order ) ) {

						$paid = false;
						break;

					}
				}

				if ( $paid ) {

					/**
					 * Filter to update the new status for parent after all deposits paid.
					 *
					 * When all deposits are paid by the customer, the linked parent
					 * order status changes to 'completed' by default. This filter
					 * allows users to change this behaviour and set their required
					 * status to the parent order after all deposits paid.
					 *
					 * @param string the status of the parent order.
					 *
					 * @since 1.5.9
					 */
					$parent_new_status = apply_filters( 'woocommerce_deposits_parent_status_on_payment', 'completed' );

					// Update the parent order.
					$parent_order->update_status( esc_html( $parent_new_status ), __( 'All deposit items fully paid', 'woocommerce-deposits' ) );
				}
			}
		}
	}

	/**
	 * Create a scheduled order.
	 *
	 * @param  string $payment_date Payment date.
	 * @param  int    $original_order_id Original order ID.
	 * @param  int    $payment_number Number of payment.
	 * @param  array  $item Order item.
	 * @param  string $status Status.
	 * @return id
	 */
	public static function create_order( $payment_date, $original_order_id, $payment_number, $item, $status = '' ) {
		$original_order = wc_get_order( $original_order_id );

		try {
			$new_order = new WC_Order();
			$new_order->set_props(
				array(
					'status'              => $status,
					'customer_id'         => $original_order->get_user_id(),
					'customer_note'       => $original_order->get_customer_note(),
					'currency'            => $original_order->get_currency(),
					'created_via'         => 'wc_deposits',
					'billing_first_name'  => $original_order->get_billing_first_name(),
					'billing_last_name'   => $original_order->get_billing_last_name(),
					'billing_company'     => $original_order->get_billing_company(),
					'billing_address_1'   => $original_order->get_billing_address_1(),
					'billing_address_2'   => $original_order->get_billing_address_2(),
					'billing_city'        => $original_order->get_billing_city(),
					'billing_state'       => $original_order->get_billing_state(),
					'billing_postcode'    => $original_order->get_billing_postcode(),
					'billing_country'     => $original_order->get_billing_country(),
					'billing_email'       => $original_order->get_billing_email(),
					'billing_phone'       => $original_order->get_billing_phone(),
					'shipping_first_name' => $original_order->get_shipping_first_name(),
					'shipping_last_name'  => $original_order->get_shipping_last_name(),
					'shipping_company'    => $original_order->get_shipping_company(),
					'shipping_address_1'  => $original_order->get_shipping_address_1(),
					'shipping_address_2'  => $original_order->get_shipping_address_2(),
					'shipping_city'       => $original_order->get_shipping_city(),
					'shipping_state'      => $original_order->get_shipping_state(),
					'shipping_postcode'   => $original_order->get_shipping_postcode(),
					'shipping_country'    => $original_order->get_shipping_country(),
				)
			);
			$new_order->save();
			if ( ! empty( $original_order->get_meta( '_vat_number' ) ) ) {
				$new_order->update_meta_data( '_vat_number', $original_order->get_meta( '_vat_number' ) );
			}

			/**
			 * Action hook to fire immediately after the new order props are set.
			 *
			 * @param WC_Order $new_order      The scheduled order object.
			 * @param WC_Order $original_order The original order object.
			 */
			do_action( 'woocommerce_deposits_after_scheduled_order_props_set', $new_order, $original_order );
		} catch ( Exception $e ) {
			/* translators: error message */
			$original_order->add_order_note( sprintf( __( 'Error: Unable to create follow up payment (%s)', 'woocommerce-deposits' ), $e->getMessage() ) );
			return;
		}

		// Handle items.
		$item_id = $new_order->add_product(
			$item['product'],
			$item['qty'],
			array(
				'totals' => array(
					'subtotal'     => $item['subtotal'], // cost before discount (for line quantity, not just unit).
					'total'        => $item['total'], // item cost (after discount) (for line quantity, not just unit).
					'subtotal_tax' => 0, // calculated within (WC_Abstract_Order) $new_order->calculate_totals.
					'tax'          => 0, // calculated within (WC_Abstract_Order) $new_order->calculate_totals.
				),
			)
		);

		$new_order->set_parent_id( $original_order_id );
		$new_order->set_date_created( date( 'Y-m-d H:i:s', $payment_date ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date

		// Add local pickup as shipping method for the follow-up order before totals calculation.
		if ( self::is_local_pickup( $original_order ) ) {
			$shipping_methods = $original_order->get_shipping_methods();

			/**
			 * Woocommerce Local Pickup method IDs
			 *
			 * @see WC_Customer::get_taxable_address()
			 */
			$local_pickup_methods = apply_filters( 'woocommerce_local_pickup_methods', array( 'legacy_local_pickup', 'local_pickup' ) );

			foreach ( $shipping_methods as $method ) {
				// Match the original order shipping method is Local Pickup.
				if ( in_array( $method->get_method_id(), $local_pickup_methods, true ) ) {

					$shipping_item = new WC_Order_Item_Shipping();
					$shipping_item->set_order_id( $new_order->get_id() );
					$shipping_item->set_method_title( $method->get_method_title( 'edit' ) );
					$shipping_item->set_method_id( $method->get_method_id() );
					// Set total to 0 to prevent duplicate charging of already paid local pickup.
					$shipping_item->set_total( 0 );

					// Add Local Pickup shipping item to the new order.
					$new_order->add_item( $shipping_item );

					// Follow up order only needs one local pickup method to calculate tax.
					break;
				}
			}
		}

		// (WC_Abstract_Order) Calculate totals by looking at the contents of the order. Stores the totals and returns the orders final total.
		$new_order->calculate_totals( wc_tax_enabled() );
		$new_order->save();

		wc_add_order_item_meta( $item_id, '_original_order_id', $original_order_id );

		/* translators: Payment number for product's title */
		wc_update_order_item( $item_id, array( 'order_item_name' => sprintf( __( 'Payment #%1$d for %2$s', 'woocommerce-deposits' ), $payment_number, $item['product']->get_title() ) ) );

		do_action( 'woocommerce_deposits_create_order', $new_order->get_id() );
		return $new_order->get_id();
	}

	/**
	 * Filter display value of order item
	 *
	 * @param string $display_value Original display value.
	 * @param object $meta Item meta.
	 * @return string
	 */
	public function deposits_order_item_display_meta_value( $display_value, $meta ) {
		$meta_key = $meta->key;
		switch ( $meta_key ) {
			case '_deposit_full_amount':
			case '_deposit_full_amount_ex_tax':
			case '_deposit_deferred_discount':
			case '_deposit_deferred_discount_ex_tax':
			case '_deposit_deposit_amount_ex_tax':
				$display_value = round( $display_value, wc_get_price_decimals() );
				break;
		}
		return $display_value;
	}

	/**
	 * Rename meta key for attribute labels.
	 *
	 * @param  string $label Original attribute label.
	 * @param  string $meta_key Meta key.
	 * @return string
	 */
	public function woocommerce_attribute_label( $label, $meta_key ) {
		switch ( $meta_key ) {
			case '_deposit_full_amount':
				$label = __( 'Full Amount', 'woocommerce-deposits' );
				break;
			case '_deposit_full_amount_ex_tax':
				$label = __( 'Full Amount (excl. tax)', 'woocommerce-deposits' );
				break;
			case '_deposit_deferred_discount':
				$label = __( 'Deferred Discount', 'woocommerce-deposits' );
				break;
			case '_deposit_deferred_discount_ex_tax':
				$label = __( 'Deferred Discount (excl. tax)', 'woocommerce-deposits' );
				break;
			case '_deposit_deposit_amount_ex_tax':
				$label = __( 'Deposit Amount (excl. tax)', 'woocommerce-deposits' );
				break;
		}
		return $label;
	}

	/**
	 * Hide meta data.
	 *
	 * @param array $meta_keys Meta keys to filter.
	 * @return array
	 */
	public function woocommerce_hidden_order_itemmeta( $meta_keys ) {
		$meta_keys[] = '_is_deposit';
		$meta_keys[] = '_remaining_balance_order_id';
		$meta_keys[] = '_remaining_balance_paid';
		$meta_keys[] = '_original_order_id';
		$meta_keys[] = '_payment_plan_scheduled';
		$meta_keys[] = '_payment_plan';
		return $meta_keys;
	}

	/**
	 * Show info before order item meta.
	 *
	 * @param int           $item_id Item ID.
	 * @param WC_Order_Item $item Item.
	 * @param WC_Product    $_product Product.
	 * @return void
	 */
	public function woocommerce_before_order_itemmeta( $item_id, $item, $_product ) {
		if ( ! WC_Deposits_Order_Item_Manager::is_deposit( $item ) ) {
			return;
		}

		$payment_plan = WC_Deposits_Order_Item_Manager::get_payment_plan( $item );

		if ( $payment_plan ) {
			echo ' (' . esc_html( $payment_plan->get_name() ) . ')';
		} else {
			echo ' (' . esc_html__( 'Deposit', 'woocommerce-deposits' ) . ')';
		}
	}

	/**
	 * Show info after order item meta.
	 *
	 * @param int           $item_id Order item ID.
	 * @param WC_Order_Item $item Order item.
	 * @param WC_Product    $_product Product.
	 * @return void
	 */
	public function woocommerce_after_order_itemmeta( $item_id, $item, $_product ) {
		if ( WC_Deposits_Order_Item_Manager::is_deposit( $item ) ) {

			$order_id     = $item->get_order_id();
			$order        = wc_get_order( $order_id );
			$currency     = $order->get_currency();
			$payment_plan = WC_Deposits_Order_Item_Manager::get_payment_plan( $item );

			// Plans.
			if ( $payment_plan ) {
				$scheduled_payments_url = WC_Deposits_COT_Compatibility::get_scheduled_payments_url( $order_id );
				echo '<a href="' . esc_url( $scheduled_payments_url ) . '" target="_blank" class="button button-small">' . esc_html__( 'View Scheduled Payments', 'woocommerce-deposits' ) . '</a>';
			} else {
				// Regular deposits.
				$remaining                  = $item['deposit_full_amount'] - $order->get_line_total( $item, true );
				$remaining_balance_order_id = ! empty( $item['remaining_balance_order_id'] ) ? absint( $item['remaining_balance_order_id'] ) : 0;
				$remaining_balance_paid     = ! empty( $item['remaining_balance_paid'] );

				$remaining_balance_order = wc_get_order( $remaining_balance_order_id );

				if ( $remaining_balance_order_id && $remaining_balance_order ) {
					/* translators: remaining balance order ID */
					echo '<a href="' . esc_url( $remaining_balance_order->get_edit_order_url() ) . '" target="_blank" class="button button-small">' . sprintf( esc_html__( 'Remainder - Invoice #%1$s', 'woocommerce-deposits' ), esc_html( $remaining_balance_order->get_order_number() ) ) . '</a>';
				} elseif ( $remaining_balance_paid ) {
					/* translators: offline paid amount */
					printf( esc_html__( 'The remaining balance of %s (plus tax) for this item was paid offline.', 'woocommerce-deposits' ), wc_price( $remaining, array( 'currency' => $currency ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo ' <a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'mark_deposit_unpaid' => $item_id ) ), 'mark_deposit_unpaid', 'mark_deposit_unpaid_nonce' ) ) . '" class="button button-small">' . sprintf( esc_html__( 'Unmark as Paid', 'woocommerce-deposits' ) ) . '</a>';
				} elseif ( ! self::has_deposit_without_future_payments( $order_id ) ) {
					$edit_order_url = $order->get_edit_order_url();
					?>
					<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'invoice_remaining_balance' => $item_id ), $edit_order_url ), 'invoice_remaining_balance', 'invoice_remaining_balance_nonce' ) ); ?>" class="button button-small"><?php esc_html_e( 'Invoice Remaining Balance', 'woocommerce-deposits' ); ?></a>
					<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'mark_deposit_fully_paid' => $item_id ), $edit_order_url ), 'mark_deposit_fully_paid', 'mark_deposit_fully_paid_nonce' ) ); ?>" class="button button-small"><?php esc_html_e( 'Mark Paid (offline)', 'woocommerce-deposits' ); ?></a>
					<?php
				}
			}
		} elseif ( ! empty( $item['original_order_id'] ) ) {
			$original_order     = wc_get_order( $item['original_order_id'] );
			$original_order_url = ( ! empty( $original_order ) ) ? $original_order->get_edit_order_url() : '';

			echo '<a href="' . esc_url( $original_order_url ) . '" target="_blank" class="button button-small">' . esc_html__( 'View Original Order', 'woocommerce-deposits' ) . '</a>';
		}
	}

	/**
	 * Create and redirect to an invoice.
	 */
	public function order_action_handler() {
		global $wpdb;

		$action  = false;
		$item_id = false;

		if ( ! empty( $_GET['mark_deposit_unpaid'] ) && isset( $_GET['mark_deposit_unpaid_nonce'] ) && check_admin_referer( 'mark_deposit_unpaid', 'mark_deposit_unpaid_nonce' ) ) {
			$action  = 'mark_deposit_unpaid';
			$item_id = absint( $_GET['mark_deposit_unpaid'] );
		}

		if ( ! empty( $_GET['mark_deposit_fully_paid'] ) && isset( $_GET['mark_deposit_fully_paid_nonce'] ) && check_admin_referer( 'mark_deposit_fully_paid', 'mark_deposit_fully_paid_nonce' ) ) {
			$action  = 'mark_deposit_fully_paid';
			$item_id = absint( $_GET['mark_deposit_fully_paid'] );
		}

		if ( ! empty( $_GET['invoice_remaining_balance'] ) && isset( $_GET['invoice_remaining_balance_nonce'] ) && check_admin_referer( 'invoice_remaining_balance', 'invoice_remaining_balance_nonce' ) ) {
			$action  = 'invoice_remaining_balance';
			$item_id = absint( $_GET['invoice_remaining_balance'] );
		}

		if ( ! $item_id ) {
			return;
		}

		$order_id = wc_get_order_id_by_order_item_id( $item_id );
		$order    = wc_get_order( $order_id );
		$item     = false;

		foreach ( $order->get_items() as $order_item_id => $order_item ) {
			if ( $item_id === $order_item_id ) {
				/**
				 * Order item
				 *
				 * @var WC_Order_Item_Product
				 */
				$item = $order_item;
			}
		}

		if ( ! $item || empty( $item['is_deposit'] ) ) {
			return;
		}

		switch ( $action ) {
			case 'mark_deposit_unpaid':
				wc_delete_order_item_meta( $item_id, '_remaining_balance_paid' );
				/**
				 * Update order status back to On-Hold/Processing/Partially Paid if it is completed.
				 *  - Cash on delivery (COD) - Processing
				 *  - Check Payments - On Hold
				 *  - Direct bank transfer (BACS) - On Hold
				 *  - other Payment methods - Partially Paid
				 */
				if ( $order && 'completed' === $order->get_status() ) {
					$payment_method = $order->get_payment_method();
					if ( in_array( $payment_method, array( 'bacs', 'cheque' ), true ) ) {
						$order->update_status( 'on-hold', __( 'Unmarked as paid.', 'woocommerce-deposits' ) );
					} elseif ( 'cod' === $payment_method ) {
						$order->update_status( 'processing', __( 'Unmarked as paid.', 'woocommerce-deposits' ) );
					} else {
						$order->update_status( 'partial-payment', __( 'Unmarked as paid.', 'woocommerce-deposits' ) );
					}
				}
				wp_safe_redirect( $order->get_edit_order_url() );
				exit;
			case 'mark_deposit_fully_paid':
				wc_add_order_item_meta( $item_id, '_remaining_balance_paid', 1 );
				// Update order status to completed if all deposits items fully paid.
				if ( $order && 'completed' !== $order->get_status() && $this->is_deposit_fully_paid( $order_id ) ) {
					$order->update_status( 'completed', __( 'Order completed with Mark Paid (offline).', 'woocommerce-deposits' ) );
				}
				wp_safe_redirect( $order->get_edit_order_url() );
				exit;
			case 'invoice_remaining_balance':
				// Used for products with fixed deposits or percentage based deposits. Not used for payment plan products
				// See WC_Deposits_Schedule_Order_Manager::schedule_orders_for_plan for creating orders for products with payment plans.

				// First, get the deposit_full_amount_ex_tax - this contains the full amount for the item excluding tax - see
				// WC_Deposits_Cart_Manager::add_order_item_meta_legacy or add_order_item_meta for where we set this amount
				// Note that this is for the line quantity, not necessarily just for quantity 1.
				$full_amount_excl_tax = floatval( $item['deposit_full_amount_ex_tax'] );

				// Next, get the initial deposit already paid, excluding tax.
				$amount_already_paid = floatval( $item['deposit_deposit_amount_ex_tax'] );

				// Then, set the item subtotal that will be used in create order to the full amount less the amount already paid.
				$subtotal = $full_amount_excl_tax - $amount_already_paid;

				// Add WC3.2 Coupons upgrade compatibility.
				// Lastly, subtract the deferred discount from the subtotal to get the total to be used to create the order.
				$discount_excl_tax = isset( $item['deposit_deferred_discount_ex_tax'] ) ? floatval( $item['deposit_deferred_discount_ex_tax'] ) : 0;
				$total             = $subtotal - $discount_excl_tax;

				// And then create an order with this item.
				$create_item = array(
					'product'  => $item->get_product(),
					'qty'      => $item['qty'],
					'subtotal' => $subtotal,
					'total'    => $total,
				);

				$new_order_id = $this->create_order( current_time( 'timestamp' ), $order_id, 2, $create_item, 'pending-deposit' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

				wc_add_order_item_meta( $item_id, '_remaining_balance_order_id', $new_order_id );

				/**
				 * Determine if we should send an email for Invoice Remaining Balance.
				 *
				 * @param int $new_order_id - Id of newly created order.
				 *
				 * @since 1.5.9
				 */
				$send_invoice_email = apply_filters( 'woocommerce_deposits_should_send_invoice_remaining_balance_email', true, $new_order_id );
				if ( true === $send_invoice_email ) {
					// Email invoice.
					$emails = WC_Emails::instance();
					$emails->customer_invoice( wc_get_order( $new_order_id ) );
				}
				$new_order = wc_get_order( $new_order_id );
				wp_safe_redirect( $new_order->get_edit_order_url() );
				exit;
		}
	}

	/**
	 * Check if all deposits items are fully paid.
	 *
	 * @param int $order_id Order ID.
	 * @return boolean
	 */
	public function is_deposit_fully_paid( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			$paid = true;
			foreach ( $order->get_items() as $item ) {
				if (
					WC_Deposits_Order_Item_Manager::is_deposit( $item ) &&
					! WC_Deposits_Order_Item_Manager::is_fully_paid( $item, $order )
				) {
					$paid = false;
					break;
				}
			}

			return $paid;
		}
		return false;
	}

	/**
	 * Detect Local Pickup shipping method from order
	 *
	 * @param WC_Order $order Order.
	 * @return boolean
	 */
	public static function is_local_pickup( $order ) {
		/**
		 * Woocommerce Local Pickup method IDs
		 *
		 * @see WC_Customer::get_taxable_address()
		 */
		$local_pickup_methods   = apply_filters( 'woocommerce_local_pickup_methods', array( 'legacy_local_pickup', 'local_pickup' ) );
		$order_shipping_methods = $order->get_shipping_methods();

		// Extract order shipping methods IDs.
		$shipping_method_ids = array_map(
			function ( $method ) {
				return $method->get_method_id();
			},
			$order_shipping_methods
		);

		// Check if one of order shipping methods match known local pickup methods.
		$is_local_pickup = count( array_intersect( $shipping_method_ids, $local_pickup_methods ) ) > 0;

		return $is_local_pickup;
	}

	/**
	 * Sends an email when a partial payment is made.
	 *
	 * @param int $order_id Order ID.
	 */
	public function payment_complete_handler( $order_id ) {
		$order = wc_get_order( $order_id );

		$post_status = 'wc-' . $order->get_status();
		if ( 'wc-partial-payment' !== $post_status ) {
			return;
		}

		$wc_emails = WC_Emails::instance();

		if ( isset( $wc_emails->emails['WC_Email_Customer_Processing_Order'] ) ) {
			/**
			 * Customer email
			 *
			 * @var WC_Email_Customer_Processing_Order $customer_email
			 */
			$customer_email = $wc_emails->emails['WC_Email_Customer_Processing_Order'];
			$customer_email->trigger( $order );
		}

		if ( isset( $wc_emails->emails['WC_Email_New_Order'] ) ) {
			/**
			 * Admin email
			 *
			 * @var WC_Email_New_Order $admin_email
			 */
			$admin_email = $wc_emails->emails['WC_Email_New_Order'];
			$admin_email->trigger( $order );
		}
	}

	/**
	 * Adds partial payment as is paid status.
	 *
	 * @param array $order_statuses Order statuses.
	 * @return array
	 */
	public function add_is_paid_status( $order_statuses ) {
		$order_statuses[] = 'partial-payment';
		return $order_statuses;
	}

	/**
	 * Append text to item names when viewing an order.
	 *
	 * @param string        $item_name Item name.
	 * @param WC_Order_Item $item Order item.
	 * @return string
	 */
	public function woocommerce_order_item_name( $item_name, $item ) {
		if ( WC_Deposits_Order_Item_Manager::is_deposit( $item ) ) {
			$payment_plan = WC_Deposits_Order_Item_Manager::get_payment_plan( $item );

			if ( $payment_plan ) {
				$item_name .= ' (' . $payment_plan->get_name() . ')';
			} else {
				$item_name .= ' (' . __( 'Deposit', 'woocommerce-deposits' ) . ')';
			}
		}
		return $item_name;
	}

	/**
	 * Add info about a deposit when viewing an order.
	 *
	 * @param int           $item_id Item ID.
	 * @param WC_Order_Item $item Order item.
	 * @param WC_Order      $order Order.
	 * @return void
	 */
	public function woocommerce_order_item_meta_end( $item_id, $item, $order ) {
		if ( WC_Deposits_Order_Item_Manager::is_deposit( $item ) && ! WC_Deposits_Order_Item_Manager::get_payment_plan( $item ) ) {
			$remaining                  = $item['deposit_full_amount'] - $order->get_line_total( $item, true );
			$remaining_balance_order_id = ! empty( $item['remaining_balance_order_id'] ) ? absint( $item['remaining_balance_order_id'] ) : 0;
			$remaining_balance_paid     = ! empty( $item['remaining_balance_paid'] );
			$currency                   = $order->get_currency();

			$remaining_balance_order = wc_get_order( $remaining_balance_order_id );

			if ( $remaining_balance_order_id && $remaining_balance_order ) {
				/* translators: remaining order ID */
				echo '<p class="wc-deposits-order-item-description"><a href="' . esc_url( $remaining_balance_order->get_view_order_url() ) . '">' . sprintf( esc_html__( 'Remainder - Invoice #%1$s', 'woocommerce-deposits' ), esc_html( $remaining_balance_order->get_order_number() ) ) . '</a></p>';
			} elseif ( $remaining_balance_paid ) {
				/* translators: paid offline amount */
				printf( '<p class="wc-deposits-order-item-description">' . esc_html__( 'The remaining balance of %s for this item was paid offline.', 'woocommerce-deposits' ) . '</p>', wc_price( $remaining, array( 'currency' => $currency ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}

	/**
	 * Adjust totals display.
	 *
	 * @since 1.3.4 Add in display of future discounts and correct discounted future payments.
	 * @param array    $total_rows Total rows.
	 * @param WC_Order $order Order.
	 * @return array
	 */
	public function woocommerce_get_order_item_totals( $total_rows, $order ) {
		if ( $this->has_deposit( $order ) ) {
			$remaining                = 0;
			$paid                     = 0;
			$total_payable            = 0;
			$currency                 = $order->get_currency();
			$is_tax_included          = wc_tax_enabled() && 'excl' !== get_option( 'woocommerce_tax_display_cart' );
			$deferred_discount_amount = 0;

			foreach ( $order->get_items() as $item ) {
				if ( WC_Deposits_Order_Item_Manager::is_deposit( $item ) ) {
					$total_payable += $item['deposit_full_amount_ex_tax'];
					if ( $is_tax_included ) {
						$deferred_discount_amount += (float) $item->get_meta( '_deposit_deferred_discount' );
					} else {
						$deferred_discount_amount += (float) $item->get_meta( '_deposit_deferred_discount_ex_tax' );
					}

					if ( ! WC_Deposits_Order_Item_Manager::get_payment_plan( $item ) ) {
						$remaining_balance_order_id = ! empty( $item['remaining_balance_order_id'] ) ? absint( $item['remaining_balance_order_id'] ) : 0;
						$remaining_balance_paid     = ! empty( $item['remaining_balance_paid'] );

						if ( empty( $remaining_balance_order_id ) && ! $remaining_balance_paid ) {

							if ( $is_tax_included ) {
								// do not show tax if included.
								$excluded_tax_amount = $is_tax_included ? 0 : $item['line_tax'];
								$item_remaining      = $item['deposit_full_amount'] - ( $order->get_line_subtotal( $item, true ) + $excluded_tax_amount );
							} else {
								$item_remaining = $item['deposit_full_amount_ex_tax'] - $order->get_line_subtotal( $item, false );
							}

							$remaining += $item_remaining;
						}
					}
				}
			}

			// PAID scheduled orders.
			$order_id       = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
			$related_orders = WC_Deposits_Scheduled_Order_Manager::get_related_orders( $order_id );

			foreach ( $related_orders as $related_order_id ) {
				$related_order     = wc_get_order( $related_order_id );
				$total_with_tax    = $related_order->get_total();
				$total_without_tax = $total_with_tax - $related_order->get_total_tax();

				if ( $related_order->has_status( array( 'processing', 'completed' ) ) ) {
					$paid += $is_tax_included ? $total_with_tax : $total_without_tax;
				} else {
					$remaining += $is_tax_included ? $total_with_tax : $total_without_tax;
				}
			}

			$tax_message = $is_tax_included ? __( '(includes tax)', 'woocommerce-deposits' ) : __( '(excludes tax)', 'woocommerce-deposits' );
			$tax_element = wc_tax_enabled() ? ' <small class="tax_label">' . $tax_message . '</small>' : '';

			if ( 0 < $deferred_discount_amount ) {
				$total_rows['deferred_discount'] = array(
					'label' => __( 'Discount Applied Toward Future Payments', 'woocommerce-deposits' ),
					'value' => wc_price( -$deferred_discount_amount, array( 'currency' => $currency ) ),
				);
			}

			// Related orders know what is the applied discount so we do not need to apply it here again.
			if ( empty( $related_orders ) ) {
				$remaining -= $deferred_discount_amount;
			}

			if ( $remaining && $paid ) {
				$total_rows['future'] = array(
					'label' => __( 'Future Payments', 'woocommerce-deposits' ),
					'value' => '<del>' . wc_price( $remaining + $paid, array( 'currency' => $currency ) ) . '</del> <ins>' . wc_price( $remaining, array( 'currency' => $currency ) ) . $tax_element . '</ins>',
				);
			} elseif ( $remaining ) {
				$total_rows['future'] = array(
					'label' => __( 'Future Payments', 'woocommerce-deposits' ),
					'value' => wc_price( $remaining, array( 'currency' => $currency ) ) . $tax_element,
				);
			}

			// Rebuild the order total rows to include our own information and
			// makes the order intact.
			foreach ( $total_rows as $row => $value ) {
				if ( 'order_total' === $row ) {
					// change the total label.
					$value['label']         = __( 'Total Due Today:', 'woocommerce-deposits' );
					$new_total_rows[ $row ] = $value;
				} else {
					$new_total_rows[ $row ] = $value;
				}
			}

			$total_rows = $new_total_rows;
		}

		return $total_rows;
	}

	/**
	 * Admin filters.
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function request_query( $vars ) {
		global $typenow, $wp_query, $wp_post_statuses;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ), true ) ) {
			if ( isset( $_GET['post_parent'] ) && $_GET['post_parent'] > 0 ) {
				$vars['post_parent'] = absint( $_GET['post_parent'] );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $vars;
	}

	/**
	 * Triggered when adding an item in the backend.
	 * If deposits are forced, set all meta data.
	 *
	 * @param int           $item_id Order item ID.
	 * @param WC_Order_Item $item Order item.
	 * @return void
	 */
	public function ajax_add_order_item_meta( $item_id, $item ) {
		if ( WC_Deposits_Product_Manager::deposits_forced( $item['product_id'] ) ) {
			$product = wc_get_product( absint( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] ) );
			wc_add_order_item_meta( $item_id, '_is_deposit', 'yes' );
			wc_add_order_item_meta( $item_id, '_deposit_full_amount', $item['line_total'] );
			wc_add_order_item_meta( $item_id, '_deposit_full_amount_ex_tax', $item['line_total'] );

			if ( 'plan' === WC_Deposits_Product_Manager::get_deposit_type( $item['product_id'] ) ) {
				$plan_id = current( WC_Deposits_Plans_Manager::get_plan_ids_for_product( $item['product_id'] ) );
				wc_add_order_item_meta( $item_id, '_payment_plan', $plan_id );
			} else {
				$plan_id = 0;
			}

			// Change line item costs.
			$deposit_amount = WC_Deposits_Product_Manager::get_deposit_amount( $product, $plan_id, 'order', $item['line_total'] );
			wc_update_order_item_meta( $item_id, '_line_total', $deposit_amount );
			wc_update_order_item_meta( $item_id, '_line_subtotal', $deposit_amount );
		}
	}

	/**
	 * Display total payable of deposit item in order item details.
	 *
	 * @since 1.1.10
	 * @param float    $subtotal Line subtotal.
	 * @param array    $item     Order item.
	 * @param WC_Order $order    Order object.
	 * @return string Formatted subtotal
	 */
	public function display_item_total_payable( $subtotal, $item, $order ) {
		if ( ! isset( $item['deposit_full_amount'] ) ) {
			return $subtotal;
		}

		if ( ! empty( $item['is_deposit'] ) ) {
			$_product    = wc_get_product( $item['product_id'] );
			$quantity    = $item['qty'];
			$currency    = $order->get_currency();
			$full_amount = 'excl' === get_option( 'woocommerce_tax_display_cart' ) ? $item['deposit_full_amount_ex_tax'] : $item['deposit_full_amount'];

			if ( ! empty( $item['payment_plan'] ) ) {
				$plan      = new WC_Deposits_Plan( $item['payment_plan'] );
				$subtotal .= '<br/><small>' . $plan->get_formatted_schedule( $full_amount, $currency ) . '</small>';
			} else {
				/* translators: full amount payable */
				$subtotal .= '<br/><small>' . sprintf( __( '%s payable in total', 'woocommerce-deposits' ), wc_price( $full_amount, array( 'currency' => $currency ) ) ) . '</small>';
			}
		}

		return $subtotal;
	}

	/**
	 * Should the order stock be reduced?
	 *
	 * @param boolean  $allowed Allow reduce stock.
	 * @param WC_Order $order Order.
	 * @return boolean
	 */
	public function allow_reduce_order_stock( $allowed, $order ) {
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		// Don't reduce stock on follow up orders.
		$created_via = is_callable( array( $order, 'get_created_via' ) ) ? $order->get_created_via() : $order->created_via;
		if ( 'wc_deposits' === $created_via ) {
			$allowed = false;
		}

		return $allowed;
	}

	/**
	 * Prevent stock level adjustment for Deposit payment order line items
	 *
	 * @param bool          $prevent If should prevent.
	 * @param WC_Order_Item $item Item object.
	 * @return bool
	 */
	public function prevent_adjust_line_item_product_stock( $prevent, $item ) {
		$order_id = $item->get_order_id();
		$order    = wc_get_order( $order_id );

		if ( false === $order ) {
			return $prevent;
		}

		// Prevent stock adjustments on follow up orders.
		$created_via = is_callable( array( $order, 'get_created_via' ) ) ? $order->get_created_via() : $order->created_via;
		if ( 'wc_deposits' === $created_via ) {
			$prevent = true;
		}

		return $prevent;
	}

	/**
	 * Alter if a download is permitted.
	 *
	 * Downloads should only be permitted for deposit plans if all
	 * orders are paid.
	 *
	 * @since  1.1.7
	 * @param  bool     $permitted Permit download.
	 * @param  WC_Order $order Order.
	 * @return bool @permitted
	 */
	public function maybe_alter_if_download_permitted( $permitted, $order ) {
		// get the parent order which is the main item.
		foreach ( $order->get_items() as $item ) {
			if ( ! isset( $item['original_order_id'] ) ) {
				continue;
			}

			$parent_order_id = $item['original_order_id'];
			$parent_order    = wc_get_order( $parent_order_id );

			if ( ! $this->has_deposit( $parent_order ) ) {
				continue;
			}

			return WC_Deposits_Plans_Manager::is_order_plan_fully_paid( $parent_order );
		}

		return $permitted;
	}

	/**
	 * Adds pending-deposit order status for inclusion for payment processes.
	 *
	 * @since  1.1.8
	 * @param  array $statuses Statuses.
	 * @return array $statuses
	 */
	public function add_status_to_needs_payment( $statuses ) {
		$statuses[] = 'pending-deposit';
		return $statuses;
	}

	/**
	 * Bypass stock management for the product
	 *
	 * @param boolean    $manage_stock Default stock management.
	 * @param WC_Product $product Product.
	 * @return boolean
	 */
	public function maybe_bypass_manage_stock( $manage_stock, $product ) {

		// Bail if not pay for order page.
		if ( ! isset( $_GET['pay_for_order'], $_GET['key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $manage_stock;
		}

		$product = $this->disable_manage_stock_for_product_variation( $product );

		// We use the same logic for bypassing manage stock as stock status but we need to inverse the result.
		return ! $this->maybe_bypass_stock_status( ! $manage_stock, $product );
	}

	/**
	 * Disables stock management in parent data of a production with variations.
	 * This update is not persistent.
	 *
	 * @since 1.4.12
	 * @param object $product product variation instance to update.
	 *
	 * @return object $product
	 */
	private function disable_manage_stock_for_product_variation( $product ) {
		if ( 'product_variation' === $product->post_type ) {
			$parent_data                 = $product->get_parent_data() ? $product->get_parent_data() : array();
			$parent_data['manage_stock'] = false;
			$product->set_parent_data( $parent_data );
		}

		return $product;
	}

	/**
	 * Filters the stock status check so that out of stock products
	 * with deposits can still be paid for. In cases like initially
	 * only one in stock and became zero.
	 *
	 * @since 1.3.3
	 * @param bool       $in_stock Default product stock status.
	 * @param WC_Product $product Product.
	 *
	 * @return bool
	 */
	public function maybe_bypass_stock_status( $in_stock, $product ) {
		// Bail if not pay for order page.
		if ( ! isset( $_GET['pay_for_order'], $_GET['key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $in_stock;
		}

		// Bail if stock status is already true.
		if ( $in_stock ) {
			return $in_stock;
		}

		$order_id = wc_get_order_id_by_order_key( wc_clean( wp_unslash( $_GET['key'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Bail if order id is not returned.
		if ( ! $order_id ) {
			return $in_stock;
		}

		$order      = wc_get_order( $order_id );
		$line_items = $order->get_items();

		// Loop through order item meta to find original order id.
		foreach ( $line_items as $item_id => $item ) {
			if ( ! is_a( $item, 'WC_Order_Item_Product' ) ) {
				continue;
			}

			// Make sure we're working with same product otherwise skip.
			if ( $product->get_id() === $item->get_id() ) {
				continue;
			}

			$original_order_id = $item->get_meta( '_original_order_id', true );

			if ( ! $original_order_id ) {
				continue;
			}

			$original_order      = wc_get_order( $original_order_id );
			$original_line_items = $original_order->get_items();

			foreach ( $original_line_items as $original_item_id => $original_item ) {
				if ( WC_Deposits_Order_Item_Manager::is_deposit( $original_item ) ) {
					$in_stock = true;
					break;
				}
			}
		}

		return $in_stock;
	}

	/**
	 * Register transitions from Pending Deposit and Scheduled Payment status
	 * to send default WooCommerce emails.
	 *
	 * @param  array $actions Actions.
	 * @return array
	 */
	public function register_status_transitions_for_core_emails( $actions ) {

		$actions = array_merge(
			$actions,
			array(
				'woocommerce_order_status_pending-deposit_to_processing',
				'woocommerce_order_status_pending-deposit_to_completed',
				'woocommerce_order_status_pending-deposit_to_on-hold',
				'woocommerce_order_status_scheduled-payment_to_processing',
				'woocommerce_order_status_pending_to_partial-payment',
			)
		);

		return $actions;
	}

	/**
	 * Send WooCommerce emails on status transitions from
	 * Pending Deposit or Scheduled Payment.
	 *
	 * @param WC_Emails $email_class WooCommerce email class.
	 */
	public function send_core_emails_on_status_changes( $email_class ) {
		if ( isset( $email_class->emails['WC_Email_New_Order'] ) ) {
			// New Order notification to admin.
			add_action( 'woocommerce_order_status_pending-deposit_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			add_action( 'woocommerce_order_status_pending-deposit_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			add_action( 'woocommerce_order_status_pending-deposit_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
			add_action( 'woocommerce_order_status_scheduled-payment_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		}

		// Customer notifications.
		if ( isset( $email_class->emails['WC_Email_Customer_Processing_Order'] ) ) {
			add_action( 'woocommerce_order_status_pending-deposit_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
			add_action( 'woocommerce_order_status_scheduled-payment_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		}

		if ( isset( $email_class->emails['WC_Email_Customer_On_Hold_Order'] ) ) {
			add_action( 'woocommerce_order_status_pending-deposit_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_On_Hold_Order'], 'trigger' ) );
		}
	}
}

WC_Deposits_Order_Manager::get_instance();
