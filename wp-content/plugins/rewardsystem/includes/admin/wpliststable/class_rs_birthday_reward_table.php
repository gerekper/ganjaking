<?php

/**
 * Birthday Reward Points Log Table
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'SRP_Birthday_Reward_Table' ) ) {

	/**
	 * SRP_Birthday_Reward_Table Class.
	 * */
	class SRP_Birthday_Reward_Table extends WP_List_Table {

		/**
		 * Total Count of Table
		 * */
		private $total_items;

		/**
		 * Per page count
		 * */
		private $perpage;

		/**
		 * Database
		 * */
		private $database;

		/**
		 * Offset
		 * */
		private $offset;

		/**
		 * Order BY
		 * */
		private $orderby = 'ORDER BY ID DESC';

		/**
		 * Post type
		 * */
		private $post_type = SRP_Register_Post_Type::BIRTHDAY_POSTTYPE;

		/**
		 * Base URL
		 * */
		private $base_url;

		/**
		 * Current URL
		 * */
		private $current_url;

		/**
		 * Table Slug
		 *
		 * @var string
		 * */
		private $table_slug = 'srp';

		/**
		 * Prepare the table Data to display table based on pagination.
		 * */
		public function prepare_items() {
			global $wpdb;
			$this->database = $wpdb;

			$this->base_url = get_permalink();

			add_filter( sanitize_key( $this->table_slug . '_query_where' ), array( $this, 'custom_search' ), 10, 1 );
			add_filter( sanitize_key( $this->table_slug . '_query_orderby' ), array( $this, 'query_orderby' ) );
			add_filter( 'disable_months_dropdown', array( $this, 'disable_months_dropdown' ), 10, 2 );

			$this->prepare_current_url();
			$this->get_perpage_count();
			$this->get_current_pagenum();
			$this->get_current_page_items();
			$this->prepare_pagination_args();
			$this->prepare_column_headers();
		}

		/**
		 * Get per page count
		 * */
		private function get_perpage_count() {

			$this->perpage = 10;
		}

		/**
		 * Prepare pagination
		 * */
		private function prepare_pagination_args() {

			$this->set_pagination_args(
				array(
					'total_items' => $this->total_items,
					'per_page'    => $this->perpage,
				)
			);
		}

		/**
		 * Get current page number
		 * */
		private function get_current_pagenum() {

			$this->offset = $this->perpage * ( $this->get_pagenum() - 1 );
		}

		/**
		 * Prepare header columns
		 * */
		private function prepare_column_headers() {
			$columns               = $this->get_columns();
			$hidden                = $this->get_hidden_columns();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
		}

		/**
		 * Initialize the columns
		 * */
		public function get_columns() {
			return array(
				'user_name'     => esc_html__( 'Username', 'rewardsystem' ),
				'email_id'      => esc_html__( 'Email ID', 'rewardsystem' ),
				'birthday_date' => esc_html__( 'Birthday Date', 'rewardsystem' ),
			);
		}

		/**
		 * Initialize the hidden columns
		 * */
		public function get_hidden_columns() {
			return array();
		}

		/**
		 * Get current url
		 * */
		private function prepare_current_url() {

			$pagenum       = $this->get_pagenum();
			$args['paged'] = $pagenum;
			$url           = add_query_arg( $args, $this->base_url );

			$this->current_url = $url;
		}

		/**
		 * Prepare each column data
		 * */
		protected function column_default( $item, $column_name ) {

			switch ( $column_name ) {
				case 'user_name':
					return $item->get_user_name();
				case 'email_id':
					return $item->get_user_email();
				case 'birthday_date':
					return SRP_Date_Time::get_wp_format_datetime( $item->get_birthday_date(), 'date', true, true );
			}
		}

		/**
		 * Initialize the columns
		 * */
		private function get_current_page_items() {
			$where = " where post_type='" . $this->post_type . "'";

			$where = $this->get_query_where();
						/**
						 * Hook:query_limit.
						 *
						 * @since 1.0
						 */
			$limit = apply_filters( $this->table_slug . '_query_limit', $this->perpage );
			/**
						 * Hook:query_offset.
						 *
						 * @since 1.0
						 */
						$offset = apply_filters( $this->table_slug . '_query_offset', $this->offset );
			/**
						 * Hook:query_orderby.
						 *
						 * @since 1.0
						 */
						$orderby = apply_filters( $this->table_slug . '_query_orderby', $this->orderby );

			$count_items       = $this->database->get_results( 'SELECT DISTINCT ID FROM ' . $this->database->posts . " AS p {$where} {$orderby}" );
			$this->total_items = count( $count_items );

			$prepare_query = $this->database->prepare( 'SELECT DISTINCT ID FROM ' . $this->database->posts . " AS p {$where} {$orderby} LIMIT %d,%d", $offset, $limit );

			$items = $this->database->get_results( $prepare_query, ARRAY_A );

			$this->prepare_item_object( $items );
		}

		/**
		 * Get the query where clauses.
		 *
		 * @return string
		 * */
		private function get_query_where() {
			$where = " where post_type='" . $this->post_type . "' and post_status IN('publish')";

			// Search.
			$where = $this->custom_search( $where );
						/**
						 * Hook:query_where.
						 *
						 * @since 1.0
						 */
			return apply_filters( $this->table_slug . '_query_where', $where );
		}

		/**
		 * Prepare item Object
		 * */
		private function prepare_item_object( $items ) {
			$prepare_items = array();
			if ( srp_check_is_array( $items ) ) {
				foreach ( $items as $item ) {
					$prepare_items[] = srp_get_birthday( $item['ID'] );
				}
			}

			$this->items = $prepare_items;
		}

		/**
		 * Sort
		 * */
		public function query_orderby( $orderby ) {

			if ( empty( $_REQUEST[ 'orderby' ] ) ) { // @codingStandardsIgnoreLine.
				return $orderby;
			}

			$order = 'DESC';
			if ( ! empty( $_REQUEST[ 'order' ] ) && is_string( $_REQUEST[ 'order' ] ) ) { // @codingStandardsIgnoreLine.
				if ( 'ASC' === strtoupper( wc_clean( wp_unslash( $_REQUEST[ 'order' ] ) ) ) ) { // @codingStandardsIgnoreLine.
					$order = 'ASC';
				}
			}

			switch ( wc_clean( wp_unslash( $_REQUEST[ 'orderby' ] ) ) ) { // @codingStandardsIgnoreLine.
				case 'status':
					$orderby = ' ORDER BY p.post_status ' . $order;
					break;
				case 'created':
					$orderby = ' ORDER BY p.post_date ' . $order;
					break;
				case 'modified':
					$orderby = ' ORDER BY p.post_modified ' . $order;
					break;
			}
			return $orderby;
		}

		/**
		 * Custom Search
		 * */
		public function custom_search( $where ) {
			if ( isset( $_REQUEST[ 's' ] ) ) { // @codingStandardsIgnoreLine.
				$birthday_ids = array();
				$terms        = explode( ' , ' , wc_clean( wp_unslash( $_REQUEST[ 's' ] ) ) ) ; // @codingStandardsIgnoreLine.

				foreach ( $terms as $term ) {
					$term       = $this->database->esc_like( ( $term ) );
					$post_query = new SRP_Query( $this->database->prefix . 'posts', 'p' );
					$post_query->select( 'DISTINCT `p`.ID' )
							->leftJoin( $this->database->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
							->where( '`p`.post_type', $this->post_type )
							->whereIn( '`p`.post_status', array( 'publish' ) )
							->where( '`pm`.meta_key', 'srp_user_name' )
							->whereLike( '`pm`.meta_value', '%' . $term . '%' )
							->where( '`pm`.meta_key', 'srp_user_email', 'OR' )
							->whereLike( '`pm`.meta_value', '%' . $term . '%' );

					$birthday_ids = $post_query->fetchCol( 'ID' );
				}

				$birthday_ids = srp_check_is_array( $birthday_ids ) ? $birthday_ids : array( 0 );
				$where       .= ' AND (id IN (' . implode( ' , ', $birthday_ids ) . '))';
			}

			return $where;
		}
	}

}
