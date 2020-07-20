<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/List-Table
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Pre-Orders List Table class
 *
 * Extends WP_List_Table to display pre-orders and related information
 *
 * @since 1.0
 * @extends \WP_List_Table
 */
class WC_Pre_Orders_List_Table extends WP_List_Table {

	private $message_transient_prefix = '_wc_pre_orders_messages_';

	/**
	 * Set of available views can include (All, Active, Completed, Cancelled, Trash)
	 *
	 * @var array
	 */
	private $views;

	/**
	 * Setup list table
	 *
	 * @see WP_List_Table::__construct()
	 * @since 1.0
	 * @return \WC_Pre_Orders_List_Table
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => __( 'Pre-Order', 'wc-pre-orders' ),
				'plural'   => __( 'Pre-Orders', 'wc-pre-orders' ),
				'ajax'     => false
			)
		);
	}

	/**
	 * Gets the bulk actions available for pre-orders: complete, cancel
	 * or message.
	 *
	 * @see WP_List_Table::get_bulk_actions()
	 * @since 1.0
	 * @return array associative array of action_slug => action_title
	 */
	public function get_bulk_actions() {

		$actions = array(
			'cancel'   => __( 'Cancel', 'wc-pre-orders' ),
			'complete' => __( 'Complete', 'wc-pre-orders' ),
			'message'  => __( 'Customer Message', 'wc-pre-orders' ),
		);

		return $actions;
	}

	/**
	 * Get list of views available (one per available pre-order status) plus
	 * default of 'all', with counts for each
	 *
	 * @see WP_List_Table::get_views()
	 * @since 1.0
	 * @return array
	 */
	public function get_views() {
		global $wpdb;

		if ( ! isset( $this->views ) ) {
			$this->views = array();

			// get counts of the current pre-order status.  Performance issues with a large db?
			$query = "
				SELECT {$wpdb->postmeta}.meta_value AS status, COUNT(*) AS count
				FROM {$wpdb->posts}
				INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)
				INNER JOIN {$wpdb->postmeta} AS mt1 ON ({$wpdb->posts}.ID = mt1.post_id)
				WHERE 1=1
					AND {$wpdb->posts}.post_type = 'shop_order'
					AND ({$wpdb->postmeta}.meta_key = '_wc_pre_orders_status')
					AND (mt1.meta_key = '_wc_pre_orders_is_pre_order' AND CAST(mt1.meta_value AS CHAR) = '1')
				GROUP BY {$wpdb->postmeta}.meta_value";

			$results = $wpdb->get_results( $query );

			// get the special all/trash counts and organize into status => count
			$counts = array( 'all' => 0 );
			$trash_count = 0;
			foreach ( $results as $row ) {
				if ( 'trash' == $row->status )
					$trash_count += $row->count;
				else {
					$counts[ $row->status ] = $row->count;
					$counts['all'] += $row->count;
				}
			}
			$counts['trash'] = $trash_count;

			// build the set of views, if any
			foreach ( $counts as $status => $count ) {
				if ( $count > 0 ) {
					if ( $this->get_current_pre_order_status( $counts ) == $status )
						$class = ' class="current"';
					else
						$class = '';

					$base_url = admin_url( 'admin.php?page=wc_pre_orders' );

					if ( isset( $_REQUEST['s'] ) )
						$base_url = add_query_arg( 's', $_REQUEST['s'], $base_url );

					$this->views[ $status ] = sprintf( '<a href="%s"%s>%s <span class="count">(%s)</span></a>', add_query_arg( 'pre_order_status', $status, $base_url ), $class, ucfirst( $status ), $count );
				}
			}
		}

