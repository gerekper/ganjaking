<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Order Detail List Class.
 *
 * A class that generates the order list (detail) for vendors.
 *
 * @category Order
 * @package  WooCommerce Product Vendors/Vendor Order Detail List
 * @version  2.0.0
 */
class WC_Product_Vendors_Vendor_Order_Detail_List extends WP_List_Table {
	private $vendor_data;

	/**
	 * Init
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function __construct() {
		global $wpdb;

		parent::__construct( array(
			'singular'  => 'order',
			'plural'    => 'orders',
			'ajax'      => false,
		) );

		$this->vendor_data = WC_Product_Vendors_Utils::get_vendor_data_from_user();

		return true;
	}

	/**
	 * Prepares the items for display
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function prepare_items() {
		global $wpdb;

		// check if table exists before continuing
		if ( ! WC_Product_Vendors_Utils::commission_table_exists() ) {
			return;
		}

		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$this->process_bulk_action();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$order_id = ! empty( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : 0;
		$vendor_id = WC_Product_Vendors_Utils::get_logged_in_vendor();

		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );

		$sql = 'SELECT * FROM ' . WC_PRODUCT_VENDORS_COMMISSION_TABLE;

		$sql .= ' WHERE 1=1';

		$sql .= ' AND `vendor_id` = %d';
		$sql .= ' AND `order_id` = %d';

		$data = $wpdb->get_results( $wpdb->prepare( $sql, $vendor_id, $order_id ) );

		$this->items = $data;

		return true;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'], '_wpnonce', false );
		}
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<?php if ( $this->has_items() ) : ?>
			<div class="alignleft actions bulkactions">
				<?php $this->bulk_actions( $which ); ?>
			</div>
			<?php endif; ?>

		<br class="clear" />
		</div>
		<?php
	}

	/**
	 * Defines the columns to show
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return array $columns
	 */
	public function get_columns() {
		$columns = array(
			'cb'                      => '<input type="checkbox" />',
			'product_name'            => __( 'Product', 'woocommerce-product-vendors' ),
			'total_commission_amount' => __( 'Commission', 'woocommerce-product-vendors' ),
			'commission_status'       => __( 'Commission Status', 'woocommerce-product-vendors' ),
			'paid_date'               => __( 'Paid Date', 'woocommerce-product-vendors' ),
			'fulfillment_status'      => __( 'Fulfillment Status', 'woocommerce-product-vendors' ),
		);

		return $columns;
	}

	/**
	 * Adds checkbox to each row
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param object $item
	 * @return mixed
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="ids[%d]" value="%d" /><input type="hidden" name="order_item_ids[%d]" value="%d" />', $item->id, $item->order_item_id, $item->id, $item->order_item_id );
	}

	/**
	 * Defines what data to show on each column
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param array $item
	 * @param string $column_name
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {

			case 'product_name' :
				$order          = wc_get_order( $item->order_id );
				$quantity       = absint( $item->product_quantity );
				$var_attributes = '';
				$refund         = '';
				$sku            = '';

				// check if product is a variable product
				if ( ! empty( $item->variation_id ) ) {
					$product = wc_get_product( absint( $item->variation_id ) );

					$order_item = WC_Order_Factory::get_order_item( $item->order_item_id );
					
					if ( $metadata = $order_item->get_formatted_meta_data() ) {
						foreach ( $metadata as $meta_id => $meta ) {
							// Skip hidden core fields
							if ( in_array( $meta->key, apply_filters( 'wcpv_hidden_order_itemmeta', array(
								'_qty',
								'_tax_class',
								'_product_id',
								'_variation_id',
								'_line_subtotal',
								'_line_subtotal_tax',
								'_line_total',
								'_line_tax',
								'_fulfillment_status',
								'_commission_status',
								'method_id',
								'cost',
							) ) ) ) {
								continue;
							}

							$var_attributes .= sprintf( __( '<br /><small>( %1$s: %2$s )</small>', 'woocommerce-product-vendors' ), wp_kses_post( rawurldecode( $meta->display_key ) ), wp_kses_post( $meta->value ) );
						}
					}
				} else {
					$product = wc_get_product( absint( $item->product_id ) );
				}

				if ( is_object( $product ) && $product->get_sku() ) {
					$sku = sprintf( __( '<br />%1$s: %2$s', 'woocommerce-product-vendors' ), 'SKU', $product->get_sku() );
				}

				$order_item = WC_Order_Factory::get_order_item( $item->order_item_id );
				$order_item_meta = wc_display_item_meta( $order_item, array( 'echo' => false ) );

				$refunded_quantity = $order->get_qty_refunded_for_item( intval( $item->order_item_id ) );

				if ( $refunded_quantity ) {
					$refund = sprintf( __( '<br /><small class="wpcv-refunded">-%s</small>', 'woocommerce-product-vendors' ), absint( $refunded_quantity ) );
				}

				if ( is_object( $product ) ) {
					return edit_post_link( $quantity . 'x ' . sanitize_text_field( $item->product_name ), '', '', absint( $item->product_id ) ) . $var_attributes . $sku . $order_item_meta . $refund;
				} elseif ( ! empty( $item->product_name ) ) {
					return $quantity . 'x ' . sanitize_text_field( $item->product_name ) . $order_item_meta . $refund;
				} else {
					return sprintf( '%s ' . __( 'Product Not Found', 'woocommerce-product-vendors' ), '#' . absint( $item->product_id ) );
				}

			case 'total_commission_amount' :
				$order           = wc_get_order( $item->order_id );
				$refund          = '';
				$refunded_amount = $order->get_total_refunded_for_item( intval( $item->order_item_id ) );

				if ( $refunded_amount ) {
					$refunded_commission = $refunded_amount * $item->product_commission_amount / $item->product_amount;
					$refund              = sprintf( __( '<br /><small class="wpcv-refunded">-%s</small>', 'woocommerce-product-vendors' ), wc_price( $refunded_commission ) );
				}

				return wc_price( sanitize_text_field( $item->total_commission_amount ) ) . $refund;

			case 'commission_status' :
				if ( 'unpaid' === $item->commission_status ) {
					$status = '<span class="wcpv-unpaid-status">' . esc_html__( 'UNPAID', 'woocommerce-product-vendors' ) . '</span>';
				}

				if ( 'paid' === $item->commission_status ) {
					$status = '<span class="wcpv-paid-status">' . esc_html__( 'PAID', 'woocommerce-product-vendors' ) . '</span>';
				}

				if ( 'void' === $item->commission_status ) {
					$status = '<span class="wcpv-void-status">' . esc_html__( 'VOID', 'woocommerce-product-vendors' ) . '</span>';
				}

				return $status;

			case 'paid_date' :
				$timezone = ! empty( $this->vendor_data['timezone'] ) ? sanitize_text_field( $this->vendor_data['timezone'] ) : '';

				return WC_Product_Vendors_Utils::format_date( sanitize_text_field( $item->paid_date ), $timezone );

			case 'fulfillment_status' :
				$status = WC_Product_Vendors_Utils::get_fulfillment_status( $item->order_item_id );

				if ( $status && 'unfulfilled' === $status ) {
					$status = '<span class="wcpv-unfulfilled-status">' . esc_html__( 'UNFULFILLED', 'woocommerce-product-vendors' ) . '</span>';

				} elseif ( $status && 'fulfilled' === $status ) {
					$status = '<span class="wcpv-fulfilled-status">' . esc_html__( 'FULFILLED', 'woocommerce-product-vendors' ) . '</span>';

				} else {
					$status = __( 'N/A', 'woocommerce-product-vendors' );
				}

				return $status;

			default :
				return print_r( $item, true );
		}
	}

	/**
	 * Defines the hidden columns
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return array $columns
	 */
	public function get_hidden_columns() {
		// get user hidden columns
		$hidden = get_hidden_columns( $this->screen );

		$new_hidden = array();

		foreach ( $hidden as $k => $v ) {
			if ( ! empty( $v ) ) {
				$new_hidden[] = $v;
			}
		}

		return array_merge( array(), $new_hidden );
	}

