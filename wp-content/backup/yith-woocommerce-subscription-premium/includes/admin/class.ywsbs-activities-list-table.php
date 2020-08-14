<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Activities List Table
 *
 * @class   YITH_YWSBS_Activities_List_Table
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

class YITH_YWSBS_Activities_List_Table extends WP_List_Table {

	/**
	 * @var string
	 */
	private $post_type;

	/**
	 * YITH_YWSBS_Activities_List_Table constructor.
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		parent::__construct( array() );
		$this->post_type = 'ywsbs_activity';
	}

	/**
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'activity'       => __( 'Activity', 'yith-woocommerce-subscription' ),
			'status'         => __( 'Status', 'yith-woocommerce-subscription' ),
			'subscription'   => __( 'Subscription', 'yith-woocommerce-subscription' ),
			'order'          => __( 'Order', 'yith-woocommerce-subscription' ),
			'description'    => __( 'Description', 'yith-woocommerce-subscription' ),
			'timestamp_date' => __( 'Date', 'yith-woocommerce-subscription' ),
		);
		return $columns;
	}

	/**
	 *
	 */
	function prepare_items() {
		global $wpdb, $_wp_column_headers;

		$screen = get_current_screen();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'act.id';
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'DESC';

		$join         = '';
		$where        = '';
		$order_string = '';
		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$order_string = ' ORDER BY ' . $orderby . ' ' . $order;
		}

		/*
		 FILTERS */
		// by user
		if ( isset( $_REQUEST['activity_status'] ) && ! empty( $_REQUEST['activity_status'] ) ) {
			$where .= " AND ( status = '" . $_REQUEST['activity_status'] . "' ) ";
		}

		if ( isset( $_REQUEST['m'] ) ) {
			// The "m" parameter is meant for months but accepts datetimes of varying specificity
			if ( $_REQUEST['m'] ) {
				$where .= ' AND YEAR(timestamp_date)=' . substr( $_REQUEST['m'], 0, 4 );
				if ( strlen( $_REQUEST['m'] ) > 5 ) {
					$where .= ' AND MONTH(timestamp_date)=' . substr( $_REQUEST['m'], 4, 2 );
				}
				if ( strlen( $_REQUEST['m'] ) > 7 ) {
					$where .= ' AND DAYOFMONTH(timestamp_date)=' . substr( $_REQUEST['m'], 6, 2 );
				}
				if ( strlen( $_REQUEST['m'] ) > 9 ) {
					$where .= ' AND HOUR(timestamp_date)=' . substr( $_REQUEST['m'], 8, 2 );
				}
				if ( strlen( $_REQUEST['m'] ) > 11 ) {
					$where .= ' AND MINUTE(timestamp_date)=' . substr( $_REQUEST['m'], 10, 2 );
				}
				if ( strlen( $_REQUEST['m'] ) > 13 ) {
					$where .= ' AND SECOND(timestamp_date)=' . substr( $_REQUEST['m'], 12, 2 );
				}
			}
		}

		$table_name = $wpdb->prefix . 'yith_ywsbs_activities_log';
		$search     = ! empty( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

		$join  = apply_filters( 'ywsbs_activities_list_table_join', $join, $table_name );
		$where = apply_filters( 'ywsbs_activities_list_table_where', $where, $table_name );

		$query = "SELECT * FROM $table_name as act $join  where 1=1 $where $order_string";

		if ( $search != '' ) {
			$query = "SELECT * FROM $table_name as act $join where 1=1 AND  act.subscription LIKE '%$search%' OR act.order = '%$search%' OR act.activity LIKE '%$search%' OR act.description LIKE '%$search%'  $where $order_string";
		}

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
	 * @return string
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'status':
				return '<span class="status ' . $item->status . '">' . ywsbs_get_activity_status( $item->status ) . '</span>';
				break;
			case 'subscription':
				return '<a href="' . admin_url( 'post.php?post=' . $item->subscription . '&action=edit' ) . '">#' . $item->subscription . '</a>';
				break;
			case 'order':
				return ( $item->order ) ? '<a href="' . admin_url( 'post.php?post=' . $item->order . '&action=edit' ) . '">#' . $item->order . '</a>' : '';
				break;
			default:
				return $item->$column_name; // Show the whole array for troubleshooting purposes
		}
	}


	/**
	 * @return array
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'status'         => array( 'status', false ),
			'activity'       => array( 'activity', false ),
			'subscription'   => array( 'subscription', false ),
			'order'          => array( 'order', false ),
			'timestamp_date' => array( 'timestamp_date', false ),
		);
		return $sortable_columns;
	}



	/**
	 * Extra controls to be displayed between bulk actions and pagination, which
	 * includes our Filters: Customers, Products, Availability Dates
	 *
	 * @see WP_List_Table::extra_tablenav();
	 * @since 1.0
	 *
	 * @param string $which the placement, one of 'top' or 'bottom'
	 */
	public function extra_tablenav( $which ) {
		if ( 'top' == $which ) {
			echo '<div class="alignleft actions">';
			global $wpdb;
			$table_name = $wpdb->prefix . 'yith_ywsbs_activities_log';

			$status         = array( 'success', 'error', 'info' );
			$options        = '<option value="">' . __( 'All status', 'yith-woocommerce-subscription' ) . '</option>';
			$current_status = '';
			if ( ! empty( $_REQUEST['activity_status'] ) ) {
				$current_status = $_REQUEST['activity_status'];
			}

			foreach ( $status as $key ) {
				$q = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as counter FROM $table_name WHERE status = '%s'", $key ) );

				$checked  = checked( $key, $current_status, false );
				$options .= '<option value="' . $key . '" ' . $checked . '>' . $key . ' (' . $q . ')</option>';
			}

			add_filter( 'months_dropdown_results', array( $this, 'get_months' ), 10 );
			?>
				<div class="alignleft actions">
					<select name="activity_status" id="activity_status">
						<?php echo $options; ?>
					</select>

					<?php $this->months_dropdown( 'ywsbs_subscription' ); ?>
					<?php submit_button( __( 'Filter', 'yith-woocommerce-subscription' ), 'button', false, false, array( 'id' => 'post-query-submit' ) ); ?>
				</div></div>
			<?php
			remove_filter( 'months_dropdown_results', array( $this, 'get_months' ), 10 );
		}

	}

	/**
	 * @param $months
	 *
	 * @return array|null|object
	 */
	public function get_months( $months ) {
			 global $wpdb;
			$table_name = $wpdb->prefix . 'yith_ywsbs_activities_log';
			$months     = $wpdb->get_results(
				"
                        SELECT DISTINCT YEAR( timestamp_date ) AS year, MONTH( timestamp_date ) AS month
                        FROM $table_name
                        ORDER BY timestamp_date DESC
                    "
			);
			return $months;
	}

	/**
	 * Display the search box.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $text The search button text
	 * @param string $input_id The search input id
	 */
	public function search_box( $text, $input_id ) {

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';}

		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id; ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id; ?>" name="s" value="<?php _admin_search_query(); ?>" placeholder="<?php _e( 'Search', 'yith-woocommerce-subscription' ); ?>"/>
			<?php submit_button( $text, 'button', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
		<?php
	}


}
