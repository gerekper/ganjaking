<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Commission Class.
 *
 * A class that generates the commission list.
 *
 * @category Commission
 * @package  WooCommerce Product Vendors/Commission
 * @version  2.0.0
 */
class WC_Product_Vendors_Store_Admin_Commission_List extends WP_List_Table {
	protected $commission;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function __construct( WC_Product_Vendors_Commission $commission ) {
		global $wpdb;

		$this->commission = $commission;

		parent::__construct( array(
			'singular'  => 'commission',
			'plural'    => 'commissions',
			'ajax'      => false,
		) );

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

		// Check if table exists before continuing.
		if ( ! WC_Product_Vendors_Utils::commission_table_exists() ) {
			return;
		}

		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$this->process_bulk_action();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$items_per_page = $this->get_items_per_page( 'commissions_per_page', apply_filters( 'wcpv_commission_list_default_item_per_page', 20 ) ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		$current_page   = $this->get_pagenum();

		$order_by       = ( ! empty( $_REQUEST['orderby'] ) ? sanitize_sql_orderby( wp_unslash( $_REQUEST['orderby'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$order_by       = ( $order_by && in_array( $order_by, array_keys( $sortable ), true ) ) ? $order_by : 'order_id';
		$order_by_order = ( 'ASC' === strtoupper( wp_unslash( $_REQUEST['order'] ?? '' ) ) ) ? 'ASC' : 'DESC'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// Query args.
		$sql_where = array();

		// check if it is a search.
		$search_arg = ! empty( $_REQUEST['s'] ) ? absint( $_REQUEST['s'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $search_arg ) {
			$sql_where[] = $wpdb->prepare( '`commission`.`order_id` = %d', $search_arg );
		} else {
			$m_arg = ! empty( $_REQUEST['m'] ) ? wp_unslash( $_REQUEST['m'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( $m_arg ) {
				$year  = absint( substr( $m_arg, 0, 4 ) );
				$month = absint( substr( $m_arg, 4, 2 ) );

				$sql_where[] = $wpdb->prepare( 'MONTH( `commission`.`order_date` ) = %d AND YEAR( `commission`.`order_date` ) = %d', $month, $year );
			}

			$status_arg = ! empty( $_REQUEST['commission_status'] ) ? wp_unslash( $_REQUEST['commission_status'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( $status_arg ) {
				$sql_where[] = $wpdb->prepare( '`commission`.`commission_status` = %s', $status_arg );
			}

			$vendor_arg = ! empty( $_REQUEST['vendor'] ) ? absint( $_REQUEST['vendor'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( $vendor_arg ) {
				$sql_where[] = $wpdb->prepare( '`commission`.`vendor_id` = %d', $vendor_arg );
			}
		}

		$sql_where = ( $sql_where ? ( ' WHERE ' . implode( ' AND ', $sql_where ) ) : '' );

		$total_items = absint(
			$wpdb->get_var(
				'SELECT COUNT(`commission`.`id`) FROM `' . WC_PRODUCT_VENDORS_COMMISSION_TABLE . "` AS `commission` $sql_where" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared
			)
		);

		$this->set_pagination_args(
			array(
				'total_items' => (float) $total_items,
				'per_page'    => $items_per_page,
			)
		);

		$offset = ( $current_page - 1 ) * $items_per_page;

		// Fetch items.
		$this->items = $wpdb->get_results(
			'SELECT * FROM `' . WC_PRODUCT_VENDORS_COMMISSION_TABLE . "` AS `commission` $sql_where ORDER BY `commission`.`$order_by` $order_by_order LIMIT $items_per_page OFFSET $offset" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared
		);

		return true;
	}

	/**
	 * Adds additional views
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param mixed $views
	 * @return bool
	 */
	public function get_views() {
		$views = array(
			'all' => '<li class="all"><a href="' . admin_url( 'admin.php?page=wcpv-commissions' ) . '">' . __( 'Show All', 'woocommerce-product-vendors' ) . '</a></li>',
		);

		return $views;
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
		<?php endif;
		$this->extra_tablenav( $which );
		$this->pagination( $which );
		?>

		<br class="clear" />
		</div>
		<?php
	}

	/**
	 * Adds filters to the table
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param string $position whether top/bottom
	 * @return bool
	 */
	public function extra_tablenav( $position ) {
		if ( 'top' === $position ) {
		?>
			<div class="alignleft actions">
				<?php
					$this->months_dropdown( 'commission' );
				?>
			</div>

			<div class="alignleft actions">
				<?php
					$this->status_dropdown( 'commission' );
				?>
			</div>

			<div class="alignleft actions">
				<?php
					$this->vendors_dropdown( 'commission' );
					submit_button( __( 'Filter', 'woocommerce-product-vendors' ), false, false, false );
				?>
			</div>

			<div class="alignleft actions">
				<?php
				// add data properties to the anchor tag so
				// we can easily retrieve it to compile the
				// CSV download
				$order_id          = '';
				$year              = '';
				$month             = '';
				$commission_status = '';
				$vendor            = '';

				if ( ! empty( $_REQUEST['s'] ) ) {
					$order_id = absint( $_REQUEST['s'] );

				} else {
					if ( ! empty( $_REQUEST['m'] ) ) {
						$year  = filter_var( substr( $_REQUEST['m'], 0, 4 ), FILTER_SANITIZE_NUMBER_INT ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$month = filter_var( substr( $_REQUEST['m'], 4, 2 ), FILTER_SANITIZE_NUMBER_INT ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					}

					if ( ! empty( $_REQUEST['commission_status'] ) ) {
						$commission_status = wc_clean( wp_unslash( $_REQUEST['commission_status'] ) );
					}

					if ( ! empty( $_REQUEST['vendor'] ) ) {
						$vendor = absint( $_REQUEST['vendor'] );
					}
				}
				?>

				<a href="#" class="button wcpv-export-commissions-button"
					data-nonce="<?php echo esc_attr( wp_create_nonce( '_wcpv_export_commissions_nonce' ) ); ?>"
					data-order_id="<?php echo esc_attr( $order_id ); ?>"
					data-year="<?php echo esc_attr( $year ); ?>"
					data-month="<?php echo esc_attr( $month ); ?>"
					data-commission_status="<?php echo esc_attr( $commission_status ); ?>"
					data-vendor="<?php echo esc_attr( $vendor ); ?>" download="<?php echo esc_attr( sprintf( __( 'commissions-%s.csv', 'woocommerce-product-vendors' ), date( 'm-d-Y' ) ) ); ?>">
					<?php esc_html_e( 'Export Commissions', 'woocommerce-product-vendors' ); ?></a>
			</div>

			<div class="alignleft actions">
				<a href="#" class="button wcpv-export-unpaid-commissions-button"
					data-nonce="<?php echo esc_attr( wp_create_nonce( '_wcpv_export_unpaid_commissions_nonce' ) ); ?>"
					download="<?php echo esc_attr( sprintf( __( 'unpaid-commissions-%s.csv', 'woocommerce-product-vendors' ), date( 'm-d-Y' ) ) ); ?>">
					<?php esc_html_e( 'Export All Unpaid Commissions', 'woocommerce-product-vendors' ); ?></a>
			</div>
		<?php
		}
	}

	/**
	 * Displays the months filter
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function months_dropdown( $post_type ) {
		global $wpdb, $wp_locale;

		// check if table exists before continuing
		if ( ! WC_Product_Vendors_Utils::commission_table_exists() ) {
			return;
		}

		$months = $wpdb->get_results( '
			SELECT DISTINCT YEAR( commission.order_date ) AS year, MONTH( commission.order_date ) AS month
			FROM ' . WC_PRODUCT_VENDORS_COMMISSION_TABLE . ' AS commission
			ORDER BY commission.order_date DESC
		' );

		$month_count = count( $months );

		if ( ! $month_count || ( 1 === $month_count && 0 === $months[0]->month ) ) {
			return;
		}

		$m = isset( $_REQUEST['m'] ) ? (int) $_REQUEST['m'] : 0;
		?>
		<select name="m" id="filter-by-date">
			<option<?php selected( $m, 0 ); ?> value='0'><?php esc_html_e( 'Show all dates', 'woocommerce-product-vendors' ); ?></option>
			<?php
			foreach ( $months as $arc_row ) {
				if ( 0 === $arc_row->year ) {
					continue;
				}

				$month = zeroise( $arc_row->month, 2 );
				$year  = $arc_row->year;

				if ( '00' === $month || '0' === $year ) {
					continue;
				}

				printf( "<option %s value='%s'>%s</option>\n",
					selected( $m, $year . $month, false ),
					esc_attr( $arc_row->year . $month ),
					/* translators: 1: month name, 2: 4-digit year */
					esc_html( sprintf( __( '%1$s %2$d', 'woocommerce-product-vendors' ), $wp_locale->get_month( $month ), $year ) )
				);
			}
			?>
		</select>

	<?php
	}

	/**
	 * Displays the commission status dropdown filter
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function status_dropdown( $post_type ) {
		$commission_status = isset( $_REQUEST['commission_status'] ) ? sanitize_text_field( $_REQUEST['commission_status'] ) : '';
	?>
		<select name="commission_status">
			<option <?php selected( $commission_status, '' ); ?> value=''><?php esc_html_e( 'Show all Statuses', 'woocommerce-product-vendors' ); ?></option>
			<option <?php selected( $commission_status, 'unpaid' ); ?> value="unpaid"><?php esc_html_e( 'Unpaid', 'woocommerce-product-vendors' ); ?></option>
			<option <?php selected( $commission_status, 'paid' ); ?> value="paid"><?php esc_html_e( 'Paid', 'woocommerce-product-vendors' ); ?></option>
			<option <?php selected( $commission_status, 'void' ); ?> value="void"><?php esc_html_e( 'Void', 'woocommerce-product-vendors' ); ?></option>
		</select>
	<?php
		return true;
	}

	/**
	 * Displays the vendors dropdown filter
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return bool
	 */
	public function vendors_dropdown( $post_type ) {
		global $wpdb;

		$vendor_filter = isset( $_REQUEST['vendor'] ) ? sanitize_text_field( $_REQUEST['vendor'] ) : '';

		$sql = 'SELECT DISTINCT vendor_name, vendor_id FROM ' . WC_PRODUCT_VENDORS_COMMISSION_TABLE;

		$vendor_lists = $wpdb->get_results( $sql );
		$vendor_ids   = array_unique( wp_list_pluck( $vendor_lists, 'vendor_id' ) );
		_prime_term_caches( $vendor_ids, false );

		/*
		 * Update vendor names to use the current name in the database.
		 *
		 * If the vendor has been deleted then the name from the SQL query will be
		 * used. This is to prevent the dropdown from being empty. If the vendor
		 * name has changed then the dropdown will display the last version received
		 * by the database query above.
		 */
		$unique_vendor_lists = array();
		foreach ( $vendor_lists as $vendor_list ) {
			$vendor_term = get_term( absint( $vendor_list->vendor_id ), WC_PRODUCT_VENDORS_TAXONOMY );

			if ( ! is_wp_error( $vendor_term ) && is_object( $vendor_term ) ) {
				$unique_vendor_lists[ $vendor_list->vendor_id ] = (object) array(
					'vendor_name' => wp_strip_all_tags( $vendor_term->name ),
					'vendor_id'   => $vendor_list->vendor_id,
				);

				continue;
			}

			$unique_vendor_lists[ $vendor_list->vendor_id ] = (object) array(
				'vendor_name' => wp_strip_all_tags( $vendor_list->vendor_name ),
				'vendor_id'   => $vendor_list->vendor_id,
			);
		}

		// Sort the list by vendor name.
		$unique_vendor_lists = wp_list_sort( $unique_vendor_lists, 'vendor_name', 'ASC', true );
	?>
		<select name="vendor">
			<option <?php selected( $vendor_filter, '' ); ?> value=""><?php esc_html_e( 'Show all Vendors', 'woocommerce-product-vendors' ); ?></option>

			<?php
			if ( ! empty( $unique_vendor_lists ) && is_array( $unique_vendor_lists ) ) {
				foreach ( $unique_vendor_lists as $vendor_list ) {
					if ( empty( $vendor_list->vendor_name ) || empty( $vendor_list->vendor_id ) ) {
						continue;
					}
					?>
					<option <?php selected( $vendor_filter, $vendor_list->vendor_id ); ?> value="<?php echo esc_attr( $vendor_list->vendor_id ); ?>"><?php echo esc_html( $vendor_list->vendor_name ); ?></option>
					<?php
				}
			}
			?>
		</select>
	<?php
		return true;
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
			'order_id'                => __( 'Order', 'woocommerce-product-vendors' ),
			'order_status'            => __( 'Order Status', 'woocommerce-product-vendors' ),
			'order_date'              => __( 'Order Date', 'woocommerce-product-vendors' ),
			'vendor_name'             => __( 'Vendor', 'woocommerce-product-vendors' ),
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
		return sprintf( '<input type="checkbox" name="ids[%d]" value="%d" />', esc_attr( $item->id ), esc_attr( $item->order_item_id ) );
	}

	/**
	 * Defines what data to show on each column
	 *
	 * @since   2.0.0
	 * @since   2.1.77 Use WC_Product_Vendors_Utils::get_total_commission_amount_html to display vendor commission.
	 *
	 * @param \stdClass  $item
	 * @param string $column_name
	 *
	 * @return mixed
	 * @version 2.0.0
	 */
	public function column_default( $item, $column_name ) {
		$order = wc_get_order( absint( $item->order_id ) );

		switch ( $column_name ) {
			case 'order_id' :
				if ( ! is_a( $order, 'WC_ORDER' ) ) {
					return sprintf( '%s ' . __( 'Order Not Found', 'woocommerce-product-vendors' ), '#' . absint( $item->order_id ) );
				}

				return sprintf( '<a href="%s">%s</a>', esc_url( $order->get_edit_order_url() ), $order->get_order_number() );

			case 'order_status' :
				if ( ! is_a( $order, 'WC_ORDER' ) ) {
					return __( 'N/A', 'woocommerce-product-vendors' );
				}

				$formated_order_status = WC_Product_Vendors_Utils::format_order_status( $order->get_status() );

				return sprintf( '<span class="wcpv-order-status-%s">%s</span>', esc_attr( $order->get_status() ), $formated_order_status );

			case 'order_date' :
				if ( ! is_a( $order, 'WC_Order' ) || ! $order->get_date_created() ) {
					return __( 'N/A', 'woocommerce-product-vendors' );
				}
				return WC_Product_Vendors_Utils::format_date( gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getTimestamp() ), wc_timezone_string() );

			case 'vendor_name' :
				$vendor = get_term( absint( $item->vendor_id ), WC_PRODUCT_VENDORS_TAXONOMY );

				if ( ! is_wp_error( $vendor ) && is_object( $vendor ) ) {
					return '<a href="' . esc_url_raw( get_edit_term_link( absint( $item->vendor_id ), WC_PRODUCT_VENDORS_TAXONOMY, 'product' ) ) . '">' . sanitize_text_field( $vendor->name ) . '</a>';

				} elseif ( ! empty( $item->vendor_name ) ) {
					return sanitize_text_field( $item->vendor_name );

				} else {
					return sprintf( '%s ' . __( 'Vendor Not Found', 'woocommerce-product-vendors' ), '#' . absint( $item->vendor_id ) );
				}

			case 'product_name' :
				$quantity = absint( $item->product_quantity );

				$var_attributes = '';
				$sku = '';
				$refund = '';

				$product = wc_get_product( absint( $item->product_id ) );

				if ( ! is_a( $order, 'WC_ORDER' ) || ! is_a( $product, 'WC_PRODUCT' ) ) {
					return sprintf( '%s ' . __( 'Product Not Found', 'woocommerce-product-vendors' ), '#' . absint( $item->product_id ) );
				}

				// check if product is a variable product
				if ( ! empty( $item->variation_id ) ) {
					$product    = wc_get_product( absint( $item->variation_id ) );
					$order_item = WC_Order_Factory::get_order_item( $item->order_item_id );
					$metadata   = array();
					if ( $order_item ) {
						$metadata = $order_item->get_formatted_meta_data();
					}

					if ( ! empty( $metadata ) ) {
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
					// translators: %s - Product SKU.
					$sku = '<br />' . sprintf( __( 'SKU: %s', 'woocommerce-product-vendors' ), $product->get_sku() );
				}

				$refunded_quantity = $order->get_qty_refunded_for_item( intval( $item->order_item_id ) );

				if ( $refunded_quantity ) {
					$refund = sprintf( __( '<br /><small class="wpcv-refunded">-%s</small>', 'woocommerce-product-vendors' ), absint( $refunded_quantity ) );
				}

				if ( is_object( $product ) ) {
					return edit_post_link( $quantity . 'x ' . sanitize_text_field( $item->product_name ), '', '', absint( $item->product_id ) ) . $var_attributes . $sku . $refund;

				} elseif ( ! empty( $item->product_name ) ) {
					return $quantity . 'x ' . sanitize_text_field( $item->product_name ) . $refund;
				}

			case 'total_commission_amount' :
				if ( ! is_a( $order, 'WC_Order' ) ) {
					return __( 'N/A', 'woocommerce-product-vendors' );
				}

				return WC_Product_Vendors_Utils::get_total_commission_amount_html( $item, $order );

			case 'commission_status' :
				$status = __( 'N/A', 'woocommerce-product-vendors' );

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

			case 'paid_date' :
				return WC_Product_Vendors_Utils::format_date( sanitize_text_field( $item->paid_date ) );

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
	 * Returns the columns that need sorting
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return array $sort
	 */
	public function get_sortable_columns() {
		$sort = array(
			'order_id'          => array( 'order_id', false ),
			'vendor_name'       => array( 'vendor_name', false ),
			'commission_status' => array( 'commission_status', false ),
		);

		return $sort;
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
		esc_html_e( 'No commissions found.', 'woocommerce-product-vendors' );

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
			'pay'         => __( 'Pay Commission', 'woocommerce-product-vendors' ),
			'unpaid'      => __( 'Mark Unpaid', 'woocommerce-product-vendors' ),
			'paid'        => __( 'Mark Paid', 'woocommerce-product-vendors' ),
			'void'        => __( 'Mark Void', 'woocommerce-product-vendors' ),
			'fulfilled'   => __( 'Mark Fulfilled', 'woocommerce-product-vendors' ),
			'unfulfilled' => __( 'Mark Unfulfilled', 'woocommerce-product-vendors' ),
			'delete'      => __( 'Delete Commission', 'woocommerce-product-vendors' ),
		);

		return $actions;
	}

	/**
	 * Processes bulk actions
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @return void
	 */
	public function process_bulk_action() {
		if ( ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ?? '' ), 'bulk-commissions' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		if ( empty( $_REQUEST['ids'] ) ) {
			return;
		}

		if ( false === $this->current_action() ) {
			return;
		}

		$status = sanitize_text_field( $this->current_action() );

		$ids = array_map( 'absint', $_REQUEST['ids'] );

		// handle pay bulk action
		if ( 'pay' === $this->current_action() ) {
			try {
				$this->commission->pay( $ids );

			} catch ( Exception $e ) {
				$message = $e->getMessage();

				if ( is_a( $e, 'PayPal\Exception\PayPalConnectionException' ) ) {
					$message .= ' Error details: ' . $e->getData();
				};

				WC_Product_Vendors_Logger::log( $message );
			}
		}

		$processed = 0;

		foreach ( $ids as $id => $order_item_id ) {
			switch ( $this->current_action() ) {
				case 'delete' :
					$this->commission->delete( $id );
					break;

				case 'unpaid' :
					$this->commission->update_status( $id, absint( $order_item_id ), 'unpaid' );
					break;

				case 'paid' :
					$this->commission->update_status( $id, absint( $order_item_id ), 'paid' );
					break;

				case 'fulfilled' :
					$this->set_fulfill_status( absint( $order_item_id ), 'fulfilled' );
					break;

				case 'unfulfilled' :
					$this->set_fulfill_status( absint( $order_item_id ), 'unfulfilled' );
					break;

				case 'void' :
					$this->commission->update_status( $id, absint( $order_item_id ), 'void' );
					break;
			}

			$processed++;
		}


		WC_Product_Vendors_Utils::clear_reports_transients();
		WC_Product_Vendors_Utils::update_order_item_meta( $order_item_id );

		do_action( 'wcpv_commission_list_bulk_action' );

		// Remove query args to prevent the resubmission of actions like paying commission.
		$redirect_url = wp_get_referer() ? remove_query_arg( array( 'action', '_wpnonce' ), wp_get_referer() ) : admin_url() . 'admin.php?page=wcpv-commissions';
		// Add query args to display notice of successful bulk action.
		$redirect_url = add_query_arg( 'processed', $processed, $redirect_url );

		if ( 'pay' === $this->current_action() ) {
			$redirect_url = add_query_arg( 'pay', true, $redirect_url );
		}

		wp_safe_redirect( esc_url_raw( $redirect_url ) );
		exit;
	}

	/**
	 * Set shipping status of an order item
	 *
	 * @access public
	 * @since 2.0.0
	 * @version 2.0.0
	 * @param int $order_item_id
	 * @param string $status
	 * @return bool
	 */
	public function set_fulfill_status( $order_item_id, $status = 'unfulfilled' ) {
		global $wpdb;

		$sql = "UPDATE {$wpdb->prefix}woocommerce_order_itemmeta";
		$sql .= ' SET `meta_value` = %s';
		$sql .= ' WHERE `order_item_id` = %d AND `meta_key` = %s';

		$status = $wpdb->get_var( $wpdb->prepare( $sql, $status, $order_item_id, '_fulfillment_status' ) );

		// Maybe update order status when product vendor is fulfilled or unfulfilled.
		$order = WC_Product_Vendors_Utils::get_order_by_order_item_id( $order_item_id );
		WC_Product_Vendors_Utils::maybe_update_order( $order, $this->current_action() );

		return true;
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

		$current_url = set_url_scheme( 'http://' . wp_unslash( $_SERVER['HTTP_HOST'] ) . wp_unslash( $_SERVER['REQUEST_URI'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$current_url = remove_query_arg( 'paged', $current_url );

		$current_orderby = wc_clean( wp_unslash( $_REQUEST['orderby'] ?? '' ) );

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

			$style = ' style="' . esc_attr( $style ) . '"';

			if ( 'cb' == $column_key ) {
				$class[] = 'check-column';
			} elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) ) {
				$class[] = 'num';
			}

			if ( isset( $sortable[ $column_key ] ) ) {
				list( $orderby, $desc_first ) = $sortable[ $column_key ];

				if ( $current_orderby === $orderby ) {
					$order = 'asc' == $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . esc_html( $column_display_name ) . '</span><span class="sorting-indicator"></span></a>';
			}

			$id = $with_id ? "id='" . esc_attr( $column_key ) . "'" : '';

			if ( ! empty( $class ) ) {
				$class = "class='" . esc_attr( join( ' ', $class ) ) . "'";
			}

			echo "<th scope='col' $id $class $style>$column_display_name</th>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return true;
	}
}
