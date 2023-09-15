<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package   WC_Pre_Orders/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pre-Orders Admin Orders class.
 */
class WC_Pre_Orders_Admin_Orders {

	/**
	 * Initialize the admin order actions.
	 */
	public function __construct() {
		// Add pre-order emails to list of available emails to resend.
		add_filter( 'woocommerce_order_actions', array( $this, 'maybe_allow_resend_of_pre_order_emails' ) ); // >= 3.2

		// Hook to make sure pre order is properly set up when added through admin.
		add_action( 'save_post', array( $this, 'check_manual_order_for_pre_order_products' ), 10, 1 );

		if ( WC_Pre_Orders::is_hpos_enabled() && is_admin() ) {
			add_action( 'woocommerce_update_order', array( $this, 'hpos_check_manual_order_for_pre_order_products' ) );
		}

		//Adds the filter on WooCommerce -> Orders page
		add_action( 'admin_init', array( $this, 'add_order_page_filters' ) );
	}

	/**
	 * Add pre-order emails to the list of order emails that can be resent, based on the pre-order status.
	 *
	 * @param  array $available_emails Simple array of WC_Email class IDs that can be resent.
	 *
	 * @return array
	 */
	public function maybe_allow_resend_of_pre_order_emails( $available_emails ) {
		global $theorder;

		$emails = array();

		if ( WC_Pre_Orders_Order::order_contains_pre_order( $theorder ) ) {

			$emails[] = 'wc_pre_orders_pre_ordered';

			$pre_order_status = WC_Pre_Orders_Order::get_pre_order_status( $theorder );

			if ( 'cancelled' === $pre_order_status ) {
				$emails[] = 'wc_pre_orders_pre_order_cancelled';
			}

			if ( 'completed' === $pre_order_status ) {
				$emails[] = 'wc_pre_orders_pre_order_available';
			}
		}

		$mailer     = WC()->mailer();
		$mails      = $mailer->get_emails();
		$new_emails = array();

		foreach ( $mails as $mail ) {
			if ( in_array( $mail->id, $emails ) && 'no' !== $mail->enabled ) {
				/* translators: %s: email title */
				$new_emails[ 'send_email_' . esc_attr( $mail->id ) ] = sprintf( __( 'Resend %s', 'woocommerce-pre-orders' ), esc_html( $mail->title ) );
			}
		}

		$emails = $new_emails;

		return array_merge( $available_emails, $emails );
	}

