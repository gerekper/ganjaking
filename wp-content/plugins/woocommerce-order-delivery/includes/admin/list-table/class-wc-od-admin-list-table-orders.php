<?php
/**
 * List table: orders.
 *
 * @package WC_OD/Admin/List_Tables
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_OD_Admin_List_Table_Orders', false ) ) {
	return;
}

if ( ! class_exists( 'WC_OD_Admin_List_Table', false ) ) {
	include_once 'abstract-class-wc-od-admin-list-table.php';
}

/**
 * WC_OD_Admin_List_Table_Orders Class
 */
class WC_OD_Admin_List_Table_Orders extends WC_OD_Admin_List_Table {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $list_table_type = 'shop_order';


	/**
	 * Loads the custom filters.
	 *
	 * @since 1.4.0
	 */
	public function load_filters() {
		/**
		 * Allow to customize the shop order filters.
		 *
		 * @since 1.4.0
		 *
		 * @param array $filters The shop order filters
		 */
		$this->filters = apply_filters( 'wc_od_admin_shop_order_filters', array(
			'shipping_date' => array(
				'id'    => 'shipping_date',
				'type'  => 'date',
				'label' => _x( 'Filter by shipping date', 'shop order filter', 'woocommerce-order-delivery' ),
				'empty' => _x( 'All shipping dates', 'shop order filter', 'woocommerce-order-delivery' ),
			),
			'delivery_date' => array(
				'id'    => 'delivery_date',
				'type'  => 'date',
				'label' => _x( 'Filter by delivery date', 'shop order filter', 'woocommerce-order-delivery' ),
				'empty' => _x( 'All delivery dates', 'shop order filter', 'woocommerce-order-delivery' ),
			)
		) );
	}

	/**
	 * Define bulk actions.
	 *
	 * @since 1.4.0
	 *
	 * @param array $actions Existing actions.
	 * @return array
	 */
	public function define_bulk_actions( $actions ) {
		$actions['calculate_shipping_date'] = __( 'Calculate shipping date', 'woocommerce-order-delivery' );

		return $actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * @since 1.4.0
	 *
	 * @param  string $redirect_to URL to redirect to.
	 * @param  string $action      Action name.
	 * @param  array  $ids         List of ids.
	 * @return string
	 */
	public function handle_bulk_actions( $redirect_to, $action, $ids ) {
		$ids     = array_map( 'absint', $ids );
		$changed = 0;

		if ( 'calculate_shipping_date' === $action ) {
			$report_action = 'calculated_shipping_date';

			foreach ( $ids as $id ) {
				if ( $this->update_shipping_date( $id ) ) {
					$changed++;
				}
			}

			$redirect_to = add_query_arg(
				array(
					'post_type'   => $this->list_table_type,
					'bulk_action' => $report_action,
					'changed'     => $changed,
					'ids'         => join( ',', $ids ),
				), $redirect_to
			);
		}

		return esc_url_raw( $redirect_to );
	}

	/**
	 * Show bulk notices.
	 *
	 * @since 1.4.0
	 *
	 * @global string $pagenow   The current admin page.
	 * @global string $post_type The current admin post type.
	 */
	public function bulk_admin_notices() {
		global $pagenow, $post_type;

		// Bail out if not on shop order list page.
		if ( 'edit.php' !== $pagenow || 'shop_order' !== $post_type || ! isset( $_REQUEST['bulk_action'] ) ) {
			return;
		}

		$number      = isset( $_REQUEST['changed'] ) ? absint( $_REQUEST['changed'] ) : 0;
		$bulk_action = wc_clean( wp_unslash( $_REQUEST['bulk_action'] ) );

		if ( 'calculated_shipping_date' === $bulk_action && $number ) {
			$message = sprintf(
				_n( 'Updated the shipping date for %d order.', 'Updated the shipping date for %d orders.', $number, 'woocommerce-order-delivery' ),
				number_format_i18n( $number )
			);

			echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
		}
	}

	/**
	 * Updates the shipping date if necessary.
	 *
	 * @since 1.4.0
	 *
	 * @param int $order_id The order ID.
	 * @return bool True if the date has been updated, false otherwise.
	 */
	protected function update_shipping_date( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return false;
		}

		$delivery_date = $order->get_meta( '_delivery_date' );

		// No delivery date or expired.
		if ( ! $delivery_date || $delivery_date < wc_od_get_local_date( false ) ) {
			return false;
		}

		$updated           = false;
		$shipping_date     = $order->get_meta( '_shipping_date' );
		$new_shipping_date = wc_od_get_order_last_shipping_date( $order, 'bulk-action' );

		if ( $new_shipping_date ) {
			$new_shipping_date = wc_od_localize_date( $new_shipping_date, 'Y-m-d' );

			if ( $new_shipping_date && $new_shipping_date !== $shipping_date ) {
				wc_od_update_order_meta( $order, '_shipping_date', $new_shipping_date, true );
				$updated = true;
			}
		} elseif ( $shipping_date ) {
			wc_od_delete_order_meta( $order, '_shipping_date', true );
			$updated = true;
		}

		return $updated;
	}

}
