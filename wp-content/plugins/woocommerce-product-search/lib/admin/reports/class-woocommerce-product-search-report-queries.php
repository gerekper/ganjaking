<?php
/**
 * class-woocommerce-product-search-report-queries.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if( !class_exists( 'WP_List_Table' ) ){
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Query report.
 */
class WooCommerce_Product_Search_Report_Queries extends WP_List_Table {

	const PER_PAGE_DEFAULT = 20;
	const PER_PAGE_MIN     = 1;

	const QUERY_RESULTS_ALL     = 0;
	const QUERY_RESULTS_ONLY    = 1;
	const QUERY_RESULTS_NONE    = 2;
	const QUERY_RESULTS_DEFAULT = self::QUERY_RESULTS_ALL;

	private $per_page = self::PER_PAGE_DEFAULT;

	/**
	 * Constructor.
	 */
	public function __construct( $args = array() ){
		parent::__construct( array(
			'singular' => 'query',
			'plural'   => 'queries',
			'ajax'     => false
		) );

		$user_per_page = intval( $this->get_items_per_page( 'woocommerce-product-search-report-queries-per-page', self::PER_PAGE_DEFAULT ) );
		$per_page = isset( $_REQUEST['per_page'] ) ? intval( $_REQUEST['per_page'] ) : $user_per_page;
		if ( $per_page < self::PER_PAGE_MIN ) {
			$per_page = self::PER_PAGE_MIN;
		}
		if ( $user_per_page !== $per_page ) {
			if ( !empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-queries' ) ) { 
				update_user_option( get_current_user_id(), 'woocommerce-product-search-report-queries-per-page', $per_page );
			}
		}
		$this->per_page = $per_page;
	}

	public function no_items() {
		esc_html_e( 'There are no queries to show.', 'woocommerce-product-search' );
	}

	public function output_report() {
		$this->prepare_items();
		echo '<style type="text/css">';

		echo '</style>';
		echo '<div id="poststuff" class="woocommerce-reports-wide">';
		echo '<form method="get" id="woocommerce-product-search-report-queries">';
		$this->display();
		echo '</form>';
		echo '</div>'; 
	}

	/**
	 * Renders table cell contents.
	 *
	 * @param array $item row data
	 * @param string $column_name the name of the column
	 *
	 * @return string the <td> content
	 */
	function column_default( $item, $column_name ){
		$result = '';
		if ( isset( $item[$column_name] ) && ( $item[$column_name] ) !== null ) {
			switch( $column_name ) {
				case 'query' :
				case 'min_date' :
				case 'max_date' :
					$result = esc_html( $item[$column_name] );
					break;
				case 'query_id' :
				case 'hits' :
					$result = intval( $item[$column_name] );
					break;
				default :
					$result = esc_html( $item[$column_name] );
			}
		}
		return $result;
	}

	/**
	 * Display the checkbox.
	 *
	 * @param array $item row data
	 *
	 * @return string checkbox HTML input field
	 */
	function column_cb( $item ){
		return sprintf(
			'<input class="query-cb" type="checkbox" name="_query_id[]" value="%d" />',
			intval( $item['query_id'] )
		);
	}

	/**
	 * Defines table columns.
	 * 
	 * @see WP_List_Table::get_columns()
	 */
	function get_columns(){
		$columns = array(

			'query'    => __( 'Query', 'woocommerce-product-search' ),
			'hits'     => __( 'Hits', 'woocommerce-product-search' ),
			'query_id' => __( 'ID', 'woocommerce-product-search' ),
			'min_date' => __( 'First', 'woocommerce-product-search' ),
			'max_date' => __( 'Last', 'woocommerce-product-search' )
		);
		return $columns;
	}