	/**
	 * Marks the order as being a pre order if it contains pre order products in
	 * case an order gets added manually from the administration panel.
	 *
	 * @param int $order_id ID of the newly saved order.
	 *
	 * @since 1.4.10
	 * @version 1.5.3
	 */
	public function check_manual_order_for_pre_order_products( $order_id ) {

		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'update-post_' . $order_id ) ) {
			return;
		}

		// Make sure we are in the administration panel and we're saving an order.
		if ( ! is_admin() || ! isset( $_POST['post_type'] ) || 'shop_order' !== $_POST['post_type'] ) {
			return;
		}

		return $this->maybe_set_pre_order_for_pre_order_products( $order_id );
	}

	/**
	 * Marks the order as being a pre order if it contains pre order products in
	 * case an order gets added manually from the administration panel. (for HPOS only)
	 *
	 * @param int $order_id ID of the newly saved order.
	 *
	 * @since 1.9.0
	 */
	public function hpos_check_manual_order_for_pre_order_products( $order_id ) {
		if ( ! is_admin() || ! isset( $_REQUEST['page'] ) || 'wc-orders' !== sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) ) {
			return;
		}

		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'update-order_' . $order_id ) ) {
			return;
		}

		return $this->maybe_set_pre_order_for_pre_order_products( $order_id );
	}

	/**
	 * Set Manual order as pre-order if it contains pre-order products.
	 *
	 * @param int $order_id ID of the newly saved order.
	 *
	 * @since 1.9.0
	 */
	public function maybe_set_pre_order_for_pre_order_products( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		// Check if the order hasn't been processed already.
		if ( WC_Pre_Orders_Order::order_contains_pre_order( $order ) ) {
			return;
		}

		foreach ( $order->get_items( 'line_item' ) as $item ) {
			$product = null;
			if ( is_array( $item ) && isset( $item['item_meta']['_product_id'][0] ) ) {
				$product = wc_get_product( $item['item_meta']['_product_id'][0] );
			} elseif ( is_object( $item ) && is_callable( array( $item, 'get_product' ) ) ) {
				$product = $item->get_product();
			}

			if ( ! $product ) {
				continue;
			}

			if ( WC_Pre_Orders_Product::product_can_be_pre_ordered( $product ) ) {
				// Set correct flags for this order, making it a pre-order.
				$order->update_meta_data( '_wc_pre_orders_is_pre_order', 1 );
				$order->update_meta_data( '_wc_pre_orders_when_charged', get_post_meta( $product->get_id(), '_wc_pre_orders_when_to_charge', true ) );
				$order->save();

				return;
			}
		}
	}

	/**
	 * Adds Pre-Order Only and Non Pre-Order to the WooCommerce Subscriptions Order Subtype select
	 * Or create a new select if WooCommerce Subscriptions is not active
	 * @since 1.6.0
	 */
	public function add_order_page_filters() {
		//We only need to create a new select if the WooCommerce Subscriptions plugin is not active
		// Otherwise we just add to the subscriptions filter
		if ( class_exists( 'WC_Subscriptions_Core_Plugin' ) || class_exists( 'WC_Subscriptions' ) ) {
			add_filter( 'woocommerce_subscriptions_order_type_dropdown', array( $this, 'add_pre_order_filter' ) );
		} else {
			// Add dropdown to admin orders screen to filter on order type
			add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_pre_order' ), 50 );

			// Add dropdown to admin orders screen to filter on order type when HPOS is enabled.
			add_action( 'woocommerce_order_list_table_restrict_manage_orders', array( $this, 'restrict_manage_pre_order' ) );
		}

		// Add filter to queries on admin orders screen to filter on order type. To avoid WC overriding our query args, we need to hook on after them on 10.
		add_filter( 'request', array( $this, 'orders_by_type_query' ), 11 );

		// Add filter to queries on admin orders screen to filter on order type when HPOS is enabled.
		add_filter( 'woocommerce_shop_order_list_table_prepare_items_query_args', array( $this, 'orders_by_type_query' ) );
	}

	/**
	 * Add an admin dropdown for order types to Woocommerce -> Orders screen
	 *
	 * @param string $order_type The type of order.
	 *
	 * @since 1.6.0
	 */
	public function restrict_manage_pre_order( $order_type = '' ) {
		if ( '' === $order_type ) {
			$order_type = isset( $GLOBALS['typenow'] ) ? wc_clean( $GLOBALS['typenow'] ) : '';
		}

		if ( 'shop_order' !== $order_type ) {
			return;
		}?>
		<select name='shop_order_subtype' id='dropdown_shop_order_pre_order_type'>
			<option value=""><?php esc_html_e( 'All orders types', 'woocommerce-pre-orders' ); ?></option>
			<?php
			$order_types = array(
				'non-pre-orders'  => _x( 'Non Pre-Orders', 'An order type', 'woocommerce-pre-orders' ),
				'pre-orders-only' => _x( 'Pre-Orders Only', 'An order type', 'woocommerce-pre-orders' ),
			);

			foreach ( $order_types as $order_type_key => $order_type_description ) {
				echo '<option value="' . esc_attr( $order_type_key ) . '"';

				if ( isset( $_GET['shop_order_subtype'] ) && ! empty( $_GET['shop_order_subtype'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					selected( $order_type_key, sanitize_text_field( wp_unslash( $_GET['shop_order_subtype'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}

				echo '>' . esc_html( $order_type_description ) . '</option>';
			}
			?>
		</select>
		<?php
	}

	/**
	 * Includes the Non Pre-Orders and Pre-Orders Only Filter to the WooCommerce Subscriptions Order Subtype Select
	 * @since 1.6.0
	 * @param $order_types
	 * @return array
	 */
	public function add_pre_order_filter( $order_types ) {
		$order_types['non-pre-orders']  = _x( 'Non Pre-Orders', 'An order type', 'woocommerce-pre-orders' );
		$order_types['pre-orders-only'] = _x( 'Pre-Orders Only', 'An order type', 'woocommerce-pre-orders' );

		return $order_types;
	}

	/**
	 * Add request filter for order types to Woocommerce -> Orders screen
	 *
	 * Orders that have _wc_pre_orders_is_pre_order meta are considered a pre order
	 * Orders that DO NOT have _wc_pre_orders_is_pre_order meta are NOT considered a pre order
	 *
	 * @since 1.6.0
	 * @param $vars array wp_query args
	 * @return array wp_query args
	 */
	public function orders_by_type_query( $vars ) {
		$order_type = isset( $GLOBALS['typenow'] ) ? wc_clean( $GLOBALS['typenow'] ) : '';
		if ( '' === $order_type ) {
			$order_type = isset( $vars['type'] ) ? wc_clean( $vars['type'] ) : '';
		}

		if ( 'shop_order' === $order_type && ! empty( $_GET['shop_order_subtype'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			switch ( $_GET['shop_order_subtype'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				case 'non-pre-orders':
					$vars['meta_query'][] = array(
						'key'     => '_wc_pre_orders_is_pre_order',
						'compare' => 'NOT EXISTS',
					);
					break;
				case 'pre-orders-only':
					$vars['meta_query'][] = array(
						'key'     => '_wc_pre_orders_is_pre_order',
						'compare' => '=',
						'value'   => '1',
					);
					break;
				default:
					break;
			}
		}

		return $vars;
	}
}

new WC_Pre_Orders_Admin_Orders();
