<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Subscription List Table
 *
 * @class   YITH_YWSBS_Subscriptions_List_Table
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
class YITH_YWSBS_Subscriptions_List_Table extends WP_List_Table {

	/**
	 * @var string
	 */
	private $post_type;
	private $valid_status_to_trash;

	/**
	 * YITH_YWSBS_Subscriptions_List_Table constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct(
			array(
				'singular' => __( 'subscription', 'yith-woocommerce-subscription' ),
				'plural'   => __( 'subscriptions', 'yith-woocommerce-subscription' ),
				'ajax'     => false,
			)
		);

		$this->valid_status_to_trash = apply_filters( 'ywsbs_valid_status_to_trash', array( 'pending', 'cancelled', 'expired' ) );

		// parent::__construct( array() );
		$this->post_type = 'ywsbs_subscription';
		$this->process_bulk_action();

	}

	/**
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'status'     => __( 'Status', 'yith-woocommerce-subscription' ),
			'id'         => __( 'ID', 'yith-woocommerce-subscription' ),
			'recurring'  => __( 'Recurring', 'yith-woocommerce-subscription' ),
			'order'      => __( 'Order', 'yith-woocommerce-subscription' ),
			'user'       => __( 'User', 'yith-woocommerce-subscription' ),
			'started'    => __( 'Started', 'yith-woocommerce-subscription' ),
			'paymentdue' => __( 'Payment Due', 'yith-woocommerce-subscription' ),
			'enddate'    => __( 'End Date', 'yith-woocommerce-subscription' ),
			'expired'    => __( 'Expires', 'yith-woocommerce-subscription' ),
			'renewals'   => __( 'Renewals', 'yith-woocommerce-subscription' ),
			'payment'    => __( 'Payment Method', 'yith-woocommerce-subscription' ),
			'failed'     => __( 'Failed attempts', 'yith-woocommerce-subscription' ),
		);

		return apply_filters( 'ywsbs_subscription_table_list_columns', $columns );
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since 1.0.0
	 */
	function prepare_items() {
		global $wpdb, $_wp_column_headers;

		$screen = get_current_screen();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$args  = array(
			'post_type' => $this->post_type,
		);
		$query = new WP_Query( $args );

		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'ID';
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'DESC';

		// ($_REQUEST); die();
		$where        = '';
		$order_string = '';
		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$order_string = 'ORDER BY ywsbs_pm.meta_value ' . $order;
			switch ( $orderby ) {
				case 'status':
					$where = " AND ( ywsbs_pm.meta_key = 'status' ) ";
					break;
				case 'started':
					$where = " AND ( ywsbs_pm.meta_key = 'start_date' ) ";
					break;
				case 'renewals':
					$where = " AND ( ywsbs_pm.meta_key = 'rates_payed' ) ";
					break;
				case 'paymentdue':
					$where = " AND ( ywsbs_pm.meta_key = 'payment_due_date' ) ";
					break;
				case 'expired':
					$where = " AND ( ywsbs_pm.meta_key = 'expired_date' ) ";
					break;
				case 'payment':
					$where = " AND ( ywsbs_pm.meta_key = 'payment_method_title' ) ";
					break;
				case 'enddate':
					$where = " AND ( ywsbs_pm.meta_key = 'end_date' ) ";
					break;
				case 'recurring':
					$order_string = 'ORDER BY ywsbs_pm.meta_value+0 ' . $order;
					$where        = " AND ( ywsbs_pm.meta_key = 'line_total' ) ";
					break;
				default:
					$order_string = ' ORDER BY ywsbs_p.' . $orderby . ' ' . $order;
			}
		}

		$join = 'INNER JOIN ' . $wpdb->prefix . 'postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id ) ';

		/*
		 FILTERS */
		// by user
		if ( isset( $_REQUEST['_customer_user'] ) && ! empty( $_REQUEST['_customer_user'] ) ) {
			$join  .= 'INNER JOIN ' . $wpdb->prefix . 'postmeta as ywsbs_pm2 ON ( ywsbs_p.ID = ywsbs_pm2.post_id ) ';
			$where .= " AND ( ywsbs_pm2.meta_key = 'user_id' AND ywsbs_pm2.meta_value = '" . $_REQUEST['_customer_user'] . "' )";
		}

