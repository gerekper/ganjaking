<?php
/**
 * Account funds: Order manager.
 *
 * @package WC_Account_Funds
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Account_Funds_Order_Manager
 */
class WC_Account_Funds_Order_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_before_checkout_process', array( $this, 'force_registration_during_checkout' ), 10 );
		add_action( 'woocommerce_checkout_create_order', array( $this, 'create_order' ) );
		add_filter( 'woocommerce_order_item_needs_processing', array( $this, 'order_item_needs_processing' ), 10, 2 );
		add_action( 'woocommerce_payment_complete', array( $this, 'maybe_remove_funds' ) );
		add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 20, 3 );
		add_filter( 'woocommerce_order_get_total', array( $this, 'adjust_total_to_include_funds' ), 10, 2 );
		add_filter( 'woocommerce_get_order_item_totals', array( $this, 'get_order_item_totals' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_meta' ), 10, 3 );
		add_filter( 'woocommerce_order_item_product', array( $this, 'order_item_product' ), 10, 2 );
		add_action( 'woocommerce_order_refunded', array( $this, 'maybe_remove_topup_funds' ), 10, 2 );

		add_action( 'woocommerce_admin_order_totals_after_tax', array( $this, 'admin_order_account_funds' ) );
		add_action( 'woocommerce_order_after_calculate_totals', array( $this, 'after_calculate_totals' ), 10, 2 );

		add_filter( 'wcs_subscription_meta_query', array( $this, 'copy_order_meta_query' ), 10, 3 );
	}

	/**
	 * Processes the order before saving it.
	 *
	 * @since 2.4.0
	 *
	 * @param WC_Order $order Order object.
	 */
	public function create_order( $order ) {
		if ( ! WC_Account_Funds_Cart_Manager::using_funds() ) {
			return;
		}

		$used_funds = WC_Account_Funds_Cart_Manager::used_funds_amount();

		$order->update_meta_data( '_funds_used', $used_funds );
		$order->update_meta_data( '_funds_removed', 0 );
		$order->update_meta_data( '_funds_version', WC_ACCOUNT_FUNDS_VERSION );
	}

	/**
	 * Processes the order after calculate totals.
	 *
	 * @since 2.4.0
	 *
	 * @param bool     $and_taxes Whether taxes are included.
	 * @param WC_Order $order     Order object.
	 */
	public function after_calculate_totals( $and_taxes, $order ) {
		$funds_used = (float) $order->get_meta( '_funds_used' );

		if ( 0 >= $funds_used ) {
			return;
		}

		// Deduct funds from the Order total on partial payments.
		if ( 'accountfunds' !== $order->get_payment_method() ) {
			$total = $order->get_total( 'edit' ) - $funds_used;

			$order->set_total( $total );
		}

		// Update the funds version.
		$order->update_meta_data( '_funds_version', WC_ACCOUNT_FUNDS_VERSION );
	}

	/**
	 * Try to remove user funds for a refund order (if the order contains any top up products)
	 *
	 * @param int $order_id  Order ID.
	 * @param int $refund_id Refund ID.
	 */
	public function maybe_remove_topup_funds( $order_id, $refund_id ) {
		$order       = wc_get_order( $order_id );
		$customer_id = $order->get_customer_id();

		if ( ! $customer_id ) {
			return;
		}

		$refund     = new WC_Order_Refund( $refund_id );
		$items      = $order->get_items();
		$top_up_sum = 0;

		foreach ( $items as $id => $item ) {
			if ( 'yes' === $item->get_meta( '_top_up_product' ) ) {
				$top_up_sum += (float) $item->get_meta( '_top_up_amount' );
			}
		}

		if ( 0 >= $top_up_sum ) {
			return;
		}

		// Calculate a percentage of the refunded total over the order total, multiplied by top up amount.
		$funds = ( ( -1 * $refund->get_total() ) / $order->get_total() ) * $top_up_sum;

		WC_Account_Funds_Manager::decrease_user_funds( $customer_id, $funds );

		$order->add_order_note(
			sprintf(
				/* translators: 1: Funds amount, 2: Customer ID */
				__( 'Removed %1$s funds from user %2$s', 'woocommerce-account-funds' ),
				wc_account_funds_format_order_price( $order, $funds ),
				$customer_id
			)
		);
	}

	/**
	 * Removes the funds applied to the Order from the customer account.
	 *
	 * @param int $order_id Order Id.
	 */
	public function maybe_remove_funds( $order_id ) {
		if ( null !== WC()->session ) {
			WC()->session->set( 'use-account-funds', false );
			WC()->session->set( 'used-account-funds', false );
		}

		$order       = wc_get_order( $order_id );
		$customer_id = $order->get_customer_id();

		if ( $customer_id && ! wc_string_to_bool( $order->get_meta( '_funds_removed' ) ) ) {
			$funds_used = (float) $order->get_meta( '_funds_used' );

			if ( $funds_used ) {
				WC_Account_Funds_Manager::decrease_user_funds( $customer_id, $funds_used );
				$order->update_meta_data( '_funds_removed', 1 );
				$order->save_meta_data();

				$order->add_order_note(
					sprintf(
						/* translators: 1: Funds amount, 2: Customer ID */
						__( 'Removed %1$s funds from user #%2$s', 'woocommerce-account-funds' ),
						wc_account_funds_format_order_price( $order, $funds_used ),
						$customer_id
					)
				);
			}
		}
	}

	/**
	 * Filters if the item needs to be processed before completing the order.
	 *
	 * @since 2.1.17
	 *
	 * @param bool       $needs_processing Needs processing?.
	 * @param WC_Product $product          Product object.
	 * @return bool
	 */
	public function order_item_needs_processing( $needs_processing, $product ) {
		if ( $product->is_type( 'deposit' ) || $product->is_type( 'topup' ) ) {
			$needs_processing = false;
		}

		return $needs_processing;
	}

	/**
	 * Processes a change in the Order status.
	 *
	 * @since 2.3.10
	 *
	 * @param int    $order_id Order ID.
	 * @param string $from     The old order status.
	 * @param string $to       The new order status.
	 */
	public function order_status_changed( $order_id, $from, $to ) {
		if ( in_array( $to, array( 'processing', 'completed' ), true ) ) {
			$this->maybe_increase_funds( $order_id );
			$this->maybe_remove_funds( $order_id );
		} elseif ( 'on-hold' === $to ) {
			$this->maybe_remove_funds( $order_id );
		} elseif ( 'cancelled' === $to ) {
			$this->maybe_restore_funds( $order_id );
		}
	}

	/**
	 * Returns the funds applied to the order to the customer's account.
	 *
	 * @param int $order_id Order ID.
	 */
	public function maybe_restore_funds( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$funds_used = (float) $order->get_meta( '_funds_used' );

		if ( ! $funds_used ) {
			return;
		}

		// Unlock the funds.
		if ( ! wc_string_to_bool( $order->get_meta( '_funds_removed' ) ) ) {
			$order->update_meta_data( '_funds_removed', 1 );
			$order->update_meta_data( '_funds_refunded', $funds_used );
			$order->save_meta_data();
		} else {
			$customer_id = $order->get_customer_id();
			$funds       = min(
				( $funds_used - (float) $order->get_meta( '_funds_refunded' ) ), // The remaining funds.
				( $order->get_total() - $order->get_total_refunded() ) // The remaining order total.
			);

			if ( $customer_id && $funds > 0 ) {
				WC_Account_Funds_Manager::increase_user_funds( $customer_id, $funds );

				$order->update_meta_data( '_funds_refunded', $funds_used );
				$order->save_meta_data();

				$order->add_order_note(
					sprintf(
						/* translators: 1: Funds amount, 2: Customer ID */
						__( 'Restored %1$s funds to user #%2$s', 'woocommerce-account-funds' ),
						wc_account_funds_format_order_price( $order, $funds ),
						$customer_id
					)
				);
			}
		}
	}

	/**
	 * See if an order contains a deposit
	 *
	 * @param int $order_id Order ID.
	 * @return bool
	 */
	public static function order_contains_deposit( $order_id ) {
		$order           = wc_get_order( $order_id );
		$deposit_product = false;

		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();

			if ( $product && ( $product->is_type( 'deposit' ) || $product->is_type( 'topup' ) ) ) {
				$deposit_product = true;
				break;
			}
		}

		return $deposit_product;
	}

	/**
	 * Handle order complete events.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id Order ID.
	 */
	public function maybe_increase_funds( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order || ! $order->get_customer_id() || wc_string_to_bool( $order->get_meta( '_funds_deposited' ) ) ) {
			return;
		}

		$customer_id = $order->get_customer_id();
		$items       = $order->get_items();

		foreach ( $items as $item ) {
			$product = $item->get_product();

			if ( ! $product || ! $product->is_type( array( 'deposit', 'topup' ) ) ) {
				continue;
			}

			$funds = ( $product->is_type( 'topup' ) ? $item['line_subtotal'] : $product->get_regular_price() * $item->get_quantity() );

			WC_Account_Funds_Manager::increase_user_funds( $customer_id, $funds );
			$order->update_meta_data( '_funds_deposited', 1 );
			$order->save_meta_data();

			$order->add_order_note(
				sprintf(
					/* translators: 1: Funds amount, 2: Customer ID */
					__( 'Added %1$s funds to user #%2$s', 'woocommerce-account-funds' ),
					wc_account_funds_format_order_price( $order, $funds ),
					$customer_id
				)
			);
		}
	}

	/**
	 * Moves Funds used row above the order total row in order details
	 *
	 * @version 2.3.0
	 *
	 * @param array    $rows  Set of items for order details.
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	public function get_order_item_totals( $rows, $order ) {
		if ( 'accountfunds' === $order->get_payment_method() ) {
			return $rows;
		}

		$funds_used = (float) $order->get_meta( '_funds_used' );

		if ( $funds_used ) {
			$index = array_search( 'payment_method', array_keys( $rows ) );
			$rows  = array_merge(
				array_slice( $rows, 0, $index ),
				array(
					'funds_used' => array(
						'label' => __( 'Funds Used:', 'woocommerce-account-funds' ),
						'value' => '-' . wc_account_funds_format_order_price( $order, $funds_used ),
					),
				),
				array_slice( $rows, $index )
			);
		}

		return $rows;
	}

	/**
	 * Adjust total to include amount paid with funds.
	 *
	 * @since 2.0.0
	 *
	 * @param float    $total Order total.
	 * @param WC_Order $order Order object.
	 * @return float
	 */
	public static function adjust_total_to_include_funds( $total, $order ) {
		global $wp;

		// Don't interfere with total while paying for order.
		if ( is_checkout() || ! empty( $wp->query_vars['order-pay'] ) || ( ! empty( $_POST ) && ! empty( $_POST['payment_status'] ) ) ) {
			return $total;
		}

		$funds_used = (float) $order->get_meta( '_funds_used' );

		if ( $funds_used > 0 && 'accountfunds' === $order->get_payment_method() && ! $order->get_meta( '_funds_version' ) ) {
			$total = floatval( $order->get_total( 'edit' ) ) + $funds_used;
		}

		return $total;
	}

	/**
	 * Forces account registration during checkout for deposit prducts
	 */
	public function force_registration_during_checkout() {
		if ( WC_Account_Funds_Cart_Manager::cart_contains_deposit() && ! is_user_logged_in() ) {
			$_POST['createaccount'] = 1;
		}
	}

	/**
	 * Store top-up info.
	 *
	 * This meta data only applies to store with WC >= 3.0.
	 *
	 * @since 2.1.3
	 *
	 * @version 2.1.3
	 *
	 * @param WC_Order_Item $item          Order item object.
	 * @param string        $cart_item_key Cart item key.
	 * @param array         $values        Cart item values.
	 */
	public function add_order_item_meta( $item, $cart_item_key, $values ) {
		if ( ! empty( $values['top_up_amount'] ) ) {
			$item->add_meta_data( '_top_up_amount', $values['top_up_amount'], true );
			$item->add_meta_data( '_top_up_product', 'yes', true );
		}
	}

	/**
	 * Update order item product with instance of WC_Product_Topup.
	 *
	 * Data store introduced in WC 3.0.0 validates item product. AF pre 2.1.3
	 * with WC < 3.0 stores product item ID as page ID of myaccount.
	 *
	 * @since 2.1.3
	 *
	 * @param bool|WC_Product       $product Product object. False otherwise.
	 * @param WC_Order_Item_Product $item    Order item product.
	 *
	 * @return WC_Product Product object.
	 */
	public function order_item_product( $product, $item ) {
		if ( 'yes' === $item->get_meta( '_top_up_product', true ) ) {
			$product = new WC_Product_Topup( 0 );
			WC_Data_Store::load( 'product-topup' )->read( $product );
		}

		return $product;
	}

	/**
	 * Outputs the funds used in the edit-order screen.
	 *
	 * @since 2.3.0
	 *
	 * @param int $order_id The order ID.
	 */
	public function admin_order_account_funds( $order_id ) {
		$order      = wc_get_order( $order_id );
		$funds_used = (float) $order->get_meta( '_funds_used' );

		if ( 0 >= $funds_used || 'accountfunds' === $order->get_payment_method() ) {
			return;
		}
		?>
		<tr>
			<td class="label"><?php _e( 'Funds Used', 'woocommerce-account-funds' ); ?>:</td>
			<td width="1%"></td>
			<td class="total"><?php echo '-' . wc_account_funds_format_order_price( $order, $funds_used ); ?></td>
		</tr>
		<?php
	}

	/**
	 * Filters the meta query used for copying the metadata from a subscription to an order and vice-versa.
	 *
	 * @since 2.3.5
	 *
	 * @param string   $meta_query The meta query string.
	 * @param WC_Order $to_order   The order to copy the metadata.
	 * @param WC_Order $from_order The order from which the metadata is copied.
	 * @return string
	 */
	public function copy_order_meta_query( $meta_query, $to_order, $from_order ) {
		// Copying the metadata from an order to a subscription.
		if ( $to_order instanceof WC_Subscription ) {
			// Exclude funds metadata.
			$meta_query .= " AND `meta_key` NOT LIKE '_funds_%%'";
		}

		return $meta_query;
	}
}

new WC_Account_Funds_Order_Manager();
