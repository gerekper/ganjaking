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
	 * Constructor.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'actions' => array(
					'calculate_shipping_date' => __( 'Calculate shipping date', 'woocommerce-order-delivery' ),
				),
			)
		);

		if ( wc_od_is_custom_order_tables_enabled() ) {
			add_filter( 'manage_woocommerce_page_wc-orders_columns', array( $this, 'get_columns' ) );
			add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( $this, 'output_column' ), 10, 2 );
			add_filter( 'woocommerce_order_list_table_restrict_manage_orders', array( $this, 'restrict_manage_orders' ), 10, 2 );
			add_filter( 'woocommerce_shop_order_list_table_prepare_items_query_args', array( $this, 'prepare_items_query_args' ) );
		} else {
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'get_columns' ), 20 );
			add_filter( 'manage_edit-shop_order_sortable_columns', array( $this, 'sortable_columns' ) );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'output_column' ), 20 );
		}
	}

	/**
	 * Get list of columns.
	 *
	 * @since 2.4.0
	 *
	 * @param array $columns The table columns.
	 * @return array
	 */
	public function get_columns( $columns ) {
		$position = array_search( 'order_date', array_keys( $columns ), true );

		/**
		 * Filters the position to add the delivery columns in the order list table.
		 *
		 * @since 2.4.0
		 * @since 2.4.1 Add the table columns as a second argument.
		 *
		 * @param int   $position The column position.
		 * @param array $columns  The table columns.
		 */
		$position = apply_filters( 'wc_od_admin_shop_order_columns_position', $position, $columns );

		if ( wc_od_is_local_pickup_enabled() ) {
			$label = __( 'Delivery / pickup date', 'woocommerce-order-delivery' );
		} else {
			$label = __( 'Delivery date', 'woocommerce-order-delivery' );
		}

		return array_merge(
			array_slice( $columns, 0, $position ),
			array(
				'shipping_date' => __( 'Shipping date', 'woocommerce-order-delivery' ),
				'delivery_date' => $label,
			),
			array_slice( $columns, $position )
		);
	}

	/**
	 * Filters the sortable columns.
	 *
	 * @since 2.4.0
	 *
	 * @param array $columns The table columns.
	 * @return array
	 */
	public function sortable_columns( $columns ) {
		$columns['shipping_date'] = 'shipping_date';
		$columns['delivery_date'] = 'delivery_date';

		return $columns;
	}

	/**
	 * Outputs a column.
	 *
	 * @since 2.4.0
	 *
	 * @global WP_Post $post The current post.
	 *
	 * @param string   $column_id Column ID.
	 * @param WC_Order $order     Optional. Order object. Default null.
	 */
	public function output_column( $column_id, $order = null ) {
		global $post;

		if ( ! in_array( $column_id, array( 'shipping_date', 'delivery_date' ), true ) ) {
			return;
		}

		// HPOS not enabled.
		if ( ! $order && $post ) {
			$order = wc_get_order( $post->ID );
		}

		if ( $order && is_callable( array( $this, 'output_' . $column_id . '_column' ) ) ) {
			call_user_func( array( $this, 'output_' . $column_id . '_column' ), $order );
		}
	}

	/**
	 * Outputs the 'shipping_date' column content.
	 *
	 * @since 2.4.0
	 *
	 * @param WC_Order $order Order object.
	 */
	protected function output_shipping_date_column( $order ) {
		$shipping_date = $order->get_meta( '_shipping_date' );

		if ( $shipping_date && ! wc_od_order_is_local_pickup( $order ) ) {
			$this->output_datetime( wc_string_to_datetime( $shipping_date ) );
		} else {
			echo '<span class="na">–</span>';
		}
	}

	/**
	 * Outputs the 'delivery_date' column content.
	 *
	 * @since 2.4.0
	 *
	 * @param WC_Order $order Order object.
	 */
	protected function output_delivery_date_column( $order ) {
		$delivery_date = $order->get_meta( '_delivery_date' );

		if ( ! $delivery_date ) {
			echo '<span class="na">–</span>';
			return;
		}

		$this->output_datetime( wc_string_to_datetime( $delivery_date ) );

		$time_frame = $order->get_meta( '_delivery_time_frame' );

		if ( $time_frame ) {
			printf(
				'<br><span class="delivery-time-frame">%s</span>',
				wp_kses_post( wc_od_time_frame_to_string( $time_frame ) )
			);
		}
	}

	/**
	 * Outputs a datetime object.
	 *
	 * @since 2.4.0
	 *
	 * @param WC_DateTime $datetime Datetime object.
	 */
	protected function output_datetime( $datetime ) {
		printf(
			'<time datetime="%1$s" title="%2$s">%3$s</time>',
			esc_attr( $datetime->date( 'c' ) ),
			esc_html( $datetime->date_i18n( get_option( 'date_format' ) ) ),
			esc_html( $datetime->date_i18n( wc_od_get_date_format( 'admin' ) ) )
		);
	}

	/**
	 * Adds custom filters before the "Filter" button on the list table for orders.
	 *
	 * @since 2.4.0
	 *
	 * @param string $order_type  The order type.
	 * @param string $location    The location of the extra table nav: 'top' or 'bottom'.
	 */
	public function restrict_manage_orders( $order_type, $location ) {
		if ( 'shop_order' !== $order_type || 'top' !== $location ) {
			return;
		}

		$this->render_filters();
	}

	/**
	 * Loads the custom filters.
	 *
	 * @since 1.4.0
	 */
	public function load_filters() {
		if ( wc_od_is_local_pickup_enabled() ) {
			$filter_label = _x( 'Filter by delivery / pickup date', 'shop order filter', 'woocommerce-order-delivery' );
			$empty_label  = _x( 'All delivery / pickup dates', 'shop order filter', 'woocommerce-order-delivery' );
		} else {
			$filter_label = _x( 'Filter by delivery date', 'shop order filter', 'woocommerce-order-delivery' );
			$empty_label  = _x( 'All delivery dates', 'shop order filter', 'woocommerce-order-delivery' );
		}

		/**
		 * Allow to customize the shop order filters.
		 *
		 * @since 1.4.0
		 *
		 * @param array $filters The shop order filters
		 */
		$this->filters = apply_filters(
			'wc_od_admin_shop_order_filters',
			array(
				'shipping_date' => array(
					'id'    => 'shipping_date',
					'type'  => 'date',
					'label' => _x( 'Filter by shipping date', 'shop order filter', 'woocommerce-order-delivery' ),
					'empty' => _x( 'All shipping dates', 'shop order filter', 'woocommerce-order-delivery' ),
				),
				'delivery_date' => array(
					'id'    => 'delivery_date',
					'type'  => 'date',
					'label' => $filter_label,
					'empty' => $empty_label,
				),
			)
		);
	}

	/**
	 * Filters the query arguments before fetching the items.
	 *
	 * @since 2.4.0
	 *
	 * @param array $query_args The query arguments.
	 * @return array
	 */
	public function prepare_items_query_args( $query_args ) {
		$filters_query_vars = $this->query_filters( array() );

		if ( isset( $filters_query_vars['meta_query'] ) ) {
			$filters_query_vars['delivery_query'] = $filters_query_vars['meta_query'];
			unset( $filters_query_vars['meta_query'] );
		}

		return array_merge( $query_args, $filters_query_vars );
	}

	/**
	 * Gets the available months for a date filter.
	 *
	 * @since 2.4.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @param string $key The meta key.
	 * @return array
	 */
	public function get_date_filter_months( $key ) {
		global $wpdb;

		if ( ! wc_od_is_custom_order_tables_enabled() ) {
			return parent::get_date_filter_months( $key );
		}

		$orders_table      = esc_sql( Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore::get_orders_table_name() );
		$orders_meta_table = esc_sql( Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore::get_meta_table_name() );

		$status = ( isset( $_GET['status'] ) ? wc_clean( wp_unslash( $_GET['status'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		// Filter by status.
		if ( $status && 'all' !== $status ) {
			$status_clause = $wpdb->prepare( " AND {$orders_table}.status = %s", $status );
		} else {
			$status_clause = " AND {$orders_table}.status != 'trash'";
		}

		return $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT DISTINCT YEAR( meta_value ) AS year, MONTH( meta_value ) AS month
				FROM $orders_meta_table
				INNER JOIN $orders_table ON {$orders_table}.id = {$orders_meta_table}.order_id
				WHERE meta_key = %s
				$status_clause
				ORDER BY meta_value DESC
			",
				"_$key"
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Handles the bulk action 'calculate_shipping_date'.
	 *
	 * @since 2.4.0
	 *
	 * @param string $redirect_to URL to redirect to.
	 * @param array  $ids         List of ids.
	 * @return string
	 */
	protected function handle_bulk_action_calculate_shipping_date( $redirect_to, $ids ) {
		$changed = 0;

		foreach ( $ids as $id ) {
			if ( $this->update_shipping_date( $id ) ) {
				$changed++;
			}
		}

		return add_query_arg(
			array(
				'bulk_action' => 'calculated_shipping_date',
				'changed'     => $changed,
				'ids'         => join( ',', $ids ),
			),
			$redirect_to
		);
	}

	/**
	 * Gets the message for specified bulk action.
	 *
	 * @since 2.4.0
	 *
	 * @param string $action  Action name.
	 * @param int    $changed The number of changed items.
	 * @return string
	 */
	protected function get_bulk_action_message( $action, $changed ) {
		$message = '';

		if ( 'calculated_shipping_date' === $action ) {
			$message = sprintf(
				/* translators: %d number of orders */
				_n( 'Updated the shipping date for %d order.', 'Updated the shipping date for %d orders.', $changed, 'woocommerce-order-delivery' ),
				number_format_i18n( $changed )
			);
		}

		return $message;
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

		if ( ! $order || wc_od_order_is_local_pickup( $order ) ) {
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