		if ( isset( $_REQUEST['payment'] ) && ! empty( $_REQUEST['payment'] ) && $_REQUEST['payment'] != 'all' ) {
			$join  .= 'INNER JOIN ' . $wpdb->prefix . 'postmeta as ywsbs_pm4 ON ( ywsbs_p.ID = ywsbs_pm4.post_id ) ';
			$where .= " AND ( ywsbs_pm4.meta_key = 'payment_method' AND ywsbs_pm4.meta_value = '" . $_REQUEST['payment'] . "' )";
		}

		if ( isset( $_REQUEST['status'] ) && ! empty( $_REQUEST['status'] ) && $_REQUEST['status'] != 'all' && $_REQUEST['status'] != 'trash' ) {
			$join  .= 'INNER JOIN ' . $wpdb->prefix . 'postmeta as ywsbs_pm3 ON ( ywsbs_p.ID = ywsbs_pm3.post_id ) ';
			$where .= " AND ( ywsbs_pm3.meta_key = 'status' AND ywsbs_pm3.meta_value = '" . $_REQUEST['status'] . "' )";
		}

		if ( isset( $_REQUEST['status'] ) && $_REQUEST['status'] == 'trash' ) {
			$where .= " AND ywsbs_p.post_status = 'trash' ";
		} else {
			$where .= " AND ywsbs_p.post_status = 'publish' ";
		}

		if ( isset( $_REQUEST['m'] ) ) {
			// The "m" parameter is meant for months but accepts datetimes of varying specificity
			if ( $_REQUEST['m'] ) {
				$where .= ' AND YEAR(ywsbs_p.post_date)=' . substr( $_REQUEST['m'], 0, 4 );
				if ( strlen( $_REQUEST['m'] ) > 5 ) {
					$where .= ' AND MONTH(ywsbs_p.post_date)=' . substr( $_REQUEST['m'], 4, 2 );
				}
				if ( strlen( $_REQUEST['m'] ) > 7 ) {
					$where .= ' AND DAYOFMONTH(ywsbs_p.post_date)=' . substr( $_REQUEST['m'], 6, 2 );
				}
				if ( strlen( $_REQUEST['m'] ) > 9 ) {
					$where .= ' AND HOUR(ywsbs_p.post_date)=' . substr( $_REQUEST['m'], 8, 2 );
				}
				if ( strlen( $_REQUEST['m'] ) > 11 ) {
					$where .= ' AND MINUTE(ywsbs_p.post_date)=' . substr( $_REQUEST['m'], 10, 2 );
				}
				if ( strlen( $_REQUEST['m'] ) > 13 ) {
					$where .= ' AND SECOND(ywsbs_p.post_date)=' . substr( $_REQUEST['m'], 12, 2 );
				}
			}
		}

		$join  = apply_filters( 'ywsbs_subscriptions_list_table_join', $join );
		$where = apply_filters( 'ywsbs_subscriptions_list_table_where', $where );

