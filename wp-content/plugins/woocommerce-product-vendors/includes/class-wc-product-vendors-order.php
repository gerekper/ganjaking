<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Order Class.
 *
 * Process the orders from WooCommerce.
 *
 * @category Order
 * @package  WooCommerce Product Vendors/Order
 * @version  2.0.35
 * @since 2.0.0
 */
class WC_Product_Vendors_Order {
	protected $commission;
	public $order;
	protected $log;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.35
	 * @return bool
	 */
	public function __construct( WC_Product_Vendors_Commission $commission ) {
		$this->commission = $commission;

		// process the order

		add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'process' ) );
		add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'process' ) );
		add_action( 'woocommerce_order_status_pending_to_processing', array( $this, 'process' ) );
		add_action( 'woocommerce_order_status_pending_to_completed', array( $this, 'process' ) );
		add_action( 'woocommerce_order_status_failed_to_processing', array( $this, 'process' ) );
		add_action( 'woocommerce_order_status_failed_to_completed', array( $this, 'process' ) );
		add_action( 'woocommerce_bookings_create_booking_page_add_order_item', array( $this, 'process' ) );
		// process deposit orders.
		add_action( 'woocommerce_order_status_pending_to_partial-payment', array( $this, 'process' ) );
		add_action( 'woocommerce_order_status_on-hold_to_partial-payment', array( $this, 'process' ) );
		add_action( 'woocommerce_order_status_failed_to_partial-payment', array( $this, 'process' ) );
		add_action( 'woocommerce_order_status_pending-deposit_to_processing', array( $this, 'process' ) );
		add_action( 'woocommerce_order_status_pending-deposit_to_completed', array( $this, 'process' ) );
		add_action( 'woocommerce_order_status_scheduled-payment_to_processing', array( $this, 'process' ) );

		// Delete commissions when order item is removed.
		add_action( 'woocommerce_before_delete_order_item', array( $this, 'maybe_remove_affected_commission' ) );

		add_action( 'delete_post', array( $this, 'remove_affected_commissions' ) );
		add_action( 'woocommerce_order_status_pending_to_processing', array( $this, 'maybe_complete_order' ), 20, 1 );
		add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'maybe_complete_order' ), 20, 1 );
		add_action( 'woocommerce_order_status_failed_to_processing', array( $this, 'maybe_complete_order' ), 20, 1 );

		add_action( 'wcpv_commission_added', array( $this, 'add_commission_order_note' ) );

		if ( is_admin() ) {
			add_filter( 'woocommerce_order_actions', array( $this, 'add_commission_order_action' ) );
		}

		add_action( 'woocommerce_order_action_wcpv_manual_create_commission', array( $this, 'process_manual_create_commission_action' ) );

		add_action( 'woocommerce_product_vendors_paypal_webhook_trigger', array( $this, 'process_paypal_webhook' ) );

		add_action( 'woocommerce_payment_complete', array( $this, 'payment_complete' ) );

		add_action( 'woocommerce_order_refunded', array( $this, 'process_order_refund' ), 10, 2 );

		$this->log = new WC_Logger();

		return true;
	}

	/**
	 * Process the webhook trigger from PayPal to mark payout "paid". This fires only
	 * if the payout was successful. Note that each notification will
	 * be for one single vendor that may contain the sum of commission
	 * payout for one entire order.
	 *
	 * @since 2.0.35
	 * @version 2.0.35
	 * @param object $notification
	 */
	public function process_paypal_webhook( $notification ) {
		$resource_parts = explode( '_', $notification->resource->payout_item->sender_item_id );
		$order_id       = absint( $resource_parts[1] );
		$vendor_id      = absint( $resource_parts[3] );

		$commissions = $this->commission->get_commission_by_order_id( $order_id, 'unpaid' );

		if ( $commissions ) {
			foreach ( $commissions as $commission ) {
				// Only process the vendor in question.
				if ( absint( $commission->vendor_id ) === $vendor_id ) {
					$this->commission->update_status( $commission->id, $commission->order_item_id, 'paid' );
					WC_Product_Vendors_Utils::update_order_item_meta( $commission->order_item_id );
				}
			}
		}
	}

	/**
	 * Process the manual create commission action
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param WC_Order $order
	 * @return bool
	 */
	public function process_manual_create_commission_action( $order ) {
		$order_id = $order->get_id();
		$refunds  = $order->get_refunds();

		if ( $refunds ) {
			$refunds = array_reverse( $refunds );

			/* @var WC_Order_Refund $refund Latest refund object for order. */
			foreach ( $refunds as $refund ) {
				$this->process_order_refund( $order_id, $refund->get_id() );
			}

			$this->add_commission_order_note( $order );
		} else {
			$this->process( $order_id );
		}

		return true;
	}

	/**
	 * Process order
	 *
	 * @access  public
	 * @since   2.1.72 Implement logic to regenerate vendor commission for given order id and order item id.
	 *              Process payout only for unpaid commissions.
	 * @since   2.0.0
	 *
	 * @param int $order_id
	 *
	 * @return bool
	 * @version 2.0.0
	 */
	public function process( $order_id ) {
		global $wpdb;

		$commission_added = false;

		$this->order = wc_get_order( $order_id );

		if ( is_a( $this->order, 'WC_Order' ) && $items = $this->order->get_items( 'line_item' ) ) {
			$order_status   = $this->order->get_status();
			$commission_ids = array();

			foreach ( $items as $order_item_id => $item ) {
				$product = wc_get_product( $item['product_id'] );
				if ( ! is_object( $product ) ) {
					continue;
				}

				$vendor_id = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product->get_id() );

				// check if it is a vendor product
				if ( $vendor_id ) {
					$_product_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
					$_product    = wc_get_product( $_product_id );

					do_action( 'wcpv_processing_vendor_order_item', $order_item_id, $item, $this->order );

					// check first to see if meta has already been added
					$check_sql  = 'SELECT `meta_value`';
					$check_sql .= " FROM {$wpdb->prefix}woocommerce_order_itemmeta";
					$check_sql .= ' WHERE `order_item_id` = %d';
					$check_sql .= ' AND `meta_key` = %s';

					$result = $wpdb->get_results( $wpdb->prepare( $check_sql, $order_item_id, '_fulfillment_status' ) );

					if ( empty( $result ) ) {

						// add ship status to order item meta
						$sql  = "INSERT INTO {$wpdb->prefix}woocommerce_order_itemmeta ( `order_item_id`, `meta_key`, `meta_value` )";
						$sql .= ' VALUES ( %d, %s, %s )';

						$fulfillment_status = 'unfulfilled';

						if ( $_product->is_virtual() ) {
							$fulfillment_status = 'fulfilled';
						}

						$fulfillment_status = apply_filters( 'wcpv_processing_init_fulfillment_status', $fulfillment_status, $order_item_id, $item, $this->order );

						$wpdb->query( $wpdb->prepare( $sql, $order_item_id, '_fulfillment_status', $fulfillment_status ) );
					}

					// create commission.
					$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_by_id( $vendor_id );

					$product_commission = $this->commission->calc_order_product_commission( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'], $vendor_id, $item['line_total'], $item['qty'] );

					$total_commission    = $product_commission;
					$shipping_amount     = '';
					$shipping_tax_amount = '';

					$product_settings = WC_Product_Vendors_Utils::get_product_vendor_settings( $product, $vendor_data );
					// get the per product shipping title.
					$pp_shipping_title = get_option( 'woocommerce_wcpv_per_product_settings', '' );
					$pp_shipping_title = ! empty( $pp_shipping_title ) ? $pp_shipping_title['title'] : '';

					// calculate shipping amount and shipping tax ( per product shipping ).
					$pp_shipping_method = $this->order->get_shipping_method();
					if ( ! empty( $pp_shipping_method ) && ! empty( $pp_shipping_title ) && false !== strpos( $pp_shipping_method, $pp_shipping_title ) && 'yes' === $product_settings['pass_shipping'] ) {
						$shipping_data       = $this->calc_per_product_shipping( $item );
						$shipping_amount     = $shipping_data['shipping_cost'];
						$shipping_tax_amount = $shipping_data['taxes'];
						$shipping_total      = round( $shipping_amount + $shipping_tax_amount, wc_get_rounding_precision() );

						$total_commission = round( $total_commission + $shipping_total, wc_get_rounding_precision() );
					}

					// calculate tax into total commission.
					if ( wc_tax_enabled() ) {
						$tax_total = $item['line_tax'];

						if ( 'pass-tax' === $product_settings['taxes'] ) {
							$total_commission = round( $total_commission + $tax_total, wc_get_rounding_precision() );
						} elseif ( 'split-tax' === $product_settings['taxes'] ) {
							$commission_array = WC_Product_Vendors_Utils::get_product_commission( $_product_id, $vendor_data );

							if ( 'percentage' === $commission_array['type'] ) {
								$tax_commission   = round( $tax_total * ( abs( $commission_array['commission'] ) / 100 ), wc_get_rounding_precision() );
								$total_commission = round( $total_commission + $tax_commission, wc_get_rounding_precision() );
							}
						}
					}

					$attributes = $this->get_serialize_product_variation_value( $_product );

					$order_date = $this->order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $this->order->get_date_created()->getOffsetTimestamp() ) : '';

					// initial commission status.
					$init_status = apply_filters( 'wcpv_processing_init_commission_status', 'unpaid' );

					if ( 0 === $total_commission ) {
						$init_status = 'void';
					}

					// Do we already add commission? If no, then add commission. if yes, then update commission.
					$last_commission = $this->commission->get_commission_by_order_item_id( $order_item_id, $order_id );

					if ( ! $last_commission ) {
						$last_commission_id = $this->commission->insert( $order_id, $order_item_id, $order_date, $vendor_id, $vendor_data['name'], $item['product_id'], $item['variation_id'], $item['name'], $attributes, $item['line_total'], $item['qty'], $shipping_amount, $shipping_tax_amount, $item['line_tax'], wc_format_decimal( $product_commission ), wc_format_decimal( $total_commission ), $init_status, null );
					} else {
						$this->commission->update(
							[
								'vendor_name'                 => $vendor_data['name'],
								'vendor_id'                   => $vendor_id,
								'variation_id'                => $item['variation_id'],
								'variation_attributes'        => $attributes,
								'product_name'                => $item['name'],
								'product_amount'              => $item['line_total'],
								'product_quantity'            => $item['qty'],
								'product_shipping_amount'     => $shipping_amount,
								'product_shipping_tax_amount' => $shipping_tax_amount,
								'product_tax_amount'          => $item['line_tax'],
								'product_commission_amount'   => $product_commission,
								'total_commission_amount'     => $total_commission,
							],
							$last_commission['id']
						);
					}

					$last_commission = $this->commission->get_commission_by_order_item_id( $order_item_id, $order_id );

					// check if we need to pay vendor commission instantly.
					// and commission is unpaid
					if (
						! empty( $vendor_data['instant_payout'] ) &&
						'yes' === $vendor_data['instant_payout'] &&
						! empty( $vendor_data['paypal'] ) &&
						( 'completed' === $order_status || 'processing' === $order_status || 'partial-payment' === $order_status ) &&
						( 0 != $total_commission && 'unpaid' === $last_commission['commission_status'] )
					) {
						$commission_ids[ $last_commission_id ] = absint( $last_commission_id );
					}

					// check first to see if meta has already been added.
					$check_sql  = 'SELECT `meta_value`';
					$check_sql .= " FROM {$wpdb->prefix}woocommerce_order_itemmeta";
					$check_sql .= ' WHERE `order_item_id` = %d';
					$check_sql .= ' AND `meta_key` = %s';

					$result = $wpdb->get_results( $wpdb->prepare( $check_sql, $order_item_id, '_commission_status' ) );

					if ( empty( $result ) ) {
						// add initial paid status to order item meta.
						$sql  = "INSERT INTO {$wpdb->prefix}woocommerce_order_itemmeta ( `order_item_id`, `meta_key`, `meta_value` )";
						$sql .= ' VALUES ( %d, %s, %s )';

						$wpdb->query( $wpdb->prepare( $sql, $order_item_id, '_commission_status', $init_status ) );
					}

					$customer_user = $this->order->get_user_id();

					// add vendor id to customer meta.
					if ( ! empty( $customer_user ) ) {
						WC_Product_Vendors_Utils::update_user_related_vendors( $customer_user, absint( $vendor_id ) );
					}

					// Record initial commission amount. Vendor commission amount can be change on refund.
					// In this case, we can use this value to find difference between latest and initial vendor commission.
					$this->order->update_meta_data( "_wcpv_commission_{$last_commission_id}_amount", $total_commission );

					$commission_added = true;
				}
			}

			if ( $commission_added ) {
				// flag order that commission was added.
				$this->order->update_meta_data( '_wcpv_commission_added', 'yes' );
				$this->order->save_meta_data();

				do_action( 'wcpv_commission_added', $this->order );
			}

			// process mass payment.
			if ( ! empty( $commission_ids ) ) {
				try {
					$this->commission->pay( $commission_ids );

				} catch ( Exception $e ) {
					$message = $e->getMessage();

					if ( is_a( $e, 'PayPal\Exception\PayPalConnectionException' ) ) {
						$message .= ' Error details: ' . $e->getData();
					};

					WC_Product_Vendors_Logger::log( $message );
				}
			}
		}

		return true;
	}
	/**
	 * Maybe change order status to completed if all products are fulfilled.
	 *
	 * @since 2.1.30
	 * @param int $order_id The Id of the order.
	 * @return void.
	 */
	public function maybe_complete_order( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}
		WC_Product_Vendors_Utils::maybe_update_order( $order, 'fulfilled' );
	}
	/**
	 * Add order note to state commission added for order
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param object $order
	 * @return bool
	 */
	public function add_commission_order_note( $order ) {
		$note = __( 'Commission data generated.', 'woocommerce-product-vendors' );

		$order->add_order_note( $note );

		return true;
	}

	/**
	 * Adds an action to manually create commission based on order
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param array $actions
	 * @return array $actions
	 */
	public function add_commission_order_action( $actions ) {
		$actions['wcpv_manual_create_commission'] = __( 'Generate Vendor Commission', 'woocommerce-product-vendors' );

		return $actions;
	}

	/**
	 * Calculate per product shipping and tax
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param object $item
	 * @return array
	 */
	public function calc_per_product_shipping( $item ) {
		$_tax          = new WC_Tax();
		$taxes         = array();
		$total_tax     = 0;
		$shipping_cost = 0;

		$package['destination']['country']  = $this->order->get_shipping_country();
		$package['destination']['state']    = $this->order->get_shipping_state();
		$package['destination']['postcode'] = $this->order->get_shipping_postcode();

		// get per product shipping settings
		$settings = get_option( 'woocommerce_wcpv_per_product_settings' );

		$item['data'] = ! empty( $item['variation_id'] ) ? wc_get_product( $item['variation_id'] ) : wc_get_product( $item['product_id'] );

		if ( $item['qty'] > 0 && $settings ) {
			if ( $item['data']->needs_shipping() ) {

				$rule               = false;
				$item_shipping_cost = 0;

				if ( $item['variation_id'] ) {
					$rule = WC_Product_Vendors_Utils::get_pp_shipping_matching_rule( $item['variation_id'], $package );
				}

				if ( $rule === false || is_null( $rule ) ) {
					$rule = WC_Product_Vendors_Utils::get_pp_shipping_matching_rule( $item['product_id'], $package );
				}

				if ( $rule ) {
					$item_shipping_cost += (float) $rule->rule_item_cost * $item['qty'];
					$item_shipping_cost += (float) $rule->rule_cost;
				} elseif ( $settings['cost'] === '0' || $settings['cost'] > 0 ) {
					// Use default
					$item_shipping_cost += $settings['cost'] * $item['qty'];
				} else {
					// NO default and nothing found - abort
					return;
				}

				// Fee
				$item_shipping_cost += $this->get_fee( $settings['fee'], $item_shipping_cost ) * $item['qty'];

				$shipping_cost += $item_shipping_cost;

				if ( $settings['tax_status'] === 'taxable' && 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {

					$rates      = $_tax->get_shipping_tax_rates( $item['data']->get_tax_class() );
					$item_taxes = $_tax->calc_shipping_tax( $item_shipping_cost, $rates );

					// Sum the item taxes
					foreach ( $item_taxes as $value ) {
						$total_tax = $total_tax + $value;
					}
				}
			}
		}

		return array(
			'shipping_cost' => $shipping_cost,
			'taxes'         => $total_tax,
		);
	}

	/**
	 * get_fee function.
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param mixed $fee
	 * @param mixed $total
	 * @return float
	 */
	public function get_fee( $fee, $total ) {
		/**
		 * $fee can either be an integer, float or a string limited
		 * to percentage as '5.25%'. Returns 0 otherise.
		 */
		if ( ! is_scalar( $fee ) ) {
			return 0;
		}

		/**
		 * $total must be a number.
		 */
		if ( is_numeric( $total ) && strstr( $fee, '%' ) ) {
			$fee = ( $total / 100 ) * str_replace( '%', '', $fee );
		}

		if ( ! is_numeric( $fee ) ) {
			return 0;
		}

		return $fee;
	}

	/**
	 * Maybe remove affected commission when an order item is removed.
	 *
	 * @since 2.2.5
	 * @param int $order_item_id Order item ID.
	 * @return void
	 */
	public function maybe_remove_affected_commission( $order_item_id ) {
		$order_item = WC_Order_Factory::get_order_item( $order_item_id );
		if ( ! $order_item || 'line_item' !== $order_item->get_type() ) {
			return;
		}

		$product_id = $order_item->get_product_id();
		$order_id   = $order_item->get_order_id();
		$vendor_id  = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product_id );
		// Check if product is a vendor product.
		if ( ! $product_id || ! $order_id || ! $vendor_id ) {
			return;
		}

		$affected_commission = $this->commission->get_commission_by_order_item_id( $order_item_id, $order_id );
		// Check if commission exists and is unpaid.
		if ( empty( $affected_commission ) || empty( $affected_commission['id'] ) || 'unpaid' !== $affected_commission['commission_status'] ) {
			return;
		}

		// Delete affected commission.
		$this->commission->delete( $affected_commission['id'] );
	}

	/**
	 * Remove affected commissions when an order gets deleted.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @access public
	 * @since 2.1.27
	 * @version 2.1.27
	 */
	public function remove_affected_commissions( $order_id ) {
		if ( ! WC_Product_Vendors_COT_Compatibility::is_order( $order_id ) ) {
			return;
		}

		$this->commission->delete_by_order_id( $order_id );
	}

	/**
	 * On payment complete do additional processes.
	 *
	 * @since 2.1.35
	 * @param int $order_id ID of the order.
	 * @return void
	 */
	public function payment_complete( $order_id ) {
		WC_Product_Vendors_Utils::clear_reports_transients();
	}


	/**
	 * Update commission data when refunds performed.
	 *
	 * @since 2.1.74
	 *
	 * @param int $order_id Order ID.
	 * @param int $refund_id Refund ID.
	 *
	 * @return void
	 */
	public function process_order_refund( $order_id, $refund_id ) {
		global $wpdb;

		$order  = wc_get_order( $order_id );
		$refund = wc_get_order( $refund_id );

		if (
			! is_a( $order, 'WC_Order' ) ||
			! is_a( $refund, 'WC_Order_Refund' ) ||
			0 >= $refund->get_amount() ||
			! ( $items = $order->get_items( 'line_item' ) )
		) {
			return;
		}

		// Order should already have a commission for vendor.
		$check_commission_added = $order->get_meta( '_wcpv_commission_added', true );
		if ( 'yes' !== $check_commission_added ) {
			return;
		}

		$is_fully_refunded = 0 === count( $refund->get_items() ) && 0 == $order->get_remaining_refund_amount();

		foreach ( $items as $order_item_id => $item ) {
			$product = wc_get_product( $item['product_id'] );

			if ( ! is_object( $product ) ) {
				continue;
			}

			// Vendor should have a commission for a order item.
			$last_commission_id = $this->commission->get_commission_id_by_order_item_id( $order_item_id, $order_id );
			if ( empty( $last_commission_id ) ) {
				continue;
			}

			// Product must have a valid vendor.
			$vendor_id = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product->get_id() );
			if ( ! $vendor_id ) {
				continue;
			}

			if ( $is_fully_refunded ) {
				$this->commission_fully_refunded( $order, $item, $refund, $last_commission_id );
				continue;
			}

			$_product_id             = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
			$_product                = wc_get_product( $_product_id );
			$vendor_data             = WC_Product_Vendors_Utils::get_vendor_data_by_id( $vendor_id );
			$item_total_tax_refunded = WC_Product_Vendors_Utils:: get_total_tax_refunded_line_item( $item, $order );

			$item_quantity_with_refunds   = $item->get_quantity() + $order->get_qty_refunded_for_item( $item->get_id() );
			$item_total_including_refunds = $item['line_total'] - $order->get_total_refunded_for_item( $item->get_id() );
			$item_taxes_including_refunds = $item['line_tax'] - $item_total_tax_refunded;

			$product_commission = $this->commission->calc_order_product_commission(
				$_product->get_id(),
				$vendor_id,
				$item_total_including_refunds,
				$item_quantity_with_refunds
			);

			$item_total_commission = $product_commission;
			$shipping_amount       = '';
			$shipping_tax_amount   = '';

			$product_settings = WC_Product_Vendors_Utils::get_product_vendor_settings( $product, $vendor_data );

			// get the per product shipping title.
			$pp_shipping_title = get_option( 'woocommerce_wcpv_per_product_settings', '' );
			$pp_shipping_title = ! empty( $pp_shipping_title ) ? $pp_shipping_title['title'] : '';

			// calculate shipping amount and shipping tax ( per product shipping ).
			$pp_shipping_method = $order->get_shipping_method();
			if (
				! empty( $pp_shipping_method ) &&
				! empty( $pp_shipping_title ) &&
				false !== strpos( $pp_shipping_method, $pp_shipping_title ) &&
				'yes' === $product_settings['pass_shipping']
			) {
				$shipping_data = WC_Product_Vendors_Utils::get_total_order_item_shipping_charges_with_refund( $item );

				$shipping_amount     = $shipping_data['shipping_cost'];
				$shipping_tax_amount = $shipping_data['taxes'];
				$shipping_total      = round(
					$shipping_amount + $shipping_tax_amount,
					wc_get_rounding_precision()
				);

				$item_total_commission = round(
					$item_total_commission + $shipping_total,
					wc_get_rounding_precision()
				);
			}

			// calculate tax into total commission.
			if ( wc_tax_enabled() ) {
				$tax_total = $item_taxes_including_refunds;

				if ( 'pass-tax' === $product_settings['taxes'] ) {
					$item_total_commission = round(
						$item_total_commission + $tax_total,
						wc_get_rounding_precision()
					);
				} elseif ( 'split-tax' === $product_settings['taxes'] ) {
					$commission_array = WC_Product_Vendors_Utils::get_product_commission(
						$_product_id,
						$vendor_data
					);

					if ( 'percentage' === $commission_array['type'] ) {
						$tax_commission        = round(
							$tax_total * ( abs( $commission_array['commission'] ) / 100 ),
							wc_get_rounding_precision()
						);
						$item_total_commission = round(
							$item_total_commission + $tax_commission,
							wc_get_rounding_precision()
						);
					}
				}
			}

			$attributes = $this->get_serialize_product_variation_value( $_product );

			$wpdb->update(
				WC_PRODUCT_VENDORS_COMMISSION_TABLE,
				[
					'variation_attributes'        => $attributes,
					'variation_id'                => $item['variation_id'],
					'product_amount'              => $item_total_including_refunds,
					'product_quantity'            => $item_quantity_with_refunds,
					'product_shipping_amount'     => $shipping_amount,
					'product_shipping_tax_amount' => $shipping_tax_amount,
					'product_tax_amount'          => $item_taxes_including_refunds,
					'product_commission_amount'   => wc_format_decimal( $product_commission ),
					'total_commission_amount'     => wc_format_decimal( $item_total_commission )
				],
				[ 'id' => $last_commission_id ],
				[ '%s', '%d' ],
				[ 'id' => '%d' ]
			);

			$order->add_order_note(
				sprintf(
					esc_html__(
						'Vendor commission (#%4$s) for "%1$s" updated to %2$s with refund #%3$s',
						'woocommerce-product-vendors'
					),
					$item->get_name(),
					wc_price( wc_format_decimal( $item_total_commission ) ),
					$refund->get_id(),
					$last_commission_id
				)
			);
		}
	}

	/**
	 * Should return serialize value of product variation.
	 *
	 * @since 2.1.74
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @return string
	 */
	private function get_serialize_product_variation_value( WC_Product $product ) {
		$data = '';
		if ( 'variation' === $product->get_type() ) {
			// get variation attributes.
			$variation_attributes = $product->get_variation_attributes();

			if ( ! empty( $variation_attributes ) ) {
				$data = [];

				foreach ( $variation_attributes as $name => $value ) {
					$name = ucfirst( str_replace( 'attribute_', '', $name ) );

					$data[ $name ] = $value;
				}

				$data = maybe_serialize( $data );
			}
		}

		return $data;
	}

	/**
	 * Should fully refund vendor commission.
	 *
	 * @since 2.1.74
	 *
	 * @return void
	 */
	private function commission_fully_refunded(
		WC_Order $order,
		WC_Order_Item $item,
		WC_Order_Refund $refund,
		int $commission_id
	) {
		global $wpdb;

		$wpdb->update(
			WC_PRODUCT_VENDORS_COMMISSION_TABLE,
			[
				'variation_id'                => $item['variation_id'],
				'product_amount'              => 0,
				'product_quantity'            => 0,
				'product_shipping_amount'     => 0,
				'product_shipping_tax_amount' => 0,
				'product_tax_amount'          => 0,
				'product_commission_amount'   => 0,
				'total_commission_amount'     => 0
			],
			[ 'id' => $commission_id ],
			[ '%s', '%d' ],
			[ 'id' => '%d' ]
		);

		$order->add_order_note(
			sprintf(
				esc_html__(
					'Vendor commission (#%3$s) for "%1$s" is fully refunded with refund #%2$s',
					'woocommerce-product-vendors'
				),
				$item->get_name(),
				$refund->get_id(),
				$commission_id
			)
		);
	}
}
