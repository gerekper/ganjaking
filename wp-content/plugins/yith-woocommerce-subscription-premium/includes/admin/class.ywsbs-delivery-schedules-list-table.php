<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Delivery Schedules List Table
 *
 * @class   YWSBS_Delivery_Schedules_List_Table
 * @package YITH WooCommerce Subscription
 * @since   2.2.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
// phpcs:disable WordPress.Security.NonceVerification.Recommended

/**
 * Class YWSBS_Delivery_Schedules_List_Table
 */
class YWSBS_Delivery_Schedules_List_Table extends WP_List_Table {


	/**
	 * YWSBS_Delivery_Schedules_List_Table constructor.
	 *
	 * @param array $args Arguments.
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array() );
	}

	/**
	 * Get the columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'product'         => esc_html_x( 'Product', 'Delivery scheduled table - subscription product name', 'yith-woocommerce-subscription' ),
			'subscription_id' => esc_html_x( 'Subscription', 'Delivery scheduled table - Subscription id', 'yith-woocommerce-subscription' ),
			'customer'        => esc_html_x( 'Customer', 'Delivery scheduled table - Customer ', 'yith-woocommerce-subscription' ),
			'status'          => esc_html_x( 'Delivery Status', 'Delivery scheduled table - Status of delivery ', 'yith-woocommerce-subscription' ),
			'scheduled_date'  => esc_html_x( 'Shipping on', 'Delivery scheduled table - Shipping date of the delivery', 'yith-woocommerce-subscription' ),
			'sent_on'         => esc_html_x( 'Shipped on', 'Delivery scheduled table - Date of delivery', 'yith-woocommerce-subscription' ),
			'delivery_info'   => esc_html_x( 'Delivery info', 'Delivery scheduled table - Delivery address', 'yith-woocommerce-subscription' ),
		);

		return $columns;
	}

	/**
	 * Prepare items to show
	 */
	public function prepare_items() {
		global $wpdb, $_wp_column_headers;

		$table_name            = YWSBS_Subscription_Delivery_Schedules()->table_name;
		$screen                = get_current_screen();
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'act.id'; //phpcs:ignore
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'DESC'; //phpcs:ignore

		$join         = '';
		$where        = '';
		$order_string = '';
		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$order_string = ' ORDER BY ' . $orderby . ' ' . $order;
		}

		$posted = $_REQUEST; // phpcs:ignore

		if ( ! empty( $posted['delivery_status_filter'] ) ) {
			$where .= ' AND status = "' . $posted['delivery_status_filter'] . '" ';
		}

		if ( ! empty( $posted['start_date'] ) ) {
			$where .= ' AND scheduled_date >= "' . $posted['start_date'] . ' 00:00:00"';
		}

		if ( ! empty( $posted['end_date'] ) ) {
			$where .= ' AND scheduled_date <= "' . $posted['end_date'] . ' 00:00:00"';
		}

		if ( ! empty( $posted['customer_user'] ) ) {
			$join   = ' LEFT JOIN ' . $wpdb->postmeta . ' as pm ON pm.post_id = act.subscription_id ';
			$where .= ' AND (pm.meta_key="user_id" AND pm.meta_value="' . $posted['customer_user'] . '" ) ';
		}

		if ( ! empty( $posted['product_search'] ) ) {
			$join   = ' LEFT JOIN ' . $wpdb->postmeta . ' as pm2 ON pm2.post_id = act.subscription_id ';
			$where .= ' AND ( pm2.meta_key IN ("product_id","variation_id") AND pm2.meta_value="' . $posted['product_search'] . '" ) ';
		}

		$search = ! empty( $posted['s'] ) ? $posted['s'] : '';

		$join  = apply_filters( 'ywsbs_delivery_schedules_list_table_join', $join, $table_name );
		$where = apply_filters( 'ywsbs_delivery_schedules_list_table_where', $where, $table_name );

		$query = "SELECT * FROM $table_name as act $join  where 1=1 $where $order_string";

		$totalitems = $wpdb->query( $query ); // phpcs:ignore

