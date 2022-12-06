<?php
/**
 * Store Credit: Order manager.
 *
 * @package WC_Store_Credit
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Order.
 */
class WC_Store_Credit_Order {

	/**
	 * The discounts instance for each order.
	 *
	 * @var array
	 */
	protected $discounts = array();

	/**
	 * Constructor.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_order_get_total', array( $this, 'get_total' ), 10, 2 );
		add_filter( 'woocommerce_order_get_total_discount', array( $this, 'get_total_discount' ), 10, 2 );

		add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 10, 4 );
		add_action( 'woocommerce_before_save_order_items', array( $this, 'before_save_items' ) );
		add_action( 'woocommerce_saved_order_items', array( $this, 'after_save_items' ) );
		add_filter( 'woocommerce_order_recalculate_coupons_coupon_object', array( $this, 'recalculate_coupon' ), 10, 4 );

		add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'get_coupon_discount' ), 10, 5 );
		add_filter( 'woocommerce_coupon_custom_discounts_array', array( $this, 'coupon_get_discounts_array' ), 10, 2 );
		add_action( 'woocommerce_order_before_calculate_taxes', array( $this, 'before_calculate_taxes' ), 10, 2 );
		add_action( 'woocommerce_order_after_calculate_totals', array( $this, 'after_calculate_totals' ), 10, 2 );
		add_action( 'woocommerce_order_item_shipping_after_calculate_taxes', array( $this, 'update_shipping_item_taxes' ) );
	}

	/**
	 * Filters the order total value.
	 *
	 * @since 2.4.4
	 *
	 * @param float    $total Order total.
	 * @param WC_Order $order Order object.
	 * @return float
	 */
	public function get_total( $total, $order ) {
		// Not necessary if applied before taxes.
		if ( ! wc_store_credit_apply_before_tax( $order ) ) {
			$credit = wc_get_store_credit_used_for_order( $order );

			if ( 0 < $credit ) {
				$backtrace  = wp_debug_backtrace_summary( 'WP_Hook', 0, false ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
				$save_index = array_search( 'WC_Abstract_Order->get_total', $backtrace, true );

				$callbacks = array(
					'WC_Gateway_Paypal_Request->get_line_item_args',
					'WC_Gateway_Paypal_Request->get_line_item_args_single_item',
				);

				/*
				 * We restore the store credit discount in order to avoid a negative value with the total amount
				 * when adding the single line item in the PayPal args.
				 */
				if ( in_array( $backtrace[ $save_index + 1 ], $callbacks, true ) ) {
					$total += $credit;
				}
			}
		}

		return $total;
	}

	/**
	 * Updates the total discount for the order.
	 *
	 * @since 2.4.4
	 *
	 * @param float    $total_discount The total discount.
	 * @param WC_Order $order          The order instance.
	 * @return float
	 */
	public function get_total_discount( $total_discount, $order ) {
		// Not necessary if applied before taxes.
		if ( ! wc_store_credit_apply_before_tax( $order ) ) {
			$credit = wc_get_store_credit_used_for_order( $order );

			if ( 0 < $credit ) {
				$backtrace  = wp_debug_backtrace_summary( 'WP_Hook', 0, false ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
				$save_index = array_search( 'WC_Abstract_Order->get_total_discount', $backtrace, true );

				$callback     = $backtrace[ $save_index + 1 ];
				$exclude_from = array(
					'WC_Abstract_Order->add_order_item_totals_discount_row',
					'WC_Abstract_Order->get_discount_to_display',
					'WC_Abstract_Order->get_subtotal_to_display',
				);

				if (
					! in_array( $callback, $exclude_from, true ) && // Exclude from item rows.
					false === strpos( $callback, 'views/html-' ) // Exclude from any views.
				) {
					$total_discount += $credit;
				}
			}
		}

		return $total_discount;
	}

	/**
	 * Handles the order status change.
	 *
	 * @since 2.4.0
	 *
	 * @param int      $order_id Order ID.
	 * @param string   $from     The old order status.
	 * @param string   $to       The new order status.
	 * @param WC_Order $order    Order object.
	 */
	public function order_status_changed( $order_id, $from, $to, $order ) {
		// The order statuses that require to restore the coupons credit.
		$restore_statuses = array( 'cancelled', 'refunded' );

		if ( in_array( $to, $restore_statuses, true ) ) {
			// Restore the coupons' credit.
			wc_restore_store_credit_for_order( $order );
		} elseif ( in_array( $from, $restore_statuses, true ) ) {
			$shipping_discount = wc_get_store_credit_discounts_for_order( $order, 'total', array( 'shipping' ) );

			// Remove shipping discount items.
			if ( 0 < array_sum( $shipping_discount ) ) {
				$order_discounts = $this->get_order_discounts( $order );
				$order_discounts->set_shipping_items_from_object();
				$order_discounts->update_shipping_discount_items();
			}

			// The merchant wants to recover the order and its store credit coupons were restored.
			wc_store_credit_delete_restored_coupons_for_order( $order, $from );
		}

		// Delete exhausted coupons.
		if ( 'completed' === $to && wc_string_to_bool( get_option( 'wc_store_credit_delete_after_use', 'yes' ) ) ) {
			wc_store_credit_delete_exhausted_order_coupons( $order );
		}

		// Update payment method.
		if ( in_array( $to, array( 'processing', 'completed' ), true ) ) {
			if ( 0 >= $order->get_total() && ! $order->get_payment_method() && 0 < wc_get_store_credit_used_for_order( $order_id ) ) {
				$order->set_payment_method( _x( 'Store Credit', 'payment method', 'woocommerce-store-credit' ) );
				$order->save();
			}
		}
	}

	/**
	 * Gets the discounts for the specified order.
	 *
	 * @since 2.4.0
	 *
	 * @param mixed $the_order Order object or ID.
	 * @return WC_Store_Credit_Discounts_Order The order discounts instance.
	 */
	public function get_order_discounts( $the_order ) {
		$order    = wc_store_credit_get_order( $the_order );
		$order_id = $order->get_id();

		if ( empty( $this->discounts[ $order_id ] ) ) {
			$this->discounts[ $order_id ] = new WC_Store_Credit_Discounts_Order( $order );
		} else {
			// Updates the object instance.
			$discounts = $this->discounts[ $order_id ];
			$discounts->set_object( $order );

			$this->discounts[ $order_id ] = $discounts;
		}

		return $this->discounts[ $order_id ];
	}

	/**
	 * Gets the posted order action.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_order_action() {
		// phpcs:disable WordPress.Security.NonceVerification
		$action   = ( ! empty( $_POST['action'] ) ? wc_clean( wp_unslash( $_POST['action'] ) ) : '' );
		$order_id = ( ! empty( $_POST['order_id'] ) ? wc_clean( wp_unslash( $_POST['order_id'] ) ) : false );

		// Check if it's a save order action.
		if ( ! $order_id && ! empty( $_POST['save'] ) ) {
			$post_id = ( ! empty( $_POST['post_ID'] ) ? wc_clean( wp_unslash( $_POST['post_ID'] ) ) : false );

			if ( ! $post_id ) { // HPOS Compatibility.
				$post_id = ( ! empty( $_REQUEST['id'] ) ? wc_clean( wp_unslash( $_REQUEST['id'] ) ) : false );
			}

			if ( $post_id ) {
				$order = wc_get_order( $post_id );

				if ( $order && 'shop_order' === $order->get_type() ) {
					$order_id = $post_id;
				}
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification

		$action = ( $order_id ? str_replace( 'woocommerce_', '', $action ) : '' );

		if ( 'editpost' === $action ) {
			$action = 'edit_order';
		}

		/**
		 * Filters the order action.
		 *
		 * @since 3.0.0
		 *
		 * @param string $action The order action.
		 */
		return apply_filters( 'wc_store_credit_order_action', $action );
	}

	/**
	 * Gets the order actions which to listen to execute a specific process.
	 *
	 * Centralizes the order action sets.
	 *
	 * @since 3.0.0
	 *
	 * @param string $process The process to execute.
	 * @return array
	 */
	public function get_order_actions_for( $process ) {
		$actions_for = array(
			'calculate_discounts' => array(
				'add_coupon_discount',
				'remove_order_coupon',
				'recalculate_coupons',
				'calc_line_taxes',
				'save_order_items',
			),
			'recalculate_coupons' => array(
				'calc_line_taxes',
				'save_order_items',
			),
		);

		$actions = ( isset( $actions_for[ $process ] ) ? $actions_for[ $process ] : array() );

		/**
		 * Filters the order actions which to listen to execute a specific process.
		 *
		 * The dynamic portion of the hook name, `$process`, refers to the process to execute.
		 *
		 * @since 3.0.0
		 *
		 * @param array $actions The order actions.
		 */
		return apply_filters( "wc_store_credit_order_actions_for_{$process}", $actions );
	}

	/**
	 * Processes a before saving order items action.
	 *
	 * @since 3.0.0
	 *
	 * @param int $order_id Order ID.
	 */
	public function before_save_items( $order_id ) {
		$action = $this->get_order_action();

		/*
		 * If the order items changes, the discounts applied by the coupons are not updated on these actions.
		 * So, we need to force a call to the protected method 'WC_Order->recalculate_coupons'.
		 */
		if ( in_array( $action, $this->get_order_actions_for( 'recalculate_coupons' ), true ) ) {
			$order        = wc_get_order( $order_id );
			$coupon_items = wc_get_store_credit_coupons_for_order( $order );

			if ( empty( $coupon_items ) ) {
				return;
			}

			$order_discounts = $this->get_order_discounts( $order );

			// Only recalculate the coupons once per request.
			if ( ! $order_discounts->get_recalculate_coupon_status() ) {
				$order_discounts->set_recalculate_coupon_status( 'in_progress' );
			}
		}
	}

	/**
	 * Processes an after saving order items action.
	 *
	 * @since 3.0.0
	 *
	 * @param int $order_id Order ID.
	 */
	public function after_save_items( $order_id ) {
		$action = $this->get_order_action();

		if ( in_array( $action, $this->get_order_actions_for( 'recalculate_coupons' ), true ) ) {
			$order           = wc_get_order( $order_id );
			$order_discounts = $this->get_order_discounts( $order );

			if ( 'in_progress' !== $order_discounts->get_recalculate_coupon_status() ) {
				return;
			}

			// Add a virtual coupon to force recalculate the coupon.
			$coupon = new WC_Coupon( 'force_recalculate_coupons' );
			$coupon->set_virtual( true );

			$order->apply_coupon( $coupon );
		}
	}

	/**
	 * Filters if the coupon should be applied when recalculating the order coupons.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Coupon            $coupon Coupon object.
	 * @param string               $coupon_code Coupon code.
	 * @param WC_Order_Item_Coupon $coupon_item Order Item coupon.
	 * @param WC_Order             $order Order object.
	 * @return mixed
	 */
	public function recalculate_coupon( $coupon, $coupon_code, $coupon_item, $order ) {
		if ( 'force_recalculate_coupons' === $coupon_code ) {
			$order_discounts = $this->get_order_discounts( $order );

			$order_discounts->set_recalculate_coupon_status( 'finished' );

			// Remove coupon silently without triggering a new 'recalculate coupons' action.
			$order->remove_item( $coupon_item->get_id() );

			return false;
		}

		// Maybe restore the missed store credit coupon data.
		$credit_used = wc_get_store_credit_used_for_order( $order, 'per_coupon' );

		if ( ! wc_is_store_credit_coupon( $coupon ) && ! empty( $credit_used[ $coupon_code ] ) ) {
			$coupon->set_discount_type( 'store_credit' );
			$coupon->set_amount( $credit_used[ $coupon_code ] );
		}

		if ( wc_is_store_credit_coupon( $coupon ) && ! $coupon->meta_exists( 'store_credit_inc_tax' ) ) {
			$version = wc_get_store_credit_version_for_order( $order );

			if ( version_compare( $version, '3.0', '>=' ) ) {
				// Regenerate the configuration from the coupon discounts.
				$discounts         = wc_get_store_credit_discounts_for_order( $order, 'per_coupon' );
				$inc_tax           = ( (float) array_sum( $discounts[ $coupon_code ] ) === (float) $credit_used[ $coupon_code ] );
				$apply_to_shipping = isset( $discounts[ $coupon_code ]['shipping'] );
			} else {
				// Use the legacy configuration.
				$before_tax        = wc_store_credit_apply_before_tax( $order );
				$inc_tax           = ! $before_tax;
				$apply_to_shipping = ! $before_tax;
			}

			$coupon->add_meta_data( 'store_credit_inc_tax', wc_bool_to_string( $inc_tax ), true );
			$coupon->add_meta_data( 'store_credit_apply_to_shipping', wc_bool_to_string( $apply_to_shipping ), true );
		}

		return $coupon;
	}

	/**
	 * Gets the coupon discount.
	 *
	 * @since 2.4.0
	 *
	 * @param float                 $discount           The coupon discount.
	 * @param float                 $discounting_amount Amount the coupon is being applied to.
	 * @param WC_Order_Item_Product $order_item         Order item being discounted if applicable.
	 * @param boolean               $single             True if discounting a single qty item, false if its the line.
	 * @param WC_Coupon             $coupon             The coupon instance.
	 * @return float Amount this coupon has discounted.
	 */
	public function get_coupon_discount( $discount, $discounting_amount, $order_item, $single, $coupon ) {
		if ( ! $order_item instanceof WC_Order_Item_Product || ! wc_is_store_credit_coupon( $coupon ) ) {
			return $discount;
		}

		/*
		 * Return the maximum amount to obtain the total discount for this coupon and fix the discounts per item
		 * in the 'woocommerce_coupon_custom_discounts_array' filter hook.
		 */
		return $discounting_amount;
	}

	/**
	 * Post-process the coupon discounts.
	 *
	 * @since 3.0.0
	 *
	 * @param array     $discounts An array with the applied discounts per item.
	 * @param WC_Coupon $coupon    The coupon instance.
	 * @return array
	 */
	public function coupon_get_discounts_array( $discounts, $coupon ) {
		// Also discard order actions.
		if ( ! wc_is_store_credit_coupon( $coupon ) || ! $this->get_order_action() ) {
			return $discounts;
		}

		$order_item_ids = array_keys( $discounts );
		$order_item     = new WC_Order_Item_Product( $order_item_ids[0] );
		$order          = $order_item->get_order();

		$order_discounts = $this->get_order_discounts( $order );
		$discounts       = $order_discounts->calculate_item_discounts( $coupon, wc_remove_number_precision_deep( $discounts ) );

		return wc_add_number_precision_deep( $discounts );
	}

	/**
	 * Processes the order before calculating its taxes.
	 *
	 * @since 3.0.0
	 *
	 * @param array    $args  Tax arguments.
	 * @param WC_Order $order Order object.
	 */
	public function before_calculate_taxes( $args, $order ) {
		$order_discounts = $this->get_order_discounts( $order );

		$order_discounts->set_shipping_items_from_object();
	}

	/**
	 * Processes the order actions after calculate its totals.
	 *
	 * @since 2.4.0
	 *
	 * @param bool     $and_taxes Calc taxes if true.
	 * @param WC_Order $order     Order object.
	 */
	public function after_calculate_totals( $and_taxes, $order ) {
		$this->process_order_action( $order );
	}

	/**
	 * Processes the order actions.
	 *
	 * @since 2.4.0
	 *
	 * @param WC_Order $order Order object.
	 */
	public function process_order_action( $order ) {
		$action = $this->get_order_action();

		// Backward compatibility with orders which contain coupons applied after tax.
		if ( 'edit_order' === $action && ! wc_store_credit_apply_before_tax( $order ) ) {
			$discount = wc_get_store_credit_used_for_order( $order );

			$order->set_total( $order->get_total() - $discount );
			return;
		}

		// Avoid breaking the calcs when saving the order post.
		if ( ! in_array( $action, $this->get_order_actions_for( 'calculate_discounts' ), true ) ) {
			return;
		}

		// Remove a store credit coupon.
		if ( 'remove_order_coupon' === $action ) {
			$coupon_code = ( ! empty( $_POST['coupon'] ) ? wc_clean( wp_unslash( $_POST['coupon'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

			if ( ! wc_is_store_credit_coupon( $coupon_code ) ) {
				return;
			}
		}

		$order_discounts = $this->get_order_discounts( $order );

		// Skip when recalculating the coupons discounts.
		if (
			'in_progress' === $order_discounts->get_recalculate_coupon_status() &&
			in_array( $action, $this->get_order_actions_for( 'recalculate_coupons' ), true )
		) {
			return;
		}

		$order_discounts->calculate_shipping_discounts();
		$order_discounts->calculate_totals();

		$order_discounts->update_shipping_discount_items();
		$order_discounts->update_credit_discounts();
		$order_discounts->update_credit_used();
	}

	/**
	 * Updates the taxes for the order shipping item.
	 *
	 * Fixes discrepancies when calculating the taxes for a shipping discount item.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order_Item_Shipping $order_item The order item instance.
	 */
	public function update_shipping_item_taxes( $order_item ) {
		if ( 'store_credit_discount' !== $order_item->get_method_id() || ! $order_item->get_order_id() ) {
			return;
		}

		$order_discounts         = $this->get_order_discounts( $order_item->get_order_id() );
		$shipping_discount_items = $order_discounts->get_shipping_discount_items();

		$order_item_id = $order_item->get_id();

		if ( ! $order_item_id ) {
			$instance_ids  = wp_list_pluck( $shipping_discount_items, 'instance_id' );
			$order_item_id = array_search( $order_item->get_instance_id(), $instance_ids, true );
		}

		// Restore the taxes calculated by our extension.
		if ( isset( $shipping_discount_items[ $order_item_id ] ) ) {
			$shipping_discount_item = $shipping_discount_items[ $order_item_id ];

			try {
				$order_item->set_taxes( array( 'total' => $shipping_discount_item->taxes ) );
			} catch ( Exception $e ) {
				return;
			}
		}
	}
}

return new WC_Store_Credit_Order();