		// Check if the request came from search form
		$query_search = ( isset( $_REQUEST['s'] ) && ! empty( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : '';
		if ( $query_search ) {
			$search = " AND ( ywsbs_p.ID LIKE '%$query_search%' OR ( ywsbs_pm.meta_key='product_name' AND  ywsbs_pm.meta_value LIKE '%$query_search%' ) ) ";
		} else {
			$search = '';
		}
		$where .= apply_filters( 'ywsbs_subscriptions_list_table_search', $search, $_REQUEST );

		$query      = "SELECT ywsbs_p.* FROM $wpdb->posts as ywsbs_p  $join
                WHERE 1=1 $where
                AND ywsbs_p.post_type = 'ywsbs_subscription'
                GROUP BY ywsbs_p.ID $order_string";
		$totalitems = $wpdb->query( $query );

		$perpage = 15;
		// Which page is this?
		$paged = ! empty( $_GET['paged'] ) ? $_GET['paged'] : '';
		// Page Number
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}
		// How many pages do we have in total?
		$totalpages = ceil( $totalitems / $perpage );
		// adjust the query to take pagination into account
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
		// The pagination links are automatically built according to those parameters

		$_wp_column_headers[ $screen->id ] = $columns;
		$this->items                       = $wpdb->get_results( $query );

	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string|void
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'status':
				$status = get_post_meta( $item->ID, 'status', true );

				return '<span class="status ' . $status . '">' . $status . '</span>';
				break;
			case 'user':
				$user_id   = get_post_meta( $item->ID, 'user_id', true );
				$user_data = get_userdata( $user_id );
				$text      = '';
				if ( ! empty( $user_data ) ) {
					$text = '<a href="' . admin_url( 'profile.php?user_id=' . $user_id ) . '">' . $user_data->user_nicename . '</a>';
				}

				return $text;
				break;
			case 'recurring':
				$recurring = apply_filters( 'ywsbs_get_recurring_totals', get_post_meta( $item->ID, 'line_total', true ), $item->ID );
				$currency  = get_post_meta( $item->ID, 'order_currency', true );

				return wc_price( $recurring, array( 'currency' => $currency ) );
				break;
			case 'renewals':
				$rates_payed = get_post_meta( $item->ID, 'rates_payed', true );

				return $rates_payed;
				break;
			case 'order':
				$order_ids = get_post_meta( $item->ID, 'order_ids', true );
				$text      = '';
				if ( ! empty( $order_ids ) ) {
					$last_order = apply_filters( 'ywsbs_last_renew_order', end( $order_ids ) );

					$text = '<a href="' . admin_url( 'post.php?post=' . $last_order . '&action=edit' ) . '">#' . $last_order . '</a>';
				}

				return $text;
				break;
			case 'started':
				$start_date = get_post_meta( $item->ID, 'start_date', true );

				return ( $start_date ) ? date_i18n( wc_date_format(), $start_date ) : '';
				break;
			case 'paymentdue':
				$paymentdue_date = get_post_meta( $item->ID, 'payment_due_date', true );

				return ( $paymentdue_date ) ? date_i18n( wc_date_format(), $paymentdue_date ) : '';
				break;
			case 'enddate':
				$end_date = get_post_meta( $item->ID, 'end_date', true );

				return ( $end_date ) ? date_i18n( wc_date_format(), $end_date ) : '-';
				break;
			case 'expired':
				$expired_date = get_post_meta( $item->ID, 'expired_date', true );
				$expired_date = ( $expired_date != '' ) ? $expired_date : '';

				return ( $expired_date ) ? date_i18n( wc_date_format(), $expired_date ) : __( 'Never', 'yith-woocommerce-subscription' );
				break;
			case 'payment':
				$payment_method = get_post_meta( $item->ID, 'payment_method_title', true );
				return $payment_method;
				break;
			case 'failed':
				$order_id          = get_post_meta( $item->ID, 'order_id', true );
				$failed_attemps    = get_post_meta( $order_id, 'failed_attemps', true ) ? get_post_meta( $order_id, 'failed_attemps', true ) : 0;
				$payment_method    = get_post_meta( $item->ID, 'payment_method', true );
				$max_failed_attemp = ywsbs_get_max_failed_attemps_list();
				if ( isset( $max_failed_attemp[ $payment_method ] ) ) {
					return $failed_attemps . '/' . $max_failed_attemp[ $payment_method ];
				}

				break;

			default:
				return apply_filters( 'ywsbs_column_default', '', $item, $column_name ); // Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * @return array
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'id'         => array( 'ID', false ),
			'status'     => array( 'status', false ),
			'started'    => array( 'started', false ),
			'paymentdue' => array( 'paymentdue', false ),
			'expired'    => array( 'expired', false ),
			'enddate'    => array( 'enddate', false ),
			'payment'    => array( 'payment', false ),
			'recurring'  => array( 'recurring', false ),
			'renewals'   => array( 'renewals', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Handles the checkbox column output.
	 *
	 * @since 1.0.0
	 *
	 * @param object $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		if ( in_array( get_post_meta( $item->ID, 'status', true ), $this->valid_status_to_trash ) ) {
			return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item->ID );
		}
	}

