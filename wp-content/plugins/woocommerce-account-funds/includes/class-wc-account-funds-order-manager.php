<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Account_Funds_Order_Manager
 */
class WC_Account_Funds_Order_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_before_checkout_process', array( $this, 'force_registration_during_checkout' ), 10 );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'woocommerce_checkout_update_order_meta' ) );
		add_filter( 'woocommerce_order_item_needs_processing', array( $this, 'order_item_needs_processing' ), 10, 2 );
		add_action( 'woocommerce_payment_complete', array( $this, 'maybe_remove_funds' ) );
		add_action( 'woocommerce_order_status_processing', array( $this, 'maybe_remove_funds' ) );
		add_action( 'woocommerce_order_status_on-hold', array( $this, 'maybe_remove_funds' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'maybe_remove_funds' ) );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'maybe_restore_funds' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'maybe_increase_funds' ) );
		add_filter( 'woocommerce_get_order_item_totals', array( $this, 'get_order_item_totals' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_meta' ), 10, 3 );
		add_filter( 'woocommerce_order_item_product', array( $this, 'order_item_product' ), 10, 2 );
		add_action( 'woocommerce_order_refunded', array( $this, 'maybe_remove_topup_funds' ), 10, 2 );

		add_action( 'woocommerce_admin_order_totals_after_tax', array( $this, 'admin_order_account_funds' ) );
		add_action( 'woocommerce_order_after_calculate_totals', array( $this, 'remove_funds_from_recalculation' ), 10, 2 );
	}

	/**
	 * Adjust total to exclude amount paid with funds when doing order save
	 * for recalculation. Note that because the recalculation doesn't use
	 * WC_Order::get_total we have to use another hook for this.
	 *
	 * @version 2.1.10
	 *
	 * @param bool     $and_taxes Whether taxes are included.
	 * @param WC_Order $order Order object.
	 */
	public function remove_funds_from_recalculation( $and_taxes, $order ) {
		$_funds_used = get_post_meta( version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(), '_funds_used', true );

		// Calling `$order->get_total()` means firing again woocommerce_order_get_total
		// or woocommerce_order_amount_total hook. We need to remove the filter
		// temporarily.
		//
		// @see https://github.com/woocommerce/woocommerce-account-funds/issues/75.
		self::remove_order_total_filter_adjustment();

		$total = floatval( $order->get_total() ) - floatval( $_funds_used );
		$order->set_total( round( $total, wc_get_price_decimals() ) );

		self::add_order_total_filter_adjustment();
	}

	/**
	 * Try to remove user funds for a refund order (if the order contains any top up products)
	 * 
	 * @param  int $order_id
	 * @param  int $refund_id
	 */
	public function maybe_remove_topup_funds( $order_id, $refund_id ) {
		$order       = wc_get_order( $order_id );
		$customer_id = $order->get_user_id();

		if ( ! $customer_id ) {
			return;
		}

		$refund     = new WC_Order_Refund( $refund_id );
		$top_up_sum = 0;

		foreach ( $order->get_items() as $id => $item ) {
			if ( 'yes' === $item->get_meta( '_top_up_product' ) ) {
				$top_up_sum += floatval( $item->get_meta( '_top_up_amount' ) );
			}
		}

		if ( 0 == $top_up_sum ) {
			return;
		}

		// Calculate a percentage of the refunded total over the order total, multiplied by top up amount.
		$funds = ( ( -1 * $refund->get_total() ) / $order->get_total() ) * $top_up_sum;

		WC_Account_Funds::remove_funds( $customer_id, $funds );

		$order->add_order_note( sprintf( __( 'Removed %s funds from user #%d', 'woocommerce-account-funds' ), wc_price( $funds ), $customer_id ) );
	}

	/**
	 * Try to remove user funds (if not already removed)
	 * 
	 * @param  int $order_id
	 */
	public function maybe_remove_funds( $order_id ) {
		if ( null !== WC()->session ) {
			WC()->session->set( 'use-account-funds', false );
			WC()->session->set( 'used-account-funds', false );
		}

		$order       = wc_get_order( $order_id );
		$customer_id = $order->get_user_id();

		if ( $customer_id && ! get_post_meta( $order_id, '_funds_removed', true ) ) {
			if ( $funds = get_post_meta( $order_id, '_funds_used', true ) ) {
				WC_Account_Funds::remove_funds( $customer_id, $funds );
				$order->add_order_note( sprintf( __( 'Removed %s funds from user #%d', 'woocommerce-account-funds' ), wc_price( $funds ), $customer_id ) );
			}
			update_post_meta( $order_id, '_funds_removed', 1 );
		}
	}

	/**
	 * Remove user funds when an order is created
	 * 
	 * @param int $order_id
	 */
	public function woocommerce_checkout_update_order_meta( $order_id ) {
		if ( ! WC_Account_Funds_Cart_Manager::using_funds() ) {
			return;
		}

		$used_funds = WC_Account_Funds_Cart_Manager::used_funds_amount();
		update_post_meta( $order_id, '_funds_used', $used_funds );
		update_post_meta( $order_id, '_funds_removed', 0 ); // The meta may exist if it's a failed subscription renewal.
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
	 * Restore user funds when an order is cancelled
	 * 
	 * @param  int $order_id
	 */
	public function maybe_restore_funds( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $funds = get_post_meta( $order_id, '_funds_used', true ) ) {
			WC_Account_Funds::add_funds( $order->get_user_id(), $funds );
			$order->add_order_note( sprintf( __( 'Restored %s funds to user #%d', 'woocommerce-account-funds' ), wc_price( $funds ), $order->get_user_id() ) );
		}
	}

	/**
	 * See if an order contains a deposit
	 * 
	 * @param  int $order_id
	 * @return bool
	 */
	public static function order_contains_deposit( $order_id ) {
		$order           = wc_get_order( $order_id );
		$deposit_product = false;

		foreach ( $order->get_items() as $item ) {
			$product = $order->get_product_from_item( $item );

			if ( $product->is_type( 'deposit' ) || $product->is_type( 'topup' ) ) {
				$deposit_product = true;
				break;
			}
		}

		return $deposit_product;
	}

	/**
	 * Handle order complete events
	 * 
	 * @since 1.0.0
	 * @version 2.1.6
	 * @param  int $order_id
	 */
	public function maybe_increase_funds( $order_id ) {
		$order          = wc_get_order( $order_id );
		$items          = $order->get_items();
		$customer_id    = $order->get_user_id();

		if ( $customer_id && ! get_post_meta( $order_id, '_funds_deposited', true ) ) {
			foreach ( $items as $item ) {
				$product = $order->get_product_from_item( $item );
				if ( ! is_a( $product, 'WC_Product' ) ) {
					continue;
				}

				$funds = 0;
				if ( $product->is_type( 'deposit' ) || $product->is_type( 'topup' ) ) {
					$funds = $item['line_subtotal'];
				} else {
					continue;
				}

				WC_Account_Funds::add_funds( $customer_id, $funds );

				$order->add_order_note( sprintf( __( 'Added %s funds to user #%d', 'woocommerce-account-funds' ), wc_price( $funds ), $customer_id ) );

				update_post_meta( $order_id, '_funds_deposited', 1 );
			}
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
		$order_id   = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();
		$funds_used = get_post_meta( $order_id, '_funds_used', true );

		if ( $funds_used ) {
			$index = array_search( 'order_total', array_keys( $rows ) );
			$rows  = array_merge(
				array_slice( $rows, 0, $index ),
				array(
					'funds_used' => array(
						'label' => __( 'Funds Used:', 'woocommerce-account-funds' ),
						'value' => '-' . wc_price( $funds_used ),
					),
				),
				array_slice( $rows, $index )
			);
		}

		return $rows;
	}

	/**
	 * Adjust total to include amount paid with funds
	 *
	 * @version 2.1.3
	 *
	 * @param float    $total Order total.
	 * @param WC_Order $order Order object.
	 *
	 * @return float Order total.
	 */
	public static function adjust_total_to_include_funds( $total, $order ) {
		global $wp;

		// Don't interfere with total while paying for order.
		if ( is_checkout() || ! empty( $wp->query_vars['order-pay'] ) || ( ! empty( $_POST ) && ! empty( $_POST['payment_status'] ) ) ) {
			return $total;
		}

		$_funds_used = get_post_meta( version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(), '_funds_used', true );

		// Calling `$order->get_total()` means firing again woocommerce_order_get_total
		// or woocommerce_order_amount_total hook. We need to remove the filter
		// temporarily.
		//
		// @see https://github.com/woocommerce/woocommerce-account-funds/issues/75.
		self::remove_order_total_filter_adjustment();

		$total = floatval( $order->get_total() ) + floatval( $_funds_used );

		self::add_order_total_filter_adjustment();

		return $total;
	}

	/**
	 * Add the filter to order total that will add amount of funds being used.
	 *
	 * @since 2.1.7
	 * @version 2.1.7
	 */
	protected static function add_order_total_filter_adjustment() {
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			add_filter( 'woocommerce_order_get_total', array( __CLASS__, 'adjust_total_to_include_funds' ), 10, 2 );
		} else {
			add_filter( 'woocommerce_order_amount_total', array( __CLASS__, 'adjust_total_to_include_funds' ), 10, 2 );
		}
	}

	/**
	 * Remove the filter to order total that will add amount of funds being used.
	 *
	 * @since 2.1.7
	 * @version 2.1.7
	 */
	protected static function remove_order_total_filter_adjustment() {
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			remove_filter( 'woocommerce_order_get_total', array( __CLASS__, 'adjust_total_to_include_funds' ), 10, 2 );
		} else {
			remove_filter( 'woocommerce_order_amount_total', array( __CLASS__, 'adjust_total_to_include_funds' ), 10, 2 );
		}
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
	 * @version 2.1.3
	 *
	 * @param bool|WC_Product       $product Product object. False otherwise.
	 * @param WC_Order_Item_Product $item    Order item product.
	 *
	 * @return WC_Product Product object.
	 */
	public function order_item_product( $product, $item ) {
		if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			if ( 'yes' === $item->get_meta( '_top_up_product', true ) ) {
				$product = new WC_Product_Topup( 0 );
				WC_Data_Store::load( 'product-topup' )->read( $product );
			}
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
		$funds_used = floatval( get_post_meta( $order_id, '_funds_used', true ) );

		if ( 0 >= $funds_used ) {
			return;
		}
		?>
		<tr>
			<td class="label"><?php _e( 'Funds Used', 'woocommerce-account-funds' ); ?>:</td>
			<td width="1%"></td>
			<td class="total"><?php echo '-' . wc_price( $funds_used ); ?></td>
		</tr>
		<?php
	}

	/**
	 * Add rows in edit order screen to display 'Funds Used' and 'Order Total
	 * after Funds Used'.
	 *
	 * @since 2.1.7
	 * @deprecated 2.3.0
	 *
	 * @param int $order_id Order ID.
	 */
	public function add_funds_used_after_order_total( $order_id ) {
		_deprecated_function( __FUNCTION__, '2.3.0' );

		$funds_used = floatval( get_post_meta( $order_id, '_funds_used', true ) );
		if ( $funds_used <= 0 ) {
			return;
		}

		$order = wc_get_order( $order_id );
		?>
		<tr>
			<td class="label"><?php _e( 'Funds Used', 'woocommerce-account-funds' ); ?>:</td>
			<td width="1%"></td>
			<td class="total">
				<?php echo wc_price( $funds_used ); ?>
			</td>
		</tr>
		<tr>
			<td class="label"><?php _e( 'Order Total after Funds Used', 'woocommerce-account-funds' ); ?>:</td>
			<td width="1%"></td>
			<td class="total">
				<?php
				self::remove_order_total_filter_adjustment();
				echo wc_price( $order->get_total() );
				self::add_order_total_filter_adjustment();
				?>
			</td>
		</tr>
		<?php
	}
}

new WC_Account_Funds_Order_Manager();