		return $this->views;
	}

	/**
	 * Gest the currently selected pre-order status (the current view) if any.
	 * Defaults to 'all'.  Status is verified to exist in $available_status if
	 * provided
	 *
	 * @since 1.0
	 * @param array $available_status optional array of status => count used for validation
	 * @return string the current pre-order status
	 */
	public function get_current_pre_order_status( $available_status = null ) {
		// is there a status view selected?
		$status = isset( $_GET['pre_order_status'] ) ? $_GET['pre_order_status'] : 'all';

		// verify the status exists, otherwise default to 'all'
		if ( ! is_null( $available_status ) && ! isset( $available_status[ $status ] ) ) {
			return 'all';
		}

		//  otherwise just return the status
		return $status;
	}

	/**
	 * Returns the column slugs and titles
	 *
	 * @see WP_List_Table::get_columns()
	 * @since 1.0
	 * @return array of column slug => title
	 */
	public function get_columns() {

		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'status'            => '<span class="status_head tips" data-tip="' . wc_sanitize_tooltip( __( 'Status', 'wc-pre-orders' ) ) . '">' . __( 'Status', 'wc-pre-orders' ) . '</span>',
			'customer'          => __( 'Customer', 'wc-pre-orders' ),
			'product'           => __( 'Product', 'wc-pre-orders' ),
			'order'             => __( 'Order', 'wc-pre-orders' ),
			'order_date'        => __( 'Order Date', 'wc-pre-orders' ),
			'availability_date' => __( 'Availability Date', 'wc-pre-orders' ),
		);

		return $columns;
	}

	/**
	 * Returns the sortable columns.  We make order_date and order sortable
	 * because they're available right in the posts table, and they make sense
	 * to order over.
	 *
	 * @see WP_List_Table::get_sortable_columns()
	 * @since 1.0
	 * @return array of sortable column slug => array( 'orderby', boolean )
	 *         where true indicates the initial sort is descending
	 */
	public function get_sortable_columns() {

		return array(
			'order_date' => array( 'post_date', false ),  // false because the inital sort direction is DESC so we want the first column click to sort ASC
			'order'      => array( 'ID', false ),         // same logic as order_date
		);
	}

	/**
	 * Get content for the special checkbox column
	 *
	 * @see WP_List_Table::single_row_columns()
	 * @since 1.0
	 * @param WC_Order $order one row (item) in the table
	 * @return string the checkbox column content
	 */
	public function column_cb( $order ) {
		return '<input type="checkbox" name="order_id[]" value="' . ( version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id() ) . '" />';
	}

	/**
	 * Get column content, this is called once per column, per row item ($order)
	 * returns the content to be rendered within that cell.
	 *
	 * @see WP_List_Table::single_row_columns()
	 * @since 1.0
	 * @param WC_Order $order one row (item) in the table
	 * @param string $column_name the column slug
	 * @return string the column content
	 */
	public function column_default( $order, $column_name ) {
		$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();

		switch ( $column_name ) {

			case 'status':
				$actions = array();

				// determine any available actions
				if ( WC_Pre_Orders_Manager::can_pre_order_be_changed_to( 'cancelled', $order ) ) {
					$item = WC_Pre_Orders_Order::get_pre_order_item( $order );
					$product_id = $item['product_id'];

					$cancel_url = add_query_arg(
						array(
							'order_id' => $order_id,
							'action'   => 'cancel_pre_order',
						)
					);
					$cancel_url = wp_nonce_url( $cancel_url, 'cancel_pre_order', 'cancel_pre_order_nonce' );

					$actions['cancel'] = sprintf( '<a href="%s">%s</a>', esc_url( $cancel_url ), esc_html__( 'Cancel', 'wc-pre-orders' ) );
				}

				$column_content = sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', WC_Pre_Orders_Order::get_pre_order_status( $order ), wc_sanitize_tooltip( WC_Pre_Orders_Order::get_pre_order_status_to_display( $order ) ), WC_Pre_Orders_Order::get_pre_order_status_to_display( $order ) );
				$column_content .= $this->row_actions( $actions );
			break;

			case 'customer':
				$billing_email = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email();
				$user_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->user_id : $order->get_customer_id();

				if ( 0 !== $user_id ) {
					$column_content = sprintf( '<a href="%s">%s</a>', get_edit_user_link( $user_id ), $billing_email );
				} else {
					$column_content = $billing_email;
				}

			break;

			case 'product':
				$item = WC_Pre_Orders_Order::get_pre_order_item( $order );
				$product_edit = get_edit_post_link( $item['product_id'] );
				$column_content = ( $product_edit ) ? sprintf( '<a href="%s">%s</a>', $product_edit, $item['name'] ) : $item['name'];
			break;

			case 'order':
				$column_content = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $order_id ), sprintf( __( 'Order %s', 'wc-pre-orders' ), $order->get_order_number() ) );
			break;

			case 'order_date':
				$column_content = date_i18n( wc_date_format(), strtotime( version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' ) ) );
			break;

			case 'availability_date':
				$product = WC_Pre_Orders_Order::get_pre_order_product( $order );
				$column_content = WC_Pre_Orders_Product::get_localized_availability_date( $product, '--' );
			break;

			default:
				$column_content = '';
			break;
		}

		return $column_content;
	}

	/**
	 * Output any messages from the bulk action handling
	 *
	 * @since 1.0
	 */
	public function render_messages() {
		if ( isset( $_GET['message'] ) ) {

			$memo = get_transient( $this->message_transient_prefix . $_GET['message'] );

			if ( ! empty( $memo ) ) {

				delete_transient( $this->message_transient_prefix . $_GET['message'] );

				if ( ! empty( $memo['messages'] ) )
					echo '<div id="moderated" class="updated"><p>' . $memo['messages'] . '</p></div>';
			}
		}
	}

	/**
	 * Gets the current orderby, defaulting to 'post_date' if none is selected
	 */
	private function get_current_orderby() {
		return isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'post_date';
	}

	/**
	 * Gets the current orderby, defaulting to 'DESC' if none is selected
	 */
	private function get_current_order() {
		return isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
	}

	/**
	 * Prepare the list of pre-order items for display
	 *
	 * @see WP_List_Table::prepare_items()
	 * @since 1.0
	 */
	public function prepare_items() {

		$per_page = $this->get_items_per_page( 'wc_pre_orders_edit_pre_orders_per_page' );

		// main query args
		$args = array(
			'post_type'      => 'shop_order',
			'post_status'    => array( 'publish', 'trash' ),
			'posts_per_page' => $per_page,
			'paged'          => $this->get_pagenum(),
			'orderby'        => $this->get_current_orderby(),
			'order'          => $this->get_current_order(),
			'meta_query'     => array(
				array(
					'key'   => '_wc_pre_orders_is_pre_order',
					'value' => 1,
				)
			)
		);

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			$args['post_status'] = array_keys( wc_get_order_statuses() );
		}

		// Pre-order status view
		$args = $this->add_view_args( $args );

		// Filter: pre-orders by customer
		$args = $this->add_filter_args( $args );

		// handle search
		$args = $this->add_search_args( $args );

		$args = apply_filters( 'wc_pre_orders_edit_pre_orders_request', $args );

		$query = new WP_Query( $args );

		$this->items = array();

		foreach ( $query->posts as $order_post ) {
			$order = new WC_Order( $order_post );
			$this->items[] = $order;
		}

		$this->set_pagination_args(
			array(
				'total_items' => $query->found_posts,
				'per_page'    => $per_page,
				'total_pages' => ceil( $query->found_posts / $per_page ),
			)
		);
	}

	/**
	 * Adds in any query arguments based on the current filters
	 *
	 * @since 1.0
	 * @param array $args associative array of WP_Query arguments used to query and populate the list table
	 * @return array associative array of WP_Query arguments used to query and populate the list table
	 */
	private function add_filter_args( $args ) {
		global $wpdb;

		// filter by customer
		if ( isset( $_GET['_customer_user'] ) && $_GET['_customer_user'] > 0 ) {
			$args['meta_query'][] = array(
				'key'   => '_customer_user',
				'value' => (int) $_GET['_customer_user'],
			);
		}

		$product_ids = array();

		// filter by product
		if ( isset( $_GET['_product'] ) && $_GET['_product'] > 0 ) {
			$product_ids[] = $_GET['_product'];
		}

		// filter by availability months (find the corresponding products since availability date is set per product)
		if ( isset( $_GET['availability_date'] ) && $_GET['availability_date'] ) {

			$year = substr( $_GET['availability_date'], 0, 4 );
			$month = ltrim( substr( $_GET['availability_date'], 4, 2 ), '0' );

			$product_ids = array_merge(
				$product_ids,
				$wpdb->get_col( $wpdb->prepare("
					SELECT DISTINCT post_id
					FROM {$wpdb->postmeta}
					WHERE meta_key = '_wc_pre_orders_availability_datetime'
						AND YEAR( FROM_UNIXTIME( meta_value ) ) = %s AND MONTH( FROM_UNIXTIME( meta_value ) ) = %s
				", $year, $month ) )
			);
		}

		// filtering by product id
		if ( $product_ids ) {
			$post_ids = $wpdb->get_col(
				$wpdb->prepare("
					SELECT order_id
					FROM {$wpdb->prefix}woocommerce_order_items as order_items
					JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta
						ON order_itemmeta.order_item_id = order_items.order_item_id
					WHERE meta_key = '_product_id' AND meta_value IN (%s)
					",
					$product_ids
				)
			);

			$args['post__in'] = $post_ids;
		}

		return $args;
	}

	/**
	 * Adds in any query arguments based on the current view
	 *
	 * @since 1.0
	 * @param array $args associative array of WP_Query arguments used to query and populate the list table
	 * @return array associative array of WP_Query arguments used to query and populate the list table
	 */
	private function add_view_args( $args ) {
		$pre_order_status = $this->get_current_pre_order_status();

		if ( 'all' != $pre_order_status ) {
			$args['meta_query'][] = array(
				'key'   => '_wc_pre_orders_status',
				'value' => $pre_order_status,
			);
		}

		return $args;
	}

	/**
	 * Adds in any query arguments based on the search term
	 *
	 * @see woocommerce_shop_order_search_custom_fields()
	 * @since 1.0
	 * @param array $args associative array of WP_Query arguments used to query and populate the list table
	 * @return array associative array of WP_Query arguments used to query and populate the list table
	 */
	private function add_search_args( $args ) {

		global $wpdb;

		if ( isset( $_GET['s'] ) && $_GET['s'] ) {
			$search_fields = array_map( 'esc_attr', apply_filters( 'wc_pre_orders_search_fields', array(
				'_order_key',
				'_billing_email',
				'_wc_pre_order_status',
			) ) );

			$search_order_id = str_replace( 'Order #', '', $_GET['s'] );
			if ( ! is_numeric( $search_order_id ) ) {
				$search_order_id = 0;
			}

			// Search orders
			$post_ids = array_merge(
				$wpdb->get_col(
					$wpdb->prepare( "
						SELECT post_id
						FROM {$wpdb->postmeta}
						WHERE meta_key IN ('" . implode( "','", $search_fields ) . "')
						AND meta_value LIKE '%%%s%%'",
						esc_attr( $_GET['s'] )
					)
				),
				$wpdb->get_col(
					$wpdb->prepare( "
						SELECT order_id
						FROM {$wpdb->prefix}woocommerce_order_items as order_items
						WHERE order_item_name LIKE '%%%s%%'
						",
						esc_attr( $_GET['s'] )
					)
				),
				$wpdb->get_col(
					$wpdb->prepare( "
						SELECT posts.ID
						FROM {$wpdb->posts} as posts
						LEFT JOIN {$wpdb->postmeta} as postmeta ON posts.ID = postmeta.post_id
						LEFT JOIN {$wpdb->users} as users ON postmeta.meta_value = users.ID
						WHERE
							post_excerpt LIKE '%%%1\$s%%' OR
							post_title   LIKE '%%%1\$s%%' OR
							(
								meta_key = '_customer_user' AND
								(
									user_login    LIKE '%%%1\$s%%' OR
									user_nicename LIKE '%%%1\$s%%' OR
									user_email    LIKE '%%%1\$s%%' OR
									display_name  LIKE '%%%1\$s%%'
								)
							)
						",
						esc_attr( $_GET['s'] )
					)
				),
				array( $search_order_id )
			);

			$args['post__in'] = $post_ids;
		}

		return $args;
	}

	/**
	 * The text to display when there are no pre-orders
	 *
	 * @see WP_List_Table::no_items()
	 * @since 1.0
	 */
	public function no_items() {

		if ( isset( $_REQUEST['s'] ) ) : ?>
			<p><?php _e( 'No pre-orders found', 'wc-pre-orders' ); ?></p>
		<?php else : ?>
			<p><?php _e( 'Pre-Orders will appear here for you to view and manage once purchased by a customer.', 'wc-pre-orders' ); ?></p>
			<p><?php printf( __( '%sLearn more about managing pre-orders%s', 'wc-pre-orders' ), '<a href="http://docs.woothemes.com/document/pre-orders/#section-6" target="_blank">', ' &raquo;</a>' ); ?></p>
			<p><?php printf( __( '%sSetup a product to allow pre-orders%s', 'wc-pre-orders' ), '<a href="' . admin_url( 'post-new.php?post_type=product' ) . '">', ' &raquo;</a>' ); ?></p>
		<?php endif;
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination, which
	 * includes our Filters: Customers, Products, Availability Dates
	 *
	 * @see WP_List_Table::extra_tablenav();
	 * @since 1.0
	 * @param string $which the placement, one of 'top' or 'bottom'
	 */
	public function extra_tablenav( $which ) {
		global $woocommerce;

		if ( 'top' == $which ) {
			echo '<div class="alignleft actions">';

			// Customers, products
			if ( version_compare( WOOCOMMERCE_VERSION, '2.3.0', '<' ) ) {
			?>
			<select id="dropdown_customers" name="_customer_user">
				<option value=""><?php _e( 'Show all customers', 'wc-pre-orders' ) ?></option>
				<?php
					if ( ! empty( $_GET['_customer_user'] ) ) {
						$user = get_user_by( 'id', absint( $_GET['_customer_user'] ) );
						echo '<option value="' . absint( $user->ID ) . '" ';
						selected( 1, 1 );
						echo '>' . esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')</option>';
					}
				?>
			</select>

			<select id="dropdown_products" name="_product">
				<option value=""><?php _e( 'Show all Products', 'wc-pre-orders' ) ?></option>
				<?php
				if ( ! empty( $_GET['_product'] ) ) {
					$product = wc_get_product( absint( $_GET['_product'] ) );
					$product_name = $product->get_formatted_name();
					echo '<option value="' . absint( $product->get_id() ) . '" ';
					selected( 1, 1 );
					echo '>' . esc_html( $product_name ) . '</option>';
				}
				?>
			</select>

			<?php
			} else {
				$user_string = '';
				$user_id     = '';
				if ( ! empty( $_GET['_customer_user'] ) ) {
					$user_id     = absint( $_GET['_customer_user'] );
					$user        = get_user_by( 'id', $user_id );
					$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email );
				}

				$product_name = '';
				$product_id   = '';
				if ( ! empty( $_GET['_product'] ) ) {
					$product_id   = absint( $_GET['_product'] );
					$product      = wc_get_product( $product_id );
					$product_name = $product->get_formatted_name();
				}

				if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			?>
					<select id="dropdown_customers" style="width: 250px;" class="wc-customer-search" name="_customer_user" data-placeholder="<?php esc_attr_e( 'Search for a customer&hellip;', 'wc-pre-orders' ); ?>">
						<?php
							if ( ! empty( $_GET['_customer_user'] ) ) {
								echo '<option value="' . esc_attr( $user_id ) . '">' . wp_kses_post( $user_string ) . '</option>';
							}
						?>
					</select>
				<?php } else { ?>
					<input type="hidden" id="dropdown_customers" class="wc-customer-search" name="_customer_user" data-placeholder="<?php _e( 'Search for a customer&hellip;', 'wc-pre-orders' ); ?>" data-selected="<?php echo esc_attr( $user_string ); ?>" value="<?php echo $user_id; ?>" data-allow_clear="true" style="width: 250px;" />
				<?php } ?>

				<?php if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) { ?>
					<select id="dropdown_products" class="wc-product-search" style="width: 250px;" name="_product" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wc-pre-orders' ); ?>" data-action="woocommerce_json_search_products_and_variations">
						<?php
							if ( ! empty( $_GET['_product'] ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '">' . wp_kses_post( $product_name ) . '</option>';
							}
						?>
					</select>
				<?php } else { ?>
					<input type="hidden" id="dropdown_products" class="wc-product-search" name="_product" data-placeholder="<?php _e( 'Search for a product&hellip;', 'wc-pre-orders' ); ?>" data-selected="<?php echo esc_attr( $product_name ); ?>" value="<?php echo $product_id; ?>" data-allow_clear="true" style="width: 250px;" />

				<?php
				}
			}

			$this->render_availability_dates_dropdown();

			submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
			echo '</div>';

			// Bulk action fields
			echo '<div id="bulk-action-fields" style="clear:left;padding-top:10px;display:none;">';
			echo '<textarea cols="62" rows="3" name="customer_message" placeholder="' . __( 'Optional message to include in the email to the customer', 'wc-pre-orders' ) . '"></textarea>';
			echo '</div>';

			$javascript = "
				$( 'select[name=\"action\"]' ).change( function() {
					if ( -1 == $(this).val() ) {
						$( '#bulk-action-fields' ).slideUp();
					} else {
						$( '#bulk-action-fields' ).slideDown();
					}
				}).change();

				$( 'select[name=\"action2\"]' ).change( function() {
					if ( -1 == $(this).val() ) {
						$('#bulk-action-fields2').slideUp();
					} else {
						$('#bulk-action-fields2').slideDown();
					}
				}).change();

				$( 'span.cancel' ).click( function( e ) {
					if ( ! window.confirm( '" . __( 'Are you sure you want to cancel this pre-order?', 'wc-pre-orders' ) . "' ) ) {
						e.preventDefault();
					}
				});
			";

			if ( version_compare( WOOCOMMERCE_VERSION, '2.3.0', '<' ) ) {
				$chosen = "
					// Ajax Chosen Product Selectors
					$('select#dropdown_availability_dates').css('width', '250px').chosen();

					$('select#dropdown_customers').css('width', '250px').ajaxChosen({
						method:         'GET',
						url:            '" . admin_url( 'admin-ajax.php' ) . "',
						dataType:       'json',
						afterTypeDelay: 100,
						minTermLength:  1,
						data: {
							action:   'woocommerce_json_search_customers',
							security: '" . wp_create_nonce( "search-customers" ) . "',
							default:  '" . __( 'Show all customers', 'wc-pre-orders' ) . "'
						}
					}, function (data) {

						var terms = {};

						$.each(data, function (i, val) {
							terms[i] = val;
						});

						return terms;
					});

					$('select#dropdown_products').css( 'width', '250px').ajaxChosen({
						method:         'GET',
						url:            '" . admin_url( 'admin-ajax.php' ) . "',
						dataType:       'json',
						afterTypeDelay: 100,
						data: {
							action:   'woocommerce_json_search_products',
							security: '" . wp_create_nonce( 'search-products' ) . "'
						}
					}, function (data) {

						var terms = {};

						jQuery.each(data, function (i, val) {
							terms[i] = val;
						});

						return terms;
					});
				";

				$javascript = $chosen . $javascript;
			}

			if ( function_exists( 'wc_enqueue_js' ) ) {
				wc_enqueue_js( $javascript );
			} else {
				$woocommerce->add_inline_js( $javascript );
			}

		} elseif ( 'bottom' == $which ) {
			// Bulk action fields
			echo '<div id="bulk-action-fields2" style="clear:left;padding-top:10px;display:none;">';
			echo '<textarea cols="62" rows="3" name="customer_message2" placeholder="' . __( 'Optional message to include in the email to the customer', 'wc-pre-orders' ) . '"></textarea>';
			echo '</div>';
		}
	}

	/**
	 * Display a monthly dropdown for filtering items by availability date
	 *
	 * @since 1.0
	 */
	private function render_availability_dates_dropdown() {
		global $wpdb, $wp_locale;

		// Performance: we could always pull out the database order-by and sort in code to get rid of a 'filesort' from the query
		$months = $wpdb->get_results("
			SELECT DISTINCT YEAR( FROM_UNIXTIME( meta_value ) ) AS year, MONTH( FROM_UNIXTIME( meta_value ) ) AS month
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_wc_pre_orders_availability_datetime'
				AND meta_value > 0
			ORDER BY meta_value+0 DESC
		");

		$month_count = count( $months );

		if ( ! $month_count || ( 1 == $month_count && 0 == $months[0]->month ) ) {
			return;
		}

		$availability_date = isset( $_GET['availability_date'] ) ? (int) $_GET['availability_date'] : 0;
		?>
		<select id="dropdown_availability_dates" name="availability_date" class="wc-enhanced-select" style="width: 250px;">
			<option<?php selected( $availability_date, 0 ); ?> value='0'><?php esc_html_e( 'Show all Availability Dates', 'wc-pre-orders' ); ?></option>
			<?php
			foreach ( $months as $arc_row ) {
				if ( 0 == $arc_row->year ) {
					continue;
				}

				$month = zeroise( $arc_row->month, 2 );
				$year = $arc_row->year;

				printf( "<option %s value='%s'>%s</option>\n",
					selected( $availability_date, $year . $month, false ),
					esc_attr( $arc_row->year . $month ),
					/* translators: %1$s month, %2$d year */
					esc_html( sprintf( __( '%1$s %2$d', 'wc-pre-orders' ), $wp_locale->get_month( $month ), $year ) )
				);
			}
			?>
		</select>
		<?php
	}

} // end \WC_Pre_Orders_List_Table class