	/**
	 * Handles the ID column output.
	 *
	 * @since 1.0.0
	 *
	 * @param $item
	 *
	 * @return string
	 */
	function column_id( $item ) {

		$product_name = get_post_meta( $item->ID, 'product_name', true );
		$quantity     = get_post_meta( $item->ID, 'quantity', true );
		$status       = get_post_meta( $item->ID, 'status', true );

		$qty = ( $quantity > 1 ) ? ' x ' . $quantity : '';

		$actions['edit'] = '<a href="' . admin_url( 'post.php?post=' . $item->ID . '&action=edit' ) . '">' . __( 'Edit', 'yith-woocommerce-subscription' ) . '</a>';

		$post_type_object = get_post_type_object( $this->post_type );

		if ( 'trash' == $item->post_status ) {
			$actions['untrash'] = '<a title="' . esc_attr__( 'Restore this item from the Trash', 'yith-woocommerce-subscription' ) . '" href="' . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $item->ID ) ), 'untrash-post_' . $item->ID ) . '">' . __( 'Restore', 'woocommerce' ) . '</a>';
		} elseif ( EMPTY_TRASH_DAYS && in_array( $status, $this->valid_status_to_trash ) ) {
			$actions['trash'] = '<a title="' . esc_attr( __( 'Move this item to the Trash', 'yith-woocommerce-subscription' ) ) . '" href="' . get_delete_post_link( $item->ID ) . '">' . __( 'Trash', 'yith-woocommerce-subscription' ) . '</a>';
		}
		if ( 'trash' == $item->post_status || ! EMPTY_TRASH_DAYS ) {
			$actions['delete'] = '<a title="' . esc_attr( __( 'Delete this item permanently', 'yith-woocommerce-subscription' ) ) . '" href="' . get_delete_post_link( $item->ID, '', true ) . '">' . __( 'Delete Permanently', 'yith-woocommerce-subscription' ) . '</a>';
		}

		return sprintf( '<strong>#%1$s</strong> - %2$s %3$s', $item->ID, $product_name . $qty, $this->row_actions( $actions ) );
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination, which
	 * includes our Filters: Customers, Products, Availability Dates
	 *
	 * @see   WP_List_Table::extra_tablenav();
	 * @since 1.0.0
	 *
	 * @param string $which the placement, one of 'top' or 'bottom'
	 */
	public function extra_tablenav( $which ) {
		if ( 'top' == $which ) {

			echo '<div class="alignleft actions">';

			// Customers, products
			$user_string = '';
			$customer_id = '';
			if ( ! empty( $_REQUEST['_customer_user'] ) ) {
				$customer_id = absint( $_REQUEST['_customer_user'] );
				$user        = get_user_by( 'id', $customer_id );
				$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email );
			}

			$args = array(
				'type'             => 'hidden',
				'class'            => 'wc-customer-search',
				'id'               => 'customer_user',
				'name'             => '_customer_user',
				'data-placeholder' => __( 'Show All Customers', 'yith-woocommerce-subscription' ),
				'data-allow_clear' => true,
				'data-selected'    => array( $customer_id => esc_attr( $user_string ) ),
				'data-multiple'    => false,
				'value'            => $customer_id,
				'style'            => 'width:200px',
			);

			yit_add_select2_fields( $args );

			echo '</div>';

			global $wpdb;

			$status         = ywsbs_get_status();
			$options        = '<option value="">' . __( 'All statuses', 'yith-woocommerce-subscription' ) . '</option>';
			$current_status = '';
			if ( ! empty( $_REQUEST['status'] ) ) {
				$current_status = $_REQUEST['status'];
			}

			foreach ( $status as $key => $value ) {
				$q = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT count(*) as counter FROM $wpdb->posts as ywsbs_p INNER JOIN " . $wpdb->prefix . "postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                  WHERE ywsbs_pm.meta_key = 'status' AND ywsbs_pm.meta_value = '%s'",
						$key
					)
				);

				$selected = selected( $key, $current_status, false );
				$options .= '<option value="' . $key . '" ' . $selected . '>' . $key . ' (' . $q . ')</option>';
			}

			?>
			<div class="alignleft actions">
				<select name="status" id="subscription_status">
					<?php echo $options; ?>
				</select>
				<?php $this->months_dropdown( 'ywsbs_subscription' ); ?>
			</div>

			<?php

			$options         = '<option value="">' . __( 'All Payment Methods', 'yith-woocommerce-subscription' ) . '</option>';
			$current_payment = '';
			if ( ! empty( $_REQUEST['payment'] ) ) {
				$current_payment = $_REQUEST['payment'];
			}
			$gateways = WC()->payment_gateways()->get_available_payment_gateways();
			foreach ( $gateways as $key => $gateway ) {
				$q = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT count(*) as counter FROM $wpdb->posts as ywsbs_p INNER JOIN " . $wpdb->prefix . "postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                  WHERE ywsbs_pm.meta_key = 'payment_method' AND ywsbs_pm.meta_value = '%s'",
						$key
					)
				);

				if ( $q > 0 ) {
					$selected = selected( $key, $current_payment, false );
					$options .= '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $gateway->get_title() ) . ' (' . esc_html( $q ) . ')</option>';
				}
			}
			?>
			<div class="alignleft actions">
				<select name="payment" id="subscription_payment">
					<?php echo $options; ?>
				</select>

				<?php submit_button( __( 'Filter', 'yith-woocommerce-subscription' ), 'button', false, false, array( 'id' => 'post-query-submit' ) ); ?>
			</div>

			<?php

		}
	}

	/**
	 * Get an associative array ( id => link ) with the list
	 * of views available on this table.
	 *
	 * @since  1.1.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_views() {
		global $wpdb;

		$links  = array();
		$status = ywsbs_get_status();

		// count all subscriptions
		$q = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as counter FROM $wpdb->posts as ywsbs_p WHERE ywsbs_p.post_type = '%s' AND ywsbs_p.post_status= 'publish' ", $this->post_type ) );

		if ( $q ) {
			$links['all'] = '<a href="' . add_query_arg( 'status', 'all' ) . '">' . __( 'All', 'yith-woocommerce-subscription' ) . ' (' . $q . ')</a>';
		}

		foreach ( $status as $key => $value ) {
			$q = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT count(*) as counter FROM $wpdb->posts as ywsbs_p INNER JOIN " . $wpdb->prefix . "postmeta as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
                  WHERE  ywsbs_p.post_status= 'publish' AND ywsbs_pm.meta_key = 'status' AND ywsbs_pm.meta_value = '%s'",
					$key
				)
			);

			if ( $q ) {
				$links[ $key ] = '<a href="' . add_query_arg( 'status', $key ) . '">' . ucfirst( $value ) . ' (' . $q . ')</a>';
			}
		}

		// check if there are subscription in trash
		$q = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as counter FROM $wpdb->posts as ywsbs_p WHERE ywsbs_p.post_type = '%s' AND ywsbs_p.post_status= 'trash' ", $this->post_type ) );
		if ( $q ) {
			$links['trash'] = '<a href="' . add_query_arg( 'status', 'trash' ) . '">' . __( 'Trash', 'yith-woocommerce-subscription' ) . ' (' . $q . ')</a>';
		}

		return $links;
	}

	/**
	 * Get bulk action
	 *
	 * @since  1.0.0
	 * @return array|false|string
	 */
	function get_bulk_actions() {

		if ( isset( $_GET['status'] ) && 'trash' == $_GET['status'] ) {
			return array(
				'untrash' => __( 'Restore', 'yith-woocommerce-subscription' ),
				'delete'  => __( 'Delete Permanently', 'yith-woocommerce-subscription' ),
			);
		}

		return array(
			'trash' => __( 'Move to Trash', 'yith-woocommerce-subscription' ),
		);

	}


	public function process_bulk_action() {

		$actions = $this->current_action();

		if ( ! empty( $actions ) && isset( $_REQUEST[ $this->_args['singular'] ] ) ) {

			$subscriptions = (array) $_REQUEST[ $this->_args['singular'] ];

			foreach ( $subscriptions as $subscription_id ) {

				$post = get_post( $subscription_id );

				if ( ! ( $post && $post->post_type == $this->post_type ) ) {
					continue;
				}

				$post_type_object = get_post_type_object( $post->post_type );

				if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {

					$subscription_obj = ywsbs_get_subscription( $subscription_id );

					// this filters is added to check if there's some recurring payment
					if ( in_array( $actions, array( 'delete', 'trash' ) ) && ! in_array( $subscription_obj->status, $this->valid_status_to_trash ) ) {
						continue;
					}

					switch ( $actions ) {
						case 'delete':
							wp_delete_post( $subscription_id, true );
							do_action( 'ywsbs_subscription_deleted', $subscription_id );
							break;
						case 'untrash':
							wp_untrash_post( $subscription_id );
							do_action( 'ywsbs_subscription_untrashed', $subscription_id );
							break;
						case 'trash':
							wp_trash_post( $subscription_id );
							do_action( 'ywsbs_subscription_trashed', $subscription_id );
							break;
						default:
					}
				}
			}
		}
	}

	/**
	 * Display the search box.
	 *
	 * @since  1.1.0
	 * @access public
	 *
	 * @param string $text     The search button text
	 * @param string $input_id The search input id
	 */
	public function search_box( $text, $input_id ) {

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}

		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id; ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id; ?>" name="s" value="<?php _admin_search_query(); ?>" placeholder="<?php _e( 'Search', 'yith-woocommerce-subscription' ); ?>" />
			<?php submit_button( $text, 'button', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
		<?php
	}


}
