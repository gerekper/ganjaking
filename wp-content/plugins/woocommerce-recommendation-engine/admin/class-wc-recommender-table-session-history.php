<?php

if ( !class_exists( 'WP_List_Table' ) ) :
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
endif;

class WC_Recommender_Table_Session_History extends WP_List_Table {

	public $is_trash = false;

	public function __construct() {
		parent::__construct( array(
			'singular' => 'Session History',
			'plural'   => 'Session History Items',
			'ajax'     => false,
			'screen'   => 'wc_recommender_admin'
		) );
	}

	public function get_bulk_actions() {
		$actions = array();
		$actions['truncate-history-60'] = __( 'Truncate History ( Keep Past 60 Days )', 'wc_recommender' );
		$actions['truncate-history-30'] = __( 'Truncate History ( Keep Past 30 Days )', 'wc_recommender' );
		$actions['truncate-history-10'] = __( 'Truncate History ( Keep Past 10 Days )', 'wc_recommender' );

		return $actions;
	}

	public function get_table_classes() {
		return array( 'widefat', 'fixed', 'posts' );
	}

	public function extra_tablenav( $which ) {
		echo '<div class="alignleft actions">';
		if ( $which == 'top' ) {

		}
		echo '</div>';
	}

	public function get_columns() {

		$c = array(
			'cb'            => '<input type="checkbox" />',
			'activity_id'   => __( 'ID' ),
			'session_id'    => __( 'Session ID', 'wc_recommender' ),
			'activity_type' => __( 'Type', 'wc_recommender' ),
			'product_id'    => __( 'Product', 'wc_recommender' ),
			'order_id'      => __( 'Order', 'wc_recommender' ),
			'user_id'       => __( 'User', 'wc_recommender' ),
			'activity_date' => __( 'Date', 'wc_recommender' )
		);

		return $c;
	}

	public function get_sortable_columns() {
		$c = array(
			'session_id'    => array(
				'session_id',
				false
			),
			'activity_type' => array(
				'activity_type',
				false
			),
			'product_id'    => array(
				'product_id',
				false
			),
			'order_id'      => array(
				'order_id',
				false
			),
			'user_id'       => array(
				'user_id',
				false
			),
			'activity_date' => array(
				'activity_date',
				true
			)

		);

		return $c;
	}

	public function display_rows() {
		// Query the post counts for this page
		//Get the records registered in the prepare_items method
		$records = $this->items;

		//Get the columns registered in the get_columns and get_sortable_columns methods
		list( $columns, $hidden ) = $this->get_column_info();

		$row_count = 0;
		//Loop for each record
		if ( !empty( $records ) ) {

			foreach ( $records as $rec ) {

				$row_count ++;
				$row_class = $row_count % 2 ? 'alternate' : '';

				//Open the line
				echo '<tr id="record_' . $rec->activity_id . '" class="' . $row_class . '">';
				foreach ( $columns as $column_name => $column_display_name ) {

					//Style attributes for each col
					$class = "class='$column_name column-$column_name'";
					$style = "";
					if ( in_array( $column_name, $hidden ) ) {
						$style = ' style="display:none;"';
					}
					$attributes = $class . $style;
					$editlink   = '';
					//Display the cell
					switch ( $column_name ) {
						case 'cb' :
							echo '<th class="check-column">';
							echo '<input type="checkbox" value="' . esc_attr( $rec->activity_id ) . '" name="ids[]"/>';
							echo '</th>';
							break;
						case "activity_id":
							echo '<td>' . ( empty( $rec->activity_id ) ? '' : $rec->activity_id ) . '</td>';
							break;
						case "activity_type":
							echo '<td>' . ( empty( $rec->activity_type ) ? '' : $rec->activity_type ) . '</td>';
							break;
						case "session_id":
							echo '<td>' . ( empty( $rec->session_id ) ? '' : $rec->session_id ) . '</td>';
							break;
						case 'activity_type' :
							echo '<td>' . ( empty( $rec->activity_type ) ? '' : $rec->activity_type ) . '</td>';
							break;
						case "product_id":
							echo '<td>' . ( empty( $rec->product_id ) ? '' : $rec->product_id ) . '</td>';
							break;
						case 'order_id' :
							echo '<td>' . ( empty( $rec->order_id ) ? '' : $rec->order_id ) . '</td>';
							break;
						case "user_id":
							echo '<td>' . ( empty( $rec->user_id ) ? '' : $rec->user_id ) . '</td>';
							break;
						case 'activity_date' :
							echo '<td>' . ( empty( $rec->activity_date ) ? '' : $rec->activity_date ) . '</td>';
							break;
					}
				}

				//Close the line
				echo '</tr>';
			}
		}
	}

	public function prepare_items() {
		global $wpdb, $woocommerce_recommender;

		$items_table_name = $woocommerce_recommender->db_tbl_session_activity;
		$items_sql        = "SELECT * FROM $items_table_name";


		//Parameters that are going to be used to order the result
		$orderby_sql = " ORDER BY ";
		$orderby     = !empty( $_REQUEST["orderby"] ) ? esc_sql( $_REQUEST["orderby"] ) : 'activity_date';
		$order       = !empty( $_REQUEST["order"] ) ? esc_sql( $_REQUEST["order"] ) : 'DESC';
		if ( !empty( $orderby ) & !empty( $order ) ) {
			$orderby_sql .= $orderby . ' ' . $order;
		} else {
			$orderby_sql .= 'activity_date' . ' ' . 'DESC';
		}

		$items_sql .= $orderby_sql;


		/* -- Pagination parameters -- */
		//Number of elements in your table?
		$totalitems = $wpdb->query( $items_sql ); //return the total number of affected rows
		//How many to display per page?
		$perpage = 40;
		//Which page is this?
		$paged = !empty( $_REQUEST["paged"] ) ? esc_sql( $_REQUEST["paged"] ) : '';
		//Page Number
		if ( empty( $paged ) || !is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}
		//How many pages do we have in total?
		$totalpages = ceil( $totalitems / $perpage );
		//adjust the query to take pagination into account
		if ( !empty( $paged ) && !empty( $perpage ) ) {
			$offset    = ( $paged - 1 ) * $perpage;
			$items_sql .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}

		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page"    => $perpage,
		) );
		//The pagination links are automatically built according to those parameters


		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/* -- Fetch the items -- */
		$items       = $wpdb->get_results( $items_sql );
		$this->items = $items;
	}

	public function no_items() {
		_e( 'Empty folder' );
	}

}