		$perpage = 25;
		// Which page is this?
		$paged = ! empty( $_GET['paged'] ) ? $_GET['paged'] : '';  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		// Page Number.
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}
		// How many pages do we have in total?
		$totalpages = ceil( $totalitems / $perpage );
		// adjust the query to take pagination into account.
		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}

		/* -- Register the pagination -- */
		$this->set_pagination_args(
			array(
				'total_items' => $totalitems,
				'total_pages' => $totalpages,
				'per_page'    => $perpage,
			)
		);
		// The pagination links are automatically built according to those parameters.

		$_wp_column_headers[ $screen->id ] = $columns;
		$this->items                       = $wpdb->get_results( $query );  //phpcs:ignore

	}

	/**
	 * Fill the columns.
	 *
	 * @param object $item Current Object.
	 * @param string $column_name Current Column.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {

		$subscription = ywsbs_get_subscription( $item->subscription_id );
		switch ( $column_name ) {
			case 'product':
				$product = $subscription->get_product();

				if ( $product ) {
					$product_page_id = 0 === $product->get_parent_id() ? $product->get_id() : $product->get_parent_id();
					$order           = $subscription->get_order();
					$formatted_meta  = '';
					if ( $order instanceof WC_Order && 'variation' === $product->get_type() ) {
						$line_item    = $order->get_items( 'line_item' );
						$variation_id = $product->get_id();
						if ( count( $line_item ) > 0 ) {
							foreach ( $line_item as $product_item ) {

								if ( $product_item instanceof WC_Order_Item_Product ) {
									/**
									 * WC_Order_Item_Product
									 *
									 * @var WC_Order_Item_Product $product_item ;
									 */

									$item_variation_id = $product_item->get_variation_id();

									if ( $variation_id === $item_variation_id ) {

										$item_meta = $product_item->get_formatted_meta_data( '_' );

										if ( count( $item_meta ) > 0 ) {
											$formatted_meta = '<table cellspacing="0" class="display_meta">';
											foreach ( $item_meta as $meta ) {

												$formatted_meta .= '<th>' . wp_kses_post( $meta->display_key ) . ':</th><td>' . wp_kses_post( force_balance_tags( $meta->display_value ) ) . '</td>';
											}

											$formatted_meta .= '</table>';
										}
									}
								}
							}
						}
					}

					return '<a href="' . admin_url( 'post.php?post=' . $product_page_id . '&action=edit' ) . '">' . $product->get_name() . ' (#' . $product->get_id() . ')</a>' . $formatted_meta;
				} else {
					return $subscription->get_product_name();
				}
			case 'subscription_id':
				return '<a href="' . admin_url( 'post.php?post=' . $item->subscription_id . '&action=edit' ) . '">' . $subscription->get_number() . '</a>';
			case 'status':
				return $this->get_status_element( $item->id, $item->status );
			case 'scheduled_date':
				return ywsbs_get_formatted_date( $item->scheduled_date, '' );
			case 'sent_on':
				return ywsbs_get_formatted_date( $item->sent_on, '-' );
			case 'delivery_info':
				$shipping = $subscription->get_address_fields( 'shipping', true );
				return $shipping ? WC()->countries->get_formatted_address( $shipping, '-' ) : '-';
			case 'customer':
				$customer = YWSBS_Subscription_User::get_user_info_for_subscription_list( $subscription );
				echo wp_kses_post( $customer );
				break;
			default:
				return $item->$column_name; // Show the whole array for troubleshooting purposes.
		}
	}


	/**
	 * Return status element to update manually the status of the delivery schedules.
	 *
	 * @param int    $delivery_scheduled_id Delivery schedules id.
	 * @param string $status Status of delivery schedules.
	 */
	public function get_status_element( $delivery_scheduled_id, $status ) {
		$status_list = YWSBS_Subscription_Delivery_Schedules()->get_status();
		?>
		<div class="delivery-status status-td" data-id="<?php echo esc_attr( $delivery_scheduled_id ); ?>">
			<div class="status-normal"
				data-value="<?php esc_attr( $status ); ?>"><?php echo esc_html( $status_list[ $status ] ); ?></div>
			<select class="status-hover">
				<?php foreach ( $status_list as $key => $single_status ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>"
						data-label="<?php echo esc_attr( $single_status ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $single_status ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Get sorttable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'subscription_id' => array( 'subscription_id', false ),
			'status'          => array( 'status', false ),
			'scheduled_date'  => array( 'scheduled_date', false ),
			'sent_on'         => array( 'sent_on', false ),
		);
		return $sortable_columns;
	}


	/**
	 * Extra controls to be displayed between bulk actions and pagination, which
	 * includes our Filters
	 *
	 * @param string $which The placement, one of 'top' or 'bottom'.
	 * @since 1.0.0
	 */
	public function extra_tablenav( $which ) {
		if ( 'top' === $which ) {
			global $wpdb;
			$this->render_status_filter();
			$this->render_product_filter();
			$this->render_customer_filter();
			$this->render_start_end_date_filter();
			$this->render_filter_button();
		}

	}

	/**
	 * Render filter button
	 */
	protected function render_filter_button() {
		echo '<button id="post-query-submit" class="button">' . esc_html__( 'Filter', 'yith-woocommerce-subscription' ) . '</button>';
	}

	/**
	 * Render customer filter.
	 */
	protected function render_customer_filter() {

		echo wp_kses_post( '<div class="alignleft actions yith-search-customer-wrapper">' );

		// Customers select 2.
		$user_string = '';
		$customer_id = '';

		if ( ! empty( $_REQUEST['customer_user'] ) ) { // phpcs:ignore
			$customer_id = absint( $_REQUEST['customer_user'] ); // phpcs:ignore
			$user        = get_user_by( 'id', $customer_id );
			$user_string = $user ? esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) : '';
		}

		$args = array(
			'type'             => 'hidden',
			'class'            => 'wc-customer-search',
			'id'               => 'customer_user',
			'name'             => 'customer_user',
			'data-placeholder' => esc_html__( 'Show All Customers', 'yith-woocommerce-subscription' ),
			'data-allow_clear' => true,
			'data-selected'    => array( $customer_id => esc_attr( $user_string ) ),
			'data-multiple'    => false,
			'value'            => $customer_id,
			'style'            => 'width:200px',
		);

		yit_add_select2_fields( $args );

		echo '</div>';
	}

	/**
	 * Render product filter.
	 */
	protected function render_product_filter() {

		echo wp_kses_post( '<div class="alignleft actions yith-search-product-wrapper">' );

		// Customers select 2.
		$product_name = '';
		$product_id   = '';

		if ( ! empty( $_REQUEST['product_search'] ) ) { // phpcs:ignore
			$product_id   = absint( $_REQUEST['product_search'] ); // phpcs:ignore
			$product      = wc_get_product( $product_id );
			$product_name = '#' . $product_id . ' ' . $product->get_name();
		}

		$args = array(
			'type'             => 'hidden',
			'class'            => 'wc-product-search',
			'id'               => 'product_search',
			'name'             => 'product_search',
			'data-placeholder' => esc_html__( 'Show All Product', 'yith-woocommerce-subscription' ),
			'data-allow_clear' => true,
			'data-selected'    => array( $product_id => esc_attr( $product_name ) ),
			'data-multiple'    => false,
			'value'            => $product_id,
			'style'            => 'width:300px',
			'data-action'      => 'ywsbs_json_search_ywsbs_products',
		);

		yit_add_select2_fields( $args );

		echo '</div>';
	}

	/**
	 * Render start date and end date filter.
	 */
	protected function render_start_end_date_filter() {
		$start_date = ( isset( $_REQUEST['start_date'] ) ) ? $_REQUEST['start_date'] : '';
		$end_date   = ( isset( $_REQUEST['end_date'] ) ) ? $_REQUEST['end_date'] : '';
		echo wp_kses_post( '<div class="alignleft actions yith-start-and-end-date-wrapper">' );
		echo esc_html__( 'From: ', 'yith-woocommerce-subscription' );
		echo '<input type="text" size="11"  value="' . esc_html( $start_date ) . '" name="start_date" placeholder="yyyy-mm-dd" data-date-format="yy-mm-dd" class="range_datepicker from yith-plugin-fw-datepicker" autocomplete="off" id="start_date">';
		echo esc_html__( ' To: ', 'yith-woocommerce-subscription' );
		echo '<input type="text" size="11"  value="' . esc_html( $end_date ) . '" name="end_date" placeholder="yyyy-mm-dd" data-date-format="yy-mm-dd" class="range_datepicker to yith-plugin-fw-datepicker" autocomplete="off" id="end_date">';
		echo '</div>';
	}
	/**
	 * Render delivery schedules filter.
	 */
	protected function render_status_filter() {
		echo wp_kses_post( '<div class="alignleft actions yith-delivery-status-filter-wrapper">' );
		$status_list = YWSBS_Subscription_Delivery_Schedules()->get_status();

		$status = ( isset( $_REQUEST['delivery_status_filter'] ) ) ? $_REQUEST['delivery_status_filter'] : '';

		echo '<select name="delivery_status_filter" class="delivery_status_filter wc-enhanced-select"><option value="">' . esc_html_x( 'All status', 'Option to select all delivered schedules status', 'yith-woocommerce-subscription' ) . '</option>';
		foreach ( $status_list as $key => $value ) {
			$selected = selected( $key, $status, 0 );
			echo '<option value="' . esc_html( $key ) . '" ' . esc_html( $selected ) . '>' . esc_html( $value ) . '</option>';
		}
		echo '</select>';
		echo '</div>';
	}


}


