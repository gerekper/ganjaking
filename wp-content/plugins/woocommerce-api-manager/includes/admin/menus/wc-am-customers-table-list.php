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
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string|void|null
	 * @throws \Exception
	 */
	public function column_default( $item, $column_name ) {
		if ( ! WC_AM_FORMAT()->empty( $item ) && ! WC_AM_FORMAT()->empty( $item ) ) {
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
				case 'customer_name' :
					if ( is_object( $order ) ) {
						if ( $customer instanceof WC_Customer ) {
							return sprintf( esc_html__( '%s%s%s', 'woocommerce-api-manager' ), '<a href="' . esc_url( admin_url( 'user-edit.php?user_id=' . absint( $item->user_id ) ) ) . '" target="_blank">' . $customer->get_first_name() . ' ' . $customer->get_last_name() . '<span style="font-size: medium; vertical-align: middle;" class="dashicons dashicons-external"></span></a>', '<hr>', $customer->get_email() );
						}
					}

					return esc_html__( 'n/a', 'woocommerce-api-manager' );
				case 'product_id' :
					return ( ! WC_AM_FORMAT()->empty( $item->product_id ) ) ? esc_html( $item->product_id ) . '<hr>' . '<a href="' . esc_url( admin_url() . 'post.php?post=' . WC_AM_PRODUCT_DATA_STORE()->get_parent_product_id( $item->product_id ) . '&action=edit' ) . '" title="' . esc_html( $item->product_title ) . '" target="_blank">' . esc_html( $item->product_title ) . '<span style="font-size: medium; vertical-align: middle;" class="dashicons dashicons-external"></span>' . '</a>' : esc_html__( 'n/a', 'woocommerce-api-manager' );
				case 'order_id' :
					return $item->order_id > 0 ? '<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $item->order_id ) . '&action=edit' ) ) . '" target="_blank">' . absint( $item->order_id ) . ' <span style="font-size: medium; vertical-align: middle;" class="dashicons dashicons-external"></span></a>' . '<hr>' . sprintf( '<a href="%s " target="_blank">%s<span style="font-size: medium; vertical-align: middle;" class="dashicons dashicons-external"></span></a>', esc_url( add_query_arg( $args, admin_url( 'edit.php' ) ) ), ' ' . esc_html__( 'View other orders ', 'woocommerce-api-manager' ) ) : esc_html__( 'n/a', 'woocommerce-api-manager' );
				case 'subscription_type':
					return ( empty( $item->sub_id ) ) ? __( 'AM Subscription', 'woocommerce-api-manager' ) : __( 'WC Subscription', 'woocommerce-api-manager' );
				case 'activations' :
					return esc_attr( $item->activations_total ) . esc_html__( ' out of ', 'woocommerce-api-manager' ) . esc_attr( $item->activations_purchased_total );
				case 'access_granted' :
					return ( ! WC_AM_FORMAT()->empty( $item->access_granted ) ) ? esc_attr( WC_AM_FORMAT()->unix_timestamp_to_date( $item->access_granted, true ) ) : esc_html__( 'n/a', 'woocommerce-api-manager' );
				case 'access_expires' :
					if ( WCAM()->get_wc_subs_exist() && ! empty( $item->sub_id ) ) {
						$expires = ( WC_AM_SUBSCRIPTION()->has_end_date_by_sub( $item->sub_id ) ) ? date_i18n( wc_date_format(), WC_AM_SUBSCRIPTION()->get_subscription_time_by_sub_id( $item->sub_id, 'end', 'site' ) ) : _x( 'When Cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-api-manager' );
					} else {
						if ( WC_AM_ORDER_DATA_STORE()->is_time_expired( $item->access_expires ?? false ) ) {
							$expires = __( 'Expired', 'woocommerce-api-manager' );
						} else {
							$expires = $item->access_expires == 0 ? _x( 'Never', 'Used as end date for an indefinite subscription', 'woocommerce-api-manager' ) : WC_AM_FORMAT()->unix_timestamp_to_date( $item->access_expires, true );
						}
					}

					return esc_html( $expires );
				case 'grace_period' :
					if ( WC_AM_GRACE_PERIOD()->exists( $item->api_resource_id ) ) {
						return esc_html__( 'Expires ', 'woocommerce-api-manager' ) . '<br>' . WC_AM_FORMAT()->unix_timestamp_to_date( WC_AM_GRACE_PERIOD()->get_expiration( $item->api_resource_id ), true );
					} else {
						return esc_html__( 'None', 'woocommerce-api-manager' );
					}
				case 'next_payment' :
					if ( $item->sub_id != 0 ) {
						$end_date = WC_AM_SUBSCRIPTION()->has_end_date_by_sub( $item->sub_id ) ? WC_AM_SUBSCRIPTION()->get_subscription_end_date_to_display( $item->order_id ) : '';

						return ( WC_AM_SUBSCRIPTION()->has_next_payment_by_sub( $item->sub_id ) ) ? esc_html__( date_i18n( wc_date_format(), WC_AM_SUBSCRIPTION()->get_subscription_time_by_sub_id( $item->sub_id, 'next_payment', 'site' ) ) ) : esc_html__( 'Pending Cancellation on ', 'woocommerce-api-manager' ) . '<br>' . esc_html( $end_date );
					} elseif ( $item->access_expires > 0 ) {
						return WC_AM_GRACE_PERIOD()->is_expired( $item->api_resource_id ) ? esc_html__( 'Pending Cancellation on ', 'woocommerce-api-manager' ) . '<br>' . esc_html( WC_AM_FORMAT()->unix_timestamp_to_date( WC_AM_GRACE_PERIOD()->get_expiration( $item->api_resource_id ), true ) ) : esc_html( WC_AM_FORMAT()->unix_timestamp_to_date( $item->access_expires, true ) );
					}

					return esc_html__( 'Lifetime Subscription', 'woocommerce-api-manager' );
			}
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
		return array(
			'customer_name'     => __( 'Customer', 'woocommerce-api-manager' ),
			'product_id'        => __( 'Product', 'woocommerce-api-manager' ),
			'order_id'          => __( 'Order', 'woocommerce-api-manager' ),
			'subscription_type' => __( 'Subscription Type', 'woocommerce-api-manager' ),
			'activations'       => __( 'Activations', 'woocommerce-api-manager' ),
			'access_granted'    => __( 'Access Granted', 'woocommerce-api-manager' ),
			'access_expires'    => __( 'Access Expires', 'woocommerce-api-manager' ),
			'grace_period'      => __( 'Grace Period', 'woocommerce-api-manager' ),
			'next_payment'      => __( 'Next Payment', 'woocommerce-api-manager' )
		);
	}

	/**
	 * Return the array of sortable columns.
	 *
	 * @since 2.8
	 *
	 * @return array[]
	 */
	public function get_sortable_columns() {
		return array(
			// true means its already sorted
			'product_id'     => array( 'product_id', false ),
			'order_id'       => array( 'order_id', false ),
			'access_granted' => array( 'access_granted', true ),
			'access_expires' => array( 'access_expires', false )
		);
	}

	/**
	 * Prepare items.
	 *
	 * @since 2.8
	 */
	public function prepare_items() {
		global $wpdb;

		$request = wc_clean( $_REQUEST );

		$this->_column_headers = $this->get_column_info(); // For hidden columns.
		$current_page          = $this->get_pagenum();
		$per_page              = $this->get_items_per_page( 'wc_am_customers_per_page' );
		$offset                = ( $current_page - 1 ) * $per_page;
		$total_items           = WC_AM_API_RESOURCE_DATA_STORE()->count_resources();
		$orderby               = ! empty( $request[ 'orderby' ] ) ? $request[ 'orderby' ] : 'access_granted';
		$order                 = empty( $request[ 'order' ] ) || $request[ 'order' ] === 'asc' ? 'DESC' : 'ASC';
		$order_id              = ! empty( $request[ 'order_id' ] ) ? absint( $request[ 'order_id' ] ) : '';

		/**
		 * Array values
		 */
		$order_by_order_num       = 0;
		$search_by_order_id_num   = 0;
		$search_by_email_num      = 0;
		$search_by_product_id_num = 0;

		if ( $order_id ) {
			$order_by_order_num = $order_id;
		}

		// Search box filters.
		if ( ! empty( $request[ 's' ] ) && array_key_exists( 's', $request ) ) {
			$query_var = $request[ 's' ];

			$search_by_order_id_num = $query_var;

			if ( is_email( $query_var ) ) {
				$user_obj = get_user_by( 'email', $query_var ) ?? null;

				if ( ! WC_AM_FORMAT()->empty( $user_obj ) ) {
					$search_by_email_num = $user_obj->ID;
				}
			} else {
				$search_by_product_id_num = $query_var;
			}
		}

		/**
		 * To prevent Array to string conversion.
		 */
		$where = array(
			'WHERE 1=1',
			! empty( $order_by_order_num ) ? ' AND order_id = ' . absint( $order_by_order_num ) : '',
			! empty( $search_by_order_id_num ) ? ' AND order_id = ' . absint( $search_by_order_id_num ) : '',
			! empty( $search_by_email_num ) ? ' OR user_id = ' . absint( $search_by_email_num ) : '',
			! empty( $search_by_product_id_num ) ? ' OR product_id = ' . absint( $search_by_product_id_num ) : ''
		);

		$where = implode( ' ', $where );

		$this->items = $wpdb->get_results( $wpdb->prepare( "
			SELECT api_resource_id, user_id, activations_total, activations_purchased_total, access_expires, access_granted, product_title, product_id, order_id, sub_id
			FROM {$wpdb->prefix}" . WC_AM_USER()->get_api_resource_table_name() . "
			$where
			ORDER BY `{$orderby}` {$order} LIMIT %d, %d
		", $offset, $per_page ) );

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
	 * @since 2.8
	 *
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
		$request = wc_clean( $_REQUEST );

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
                    <input type="hidden" name="page" value="<?= esc_attr( $request[ 'page' ] ) ?>"/>
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