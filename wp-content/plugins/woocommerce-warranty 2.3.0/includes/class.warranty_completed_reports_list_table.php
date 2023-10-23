<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Warranty_Completed_Reports_List_Table extends WP_List_Table {

	public $valid_orders = array();

	public function __construct( $args = array() ) {
		parent::__construct( $args );
	}

	public function get_columns() {
		$columns = array(
			'order_id' => esc_html__( 'Order ID', 'wc_warranty' ),
			'status'   => esc_html__( 'RMA Status', 'wc_warranty' ),
			'customer' => esc_html__( 'Customer Name', 'wc_warranty' ),
			'product'  => esc_html__( 'Product', 'wc_warranty' ),
			'validity' => esc_html__( 'Validity', 'wc_warranty' ),
			'date'     => esc_html__( 'Order Date', 'wc_warranty' ),
		);

		return $columns;
	}

	protected function get_sortable_columns() {
		$sortable_columns = array(
			'order_id' => array( 'order_id', false ),
			'date'     => array( 'date', false ),
		);

		return $sortable_columns;
	}

	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden  = array();

		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$per_page         = 10;
		$completed_status = warranty_get_completed_status();

		$args = array(
			'post_type' => 'warranty_request',
			'per_page'  => $per_page,
			'paged'     => $this->get_pagenum(),
			'tax_query' => array(
				array(
					'taxonomy' => 'shop_warranty_status',
					'field'    => 'slug',
					'terms'    => $completed_status->slug,
				),
			),
		);

		$get_data = warranty_request_get_data();
		if ( ! empty( $get_data['s'] ) ) {
			$args['meta_query'][] = array(
				'key'     => '_order_id',
				'value'   => absint( $get_data['s'] ),
				'compare' => 'LIKE',
			);
		}

		$warranty_req_query = new WP_Query( $args );

		$warranties = array();
		foreach ( $warranty_req_query->posts as $warranty_req ) {
			$order_id = get_post_meta( $warranty_req->ID, '_order_id', true );
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				continue;
			}

			$warranties[] = $warranty_req;
		}

		$this->items = $warranties;
		$total_items = count( $warranties );

		$this->items = $warranties;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		wp_reset_postdata();
	}

	public function column_order_id( $item ) {
		$order_id     = get_post_meta( $item->ID, '_order_id', true );
		$order        = wc_get_order( $order_id );
		$order_number = ( $order ) ? $order->get_order_number() : '-';
		$edit_url     = WC_Warranty_Compatibility::get_order_admin_edit_url( $order_id );

		if ( $edit_url ) {
			return '<a href="' . esc_url( $edit_url ) . '">' . $order_number . '</a>';
		}

		return $order_number;
	}

	public function column_status( $item ) {
		$term   = wp_get_post_terms( $item->ID, 'shop_warranty_status' );
		$status = isset( $term[0] ) ? $term[0]->name : '-';

		return $status;
	}

	public function column_customer( $item ) {
		$order_id   = get_post_meta( $item->ID, '_order_id', true );
		$order      = wc_get_order( $order_id );
		$first_name = $order ? $order->get_billing_first_name() : '-';
		$last_name  = $order ? $order->get_billing_last_name() : '-';

		return $first_name . ' ' . $last_name;
	}

	public function column_product( $item ) {
		$warranty_products = warranty_get_request_items( $item->ID );

		$out = '';

		foreach ( $warranty_products as $warranty_product ) {
			if ( empty( $warranty_product['product_id'] ) ) {
				continue;
			}

			$product = wc_get_product( $warranty_product['product_id'] );
			if ( ! ( $product instanceof WC_Product ) ) {
				continue;
			}

			/**
			 * @var $product \WC_Product
			 */
			$out .= '<a href="' . esc_url( admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' ) ) . '">' . esc_html( $product->get_title() ) . '</a> &times; ' . esc_html( $warranty_product['quantity'] ) . '<br/>';
		}

		return $out;
	}

	public function column_validity( $item ) {
		$order_id     = get_post_meta( $item->ID, '_order_id', true );
		$order        = wc_get_order( $order_id );
		$item_indexes = array_column( warranty_get_request_items( $item->ID ), 'order_item_index' );

		if ( empty( $item_indexes ) ) {
			return;
		}

		foreach ( $item_indexes as $item_index ) {
			$warranty    = wc_get_order_item_meta( $item_index, '_item_warranty', true );
			$warranty    = maybe_unserialize( $warranty );
			$addon_index = wc_get_order_item_meta( $item_index, '_item_warranty_selected', true );

			if ( $warranty ) {
				$completed = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;

				if ( 'addon_warranty' === $warranty['type'] ) {
					if ( ! empty( $completed ) ) {
						$addon = $warranty['addons'][ $addon_index ];
						$date  = warranty_get_date( $completed, $addon['value'], $addon['duration'] );

						echo esc_html( $date );
					}
				} elseif ( $warranty['type'] == 'included_warranty' ) {
					if ( $warranty['length'] == 'lifetime' ) {
						esc_html_e( 'Lifetime', 'wc_warranty' );
					} else {
						if ( ! empty( $completed ) ) {
							$date = warranty_get_date( $completed, $warranty['value'], $warranty['duration'] );

							echo esc_html( $date );
						}
					}
				}
				echo '<br>';
			}
		}
	}

	public function column_date( $item ) {
		$order_id = get_post_meta( $item->ID, '_order_id', true );
		$order    = wc_get_order( $order_id );

		return $order ? $order->get_date_modified()->date_i18n( WooCommerce_Warranty::get_datetime_format() ) : '-';
	}

	public function no_items() {
		esc_html_e( 'No requests found.', 'wc_warranty' );
	}

}

echo '<style type="text/css">
table.woocommerce_page_warranty_requests #status { width: 200px; }
.wc-updated {width: 95%; margin: 5px 0 15px; background-color: #ffffe0; border-color: #e6db55; padding: 0 .6em; -webkit-border-radius: 3px; border-radius: 3px; border-width: 1px; border-style: solid;}
.wc-updated p {margin: .5em 0 !important; padding: 2px;}
</style>';
$get_data = warranty_request_get_data();
$updated  = isset( $get_data['updated'] ) ? $get_data['updated'] : false;
if ( $updated ) {
	echo '<div class="updated"><p>' . esc_html( $updated ) . '</p></div>';
}

$completed_table = new Warranty_Completed_Reports_List_Table();
$completed_table->prepare_items();
?>

	<form action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" method="get" style="margin-top: 20px;">
		<input type="hidden" name="page" value="warranties-reports" />
		<?php
		$get_data      = warranty_request_get_data();
		$hidden_status = isset( $get_data['status'] ) ? $get_data['status'] : '';
		?>
		<input type="hidden" name="status" value="<?php echo esc_attr( $hidden_status ); ?>" />

		<p class="search-box">
			<label class="screen-reader-text" for="search"><?php esc_html_e( 'Search', 'wc_warranty' ); ?>:</label>
			<input type="search" id="search" name="s" value="<?php _admin_search_query(); ?>" placeholder="Order #" />
			<?php submit_button( esc_html__( 'Search', 'wc_warranty' ), 'button', false, false, array( 'id' => 'search-submit' ) ); ?>
		</p>
	</form>

<?php
$completed_table->display();