	/**
	 * Display custom no items found text
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function no_items() {
		_e( 'No orders found.', 'woocommerce-product-vendors' );

		return true;
	}

	/**
	 * Add bulk actions
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function get_bulk_actions() {
		$actions = array(
			'fulfilled'   => __( 'Mark Fulfilled', 'woocommerce-product-vendors' ),
			'unfulfilled' => __( 'Mark Unfulfilled', 'woocommerce-product-vendors' ),
		);

		return $actions;
	}

	/**
	 * Processes bulk actions
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function process_bulk_action() {
		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-orders' ) ) {
			return;
		}

		if ( empty( $_REQUEST['ids'] ) ) {
			return;
		}

		if ( false === $this->current_action() ) {
			return;
		}

		$status = sanitize_text_field( $this->current_action() );
		if ( ! in_array( $status, array( 'fulfilled', 'unfulfilled' ), true ) ) {
			return;
		}

		$ids = array_map( 'absint', $_REQUEST['ids'] );

		foreach ( $ids as $order_item_id ) {
			if ( ! WC_Product_Vendors_Utils::can_logged_in_user_manage_order_item( $order_item_id ) ) {
				continue;
			}

			WC_Product_Vendors_Utils::set_fulfillment_status( $order_item_id, $status );

			// Maybe update order status when product vendor is fulfilled or unfulfilled.
			$order = WC_Product_Vendors_Utils::get_order_by_order_item_id( $order_item_id );
			WC_Product_Vendors_Utils::maybe_update_order( $order, $status );

			WC_Product_Vendors_Utils::send_fulfill_status_email( $this->vendor_data, $status, $order_item_id );
		}

		WC_Product_Vendors_Utils::clear_reports_transients();

		return true;
	}

	/**
	 * Checks if order item is a variable product
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param array $item
	 * @return bool
	 */
	public function is_variable_product( $item ) {
		if ( ! empty( $item['variation_id'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Print column headers, accounting for hidden and sortable columns.
	 * this overrides WP core simply to make column headers use REQUEST instead of GET
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param bool $with_id Whether to set the id attribute or not
	 * @return bool
	 */
	public function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable ) = $this->get_column_info();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( 'paged', $current_url );

		if ( isset( $_REQUEST['orderby'] ) ) {
			$current_orderby = $_REQUEST['orderby'];
		} else {
			$current_orderby = '';
		}

		if ( isset( $_REQUEST['order'] ) && 'desc' == $_REQUEST['order'] ) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;

			$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . esc_html__( 'Select All', 'woocommerce-product-vendors' ) . '</label>'
				. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';

			$cb_counter++;
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			$style = '';

			if ( in_array( $column_key, $hidden ) ) {
				$style = 'display:none;';
			}

			$style = ' style="' . $style . '"';

			if ( 'cb' == $column_key ) {
				$class[] = 'check-column';
			} elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) ) {
				$class[] = 'num';
			}

			if ( isset( $sortable[ $column_key ] ) ) {
				list( $orderby, $desc_first ) = $sortable[ $column_key ];

				if ( $current_orderby == $orderby ) {
					$order = 'asc' == $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$id = $with_id ? "id='$column_key'" : '';

			if ( ! empty( $class ) ) {
				$class = "class='" . join( ' ', $class ) . "'";
			}

			echo "<th scope='col' $id $class $style>$column_display_name</th>";
		}
	}
}
