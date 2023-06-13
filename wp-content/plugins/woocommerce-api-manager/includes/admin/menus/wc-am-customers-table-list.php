<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager
 *
 * @since       2.8
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * @extends WP_List_Table
 *
 * @since 2.8
 */
class WC_AM_Customers_Table_List extends WP_List_Table {

	public function __construct( $args = array() ) {
		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
			                     'singular' => __( 'API Customer', 'woocommerce-api-manager' ),
			                     'plural'   => __( 'API Customers', 'woocommerce-api-manager' ),
			                     'ajax'     => false
		                     ) );
	}

	/**
	 * Returns the column data.
	 *
	 * @since 2.8
	 *
	 * @param $item
	 * @param $column_name
	 *
	 * @return string|void|null
	 * @throws \Exception
	 */
	public function column_default( $item, $column_name ) {
		$args  = array();
		$order = wc_get_order( $item->order_id );

		if ( is_object( $order ) ) {
			$customer = $item->user_id == absint( $order->get_user_id() ) ? new WC_Customer( absint( $order->get_user_id() ) ) : null;
			$args     = array(
				'post_status'    => 'all',
				'post_type'      => 'shop_order',
				'_customer_user' => $order->get_user_id( 'edit' ),
			);
		}

		switch ( $column_name ) {
			case 'user_id' :
				return ( ! WC_AM_FORMAT()->empty( $item->user_id ) ) ? esc_html( $item->user_id ) . '<hr>' . '<a href="' . admin_url( 'user-edit.php?user_id=' . absint( $item->user_id ) ) . '" target="_blank">' . esc_html__( 'Profile ', 'woocommerce-api-manager' ) . '<span style="font-size: medium; vertical-align: middle;" class="dashicons dashicons-external"></span></a>' : esc_html__( 'n/a', 'woocommerce-api-manager' );
			case 'customer_name' :
				if ( is_object( $order ) ) {
					if ( $customer instanceof WC_Customer ) {
						return sprintf( esc_html__( '%s%s%s', 'woocommerce-api-manager' ), $customer->get_first_name() . ' ' . $customer->get_last_name(), '<hr>', $customer->get_email() );
					}
				}

				return esc_html__( 'n/a', 'woocommerce-api-manager' );
			case 'product_order_api_key' :
				return sprintf( '%s', $item->product_order_api_key );
			case 'product' :
				return ( ! WC_AM_FORMAT()->empty( $item->product_id ) ) ? esc_html( $item->product_id ) . '<hr>' . '<a href="' . esc_url( admin_url() . 'post.php?post=' . WC_AM_PRODUCT_DATA_STORE()->get_parent_product_id( $item->product_id ) . '&action=edit' ) . '" title="' . esc_html( $item->product_title ) . '" target="_blank">' . esc_html( $item->product_title ) . '<span style="font-size: medium; vertical-align: middle;" class="dashicons dashicons-external"></span>' . '</a>' : esc_html__( 'n/a', 'woocommerce-api-manager' );
			case 'order_id' :
				return $item->order_id > 0 ? '<a href="' . admin_url( 'post.php?post=' . absint( $item->order_id ) . '&action=edit' ) . '" target="_blank">' . absint( $item->order_id ) . ' <span style="font-size: medium; vertical-align: middle;" class="dashicons dashicons-external"></span></a>' . '<hr>' . sprintf( '<a href="%s " target="_blank">%s<span style="font-size: medium; vertical-align: middle;" class="dashicons dashicons-external"></span></a>', esc_url( add_query_arg( $args, admin_url( 'edit.php' ) ) ), ' ' . esc_html__( 'View other orders ', 'woocommerce-api-manager' ) ) : esc_html__( 'n/a', 'woocommerce-api-manager' );
			case 'activations' :
				return esc_attr( $item->activations_total ) . __( ' out of ', 'woocommerce-api-manager' ) . esc_attr( $item->activations_purchased_total );
			case 'access_granted' :
				return ( ! WC_AM_FORMAT()->empty( $item->access_granted ) ) ? esc_attr( WC_AM_FORMAT()->unix_timestamp_to_date( $item->access_granted, true ) ) : esc_html__( 'n/a', 'woocommerce-api-manager' );
			case 'access_expires' :
				if ( WCAM()->get_wc_subs_exist() && ! empty( $item->sub_id ) ) {
					$expires = ( WC_AM_SUBSCRIPTION()->has_end_date_by_sub( $item->sub_id ) ) ? date_i18n( wc_date_format(), WC_AM_SUBSCRIPTION()->get_subscription_time_by_sub_id( $item->sub_id, 'end', 'site' ) ) : _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-api-manager' );
				} else {
					if ( WC_AM_ORDER_DATA_STORE()->is_time_expired( $item->access_expires ?? false ) ) {
						$expires = esc_html__( 'Expired', 'woocommerce-api-manager' );
					} else {
						$expires = $item->access_expires == 0 ? _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-api-manager' ) : esc_html( WC_AM_FORMAT()->unix_timestamp_to_date( $item->access_expires, true ) );
					}
				}

				return esc_html( $expires );
			case 'next_payment' :
				if ( $item->sub_id != 0 ) {
					return ( ( WC_AM_SUBSCRIPTION()->has_next_payment_by_sub( $item->sub_id ) ) ) ? date_i18n( wc_date_format(), WC_AM_SUBSCRIPTION()->get_subscription_time_by_sub_id( $item->sub_id, 'next_payment', 'site' ) ) : esc_html__( 'Pending Cancellation', 'woocommerce-api-manager' );
				} elseif ( $item->access_expires > 0 ) {
					return esc_html( WC_AM_FORMAT()->unix_timestamp_to_date( $item->access_expires, true ) );
				}

				return esc_html__( 'Lifetime Subscription', 'woocommerce-api-manager' );
		}
	}

	/**
	 * Returns the columns array.
	 *
	 * @since 2.8
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'user_id'               => __( 'User ID', 'woocommerce-api-manager' ),
			'customer_name'         => __( 'Customer Name', 'woocommerce-api-manager' ),
			'product_order_api_key' => __( 'Product Order API Key', 'woocommerce-api-manager' ),
			'product'               => __( 'Product', 'woocommerce-api-manager' ),
			'order_id'              => __( 'Order ID', 'woocommerce-api-manager' ),
			'activations'           => __( 'Activations', 'woocommerce-api-manager' ),
			'access_granted'        => __( 'Access Granted', 'woocommerce-api-manager' ),
			'access_expires'        => __( 'Access Expires', 'woocommerce-api-manager' ),
			'next_payment'          => __( 'Next Payment', 'woocommerce-api-manager' )
		);

		return $columns;
	}

	/**
	 * Return the array of sortable columns.
	 *
	 * @since 2.8
	 *
	 * @return array[]
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			// true means its already sorted
			'user_id'        => array( 'user_id', true ),
			'product'        => array( 'product', false ),
			'order_id'       => array( 'order_id', false ),
			'access_granted' => array( 'access_granted', false ),
			'access_expires' => array( 'access_expires', false )
		);

		return $sortable_columns;
	}

	/**
	 * Prepare items.
	 *
	 * @since 2.8
	 */
	public function prepare_items() {
		global $wpdb;

		//$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$this->_column_headers = $this->get_column_info(); // For hidden columns.
		$current_page          = $this->get_pagenum();
		$per_page              = $this->get_items_per_page( 'wc_am_customers_per_page', 10 );
		$total_items           = WC_AM_API_RESOURCE_DATA_STORE()->count_resources();
		$orderby               = ! empty( $_REQUEST[ 'orderby' ] ) ? wc_clean( $_REQUEST[ 'orderby' ] ) : 'user_id';
		$order                 = empty( $_REQUEST[ 'order' ] ) || $_REQUEST[ 'order' ] === 'asc' ? 'ASC' : 'DESC';
		$order_id              = ! empty( $_REQUEST[ 'order_id' ] ) ? absint( $_REQUEST[ 'order_id' ] ) : '';
		$where                 = array( 'WHERE 1=1' );

		if ( $order_id ) {
			$where[] = ' AND order_id = ' . $order_id;;
		}

		// Search box filters.
		if ( array_key_exists( 's', $_REQUEST ) && $_REQUEST[ 's' ] ) {
			$query_var = 0;

			if ( is_email( $_REQUEST[ 's' ] ) ) {
				$user_obj = get_user_by( 'email', $_REQUEST[ 's' ] ) ?? null;

				if ( ! WC_AM_FORMAT()->empty( $user_obj ) ) {
					$query_var = $user_obj->ID;
				}
			} else {
				// $query_var = esc_sql( $wpdb->esc_like( wc_clean( wp_unslash( $_REQUEST['s'] ) ) ) );
				$query_var = esc_sql( wc_clean( wp_unslash( $_REQUEST[ 's' ] ) ) );
			}

			$where[] = ' AND order_id = ' . $query_var;
			$where[] = ' OR product_id = ' . $query_var;
			$where[] = ' OR user_id = ' . $query_var;
		}

		$where = implode( ' ', $where );

		$this->items = $wpdb->get_results( $wpdb->prepare( "
			SELECT user_id, activations_total, activations_purchased_total, access_expires, access_granted, product_order_api_key, product_title, product_id, order_id, sub_id
			FROM {$wpdb->prefix}" . WC_AM_USER()->get_api_resource_table_name() . "
			$where
			ORDER BY `{$orderby}` {$order} LIMIT %d, %d
		", ( $current_page - 1 ) * $per_page, $per_page ) );

		// Pagination
		$this->set_pagination_args( array(
			                            'total_items' => $total_items,
			                            'total_pages' => ceil( $total_items / $per_page ),
			                            'per_page'    => $per_page
		                            ) );
	}

	/**
	 * Custom no items exist message.
	 *
	 * @since 2.8
	 */
	public function no_items() {
		esc_html_e( 'No API Customers found.', 'woocommerce-api-manager' );
	}

	/**
	 * Generates the table navigation above or below the table
	 *
	 * @since 3.1.0
	 *
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args[ 'plural' ] );
		}
		?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            <div class="alignleft actions">
                <form action="" method="POST">
					<?php
					$this->search_box( esc_html__( 'Search' ), 'woocommerce-api-manager' );
					?>
                    <input type="hidden" name="page" value="<?= esc_attr( $_REQUEST[ 'page' ] ) ?>"/>
                </form>
            </div>
			<?php if ( $this->has_items() ) : ?>
                <div class="alignleft actions bulkactions">
					<?php $this->bulk_actions( $which ); ?>
                </div>
			<?php
			endif;
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
            <br class="clear"/>
        </div>
		<?php
	}
}