	/**
	 * Defines sortable columns.
	 *
	 * @see WP_List_Table::get_sortable_columns()
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'query_id' => array( 'query_id', false ),
			'query'    => array( 'query', false ),
			'hits'     => array( 'hits', false ),
			'min_date' => array( 'min_date', false ),
			'max_date' => array( 'max_date', false )
		);
		return $sortable_columns;
	}

	/**
	 * Overrides the parent method to make it show the initial sorting-indicator as used by default.
	 *
	 * {@inheritDoc}
	 * @see WP_List_Table::print_column_headers()
	 */
	public function print_column_headers( $with_id = true ) {

		if ( !isset( $_GET['orderby'] ) ) {
			$_GET['orderby'] = 'hits';
			$_GET['order']   = 'desc';
			parent::print_column_headers( $with_id );
			unset( $_GET['orderby'] );
			unset( $_GET['order'] );
		} else {
			parent::print_column_headers( $with_id );
		}
	}

	/**
	 * Defines bulk actions available.
	 * 
	 * @see WP_List_Table::get_bulk_actions()
	 */
	function get_bulk_actions() {
		$actions = array(

		);
		return $actions;
	}

	/**
	 * Bulk action handler.
	 */
	function process_bulk_action() {
		$action = $this->current_action();
		switch( $action ) {
			case 'what' :
				if ( !empty( $_REQUEST['_query_id'] ) ) {
					$query_ids = $_REQUEST['_query_id'];
					if ( is_array( $query_ids ) ) {
						foreach( $query_ids as $query_id ) {

						}
					}
				}
				break;
		}
	}

