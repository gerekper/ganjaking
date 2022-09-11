<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Activities List Table
 *
 * @class   YITH_YWSBS_Activities_List_Table
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Subscription
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
// phpcs:disable WordPress.Security.NonceVerification.Recommended

/**
 * Class YITH_YWSBS_Activities_List_Table
 */
class YITH_YWSBS_Activities_List_Table extends WP_List_Table {


	/**
	 * YITH_YWSBS_Activities_List_Table constructor.
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
			'activity'       => esc_html__( 'Activity', 'yith-woocommerce-subscription' ),
			'subscription'   => esc_html__( 'Subscription', 'yith-woocommerce-subscription' ),
			'order'          => esc_html__( 'Order', 'yith-woocommerce-subscription' ),
			'description'    => esc_html__( 'Description', 'yith-woocommerce-subscription' ),
			'timestamp_date' => esc_html__( 'Date', 'yith-woocommerce-subscription' ),
		);

		return $columns;
	}

	/**
	 * Prepare items to show
	 */
	public function prepare_items() {
		global $wpdb, $_wp_column_headers;

		$screen = get_current_screen();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$orderby = ! empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'act.id'; //phpcs:ignore
		$order   = ! empty( $_GET['order'] ) ? $_GET['order'] : 'DESC';       //phpcs:ignore

		$join         = '';
		$where        = '';
		$order_string = '';
		if ( ! empty( $orderby ) & ! empty( $order ) ) {
			$order_string = ' ORDER BY ' . $orderby . ' ' . $order;
		}

		$posted = $_REQUEST; // phpcs:ignore
		// by user.

		if ( isset( $posted['m'] ) ) {
			// The "m" parameter is meant for months but accepts datetimes of varying specificity.
			if ( $posted['m'] ) {
				$where .= ' AND YEAR(timestamp_date)=' . substr( $posted['m'], 0, 4 );
				if ( strlen( $posted['m'] ) > 5 ) {
					$where .= ' AND MONTH(timestamp_date)=' . substr( $posted['m'], 4, 2 );
				}
				if ( strlen( $posted['m'] ) > 7 ) {
					$where .= ' AND DAYOFMONTH(timestamp_date)=' . substr( $posted['m'], 6, 2 );
				}
				if ( strlen( $posted['m'] ) > 9 ) {
					$where .= ' AND HOUR(timestamp_date)=' . substr( $posted['m'], 8, 2 );
				}
				if ( strlen( $posted['m'] ) > 11 ) {
					$where .= ' AND MINUTE(timestamp_date)=' . substr( $posted['m'], 10, 2 );
				}
				if ( strlen( $posted['m'] ) > 13 ) {
					$where .= ' AND SECOND(timestamp_date)=' . substr( $posted['m'], 12, 2 );
				}
			}
		}

		if ( isset( $posted['subscription'] ) ) {
			$where .= ' AND subscription=' . $posted['subscription'];
		}

		$table_name = $wpdb->prefix . 'yith_ywsbs_activities_log';
		$search     = ! empty( $posted['s'] ) ? $posted['s'] : '';

		$join  = apply_filters( 'ywsbs_activities_list_table_join', $join, $table_name );
		$where = apply_filters( 'ywsbs_activities_list_table_where', $where, $table_name );

		$query = "SELECT * FROM $table_name as act $join  where 1=1 $where $order_string";

		if ( '' !== $search ) {
			$query = "SELECT * FROM $table_name as act $join where 1=1 AND  act.subscription LIKE '%$search%' OR act.order = '%$search%' OR act.activity LIKE '%$search%' OR act.description LIKE '%$search%'  $where $order_string";
		}

		$totalitems = $wpdb->query( $query );                      // phpcs:ignore

		$perpage = 20;
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
	 * @param object $item        Current Object.
	 * @param string $column_name Current Column.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {

		$subscription        = ywsbs_get_subscription( $item->subscription );
		$order               = wc_get_order( $item->order );
		$order_number        = $order instanceof WC_Product ? $order->get_order_number() : '#' . $item->order;
		$subscription_number = isset( $subscription ) ? $subscription->get_number() : '#' . $item->subscription;

		switch ( $column_name ) {

			case 'subscription':
				return '<a href="' . admin_url( 'post.php?post=' . $item->subscription . '&action=edit' ) . '">' . $subscription_number . '</a>';
			case 'order':
				return ( $item->order ) ? '<a href="' . admin_url( 'post.php?post=' . $item->order . '&action=edit' ) . '">' . $order_number . '</a>' : '';
			case 'timestamp_date':
				// translators: 1. Date 2. Time.
				return sprintf( _x( '%1$s at %2$s', '1$: Date 2$: Time', 'yith-woocommerce-subscription' ), date_i18n( wc_date_format(), strtotime( $item->timestamp_date ) ), date_i18n( wc_time_format(), strtotime( $item->timestamp_date ) ) );
			default:
				return $item->$column_name; // Show the whole array for troubleshooting purposes.
		}
	}

	/**
	 * Get sorttable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'activity'       => array( 'activity', false ),
			'subscription'   => array( 'subscription', false ),
			'order'          => array( 'order', false ),
			'timestamp_date' => array( 'timestamp_date', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination, which
	 * includes our Filters
	 *
	 * @param string $which The placement, one of 'top' or 'bottom'.
	 *
	 * @since 1.0.0
	 */
	public function extra_tablenav( $which ) {
		if ( 'top' === $which ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'yith_ywsbs_activities_log';

			$options        = '<option value="">' . esc_html__( 'All status', 'yith-woocommerce-subscription' ) . '</option>';
			$current_status = empty( $_REQUEST['activity_status'] ) ? '' : wp_unslashes( $_REQUEST['activity_status'] ); // phpcs:ignore

			$join  = apply_filters( 'ywsbs_activities_status_join', '', $table_name );
			$where = apply_filters( 'ywsbs_activities_status_where', 'WHERE 1=1 ', $table_name );
			$query = "SELECT count(*) as counter, status FROM $table_name as act " . $join . ' ' . $where . ' GROUP BY status';

			$results = $wpdb->get_results( $query ); // phpcs:ignore

			foreach ( $results as $item ) {
				$checked  = checked( $item->status, $current_status, false );
				$options .= '<option value="' . $item->status . '" ' . $checked . '>' . ywsbs_get_activity_status( $item->status ) . ' (' . $item->counter . ')</option>';
			}

			add_filter( 'months_dropdown_results', array( $this, 'get_months' ), 10 );
			?>
			<div class="alignleft actions">
				<?php $this->months_dropdown( 'ywsbs_subscription' ); ?>
				<?php submit_button( esc_html__( 'Filter', 'yith-woocommerce-subscription' ), 'button', false, false, array( 'id' => 'post-query-submit' ) ); ?>
			</div>
			<?php
			remove_filter( 'months_dropdown_results', array( $this, 'get_months' ), 10 );
		}

	}

	/**
	 * Get months.
	 *
	 * @param string $months Months.
	 *
	 * @return array|null|object
	 */
	public function get_months( $months ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'yith_ywsbs_activities_log';
		$months     = $wpdb->get_results( " SELECT DISTINCT YEAR( timestamp_date ) AS year, MONTH( timestamp_date ) AS month FROM $table_name ORDER BY timestamp_date DESC" ); // phpcs:ignore

		return $months;
	}

	/**
	 * Display the search box.
	 *
	 * @param string $text     The search button text.
	 * @param string $input_id The search input id.
	 *
	 * @since  1.0.0
	 * @access public
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
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label>
			<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" placeholder="<?php esc_attr_e( 'Search', 'yith-woocommerce-subscription' ); ?>" />
			<?php submit_button( $text, 'button', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
		<?php
	}

}