	/**
	 * Gets query data and prepares it for the table.
	 *
	 * @see WP_List_Table::prepare_items()
	 */
	function prepare_items() {
		global $wpdb;

		$per_page = $this->per_page;

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$primary  = 'query';
		$this->_column_headers = array( $columns, $hidden, $sortable, $primary );

		$this->process_bulk_action();

		$search_query = isset( $_REQUEST['search_query'] ) ? trim( $_REQUEST['search_query'] ) : '';

		$search_query_mode  = !empty( $_REQUEST['search_query_mode'] ) ? $_REQUEST['search_query_mode'] : 'startswith';
		switch( $search_query_mode ) {
			case 'startswith' :
			case 'exact' :
			case 'contains' :
				break;
			default :
				$search_query_mode = 'startswith';
		}

		$query_results = isset( $_REQUEST['query_results'] ) ? intval( $_REQUEST['query_results'] ) : self::QUERY_RESULTS_DEFAULT;
		$start_date = !empty( $_REQUEST['start_date'] ) ? strtotime( $_REQUEST['start_date'] ) : false;
		$end_date   = !empty( $_REQUEST['end_date'] ) ? strtotime( $_REQUEST['end_date'] ) : false;

		$orderby = isset( $_REQUEST['orderby'] ) ? strtolower( $_REQUEST['orderby'] ) : 'hits';
		switch( $orderby ) {
			case 'hits' :
			case 'query' :
			case 'query_id' :
			case 'min_date' :
			case 'max_date' :
				break;
			default :
				$orderby = 'hits';
		}

		$order = isset( $_REQUEST['order'] ) ? strtoupper( $_REQUEST['order'] ) : 'DESC';
		switch( $order ) {
			case 'ASC' :
			case 'DESC' :
				break;
			default :
				$order = 'DESC';
		}

		$hit_table = WooCommerce_Product_Search_Controller::get_tablename( 'hit' );
		$query_table = WooCommerce_Product_Search_Controller::get_tablename( 'query' );

		$current_page = $this->get_pagenum();

		$conditions = array();
		$values     = array();

		if ( !empty( $search_query ) ) {
			switch( $search_query_mode ) {
				case 'startswith' :
					$conditions[] = "q.query LIKE %s";
					$values[]     = $wpdb->esc_like( $search_query ) . '%';
					break;
				case 'exact' :
					$conditions[] = 'q.query = %s';
					$values[]     = $search_query;
					break;
				case 'contains' :
					$conditions[] = "q.query LIKE %s";
					$values[]     = '%' . $wpdb->esc_like( $search_query ) . '%';
					break;
			}
		}

		if ( $start_date !== false ) {
			$conditions[] = 'h.date >=  %s ';
			$values[]     = date( 'Y-m-d', $start_date );
		}
		if ( $end_date !== false ) {
			$conditions[] = 'h.date <=  %s ';
			$values[]     = date( 'Y-m-d', $end_date );
		}
		switch( $query_results ) {
			case self::QUERY_RESULTS_ONLY :
				$conditions[] = 'h.count > %d';
				$values[]     = 0;
				break;
			case self::QUERY_RESULTS_NONE :
				$conditions[] = 'h.count = %d';
				$values[]     = 0;
				break;
		}

		$where = '';
		if ( count( $conditions ) > 0 ) {
			$where = 'WHERE ' . implode( ' AND ', $conditions );
		}

		$values[] = intval( $per_page ); 
		$values[] = intval( $per_page ) * ( $current_page - 1 ); 

		$query = $wpdb->prepare(
			"SELECT SQL_CALC_FOUND_ROWS q.query, q.query_id, MIN(h.date) min_date, MAX(h.date) max_date, COUNT(DISTINCT h.ip) hits ".
			"FROM $query_table q " .
			"LEFT JOIN $hit_table h ON q.query_id = h.query_id " .
			"$where " .
			"GROUP BY q.query_id " .
			"ORDER BY $orderby $order " .
			"LIMIT %d " .
			"OFFSET %d",
			$values
		);

		$rows = $wpdb->get_results( $query, ARRAY_A );

		$total_items = intval( $wpdb->get_var( "SELECT FOUND_ROWS()" ) );
		if ( $rows !== null ) {
			$this->items = $rows;
		}

		$pagination_args = array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		);
		$this->set_pagination_args( $pagination_args );
	}

	/**
	 * Render table filters.
	 *
	 * {@inheritDoc}
	 * @see WP_List_Table::extra_tablenav()
	 */
	function extra_tablenav( $which ) {
		echo '<div class="alignleft actions">';
		if ( $which === 'top' && !is_singular() ) {

			$per_page = $this->per_page;

			$search_query = isset( $_REQUEST['search_query'] ) ? trim( $_REQUEST['search_query'] ) : '';
			$search_query_exact = !empty( $_REQUEST['search_query_exact'] );
			$search_query_mode  = !empty( $_REQUEST['search_query_mode'] ) ? $_REQUEST['search_query_mode'] : 'startswith';
			switch( $search_query_mode ) {
				case 'startswith' :
				case 'exact' :
				case 'contains' :
					break;
				default :
					$search_query_mode = 'startswith';
			}
			$query_results = isset( $_REQUEST['query_results'] ) ? $_REQUEST['query_results'] : self::QUERY_RESULTS_DEFAULT;

			echo '<div style="display:inline-block;white-space:nowrap">';
			echo '<label>';
			esc_html_e( 'Query', 'woocommerce-product-search' );
			echo '&nbsp;';
			printf(
				'<input type="text" name="search_query" value="%s" />',
				esc_attr( $search_query )
			);
			echo '</label>';
			echo '&ensp;';
			printf( '<label title="%s" style="white-space:nowrap">', esc_attr__( 'Queries that start with &hellip;', 'woocommerce-product-search' ) );
			printf(
				'<input type="radio" name="search_query_mode" value="startswith" %s/>',
				$search_query_mode === 'startswith' ? 'checked="checked"' : ''
			);
			esc_html_e( 'Starts', 'woocommerce-product-search' );
			echo '</label>';
			echo '&nbsp;';
			printf( '<label title="%s" style="white-space:nowrap">', esc_attr__( 'Queries that exactly match &hellip;', 'woocommerce-product-search' ) );
			printf(
				'<input type="radio" name="search_query_mode" value="exact" %s/>',
				$search_query_mode === 'exact' ? 'checked="checked"' : ''
			);
			esc_html_e( 'Exact', 'woocommerce-product-search' );
			echo '</label>';
			echo '&nbsp;';
			printf( '<label title="%s" style="white-space:nowrap">', esc_attr__( 'Queries that contain &hellip;', 'woocommerce-product-search' ) );
			printf(
				'<input type="radio" name="search_query_mode" value="contains" %s/>',
				$search_query_mode === 'contains' ? 'checked="checked"' : ''
			);
			esc_html_e( 'Contains', 'woocommerce-product-search' );
			echo '</label>';
			echo '</div>';

			echo '&emsp;';

			echo '<div style="display:inline-block;white-space:nowrap">';
			printf('<label title="%s">', esc_attr__( 'Number of items per page', 'woocommerce-product-search' ) );
			esc_html_e( 'Display', 'woocommerce-product-search' );
			echo '&nbsp;';
			printf(
				'<input step="1" min="1" max="999" class="screen-per-page" name="per_page" maxlength="3" value="%d" type="number">',
				intval( $per_page )
			);
			echo '</label>';
			echo '</div>';

			echo '&emsp;';

			echo '<div style="display:inline-block;white-space:nowrap">';
			printf(
				'<label title="%s"><input type="radio" name="query_results" value="%d" %s/>&nbsp;%s</label>',
				esc_html__( 'Show results for queries that matched products or not', 'woocommerce-product-search' ),
				self::QUERY_RESULTS_ALL,
				$query_results == self::QUERY_RESULTS_ALL ? ' checked="checked" ' : '',
				esc_html__( 'All', 'woocommerce-product-search' )
			);
			echo '&ensp;';
			printf(
				'<label title="%s"><input type="radio" name="query_results" value="%d" %s/>&nbsp;%s</label>',
				esc_html__( 'Show results for queries that matched products', 'woocommerce-product-search' ),
				self::QUERY_RESULTS_ONLY,
				$query_results == self::QUERY_RESULTS_ONLY ? ' checked="checked" ' : '',
				esc_html__( 'With', 'woocommerce-product-search' )
			);
			echo '&ensp;';
			printf(
				'<label title="%s"><input type="radio" name="query_results" value="%d" %s/>&nbsp;%s</label>',
				esc_html__( 'Show results for queries that matched no products', 'woocommerce-product-search' ),
				self::QUERY_RESULTS_NONE,
				$query_results == self::QUERY_RESULTS_NONE ? ' checked="checked" ' : '',
				esc_html__( 'Without', 'woocommerce-product-search' )
			);
			echo '</div>';

			echo '&emsp;';

			echo '<div style="display:inline-block;white-space:nowrap">';
			printf(
				'<input type="text" size="11" placeholder="yyyy-mm-dd" value="%s" name="start_date" class="range_datepicker from" />',
				( ! empty( $_REQUEST['start_date'] ) ? esc_attr( wp_unslash( $_REQUEST['start_date'] ) ) : '' )
			);
			echo '<span>&ndash;</span>';
			printf(
				'<input type="text" size="11" placeholder="yyyy-mm-dd" value="%s" name="end_date" class="range_datepicker to" />',
				( ! empty( $_REQUEST['end_date'] ) ? esc_attr( wp_unslash( $_REQUEST['end_date'] ) ) : '' )
			);
			echo '</div>';

			echo '&emsp;';

			printf( '<input type="hidden" name="page" value="%s"/>', esc_attr( $_REQUEST['page'] ) );
			printf( '<input type="hidden" name="tab" value="%s"/>', esc_attr( $_REQUEST['tab'] ) );
			printf( '<input type="hidden" name="report" value="%s"/>', esc_attr( $_REQUEST['report'] ) );
			submit_button( esc_html__( 'Filter', 'woocommerce-product-search' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );

			echo '&ensp;';

			printf(
				'<a style="display:inline-block;vertical-align:middle" href="%s">%s</a>',
				esc_url( wp_nonce_url( add_query_arg( array( 'per_page' => self::PER_PAGE_DEFAULT ), admin_url( 'admin.php?page=wc-reports&tab=search&report=queries' ) ), 'bulk-queries' ) ),
				esc_html__( 'Clear', 'woocommerce-product-search' )
			);
		}
		echo '</div>'; 
	}

}
